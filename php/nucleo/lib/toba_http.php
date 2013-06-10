<?php

/**
 * Maneja HEADERS de HTTP.
 * Hay que llamarla antes de devolver cualquier tipo de contenido o llamar a session_start
 * 
 * @package SalidaGrafica
 */
class toba_http
{
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
		toba_http::encoding();
		if( acceso_post() ){
			if(!headers_sent()){
				toba_http::cache();
			}else{
				toba_http::no_cache();
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
		$srv_name = $_SERVER['SERVER_NAME'];					//Igual a HTTP_HOST si no esta forzando UseCanonicalName pero escapado minimamente
		$nombre = htmlentities($srv_name, ENT_QUOTES, 'UTF-8');		//Se debe usar UseCanonicalName junto con esta variable en la config del webserver
		return $nombre;
	}
	
	static function usa_protocolo_seguro()
	{
		return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off'));
	}
}
?>