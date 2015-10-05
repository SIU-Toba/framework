<?php

/**
 * Muestra un Arbol donde el usuario puede colapsar/descolapsar niveles
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
	protected $s__nodos_inicial;
	protected $_item_propiedades = array();
	protected $_nivel_apertura = 1;
	protected $_datos_apertura;
	protected $_todos_abiertos = false;
	protected $_mostrar_utilerias = true;
	protected $_mostrar_propiedades_nodos = true;
	protected $_mostrar_filtro_rapido = false;
	protected $_frame_destino = null;
	protected $ids = array();
	protected $chequear_ids_unicos = false;
	protected $_mostrar_ayuda = true;
	protected $_ancho_nombres = 30;
	protected $_ids_enviados = array();
	protected $_chequear_id_nodos = true;
	protected $_id_persistidor_sesion;

	final function __construct($datos)
	{
		parent::__construct($datos);
	}

	function inicializar($parametros = array())
	{
		parent::inicializar($parametros);
		//Creo un identificador unico para almacenar la lista de nodos enviados al cliente.
		$this->_id_persistidor_sesion = $this->_canal . 'ei_arbol_ids_enviados';
		if (isset($this->s__nodos_inicial)) {
			$this->_nodos_inicial = $this->s__nodos_inicial;
		}
		$ids = toba::memoria()->get_dato_operacion($this->_id_persistidor_sesion);
		if (! is_null($ids)) {
			$this->_ids_enviados = $ids;
		}
	}

	function destruir()
	{
		$this->_ids_enviados = array_unique($this->_ids_enviados);		//Saco los repetidos por si acaso
		toba::memoria()->set_dato_operacion($this->_id_persistidor_sesion, $this->_ids_enviados);
		//toba::logger()->var_dump($this->_ids_enviados);
		parent::destruir();
	}

	/**
	 * @ignore
	 */
	protected function cargar_eventos()
	{
		$this->cargar_lista_eventos();
	}

	/**
	 * @ignore
	 * Permite desactivar temporalmente el chequeo de ids de los nodos
	 * util para el toba_editor
	 */
	function desactivar_chequeo_nodos()
	{
		$this->_chequear_id_nodos = false;
	}

	/**
	 * Fuerza a que determinados nodos se encuentren abiertos o cerrados
	 * @param array $datos_apertura array('id_nodo' => boolean, ...)
	 */
	function set_apertura_nodos($datos_apertura)
	{
		$this->_datos_apertura = $datos_apertura;
		if (isset($this->_nodos_inicial)) {
			foreach ($this->_nodos_inicial as $nodo) {
				$this->set_apertura_nodo($nodo);
			}
		}
	}
	/**
	 * Se comunica la apertura a un nodo y se invoca el método recursivamente a sus hijos
	 * @param toba_nodo_basico $nodo
	 */
	function set_apertura_nodo($nodo)
	{
		if (($nodo instanceof toba_nodo_arbol_form) && isset($this->_datos_apertura[$nodo->get_id()])) {
			$nodo->set_apertura($this->_datos_apertura[$nodo->get_id()]);
		}
		foreach ($nodo->get_hijos() as $hijo) {
			$this->set_apertura_nodo($hijo);
		}
	}
	
	/**
	 * Determina el ancho máximo de un nombre de un nodo, a partir de ese tamaño se utilizan puntos suspensivos
	 * @param integer $caracteres
	 */
	function set_ancho_nombres($caracteres)
	{
		$this->_ancho_nombres = $caracteres;
	}

	/**
	 * Cambia el nivel inicial de apertura grafico de los nodos
	 * @param integer $nivel
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

	/**
	 * Determina si se muestran o no las utilerias de cada nodo
	 * @param boolean $mostrar
	 */
	function set_mostrar_utilerias($mostrar)
	{
		$this->_mostrar_utilerias = $mostrar;
	}

	/**
	 * @ignore
	 * @param boolean $mostrar
	 */
	function set_mostrar_propiedades_nodos($mostrar)
	{
		$this->_mostrar_propiedades_nodos = $mostrar;
	}

	/**
	 * @ignore
	 * @param boolean $mostrar
	 */
	function set_mostrar_ayuda($mostrar)
	{
		$this->_mostrar_ayuda = $mostrar;
	}

	/**
	 * @ignore
	 * @param boolean $mostrar
	 */
	function set_mostrar_filtro_rapido($mostrar)
	{
		$this->_mostrar_filtro_rapido = $mostrar;
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
	function set_datos($nodos, $mantener_en_sesion=false)
	{
		$this->_nodos_inicial = $nodos;
		if ($mantener_en_sesion) {
			$this->s__nodos_inicial = $nodos;
		}
	}

	function get_datos()
	{
		return $this->_nodos_inicial;
	}

	/**
	 * Devuelve los ids de los nodos enviados al cliente
	 */
	function get_ids_nodos_enviados()
	{
		$resultado = array();
		if (isset($this->_ids_enviados)) {
			$resultado = $this->_ids_enviados;
		}
		return $resultado;
	}
	
	//----------------------------------------------------------------------------------------------------------------------------------------------//
	
	/**
	 * Carga la lista de eventos definidos desde el administrador
	 * La redefinicion filtra solo aquellos utilizados en esta pantalla
	 * y agrega los tabs como eventos
	 * @ignore
	 */
	protected function cargar_lista_eventos()
	{
		parent::cargar_lista_eventos();
		$this->_eventos['cambio_apertura'] = array();
		$this->_eventos['ver_propiedades'] = array('maneja_datos' => true);
		$this->_eventos['cargar_nodo'] = array();
	}

	/**
	 * @ignore
	 */
	protected function disparar_cambios_apertura()
	{		
		if (isset($_POST[$this->_submit.'__apertura_datos'])) {
			$nodos = array();
			$pares = explode('||', $_POST[$this->_submit.'__apertura_datos']);			
			foreach ($pares as $par) {
				$par = explode('=', $par);
				if (count($par) == 2) {
					list($id, $visible) = $par;
					if ($this->validar_id_nodo_recibido($id)) {
							$nodos[$id] = $visible;
					}
				}
			}
			$this->_datos_apertura = $nodos;
			//Se reporta el cambio de layout al padre
			$this->reportar_evento_interno('cambio_apertura', $this->_datos_apertura);
		}		
	}
	
	/**
	 * @ignore
	 */
	protected function disparar_eventos_propios()
	{	
		if(isset($_POST[$this->_submit]) && $_POST[$this->_submit] !='') {
			$evento = $_POST[$this->_submit];
			//El evento estaba entre los ofrecidos?
			if(isset($this->_memoria['eventos'][$evento]) ) {
				$parametros = null;
				$existe_seleccion = (isset($_POST[$this->_submit.'__seleccion']));
				$evento_propiedades = ($evento == 'ver_propiedades');

				if ( $evento_propiedades && $existe_seleccion &&
					($this->validar_id_nodo_recibido($_POST[$this->_submit.'__seleccion']))) {
							$this->reportar_evento_interno( $evento, $_POST[$this->_submit.'__seleccion'] );
				}
			}
		}			
	}
		
	/**
	 * Se cargan los datos del nodo, se le comunica la apertura
	 * y se disparan los eventos de los hijos del nodo.
	 * @param toba_nodo_basico $nodo
	 */
	protected function disparar_eventos_nodo($nodo)
	{
		//Le paso al nodo una referencia al js del arbol que lo contiene
		if (method_exists($nodo, 'set_js_ei_arbol')) {			
			$nodo->set_js_ei_arbol( $this->get_id_objeto_js());
		}
		$id = $this->_submit.'_'.$nodo->get_id();
		// Se le pide al nodo que cargue su estado a partir del post
		$nodo->cargar_estado_post($id);
		// Se le comunica al nodo si esta abierto o no
		if (isset($this->_datos_apertura) && isset($this->_datos_apertura[$nodo->get_id()])) {
			$nodo->set_apertura($this->_datos_apertura[$nodo->get_id()]);
		}
		foreach ($nodo->get_hijos() as $hijo) {
			$this->disparar_eventos_nodo($hijo);
		}
	}

	/**
	 * @ignore
	 */
	function disparar_eventos()
	{
		//Actualiza el estado de los nodos
		if (isset($this->_nodos_inicial)) {
			foreach ($this->_nodos_inicial as $nodo) {
				if ($nodo instanceof toba_nodo_arbol_form) {
						$this->disparar_eventos_nodo($nodo);
				}
			}
		}
				
		$this->disparar_cambios_apertura();		//Se guarda el layout del arbol actual
		$this->disparar_eventos_propios();
		$this->borrar_memoria_eventos_atendidos();
	}
	
	/*
	 * Valida que el id del nodo haya sido enviado al cliente
	 * @ignore
	 */
	protected function validar_id_nodo_recibido($id_nodo)
	{
		if ($this->_chequear_id_nodos && ! in_array($id_nodo, $this->_ids_enviados)) {
			toba::logger()->debug("Se intenta acceder al nodo con id $id_nodo , pero no fue enviado al cliente");
			throw new toba_error('Se intenta acceder a un nodo que no existe.');
		}
		return true;
	}
	
	/**
	 * Determina si un nodo es visible fijandose en la apertura de nodos
	 * @ignore
	 */
	protected function nodo_es_visible($nodo, $nivel)
	{
		if ($nodo instanceof toba_nodo_arbol_form) {
			return $nodo->get_apertura();
		}

		$cargado_parcial = (!$nodo->es_hoja() && $nodo->tiene_hijos_cargados());
		if ($this->_todos_abiertos) {
			return $cargado_parcial;
		}
		if (isset($this->_datos_apertura[$nodo->get_id()])) {
			return $this->_datos_apertura[$nodo->get_id()] && $cargado_parcial;
		}
		//Si no esta se determina por el nivel de apertura estandar
		return ($nivel < $this->_nivel_apertura) && $cargado_parcial;
	}
	
	//-------------------------------------------------------------------------------------------------------
	//--	Generacion de HTML
	//-------------------------------------------------------------------------------------------------------
	
	protected function generar_campos_hidden()
	{
		echo toba_form::hidden($this->_submit, '');
		echo toba_form::hidden($this->_submit.'__apertura_datos', '');
		echo toba_form::hidden($this->_submit.'__seleccion', '');		
	}
	
	/**
	 * @ignore
	 */
	protected  function generar_barras_grales()
	{
		echo $this->get_html_barra_editor();
		$this->generar_html_barra_sup(null, true,'ei-arbol-barra-sup');
		if ($this->_mostrar_filtro_rapido) {
			$this->generar_html_filtro_rapido();
		}
		$this->generar_html_barra_especifica();
	}	
	
	/**
	 * @ignore
	 */
	protected function generar_barra_navegacion()
	{
		$camino = '';
		if (count($this->_nodos_inicial) > 0) {
			$nodo = $this->_nodos_inicial[0];
			while ($nodo->get_padre() != null) {
				$nodo = $nodo->get_padre();
				$nodo_barra = "<a href='#' onclick='{$this->objeto_js}.ver_propiedades(\"{$nodo->get_id()}\");'";
				$nodo_barra .= "class='ei-arbol-ver-prop'>". $this->acortar_nombre($nodo->get_nombre_corto(), 20).'</a>';
				$camino = $nodo_barra . ' > '. $camino;
				$this->_ids_enviados[] = $nodo->get_id();															//Agrego los nodos de la barra del path
			}
			if ($camino != '') {
				echo "<div class='ei-arbol-barra-path'>$camino</div>";
			}				
		}
	}
	
	/**
	 * @ignore
	 */
	protected function generar_cuerpo()
	{
		$id_div = '';
		$id = "id='{$this->objeto_js}_nodo_raiz'";								
		if (count($this->_nodos_inicial) > 1) {
			$id_div = $id;
			$id = '';		
		}			

		echo "<div class='ei-cuerpo ei-arbol-cuerpo' $id_div>\n";
		foreach ($this->_nodos_inicial as $nodo_inicial) {
			echo "\n<ul $id class='ei-arbol-raiz'>";
			echo $this->recorrer_recursivo($nodo_inicial, true);
			echo '</ul>';
			$id = null;	//El id lo tiene solo el primer nodo
		}
		echo '</div>';	
	}
	
	/**
	 * Genera el HTML del arbol
	 */
	public function generar_html()
	{
		$this->generar_campos_hidden();					//Genero los campos a usar para la comunicacion
		$this->_ids_enviados = array();					//Reinicio el arreglo para que guarde solo el pedido actual
		
		echo "<div class='ei-base ei-arbol-base'>";
		$this->generar_barras_grales();		
		echo "<div id='cuerpo_{$this->objeto_js}'>";
		
		if (isset($this->_nodos_inicial)) {					//Si hay nodos			
			$this->generar_barra_navegacion();			// Se incluye la barrita que contiene el path actual
			$this->generar_cuerpo();	
		}
		$this->generar_botones();		
		echo '</div>
			</div>';
	}

	/**
	 * @ignore
	 */
	public function recorrer_hijos($nodo, $nivel)
	{
		$salida = '';
		foreach ($nodo->get_hijos() as $nodo_hijo) {
			$salida .= $this->recorrer_recursivo($nodo_hijo, false, $nivel);
		}
		return $salida;
	}
	
	/**
	 * @ignore
	 */
	public function recorrer_recursivo($nodo, $es_raiz = false, $nivel = 0, $solo_contenido=false)
	{
		//Le paso al nodo una referencia al js del arbol que lo contiene
		if (method_exists($nodo, 'set_js_ei_arbol')) {			
			$nodo->set_js_ei_arbol( $this->get_id_objeto_js());
		}
		$id_nodo = $nodo->get_id();
		$this->_ids_enviados[] = $id_nodo;
		//Verifico que no haya un mismo id perteneciente a clases diferentes
		if ($this->chequear_ids_unicos) {			
			if (isset($this->ids[$id_nodo])) {
				$clase = get_class($nodo);
				$clase_vieja = $this->ids[$id_nodo];
				throw new toba_error("Error al procesar el nodo '$id_nodo' de clase '$clase'. Ya existe el mismo id de clase '$clase_vieja'");
			}
			$this->ids[$id_nodo] = get_class($nodo);
		}
		
		//Genero el html para el nodo
		$salida_generada = $this->generar_fila_nodo($nodo, $nivel);
		
		//Diferencio la salida segun corresponda		
		if (! $solo_contenido) { 
			$estilo_li = '';							//Configuracion del estilo del nodo
			$clase_li = 'ei-arbol-nodo ';			
			if (method_exists($nodo, 'get_estilo_css_li')) {
				$estilo_li .= $nodo->get_estilo_css_li();
			}
			if (method_exists($nodo, 'get_clase_css_li')) {
				$clase_li .= $nodo->get_clase_css_li();
			}
			
			$salida = "\n\t<li class='$clase_li' id_nodo='{$nodo->get_id()}' style='$estilo_li' >";					
			$salida .= $salida_generada;
			$salida .= "</li>\n";
		} else {
			$salida = $salida_generada;
		}
		
		return $salida;
	}
	
	/**
	 * @ignore
	 * @param mixed $nodo
	 * @param smallint $nivel
	 * @return string $salida
	 */
	function generar_fila_nodo($nodo, $nivel)
	{
		//Determina si el nodo es visible en la apertura
		$es_visible = $this->nodo_es_visible($nodo, $nivel);
		$salida = $this->mostrar_nodo($nodo, $es_visible);

		//Recursividad
		if (! $nodo->es_hoja()) {	
			//Configuracion del estilo del nodo
			$clase_ul = 'ei-arbol-rama ';			
			if (method_exists($nodo, 'get_clase_css_ul')) {
				$clase_ul .= $nodo->get_clase_css_ul();
			}
			
			$estilo_ul = ($es_visible) ? '' : 'display:none ';
			if (method_exists($nodo, 'get_estilo_css_ul')) {
				$estilo_ul .= $nodo->get_estilo_css_ul();
			}
			$estilo = ($estilo_ul != '') ? "style='$estilo_ul'" : '';
			
			$salida .= "\n<ul id_nodo='{$nodo->get_id()}' class='$clase_ul' $estilo>";			
			if ($nodo->tiene_hijos_cargados()) {
				$nivel ++;
				$salida .= $this->recorrer_hijos($nodo, $nivel);
			}
			$salida .= '</ul>';
		}		
		return $salida;
	}

	/**
	 * @ignore
	 */
	public function mostrar_nodo(toba_nodo_arbol $nodo, $es_visible)
	{
		$salida = $this->mostrar_utilerias($nodo);
		$salida .= $this->mostrar_cambio_expansion($nodo, $es_visible);
		$salida .= $this->mostrar_iconos($nodo);

		//Nombre y ayuda
		$corto = $this->acortar_nombre($nodo->get_nombre_corto());
		$id = $nodo->get_id();
		$largo = $nodo->get_nombre_largo();
		$extra = $nodo->get_info_extra();

		if($this->_mostrar_ayuda && ($largo || $id || $extra)) {
			$title= "<b>Nombre</b>: $largo<br /><b>Id</b>:  $id";
			if ($extra != '') {
				$title .= "<hr />$extra";
			}
			$ayuda = toba_recurso::ayuda(null,  $title, 'ei-arbol-nombre');
			if (get_class($nodo) == 'toba_ci_pantalla_info') {
				$nombre= "<span $ayuda>$id</span>";
			} else {
				$nombre= "<span $ayuda>$corto</span>";
			}
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
	 * @ignore
	 */
	function generar_html_filtro_rapido()
	{
		echo "<div class='ei-arbol-filtro'>";
		$eventos = "onkeyup='{$this->objeto_js}.filtro_cambio()' onblur='{$this->objeto_js}.filtro_salir()' onfocus='{$this->objeto_js}.filtro_foco()'";
		echo "<input id='{$this->_submit}_filtro_rapido' type='text' value='Buscar...' $eventos />";
		echo '</div>';
	}

	/**
	 * Ventana para generar una barra especifica para el componente
	 */
	function generar_html_barra_especifica(){}

	/**
	 * @ignore
	 */
	protected function mostrar_cambio_expansion($nodo, $es_visible)
	{
		if ($this->_mostrar_propiedades_nodos && ! $nodo->es_hoja()) {			//Muestro el icono para cambiar la expansion
			if ($es_visible) {
				$img_exp_contr = toba_recurso::imagen_toba('nucleo/contraer.gif', false);
			} else {
				$img_exp_contr = toba_recurso::imagen_toba('nucleo/expandir.gif', false);
			}
			$salida = "<img src='$img_exp_contr' onclick='{$this->objeto_js}.cambiar_expansion(this);'
			class='ei-arbol-exp-contr' alt='' /> ";
		} else {
			$salida = gif_nulo(14,1);
		}
		return $salida;
	}
	
	/**
	 * @ignore
	 */
	protected function mostrar_iconos($nodo)
	{
		$salida = '';
		foreach ($nodo->get_iconos() as $icono) {
			$ayuda = toba_parser_ayuda::parsear($icono['ayuda']);
			$js = isset($icono['javascript']) ? $icono['javascript'] : '';
			$img = toba_recurso::imagen($icono['imagen'], null, null, $ayuda, null, $js);
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
		$salida = '';
		if($nodo instanceof toba_nodo_arbol_form) {
			$id = $this->_submit. '_'. $nodo->get_id();
			$salida .= '<span style=\'float:right;\'>'. $nodo->get_input($id). '</span>';
		}
		$utilerias = $nodo->get_utilerias();
		if ($this->_mostrar_utilerias && (count($utilerias) > 0)) {
			$plegados = '';
			$despl = '';
			$salida .= '<span style=\'float:right;\'>';
			$cant_plegados = 0;
			foreach ($utilerias as $utileria) {
				$ayuda = toba_parser_ayuda::parsear($utileria['ayuda']);
				$js = isset($utileria['javascript']) ? $utileria['javascript'] : '';
				$img = toba_recurso::imagen($utileria['imagen'], null, null, $ayuda, null, $js);
				if (isset($utileria['vinculo'])) {
					$target = (isset($utileria['target'])) ? $utileria['target'] : $this->_frame_destino;
					$html = "<a href=\"{$utileria['vinculo']}\" target='$target'>$img</a>\n";
				} else {
					$html = $img;
				}
				if (isset($utileria['plegado']) && $utileria['plegado']) {
					$cant_plegados++;
					$plegados .= $html;					
				} else {
					$despl .= $html;
				}
			}
			if ($cant_plegados > 0) {
				$img = toba_recurso::imagen_toba('nucleo/expandir_izq.gif',true);
				$salida .= "<a href='#' style='padding-right:2px' onclick='toggle_nodo(this.nextSibling);return false'>$img</a>";
				$salida .= "<span style='display:none'>$plegados</span>";
			}
			$salida .= $despl;
			$salida .= '</span>';
		}
		return $salida;
	}

	/**
	 * Formatea el nombre de un nodo para incluir en un listado
	 * @param string $nombre Nombre del nodo
	 * @param integer $limite Cantidad de caracteres a mostrar
	 * @return string
	 */
	protected function acortar_nombre($nombre, $limite=null)
	{
		if (! isset($limite)) {
			$limite = $this->_ancho_nombres;
		}
		if (strlen($nombre) > $limite) {
			$nombre = substr($nombre, 0, $limite)."...";
		}
		return texto_plano($nombre);
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Respuesta al pedido AJAX de apertura de un nodo no cargado anteriormente
	 * Dispara el evento cargar_nodo($id) para que se retorne el toba_nodo_arbol asociado
	 */
	function servicio__ejecutar()
	{
		toba::memoria()->desactivar_reciclado();
		$id_nodo = toba::memoria()->get_parametro('id_nodo');
		$nodo = $this->reportar_evento_interno('cargar_nodo', $id_nodo);
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
	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		$opciones['servicio'] = 'ejecutar';
		$opciones['objetos_destino'] = array($this->_id);
		$autovinculo = toba::vinculador()->get_url(null, null, '', $opciones );
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
