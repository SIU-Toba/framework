<?php

/**
 * Esta clase maneja la VINCULACION entre operaciones. Conoce todos los lugares a los que la
 * operación actual puede acceder (considerando el USUARIO que lo solicito)
 * Para navegar hacia ellas puede construir URLs e incluirlos en algún TAG
 *
 * @see toba_vinculo
 * @package Centrales
 * @jsdoc vinculador vinculador
 * @wiki Referencia/Operacion Vincula operaciones
 * @todo agregar un nivel de vinculos globales para un OBJETO puntual
 */
class toba_vinculador 
{
	protected $prefijo;			//Prefijo de cualquier URL
	protected $vinculos = array();
	protected $vinculos_posibles = array();
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
		$this->prefijo = toba::memoria()->prefijo_vinculo();
	}

	/**
	 * @deprecated Usar get_url
	 * @see get_url
	 */
	function crear_vinculo($proyecto=null, $item=null, $parametros=array(), $opciones=array())
	{
		return $this->get_url($proyecto, $item, $parametros, $opciones);
	}
	
	
	/**
	 * @deprecated Usar get_url
	 * @see get_url
	 **/
	function crear_autovinculo($parametros=array(), $opciones=array())
	{
		return $this->get_url(null, null, $parametros, $opciones);
	}
	
	
	/**
	 * Genera una url que apunta a una operación de un proyecto
	 *
	 * @param string $proyecto Proyecto destino, por defecto el actual
	 * @param string $item Item destino, por defecto el actual
	 * @param array $parametros Parametros pasados al item, es un arreglo asociativo id_parametro => valor
	 * @param array $opciones Arreglo asociativo de opciones ellas son:
	 * <code> zona => Activa la propagación automática del editable en la zona
	 * cronometrar => Indica si la solicitud generada por este vinculo debe cronometrarse
	 * param_html => Parametros para la construccion de HTML. Si esta presente se genera HTML en vez de una URL. Las claves asociativas son: frame, clase_css, texto, tipo [normal,popup], inicializacion, imagen_recurso_origen, imagen
	 * texto => Texto del vínculo
	 * menu => El vinculo esta solicitado por una opción menu?
	 * celda_memoria => Namespace de memoria a utilizar, por defecto el actual
	 * servicio => Servicio solicitado, por defecto get_html
	 * objetos_destino => array(array(proyecto, id_objeto), ...) Objetos destino del vinculo
	 * prefijo => Punto de acceso a llamar.
	 * nombre_ventana => Id con que se creara la ventana hija (solo tipo popup)</code>
	 * @param bool $uri_valida Toba desde sus primeras versiones genera URIs invalidas al utilizar el caracter || sin codificar, algunos clientes de mail o applets java requieren que se encodeen estrictamente estos caracteres, en ese caso indicarlo con este parametro en true, por defecto es false (compatibilidad hacia atras)
	 * @return string Una URL o el link html en caso
	 */	
	function get_url($proyecto=null, $item=null, $parametros=array(), $opciones=array(), $uri_valida = false)
	{
		if (!isset($opciones['zona'])) $opciones['zona'] = true;
		if (!isset($opciones['cronometrar'])) $opciones['cronometrar'] = false;
		if (!isset($opciones['param_html'])) $opciones['param_html'] = null;
		if (!isset($opciones['menu'])) $opciones['menu'] = null;
		if (!isset($opciones['celda_memoria'])) $opciones['celda_memoria'] = null;
		if (!isset($opciones['texto'])) $opciones['texto'] = '';
		if (!isset($opciones['servicio'])) $opciones['servicio'] = apex_hilo_qs_servicio_defecto;
		if (!isset($opciones['objetos_destino'])) $opciones['objetos_destino'] = null;
		if (!isset($opciones['prefijo'])) $opciones['prefijo'] = null;
		if (!isset($opciones['nombre_ventana'])) $opciones['nombre_ventana'] = null;
		$url = $this->generar_solicitud($proyecto, $item, $parametros, $opciones['zona'],
								 $opciones['cronometrar'], $opciones['param_html'],
								 $opciones['menu'], $opciones['celda_memoria'], 
								 $opciones['servicio'], $opciones['objetos_destino'],
								 $opciones['prefijo'], $opciones['nombre_ventana'] );
		if ($uri_valida) {
			$invalidos = "||";
			$url = str_replace($invalidos, urlencode($invalidos), $url);
		}
		return $url;
	}
	
	function get_url_ws($proyecto = null, $ws, $parametros = array(),  $opciones=array())
	{
		$separador = '&';
		if (is_null($proyecto)) {
			$proyecto = toba::proyecto()->get_id();			
		}
				
		$disponibles = toba_info_editores::get_items_servicios_web($proyecto);
		$items = array();
		foreach($disponibles as $wservice) {
			$items[] = $wservice['item'];
		}
		if (!in_array($ws, $items)) {			//Si no forma parte de los ws, null
			return null;
		}
		
		$query_str = toba_recurso::url_proyecto($proyecto);
		$query_str .= '/servicios.php/'.$ws;
		if (isset($opciones['wsdl'])) {
			$query_str .= '?wsdl';
		} elseif (isset($opciones['wsdl2'])) {
			$query_str .= '?wsdl2';
		}
		
		if(isset($parametros) && is_array($parametros)) {			
			foreach($parametros as $clave => $valor){
				$query_str .= $separador."$clave=$valor";
			}
		}
		
		if (isset($opciones['html'])) {
			$params = (isset($opciones['texto'])) ? array('texto' => $opciones['texto']): array();
			return $this->generar_html($query_str, $params);
		}
		return $query_str;		
	}
	
	
	function registrar_vinculo( toba_vinculo $vinculo )
	{
		$item = $vinculo->get_item();
		$proyecto = $vinculo->get_proyecto();
		if (toba::proyecto()->get_id() == $proyecto && !toba::proyecto()->puede_grupo_acceder_item($item)) { 
			//El control es solo dentro del proyecto actual
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
	 * @param string $nombre_ventana Nombre con que se abrira la ventana hija en caso de ser popup
	 * @return string URL hacia el ítem solicitado
	 * @deprecated Desde 1.0 usar get_url o get_url_
	 */
	protected function generar_solicitud($item_proyecto=null,$item=null,$parametros=null,
								$zona=false,$cronometrar=false,$param_html=null,
								$menu=null,$celda_memoria=null, $servicio=null,
								$objetos_destino=null, $prefijo=null, $nombre_ventana=null)
 	{
 		$separador = '&';
		$escapador = toba::escaper();
		//-[1]- Determino ITEM
		//Por defecto se propaga el id de la operación actual, o una operación del mismo proyecto
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
			//El control es solo dentro del proyecto actual
			if ( toba::proyecto()->get_id() == $item_proyecto && 
						!toba::proyecto()->puede_grupo_acceder_item($item)) {
				toba::logger()->notice("VINCULADOR: Fallo la creacion de un vinculo al item '$item' porque el usuario no posee permisos para acceder al mismo.");
				return null;	
			}
		}

		$item_a_llamar = $escapador->escapeUrl($item_proyecto . apex_qs_separador . $item);
		//-[2]- Determino parametros
		$parametros_formateados = "";
		if ($zona){//Hay que propagar la zona?
			$parametros_formateados .= $this->get_qs_zona();
		}
		//Cual es el tipo de salida?
		if (isset($servicio) && $servicio != apex_hilo_qs_servicio_defecto) {
			$parametros_formateados .= $separador.apex_hilo_qs_servicio ."=". $escapador->escapeUrl($servicio);
		}
		if (isset($objetos_destino) && is_array($objetos_destino)) {
			$objetos = array();
			foreach ($objetos_destino as $obj) {
				$objetos[] =  $escapador->escapeUrl($obj[0] . apex_qs_separador . $obj[1]);
			}
			$qs_objetos = implode(',', $objetos);
			$parametros_formateados .= $separador.apex_hilo_qs_objetos_destino ."=". $qs_objetos;
		}
		//Cual es la celda de memoria del proximo request?
		if(!isset($celda_memoria)) {
			$celda_actual = toba::memoria()->get_celda_memoria_actual_id();
			//Si es la celda por defecto, no explicitar ya que se asume
			if ($celda_actual != apex_hilo_qs_celda_memoria_defecto) {
				$celda_memoria = toba::memoria()->get_celda_memoria_actual_id();
			}
		}		
		if (isset($celda_memoria)) {
			$parametros_formateados .= $separador. apex_hilo_qs_celda_memoria ."=".  $escapador->escapeUrl($celda_memoria);
		}
		//La proxima pagina va a CRONOMETRARSE?
		if($cronometrar){
			$parametros_formateados .= $separador. apex_hilo_qs_cronometro ."=1";
		}
		//Formateo paremetros directos
		if(isset($parametros) && is_array($parametros)){
			foreach($parametros as $clave => $valor){
				$parametros_formateados .= $separador."$clave=".  $escapador->escapeUrl($valor);
			}
		}
		//Obtengo el prefijo del vinculo
		if ( ! isset($prefijo) ) {
			$prefijo = $this->prefijo;	
		} elseif (strpos($prefijo,'?') === false) {
			$prefijo = $prefijo . '?';
		}
		//Genero la URL que invoca la solicitud
		$vinculo = (substr($prefijo, -1, 1) == '?') ? $prefijo : $prefijo . $separador; 
		$vinculo .=  apex_hilo_qs_item . "=" . $item_a_llamar;
		if(trim($parametros_formateados)!="") {
			$encriptar_qs = toba::proyecto()->get_parametro('encriptar_qs');
			if($encriptar_qs){
				$claves = toba::instalacion()->get_claves_encriptacion();
				//Le concateno un string unico al texto que quiero encriptar asi evito que conozca 
				//la clave alguien que ve los parametros encriptados y sin encriptar
				$parametros_formateados .= $parametros_formateados . $separador."jmb76=". uniqid("");
				$vinculo = $vinculo . $separador . apex_hilo_qs_parametros ."=". toba::encriptador()->cifrar_para_web($parametros_formateados, $claves['get']);
			}else{
				$vinculo = $vinculo . $parametros_formateados;
			}
		}
		//El vinculo esta solicitado por el menu?
		//Esto se maneja directamente $_GET por performance (NO encriptar todo el menu)
		if($menu){
			$vinculo .= $separador . apex_hilo_qs_menu ."=1";
		}		
		//Genero HTML o devuelvo el VINCULO
		if(is_array($param_html)) {
			return $this->generar_html($vinculo, $param_html, $nombre_ventana);
		} else {
			return $vinculo;
		}
	}
	
	/**
	 * Retorna el querystring propagando la zona actual (si es que hay y está cargada)
	 * @return string
	 */
	function get_qs_zona()
	{
		$qs = '';
		$solicitud_actual = toba::solicitud();
		if ($solicitud_actual->hay_zona() && toba::zona()->cargada()) {
			if (toba::zona()->get_modo_url()) {
				$editable = $this->variable_a_url(toba::zona()->get_editable());
				$qs .= '&'.apex_hilo_qs_zona."=". toba::escaper()->escapeUrl($editable);
			}else{
				$qs .= '&'.apex_hilo_qs_zona.'=1';
				toba::zona()->propagar_id();
			}			
		}		
		return $qs;
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
			$salida[] = toba::escaper()->escapeUrl($clave . apex_qs_sep_interno. $valor);
		}
		return implode(apex_qs_separador, $salida);
	}

	/**
	 * Desemmpaqueta una variable compleja (ej. array) que formaba parte de una URL
	 * @param mixed $url Parte de una url que contiene una variable
	 * @return mixed
	 * @see toba_vinculador::variable_a_url
	 */
	static function url_a_variable($url)
	{
		$url_dcd = urldecode($url);
		if (strpos($url, apex_qs_separador) === false && strpos($url_dcd, apex_qs_sep_interno) === false) {
			//--- string simple
			return $url_dcd;
		}
		
		//--- Era una arreglo
		$salida = array();
		$partes = explode(apex_qs_separador, $url);
		foreach ($partes as $parte) {
			$parte_dcd = urldecode($parte);
			if (strpos($parte_dcd, apex_qs_sep_interno) === false) {
				$salida[] = $parte_dcd;
			} else {
				//--- Manejo de claves asociativas
				list($clave, $valor) = explode(apex_qs_sep_interno, $parte_dcd);
				$salida[$clave] = $valor;
			}
		}
		return $salida;
	}

	//-------------------------------------------------------------------------------------
	//------------------------------ SALIDA  ----------------------------------------------
	//-------------------------------------------------------------------------------------

	/**
		@@acceso: interno
		@@desc: Genera un VINCULO
		@@param: string | URL
		@@param: array | Parametros para la construccion del HTML. Las claves asociativas son: frame, clase_css, texto, tipo [normal,popup], inicializacion, imagen_recurso_origen, imagen
		@@param: string | Nombre de la ventana hija cuando el vinculo se abre en popup
		@@retorno: string | HTML del vinculo generado
	*/
	protected function generar_html($url, $parametros, $nombre_ventana=null)
	{
		if(!isset($parametros['tipo'])) $parametros['tipo'] = 'normal';
		if(!isset($parametros['texto'])) $parametros['texto'] = '';
		$id='';
		$escapador = toba::escaper();
		if (isset($parametros['id'])) {
			$id = "id='" . $escapador->escapeHtmlAttr($parametros['id']) . "'";
		}
		
		//ei_arbol($parametros);
		//El vinculo corresponde a un FRAME
		if(isset($parametros['frame'])){
			if(trim($parametros['frame']!="")){
				$frame = " target='" . $escapador->escapeHtmlAttr($parametros['frame']) . "' ";
			}else{
				$frame = "";
			}
		}else{
			$frame = "";
		}
		if(isset($parametros['clase_css'])){
			if(trim($parametros['clase_css']!="")){
				$clase = " class='" . $escapador->escapeHtmlAttr($parametros['clase_css']) . "' ";
			}else{
				$clase = " class='lista-link'";
			}
		}else{
			$clase = " class='lista-link'";
		}
		//La llamada depende del tipo de vinculo (normal, popup, etc.)
		if( $parametros['tipo']=="normal" ) {	//	*** Ventana NORMAL ***
			//El vinculo es normal
			$html = "<a $id href='$url' $clase $frame>";
		} elseif( $parametros['tipo']=="popup" )	//	*** POPUP javascript ***
		{
			$init = explode(",",$parametros['inicializacion']);
			$init = array_map("trim",$init);
			//$init = array_map('toba::escaper()->escapeHtmlAttr', $init);			
			//ei_arbol($init);
			$tx = (isset($init[0])) ? $escapador->escapeHtmlAttr($init[0]) : 400;
			$ty = (isset($init[1])) ? $escapador->escapeHtmlAttr($init[1]) : 400;
			$scroll = (isset($init[2])) ? $escapador->escapeHtmlAttr($init[2]) : "1";
			$resizable = (isset($init[3])) ? $escapador->escapeHtmlAttr($init[3]) : "1";
			//---SE utiliza el parametro frame para determinar si el popup tiene un id especifico
			$id_popup = isset($parametros['frame']) ? $escapador->escapeHtmlAttr($parametros['frame']) : 'general';
			$wn = (! is_null($nombre_ventana)) ? "'".$escapador->escapeHtmlAttr($nombre_ventana)."'" : 'null';
			$html = "<a $id href='#' $clase onclick=\"javascript:return abrir_popup('$id_popup','$url', {'width': '$tx', 'scrollbars' : '$scroll', 'height': '$ty', 'resizable': '$resizable'}, null , null ,$wn)\">";
		}

		if( isset($parametros['imagen']) && 
				isset($parametros['imagen_recurso_origen'])){
			if($parametros['imagen_recurso_origen']=="apex"){
				$html.= toba_recurso::imagen_toba($parametros['imagen'],true,null,null,$parametros['texto']);
			}elseif($parametros['imagen_recurso_origen']=="proyecto"){
				$html.= toba_recurso::imagen_proyecto($parametros['imagen'],true,null,null,$parametros['texto']);
			}else{
				$html.= $escapador->escapeHtml($parametros['texto']);
			}
		}else{
			$html.= $escapador->escapeHtml($parametros['texto']);
		}
		$html.= "</a>";
		return $html;
	}
	
	/**
	 * Genera un salto de javascript directo a una pagina
	 *
	 * @param string $item_proyecto Proyecto al que pertenece el ítem destino (por defecto el actual)
	 * @param string $item ID. del ítem destino (por defecto el actual)
	 * @param array $parametros Parametros pasados a la OPERACION (Array asociativo de strings)
	 * @param boolean $zona Activa la propagacion automatica del editable de la ZONA
	 * @param boolean $cronometrar Indica si la solicitud generada por este vinculo debe cronometrarse
	 * @return string Comando JS que contiene el salto de página
	 */
	function navegar_a($item_proyecto="",$item="",$parametros=null,
								$zona=false,$cronometrar=false)
	{
		echo toba_js::abrir();
		echo "document.location.href='".
				$this->get_url($item_proyecto,$item,$parametros, array('zona' =>$zona, 'cronometrar' => $cronometrar))."'\n";
		echo toba_js::cerrar();
	}

	/*
	* Registra vinculos en la clase homologa de javascript
	*/
	function generar_js()
	{
		echo "vinculador.limpiar_vinculos();\n";
		$escapador = toba::escaper();
		foreach( $this->vinculos as $id => $vinculo ) {
			$opciones = $vinculo->get_opciones();
			if( !isset( $opciones['validar']) ) {
				//Por defecto los vinculos no se validan.
				$opciones['validar'] = false;
			}			
			$datos['url'] = $this->get_url( $vinculo->get_proyecto(),
													$vinculo->get_item(),
													$vinculo->get_parametros(),
													$opciones);
			if (isset($datos['url'])) {
				$datos['popup'] = $escapador->escapeJs($vinculo->estado_popup());
				$datos['popup_parametros'] = $vinculo->get_popup_parametros();
				$datos['target'] = $escapador->escapeJs($vinculo->get_target());
				$datos['activado'] = 1;
				$datos['ajax'] = $escapador->escapeJs($vinculo->get_ajax());
				$datos['nombre_ventana' ] = $escapador->escapeJs($vinculo->get_id_ventana_popup());
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