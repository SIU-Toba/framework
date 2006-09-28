<?php
require_once("toba_ei.php");

/**
* Muestra un árbol donde el usuario puede colapsar/descolapsar niveles
* Estos niveles se pueden cargar por adelantado o hacer una cargar AJAX
* Cada nodo debe implementar la interfaz toba_nodo_arbol
* 
* @see toba_nodo_arbol
* @package Componentes
* @subpackage Eis
*/
class toba_ei_arbol extends toba_ei
{
	protected $prefijo = 'arbol';	
	protected $nodos_inicial;
	protected $item_propiedades = array();
	protected $nivel_apertura = 1;
	protected $datos_apertura;
	protected $todos_abiertos = false;
	protected $frame_destino = null;
	
	function __construct($datos)
	{
		parent::__construct($datos);
	}
	
	/**
	 * Respuesta al pedido AJAX de apertura de un nodo no cargado anteriormente
	 * Dispara el evento cargar_nodo($id) para que se retorne el toba_nodo_arbol asociado
	 */
	function servicio__ejecutar()
	{
		toba::memoria()->desactivar_reciclado();		
		$id_nodo = toba::memoria()->get_parametro('id_nodo');
		$nodo = $this->reportar_evento('cargar_nodo', $id_nodo);
		if (isset($nodo) && $nodo !== apex_ei_evt_sin_rpta) {
			$html = $this->recorrer_hijos(current($nodo), 0);
			echo $html;
		} else {
			toba::logger()->warning("toba_ei_arbol: No se pudo obtener el nodo que representa al ID $id_nodo");
		}
	}
	
	protected function cargar_eventos()
	{
		parent::cargar_lista_eventos();		
		$this->eventos['cambio_apertura'] = array();
		$this->eventos['ver_propiedades'] = array();
		$this->eventos['cargar_nodo'] = array();		
	}	

	/**
	 * Fuerza a que determinados nodos se encuentren abiertos o cerrados
	 * @param array $datos_apertura array('id_nodo' => boolean, ...)
	 */
	function set_apertura_nodos($datos_apertura)
	{
		$this->datos_apertura = $datos_apertura;
	}
	
	/**
	 * Cambia el nivel inicial de apertura grafico de los nodos
	 */
	function set_nivel_apertura($nivel)
	{
		$this->nivel_apertura = $nivel;
	}
	
	/**
	 * Fuerza a que todos los nodos se muestren abiertos
	 */
	function set_todos_abiertos()
	{
		$this->todos_abiertos = true;	
	}
	
	/**
	 * Determina la propiedad TARGET del tag <A> html de los vinculos de cada nodo
	 * @param string $frame
	 */
	function set_frame_destino($frame)
	{
		$this->frame_destino = $frame;
	}
	
	/**
	 * Cambia los nodos del arbol, suministrandole nuevos nodo/s raiz
	 * @param array $nodos Arreglo de nodos raiz del arbol
	 */
    function set_datos($nodos)
    {
		$this->nodos_inicial = $nodos;
	}
	
	/**
	 * Carga la lista de eventos definidos desde el administrador 
	 * La redefinicion filtra solo aquellos utilizados en esta pantalla
	 * y agrega los tabs como eventos
	 */
	protected function cargar_lista_eventos()
	{
		parent::cargar_lista_eventos();
		$this->eventos['ver_propiedades'] = array('maneja_datos' => true);
	}	
	
	function disparar_eventos()
	{
		//Se guarda el layout del arbol actual				
		if (isset($_POST[$this->submit."__apertura_datos"])) {
			$datos_apertura = $_POST[$this->submit."__apertura_datos"];
			$pares = explode("||", $datos_apertura);
			$nodos = array();
			foreach ($pares as $par) {
				$par = explode("=", $par);
				if (count($par) == 2) {
					list($id, $visible) = $par;
					$nodos[$id] = $visible;
				}
			}				
			$this->datos_apertura = $nodos;
			//Se reporta el cambio de layout al padre				
			$this->reportar_evento("cambio_apertura", $this->datos_apertura);
		}
		if(isset($_POST[$this->submit]) && $_POST[$this->submit]!="") {
			$evento = $_POST[$this->submit];	
			//El evento estaba entre los ofrecidos?
			if(isset($this->memoria['eventos'][$evento]) ) {
				$parametros = null;
				if ($evento == 'ver_propiedades' && isset($_POST[$this->submit."__seleccion"])) {
					$this->reportar_evento( $evento, $_POST[$this->submit."__seleccion"] );
				}
			}
		}
	}
	
