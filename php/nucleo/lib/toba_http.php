<?php

/**
 * Maneja HEADERS de HTTP.
 * Hay que llamarla antes de devolver cualquier tipo de contenido o llamar a session_start
 * 
 * @package SalidaGrafica
 */
class toba_http
{
	static protected $nombre_ini = 'web_server.ini';
	static protected $config;
	
	static function cache()
	//Induce al BROWSER a cachear esta pagina
	{
		//Atencion!! Esto no funcion si se llama despues del session_start()!!!!
		session_cache_limiter ('private');
	}

	static function no_cache()
	//Induce al BROWSER a NO cachear esta pagina
	{
		header("Expires: Mon, 26 Jul 1987 05:00:00 GMT");					// Pone una fecha vieja
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");		// Siempre modificado
		header("Cache-Control: no-cache, must-revalidate");					// HTTP/1.1
		header("Pragma: no-cache");
	}
	
	static function pdf()
	{
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="downloaded.pdf"');
	}
	
	static function encoding()
	{
		header('Content-Type: text/html; charset=iso-8859-1');
	}

	static function headers_standart()
	//Manejo standart de headers
	{
		//Parche para solucionar el error del HISTORY BACK de los browsers:
		//	cuando la pagina anterior fue solicitada con un POST y se presiona el boton BACK
		//	se muestra un mensaje de pagina caducada y un usuario puede pensar que es un error del sistema
		//	Este error de transparencia del cache esta comentado en el RFC del HTTP 
		self::encoding();
		if (acceso_post()){
			if (!headers_sent()) {
				self::cache();
			} else {
				self::no_cache();
			}
		}
	}
	
	static function headers_download($tipo, $archivo, $longitud) 
	{ 
		header("Cache-Control: private"); 
		header("Content-type: $tipo"); 
		header("Content-Length: $longitud"); 
		header("Content-Disposition: attachment; filename=$archivo"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); 

	} 
	
	static function get_url_actual($incluir_qs= false, $incluir_uri=false)
	{
		$qs =self::get_query_string();
		$ru = self::get_uri();
		$url = self::get_protocolo() . self::get_nombre_servidor();
		if ($incluir_uri) {		
			$url .= $ru;
		}
		if ($incluir_qs) {		
			if ($qs != '' && stripos($ru, $qs) === FALSE) {			//Si el querystring no esta dentro del request uri, lo agrego
				$url .= $qs;
			}			
		}
		
		return $url;
	}
	
	static function get_protocolo($basado_en_host = true, $forzar_seguro = false)
	{
		$basico = 'http';
		if ($forzar_seguro || ($basado_en_host &&  self::usa_protocolo_seguro())) {
			$basico .= 's';
		}
		$basico .= '://';
		return $basico;
	}
	
	static function get_nombre_servidor()
	{
		$srv_name = self::get_config('SERVER_NAME');					//Igual a HTTP_HOST si no esta forzando UseCanonicalName pero escapado minimamente
		$nombre = htmlentities($srv_name, ENT_QUOTES, 'UTF-8');			//Se debe usar UseCanonicalName junto con esta variable en la config del webserver
		$puerto = self::get_puerto();									//Se debe usar UseCanonicalPhysicalPort  On para obtener el puerto real del webserver, sino es un nro cualquiera
		if (trim($puerto) != '' && $puerto != '80' && $puerto != '443') {
			$nombre .= ':'. $puerto;
		}
		return $nombre;
	}
	
	static function usa_protocolo_seguro()
	{
		$secure = self::get_config('HTTPS');
		return (isset($secure) && (strtolower($secure) != 'off'));		//Hay que poder forzarlo
	}
	
	static function get_puerto()
	{
		return self::get_config('SERVER_PORT');
	}
	
	static function get_uri()
	{
		return self::strleft($_SERVER['REQUEST_URI'], '?');
	}
		
	static function get_query_string()
	{
		$qs = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING']: '';
		return $qs;
	}
	
	//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	protected static function strleft($s1, $s2) 
	{
		$length = strpos($s1, $s2);
		if ($length !== false) {
			return substr($s1, 0, $length);
		} 		
		return $s1;
	}
	
	protected static function get_config($entrada)
	{
		$conf = self::cargar_config();
		$entrada_file = strtolower($entrada);
		if (isset($conf[$entrada_file])) {
			return $conf[$entrada_file];
		} elseif (isset($_SERVER[$entrada])) {
			return $_SERVER[$entrada];
		}
		return null;
	}
	
	protected static function cargar_config()
	{
		if (! isset(self::$config)) {
			$path_base = toba::instalacion()->get_path_carpeta_instalacion();
			$path_real = realpath($path_base .'/'. self::$nombre_ini);		
			if ($path_real !== false && file_exists($path_real)) {
				$ini = new toba_ini($path_real);
				if ($ini->existe_entrada('server_config')) {
					self::$config = $ini->get('server_config');
				}
			} else {
				self::$config = array();
			}
		}
		return self::$config;
	}
}
?>