<?
require_once("nucleo/acceso_inicio.inc.php");
define("apex_solicitud_tipo","browser");//Establezco el tipo de solicitud
//INCLUYO el del array de SERIES de COLORES
require_once("nucleo/browser/color/series/".apex_pa_color.".inc.php");

//-------------------------------------------------------------------------
require_once("nucleo/browser/http.php");				//Genera Encabezados de HTTP
require_once("nucleo/browser/sesion.php");				//Control de sesiones HTTP 
require_once("nucleo/browser/sesion_usuario.php");		//Validador de usuarios
require_once("nucleo/browser/servicio.php");			//Utilides genericas
require_once("nucleo/browser/recurso.php");				//Encapsulamiento de la llamada a recursos
require_once("nucleo/browser/debug.php");				//DUMP de arrays, arboles y estructuras centrales
require_once("nucleo/browser/vinculador.php");			//Vinculos a otros ITEMS
require_once("nucleo/browser/hilo.php");				//Canal de comunicacion inter-ejecutable
require_once("nucleo/browser/interface/ei.php");		//Elementos de interface

//-------------------------------------------------------------------------

	http::headers_standart();// Es importante que este antes de la llamada a la sesion
	$status_sesion = sesion_browser::autorizar();//Averiguo el estado de la sesion HTTP
	//dump_arbol($status_sesion,"Status");
	$cronometro->marcar('BROWSER: Controlar el status de la sesion',"nucleo");
	if($status_sesion[0])	//------> Sesion VALIDA, creo una solicitud al item
	{
		$solicitud =& new solicitud_browser();
		$solicitud->cargar_info_objetos();
		$solicitud->procesar();
		$solicitud->registrar();
		//dump_conexiones();
		//dump_SESSION();
	}
	else					//------> Sesion INVALIDA: Mostrar pantalla de validacion con mensaje
	{
		$mensaje = $status_sesion[1];
		include("nucleo/browser/logon.php");
	}
//-------------------------------------------------------------------------
require_once("nucleo/acceso_fin.inc.php");
?>