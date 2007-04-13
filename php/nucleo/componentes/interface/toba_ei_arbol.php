<?php
/**
* Muestra un árbol donde el usuario puede colapsar/descolapsar niveles
* Estos niveles se pueden cargar por adelantado o hacer una cargar AJAX
* Cada nodo debe implementar la interfaz toba_nodo_arbol
* 
* @see toba_nodo_arbol
* @package Componentes
* @subpackage Eis
* @jsdoc ei_arbol ei_arbol
* @wiki Referencia/Objetos/ei_arbol
*/
class toba_ei_arbol extends toba_ei
{
	protected $_prefijo = 'arbol';	
	protected $_nodos_inicial;
	protected $_item_propiedades = array();
	protected $_nivel_apertura = 1;
	protected $_datos_apertura;
	protected $_todos_abiertos = false;
	protected $_mostrar_utilerias = true;
	protected $_mostrar_propiedades_nodos = true;
	protected $_frame_destino = null;
	
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
	
	/**
	 * @ignore 
	 */
	protected function cargar_eventos()
	{
		parent::cargar_lista_eventos();		
		$this->_eventos['cambio_apertura'] = array();
		$this->_eventos['ver_propiedades'] = array();
		$this->_eventos['cargar_nodo'] = array();		
	}	

	/**
	 * Fuerza a que determinados nodos se encuentren abiertos o cerrados
	 * @param array $datos_apertura array('id_nodo' => boolean, ...)
	 */
	function set_apertura_nodos($datos_apertura)
	{
		$this->_datos_apertura = $datos_apertura;
	}
	
	/**
	 * Cambia el nivel inicial de apertura grafico de los nodos
	 */
	function set_nivel_apertura($nivel)
	{
		$this->_nivel_apertura = $nivel;
	}
	
	/**
	 * Fuerza a que todos los nodos se muestren abiertos
	 */
	function set_todos_abiertos()
	{
		$this->_todos_abiertos = true;	
	}
	
	function set_mostrar_utilerias($mostrar)
	{
		$this->_mostrar_utilerias = $mostrar;	
	}
	
	function set_mostrar_propiedades_nodos($mostrar)
	{
		$this->_mostrar_propiedades_nodos = $mostrar;	
	}
	
	/**
	 * Determina la propiedad TARGET del tag <A> html de los vinculos de cada nodo
	 * @param string $frame
	 */
	function set_frame_destino($frame)
	{
		$this->_frame_destino = $frame;
	}
	
	/**
	 * Cambia los nodos del arbol, suministrandole nuevos nodo/s raiz
	 * @param array $nodos Arreglo de nodos raiz del arbol
	 */
    function set_datos($nodos)
    {
		$this->_nodos_inicial = $nodos;
	}
	
	/**
	 * Carga la lista de eventos definidos desde el administrador 
	 * La redefinicion filtra solo aquellos utilizados en esta pantalla
	 * y agrega los tabs como eventos
	 * @ignore 
	 */
	protected function cargar_lista_eventos()
	{
		parent::cargar_lista_eventos();
		$this->_eventos['ver_propiedades'] = array('maneja_datos' => true);
	}	
	
	/**
	 * @ignore 
	 */
	function disparar_eventos()
	{
		//Se guarda el layout del arbol actual				
		if (isset($_POST[$this->_submit."__apertura_datos"])) {
			$datos_apertura = $_POST[$this->_submit."__apertura_datos"];
			$pares = explode("||", $datos_apertura);
			$nodos = array();
			foreach ($pares as $par) {
				$par = explode("=", $par);
				if (count($par) == 2) {
					list($id, $visible) = $par;
					$nodos[$id] = $visible;
				}
			}				
			$this->_datos_apertura = $nodos;
			//Se reporta el cambio de layout al padre				
			$this->reportar_evento("cambio_apertura", $this->_datos_apertura);
		}
		if(isset($_POST[$this->_submit]) && $_POST[$this->_submit]!="") {
			$evento = $_POST[$this->_submit];	
			//El evento estaba entre los ofrecidos?
			if(isset($this->_memoria['eventos'][$evento]) ) {
				$parametros = null;
				if ($evento == 'ver_propiedades' && isset($_POST[$this->_submit."__seleccion"])) {
					$this->reportar_evento( $evento, $_POST[$this->_submit."__seleccion"] );
				}
			}
		}
	}
	