	function generar_html()
	{
		$salida = "";
		$salida .= toba_form::hidden($this->submit, '');
		$salida .= toba_form::hidden($this->submit."__apertura_datos", '');
		$salida .= toba_form::hidden($this->submit."__seleccion", '');
		$id = "id='{$this->objeto_js}_nodo_raiz'";
		$salida .= "<div class='ei-base ei-arbol-base'>";
		$this->generar_html_barra_sup(null, true,"ei-arbol-barra-sup");		
		if (isset($this->nodos_inicial)) {
			//--- Se incluye la barrita que contiene el path actual
			$barra = "";
			if (count($this->nodos_inicial) > 0) {
				$nodo = $this->nodos_inicial[0];
				while ($nodo->get_padre() != null) {
					$nodo = $nodo->get_padre();	
					$nodo_barra = "<a href='#' onclick='{$this->objeto_js}.ver_propiedades(\"";
					$nodo_barra .= $nodo->get_id()."\");' ";
					$nodo_barra .= "class='ei-arbol-ver-prop'>". $this->acortar_nombre($nodo->get_nombre_corto(),20)."</a>";
					$barra = $nodo_barra . " > ". $barra;
				}
				if ($barra != '') {
					$barra = "<div class='ei-arbol-barra-path'>$barra</div>";	
				}
				$salida .= $barra;
			}
			$salida .= "<div class='ei-cuerpo ei-arbol-cuerpo'>\n";
			foreach ($this->nodos_inicial as $nodo_inicial) {
				$salida .= "\n<ul $id class='ei-arbol-raiz'>";
				$salida .= $this->recorrer_recursivo($nodo_inicial, true);
				$salida .= "</ul>";
				$id = null;	//El id lo tiene sólo el primer nodo
			}
			$salida .= "</div>";			
		}
		$salida .= "</div>";
		echo $salida;
	}
	
	protected function recorrer_recursivo($nodo, $es_raiz = false, $nivel = 0)
	{
		//Determina si el nodo es visible en la apertura
		$salida = "\n\t<li class='ei-arbol-nodo'>";
		$es_visible = $this->nodo_es_visible($nodo, $nivel);
		$salida .= $this->mostrar_nodo($nodo, $es_visible);

		//Recursividad
		if (! $nodo->es_hoja()) {
			$estilo =  ($es_visible) ? "" : "style='display:none'";
			$salida .= "\n<ul id_nodo='{$nodo->get_id()}' class='ei-arbol-rama' $estilo>";
			$nivel = $nivel + 1;
			if ($nodo->tiene_hijos_cargados()) {
				$salida .= $this->recorrer_hijos($nodo, $nivel);
			}
			$salida .= "</ul>";
		}
		$salida .= "</li>\n";
		return $salida;
	}
	
	protected function recorrer_hijos($nodo, $nivel)
	{
		$salida = "";
		foreach ($nodo->get_hijos() as $nodo_hijo) {
			$salida .= $this->recorrer_recursivo($nodo_hijo, false, $nivel);										
		}
		return $salida;
	}
	
