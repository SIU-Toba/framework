<?php

class toba_http
{
//Maneja HEADERS de HTTP.
//Atencion, hay que llamarla antes de devolver cualquier tipo de contenido o llamar a session_start

	function cache()
	//Induce al BROWSER a cachear esta pagina
	{
		//Atencion!! Esto no funcion si se llama despues del session_start()!!!!
        session_cache_limiter ('private');
	}

	function no_cache()
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

	function headers_standart()
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
}
?>