	function generar_html()
	{
		$salida = "";
		$salida .= toba_form::hidden($this->_submit, '');
		$salida .= toba_form::hidden($this->_submit."__apertura_datos", '');
		$salida .= toba_form::hidden($this->_submit."__seleccion", '');
		$id = "id='{$this->objeto_js}_nodo_raiz'";
		$salida .= "<div class='ei-base ei-arbol-base'>";
		$salida .= $this->get_html_barra_editor();
		$this->generar_html_barra_sup(null, true,"ei-arbol-barra-sup");		
		if (isset($this->_nodos_inicial)) {
			//--- Se incluye la barrita que contiene el path actual
			$barra = "";
			if (count($this->_nodos_inicial) > 0) {
				$nodo = $this->_nodos_inicial[0];
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
			foreach ($this->_nodos_inicial as $nodo_inicial) {
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
	
	/**
	 * @ignore 
	 */
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

	/**
	 * @ignore 
	 */	
	protected function recorrer_hijos($nodo, $nivel)
	{
		$salida = "";
		foreach ($nodo->get_hijos() as $nodo_hijo) {
			$salida .= $this->recorrer_recursivo($nodo_hijo, false, $nivel);										
		}
		return $salida;
	}
	
	/**
	 * @ignore 
	 */	
	protected function mostrar_nodo(toba_nodo_arbol $nodo, $es_visible)
	{
		$salida = '';
		$salida .= $this->mostrar_utilerias($nodo);
		if ($this->_mostrar_propiedades_nodos && ! $nodo->es_hoja()) {
			if ($es_visible) {
				$img_exp_contr = toba_recurso::imagen_toba('nucleo/contraer.gif', false); 
			} else {
				$img_exp_contr = toba_recurso::imagen_toba('nucleo/expandir.gif', false);
			}
			$salida .= "<img src='$img_exp_contr' onclick='{$this->objeto_js}.cambiar_expansion(this);' 
						 class='ei-arbol-exp-contr' alt='' /> ";
		} else {
			$salida .= gif_nulo(14,1);
		}
		$salida .= $this->mostrar_iconos($nodo);
		
		//Nombre y ayuda
		$corto = $this->acortar_nombre($nodo->get_nombre_corto());
		$id = $nodo->get_id();
		$largo = $nodo->get_nombre_largo();
		$extra = $nodo->get_info_extra();

		if($largo || $id || $extra) {
			$title= "<b>Nombre</b>: $largo<br /><b>Id</b>:  $id";
			if ($extra != '') {
				$title .= "<hr />$extra";
			}
			$ayuda = toba_recurso::ayuda(null,  $title, 'ei-arbol-nombre');
			$nombre= "<span $ayuda>$corto</span>";
		} else {
			$nombre = $corto;	
		}
		if ($this->_mostrar_propiedades_nodos && $nodo->tiene_propiedades()) {
			$salida .= "<a href='#' onclick='{$this->objeto_js}.ver_propiedades(\"".$nodo->get_id()."\");' ".
						"class='ei-arbol-ver-prop'>$nombre</a>";			
		} else {
			$salida .= $nombre;
		}
		return $salida;
	}
	
	/**
	 * Determina si un nodo es visible fijandose en la apertura de nodos
	 * @ignore 
	 */	
	protected function nodo_es_visible($nodo, $nivel)
	{
		$cargado_parcial = !$nodo->es_hoja() && $nodo->tiene_hijos_cargados();
		if ($this->_todos_abiertos) {
			return $cargado_parcial;
		}
		if (isset($this->_datos_apertura[$nodo->get_id()])) {
			return $this->_datos_apertura[$nodo->get_id()] && $cargado_parcial;
		}
		//Si no esta se determina por el nivel de apertura estandar
		return ($nivel < $this->_nivel_apertura) && $cargado_parcial;
	}
	
	/**
	 * @ignore 
	 */
	protected function mostrar_iconos($nodo)
	{
		$salida = '';
		foreach ($nodo->get_iconos() as $icono) {
			$img = toba_recurso::imagen($icono['imagen'], null, null, $icono['ayuda']);
			if (isset($icono['vinculo'])) {
				$salida .= "<a target='{$this->_frame_destino}' href='".$icono['vinculo']."'>$img</a>\n";
			} else {
				$salida .= $img."\n";
			}
		}	
		return $salida;
	}
	
	/**
	 * @ignore 
	 */	
	protected function mostrar_utilerias($nodo)
	{
		if (! $this->_mostrar_utilerias) {
			return '';	
		}
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
						$target = "target='{$this->_frame_destino}'";
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
				$img = toba_recurso::imagen_toba("nucleo/expandir_izq.gif",true);
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

	/**
	 * @ignore 
	 */
	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		$opciones['servicio'] = 'ejecutar';
		$opciones['objetos_destino'] = array($this->_id);
		$autovinculo = toba::vinculador()->crear_autovinculo("", $opciones );
		echo $identado."window.{$this->objeto_js} = new ei_arbol('{$this->objeto_js}',
												 '{$this->_submit}', '$autovinculo');\n";
	}

	/**
	 * @ignore 
	 */
	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'componentes/ei_arbol';
		return $consumo;
	}	

}

?>