	protected function mostrar_nodo(toba_nodo_arbol $nodo, $es_visible)
	{
		$salida = '';
		$salida .= $this->mostrar_utilerias($nodo);
		if (! $nodo->es_hoja()) {
			if ($es_visible) {
				$img_exp_contr = toba_recurso::imagen_toba('arbol/contraer.gif', false); 
			} else {
				$img_exp_contr = toba_recurso::imagen_toba('arbol/expandir.gif', false);
			}
			$salida .= "<img src='$img_exp_contr' onclick='{$this->objeto_js}.cambiar_expansion(this);' 
						 class='ei-arbol-exp-contr'> ";
		} else {
			$salida .= gif_nulo(14,1);
		}
		$salida .= $this->mostrar_iconos($nodo);
		
		//Nombre
		$corto = $this->acortar_nombre($nodo->get_nombre_corto());
		$title= "<b>Nombre</b>: ".$nodo->get_nombre_largo()."<br><b>Id</b>:  ".$nodo->get_id();
		$extra = $nodo->get_info_extra();
		if ($extra != '') {
			$title .= "<hr>$extra";
		}
		
		$ayuda = toba_recurso::ayuda(null,  $title, 'ei-arbol-nombre');
		$nombre= "<span $ayuda>$corto</span>";
		if ($nodo->tiene_propiedades()) {
			$salida .= "<a href='#' onclick='{$this->objeto_js}.ver_propiedades(\"".$nodo->get_id()."\");' ".
						"class='ei-arbol-ver-prop'>$nombre</a>";			
		} else {
			$salida .= $nombre;
		}
		return $salida;
	}
	
	protected function nodo_es_visible($nodo, $nivel)
	//Determina si un nodo es visible viendo en la apertura de nodos
	{
		$cargado_parcial = !$nodo->es_hoja() && $nodo->tiene_hijos_cargados();
		if ($this->todos_abiertos) {
			return $cargado_parcial;
		}
		if (isset($this->datos_apertura[$nodo->get_id()])) {
			return $this->datos_apertura[$nodo->get_id()] && $cargado_parcial;
		}
		//Si no esta se determina por el nivel de apertura estandar
		return ($nivel < $this->nivel_apertura) && $cargado_parcial;
	}
	
	protected function mostrar_iconos($nodo)
	{
		$salida = '';
		foreach ($nodo->get_iconos() as $icono) {
			$img = toba_recurso::imagen($icono['imagen'], null, null, $icono['ayuda']);
			if (isset($icono['vinculo'])) {
				$salida .= "<a target='{$this->frame_destino}' href='".$icono['vinculo']."'>$img</a>\n";
			} else {
				$salida .= $img."\n";
			}
		}	
		return $salida;
	}
	
	protected function mostrar_utilerias($nodo)
	{
		$salida = "";
		$utilerias = $nodo->get_utilerias();
		if (count($utilerias) > 0) {
			$plegados = "";
			$despl = "";
			$salida .= "<span style='float:right;'>";	
			$cant_plegados = 0;
			foreach ($utilerias as $utileria) {
				$img = toba_recurso::imagen($utileria['imagen'], null, null, $utileria['ayuda']);
				if (isset($utileria['vinculo'])) {
					if (isset($utileria['target'])) {
						$target = "target='".$utileria['target']."'";
					} else {
						$target = "target='{$this->frame_destino}'";
					}
					$html = "<a href=\"".$utileria['vinculo']."\" $target>$img</a>\n";
				} else {
					$html = $img;
				}
				if (isset($utileria['plegado']) && $utileria['plegado']) {
					$plegados .= $html;
					$cant_plegados++;
				} else {
					$despl .= $html;	
				}
			}
			if ($cant_plegados > 0) {
				$img = toba_recurso::imagen_toba("expandir_izq.gif",true);
				$salida .= "<a href='#' style='padding-right:2px' onclick='toggle_nodo(this.nextSibling);return false'>$img</a>";
				$salida .= "<span style='display:none'>$plegados</span>";
			}
			$salida .= $despl;
			$salida .= "</span>";
			return $salida;
		}
	}
	
	/**
	 * Formatea el nombre de un nodo para incluir en un listado
	 */
	protected function acortar_nombre($nombre, $limite=30) 
	{
		if (strlen($nombre) <= $limite)
			return $nombre;
		else
			return substr($nombre, 0, $limite)."...";
		return $nombre;
	}
	
	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		$opciones['servicio'] = 'ejecutar';
		$opciones['objetos_destino'] = array($this->id);
		$autovinculo = toba::vinculador()->crear_autovinculo("", $opciones );
		echo $identado."window.{$this->objeto_js} = new ei_arbol('{$this->objeto_js}',
												 '{$this->submit}', '$autovinculo');\n";
	}

	//-------------------------------------------------------------------------------

	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'componentes/ei_arbol';
		return $consumo;
	}	

}

?>