<?php

/**
 * Esta clase maneja la VINCULACION entre ITEMS. Conoce todos los lugares a los que el 
 * ITEM actual puede acceder (considerando el USUARIO que lo solicito)
 * Para navegar hacia estos items puede construir URLs e incluirlos en algún TAG
 * @package Centrales
 * @jsdoc vinculador vinculador
 * @wiki Referencia/Item Vincula ítems
 * @todo agregar un nivel de vinculos globales para un OBJETO puntual
 */
class toba_vinculador 
{
	protected $prefijo;			//Prefijo de cualquier URL
	protected $vinculos = array();
	static private $instancia;
	
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_vinculador();
		}
		return self::$instancia;		
	}
	
	private function __construct()
	{
		//if(!isset($_SESSION['toba']['instancia']['vinculos_posibles'])){
			$this->cargar_vinculos_posibles();			
		//}
		$this->prefijo = toba::memoria()->prefijo_vinculo();
	}

	/**
	 * Crea un vinculo hacia un item
	 *
	 * @param string $proyecto Proyecto destino, por defecto el actual
	 * @param string $item Item destino, por defecto el actual
	 * @param array $parametros Parametros pasados al item, es un arreglo asociativo id_parametro => valor
	 * @param array $opciones Arreglo asociativo de opciones ellas son:
	 * 					zona => Activa la propagación automática del editable en la zona,
	 * 					cronometrar => Indica si la solicitud generada por este vinculo debe cronometrarse,
	 * 					param_html => Parametros para la construccion de HTML. Si esta presente se genera HTML en vez de una URL.
	 									Las claves asociativas son: frame, clase_css, texto, tipo [normal,popup], inicializacion, imagen_recurso_origen, imagen,
	 * 					texto => Texto del vínculo
	 * 					menu => El vinculo esta solicitado por una opción menu?
	 * 					celda_memoria => Namespace de memoria a utilizar, por defecto el actual
	 * 					servicio => Servicio solicitado, por defecto get_html
	 * 					objetos_destino => array(array(proyecto, id_objeto), ...) Objetos destino del vinculo
	 * 					prefijo => Punto de acceso a llamar.
	 * @return string Una URL o el link html en caso
	 */
	function crear_vinculo($proyecto=null, $item=null, $parametros=array(), $opciones=array())
	{
		if (!isset($opciones['zona'])) $opciones['zona'] = false;
		if (!isset($opciones['cronometrar'])) $opciones['cronometrar'] = false;
		if (!isset($opciones['param_html'])) $opciones['param_html'] = null;
		if (!isset($opciones['menu'])) $opciones['menu'] = null;
		if (!isset($opciones['celda_memoria'])) $opciones['celda_memoria'] = null;
		if (!isset($opciones['texto'])) $opciones['texto'] = '';
		if (!isset($opciones['servicio'])) $opciones['servicio'] = apex_hilo_qs_servicio_defecto;
		if (!isset($opciones['objetos_destino'])) $opciones['objetos_destino'] = null;
		if (!isset($opciones['prefijo'])) $opciones['prefijo'] = null;
		return $this->generar_solicitud($proyecto, $item, $parametros, $opciones['zona'],
								 $opciones['cronometrar'], $opciones['param_html'],
								 $opciones['menu'], $opciones['celda_memoria'], 
								 $opciones['servicio'], $opciones['objetos_destino'],
								 $opciones['prefijo'] );
	}
	
	/**
	 * Crea un vinculo al mismo item propagando la zona
	 */
	function crear_autovinculo($parametros=array(), $opciones=array())
	{
		if (! isset($opciones['zona'])) {
			$opciones['zona'] = true;	
		}
		return $this->crear_vinculo(null, null, $parametros, $opciones);
	}

	function registrar_vinculo( toba_vinculo $vinculo )
	{
		$item = $vinculo->get_item();
		$proyecto = $vinculo->get_proyecto();
		if ( ! $this->posee_acceso_item($proyecto, $item) ) {
			return null;
		}
		$id = count( $this->vinculos );
		$this->vinculos[$id] = $vinculo;
		return $id;
	}

//##################################################################################
//########################   Solicitud DIRECTA de URLS  ############################
//##################################################################################

	/**
	 * Generacion directa de una URL que representa un posible futuro acceso a la infraestructura
	 * No se chequean permisos
	 *
	 * @param string $item_proyecto Proyecto al que pertenece el ítem destino (por defecto el actual)
	 * @param string $item ID. del ítem destino (por defecto el actual)
	 * @param array $parametros Párametros enviados al ítem, arreglo asociativo de strings
	 * @param boolean $zona Activa la propagación automática del editable en la zona
	 * @param boolean $cronometrar Indica si la solicitud generada por este vinculo debe cronometrarse
	 * @param array $param_html 
	 * @param boolean $menu El vinculo esta solicitado por el menu?
	 * @param string $celda_memoria Namespace de memoria a utilizar, por defecto el actual
	 * @return string URL hacia el ítem solicitado
	 * @deprecated Desde 1.0 usar crear_vinculo o crear_autovinculo
	 */
	function generar_solicitud($item_proyecto=null,$item=null,$parametros=null,
								$zona=false,$cronometrar=false,$param_html=null,
								$menu=null,$celda_memoria=null, $servicio=null,
								$objetos_destino=null, $prefijo=null)
 	{
		//-[1]- Determino ITEM
		//Por defecto se propaga el item actual, o un item del mismo proyecto
		$autovinculo = false;
		if ($item_proyecto == null || $item == null) {
			$item_solic = toba::memoria()->get_item_solicitado();
			if($item_proyecto==null) { 
				$item_proyecto = $item_solic[0];
			}
			if($item==null){
				$item = $item_solic[1];
				$autovinculo = true;
			}
		}
		//Controlo que el usuario posea permisos para acceder al ITEM
		if ( !$autovinculo ) {
			if ( ! $this->posee_acceso_item($item_proyecto, $item) ) {
				return null;	
			}
		}

		$item_a_llamar = $item_proyecto . apex_qs_separador . $item;
		//-[2]- Determino parametros
		$parametros_formateados = "";
		if ($zona){//Hay que propagar la zona?
			$solicitud_actual = toba::solicitud();
			if ($solicitud_actual->hay_zona() && toba::zona()->cargada()) {
				$editable = $this->variable_a_url(toba::zona()->get_editable());
				$parametros_formateados .= "&". apex_hilo_qs_zona 
						."=".$editable;
			}
		}
		//Cual es el tipo de salida?
		if (isset($servicio) && $servicio != apex_hilo_qs_servicio_defecto) {
			$parametros_formateados .= '&'.apex_hilo_qs_servicio ."=". $servicio;
		}
		if (isset($objetos_destino) && is_array($objetos_destino)) {
			$objetos = array();
			foreach ($objetos_destino as $obj) {
				$objetos[] = $obj[0] . apex_qs_separador . $obj[1];
			}
			$qs_objetos = implode(',', $objetos);
			$parametros_formateados .= '&'.apex_hilo_qs_objetos_destino ."=". $qs_objetos;
		}
		//Cual es la celda de memoria del proximo request?
		if(!isset($celda_memoria)){
			//Por defecto propago la celda actual del HILO
			$celda_memoria = toba::memoria()->get_celda_memoria_actual();
		}		
		$parametros_formateados .= "&". apex_hilo_qs_celda_memoria ."=". $celda_memoria;
		//La proxima pagina va a CRONOMETRARSE?
		if($cronometrar){
			$parametros_formateados .= "&". apex_hilo_qs_cronometro ."=1";
		}
		//Formateo paremetros directos
		if(isset($parametros) && is_array($parametros)){
			foreach($parametros as $clave => $valor){
				$parametros_formateados .= "&$clave=$valor";
			}
		}
		//Obtengo el prefijo del vinculo
		if ( ! isset($prefijo) ) {
			$prefijo = $this->prefijo;	
		} else {
			if (strpos($prefijo,'?') === false) {
				$prefijo = $prefijo . '?';
			}
		}
		//Genero la URL que invoca la solicitud
		$vinculo = $prefijo . "&" . apex_hilo_qs_item . "=" . $item_a_llamar;
		if(trim($parametros_formateados)!=""){

			$encriptar_qs = toba::proyecto()->get_parametro('encriptar_qs');
			if($encriptar_qs){
				//Le concateno un string unico al texto que quiero encriptar asi evito que conozca 
				//la clave alguien que ve los parametros encriptados y sin encriptar
				$parametros_formateados .= $parametros_formateados . "&jmb76=". uniqid("");
				$vinculo = $vinculo . "&". apex_hilo_qs_parametros ."=". toba::encriptador()->cifrar($parametros_formateados);
			}else{
				$vinculo = $vinculo . $parametros_formateados;
			}
		}
		//El vinculo esta solicitado por el menu?
		//Esto se maneja directamente $_GET por performance (NO encriptar todo el menu)
		if($menu){
			$vinculo .= "&". apex_hilo_qs_menu ."=1";
		}
		//Genero HTML o devuelvo el VINCULO
		if(is_array($param_html)){
			return $this->generar_html($vinculo, $param_html);
		}else{
			return $vinculo;
		}
	}

	//-------------------------------------------------------------------------------------
	//---------------------------- CONVERSIONES  ------------------------------------------
	//-------------------------------------------------------------------------------------

	/**
	 * Empaqueta una variable compleja (ej. array) para poder ser parte de una URL
	 * @param mixed $variable Arreglo o tipo basico
	 * @return string
	 * @see toba_vinculador::url_a_variable
	 */
	static function variable_a_url($variable)
	{
		if (! is_array($variable)) {
			return urlencode($variable);
		}
		$salida = array();
		foreach ($variable as $clave => $valor) {
			$salida[] = urlencode($clave . apex_qs_sep_interno. $valor);
		}
		return implode(apex_qs_separador, $salida);
	}

	/**
	 * Desemmpaqueta una variable compleja (ej. array) que formaba parte de una URL
	 * @param mixed $url Parte de una url que contiene una variable
	 * @return miexed
	 * @see toba_vinculador::variable_a_url
	 */
	static function url_a_variable($url)
	{
		if (strpos($url, apex_qs_separador) === false && strpos($url, apex_qs_sep_interno) === false) {
			//--- string simple
			return urldecode($url);
		}
		
		//--- Era una arreglo
		$salida = array();
		$partes = explode(apex_qs_separador, $url);
		foreach ($partes as $parte) {
			if (strpos($parte, apex_qs_sep_interno) === false) {
				$salida[] = urldecode($parte);
			} else {
				//--- Manejo de claves asociativas
				list($clave, $valor) = explode(apex_qs_sep_interno, urldecode($parte));
				$salida[$clave] = $valor;
			}
		}
		return $salida;
	}

	//-------------------------------------------------------------------------------------
	//------------------------------ CONTROL de ACCESO  -----------------------------------
	//-------------------------------------------------------------------------------------

	/**
	 * @ignore 
	 */	
	protected function cargar_vinculos_posibles()
	{
		$usuario = toba::usuario()->get_id();
		$rs = toba::instancia()->get_vinculos_posibles($usuario);
		foreach($rs as $vinculo) {
			$vinculos[$vinculo['proyecto'].'-'.$vinculo['item']] = 1;
		}
		if(isset($vinculos)) {
			$_SESSION['toba']['instancia']['vinculos_posibles'] = $vinculos;
		}
	}

	/**
	 * @ignore 
	 */
	protected function posee_acceso_item($proyecto, $item)
	{
		if(array_key_exists('vinculos_posibles',$_SESSION['toba']['instancia'])){
			return isset($_SESSION['toba']['instancia']['vinculos_posibles'][$proyecto.'-'.$item]);
		}
	}
	
	//-------------------------------------------------------------------------------------
	//------------------------------ SALIDA  ----------------------------------------------
	//-------------------------------------------------------------------------------------

	/**
		@@acceso: interno
		@@desc: Genera un VINCULO
		@@param: string | URL
		@@param: array | Parametros para la construccion del HTML. Las claves asociativas son: frame, clase_css, texto, tipo [normal,popup], inicializacion, imagen_recurso_origen, imagen
		@@retorno: string | HTML del vinculo generado
	*/
	protected function generar_html($url, $parametros)
	{
		if(!isset($parametros['tipo'])) $parametros['tipo'] = 'normal';
		if(!isset($parametros['texto'])) $parametros['texto'] = '';
		$id='';
		if (isset($parametros['id'])) {
			$id = "id='{$parametros['id']}'";
		}
		
		//ei_arbol($parametros);
		//El vinculo corresponde a un FRAME
		if(isset($parametros['frame'])){
			if(trim($parametros['frame']!="")){
				$frame = " target='" . $parametros['frame'] . "' ";
			}else{
				$frame = "";
			}
		}else{
			$frame = "";
		}
		if(isset($parametros['clase_css'])){
			if(trim($parametros['clase_css']!="")){
				$clase = " class='" . $parametros['clase_css'] . "' ";
			}else{
				$clase = " class='lista-link'";
			}
		}else{
			$clase = " class='lista-link'";
		}
		//La llamada depende del tipo de vinculo (normal, popup, etc.)
		if( $parametros['tipo']=="normal" ){	//	*** Ventana NORMAL ***
			//El vinculo es normal
			$html = "<a $id href='$url' $clase $frame>";
		}elseif( $parametros['tipo']=="popup" )	//	*** POPUP javascript ***
		{
			$init = explode(",",$parametros['inicializacion']);
			$init = array_map("trim",$init);
			//ei_arbol($init);
			$tx = (isset($init[0])) ? $init[0] : 400;
			$ty = (isset($init[1])) ? $init[1] : 400;
			$scroll = (isset($init[2])) ? $init[2] : "1";
			$resizable = (isset($init[3])) ? $init[3] : "1";
			//---SE utiliza el parametro frame para determinar si el popup tiene un id especifico
			$id_popup = isset($parametros['frame']) ? $parametros['frame'] : 'general';
			$html = "<a $id href='#' $clase onclick=\"javascript:return abrir_popup('$id_popup','$url', {'width': '$tx', 'scrollbars' : '$scroll', 'height': '$ty', 'resizable': '$resizable'})\">";
		}

		if( isset($parametros['imagen']) && 
				isset($parametros['imagen_recurso_origen'])){
			if($parametros['imagen_recurso_origen']=="apex"){
				$html.= toba_recurso::imagen_toba($parametros['imagen'],true,null,null,$parametros['texto']);
			}elseif($parametros['imagen_recurso_origen']=="proyecto"){
				$html.= toba_recurso::imagen_proyecto($parametros['imagen'],true,null,null,$parametros['texto']);
			}else{
				$html.= $parametros['texto'];
			}
		}else{
			$html.= $parametros['texto'];
		}
		$html.= "</a>";
		return $html;
	}

	/**
	 * Genera un salto de javascript directo a una pagina
	 *
	 * @param string $item_proyecto Proyecto al que pertenece el ítem destino (por defecto el actual)
	 * @param string $item ID. del ítem destino (por defecto el actual)
	 * @param array $parametros Parametros pasados al ITEM (Array asociativo de strings)
	 * @param boolean $zona Activa la propagacion automatica del editable de la ZONA
	 * @param boolean $cronometrar Indica si la solicitud generada por este vinculo debe cronometrarse
	 * @return string Comando JS que contiene el salto de página
	 */
	function navegar_a($item_proyecto="",$item="",$parametros=null,
								$zona=false,$cronometrar=false)
	{
		echo toba_js::abrir();
		echo "document.location.href='".
				$this->generar_solicitud($item_proyecto,$item,$parametros,$zona,$cronometrar)."'\n";
		echo toba_js::cerrar();
	}

	/*
	* Registra vinculos en la clase homologa de javascript
	*/
	function generar_js()
	{
		foreach( $this->vinculos as $id => $vinculo ) {
			$opciones = $vinculo->get_opciones();
			if( !isset( $opciones['validar']) ) {
				//Por defecto los vinculos no se validan.
				$opciones['validar'] = false;
			}
			if( (!isset($opciones['celda_memoria'])) && ($vinculo->estado_popup() == 1) ) {
				//Accion preventiva para que los popups no se carguen en la celda de la operacion
				$opciones['celda_memoria'] = 'popup';
			}
			$datos['url'] = $this->crear_vinculo( 	$vinculo->get_proyecto(),
													$vinculo->get_item(),
													$vinculo->get_parametros(),
													$opciones	);
			if (isset($datos['url'])) {
				$datos['popup'] = $vinculo->estado_popup();
				$datos['popup_parametros'] = $vinculo->get_popup_parametros();
				$datos['target'] = $vinculo->get_target();
				$datos['activado'] = 1;
				$datos_js = toba_js::arreglo($datos, true);
				echo "vinculador.agregar_vinculo('$id',$datos_js);\n";
			}
		}
	}
	
	/**
	 * Método de debug que dumpea el estado actual de los vinculos registrados
	 */
	function info()
	{
		echo "<ul>";
		foreach( $this->vinculos as $id => $vinculo ) {
			echo "<li>$id: ".var_export($vinculo, true)."</li>";
		}
		echo "</ul>";
	}
}
?>