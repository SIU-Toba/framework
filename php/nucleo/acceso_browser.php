<?
define("apex_solicitud_tipo","browser");                //Establezco el tipo de solicitud
//-------------------------------------------------------------------------
require_once("nucleo/acceso_inicio.inc.php");           //Configuracion comun a todas las solicitudes
//-------------------------------------------------------------------------
require_once("nucleo/browser/http.php");				//Genera Encabezados de HTTP
require_once("nucleo/browser/sesion.php");				//Control de sesiones HTTP 
require_once("nucleo/browser/usuario_http.php");		//Validador de usuarios
require_once("nucleo/browser/recurso.php");				//Encapsulamiento de la llamada a recursos
require_once("nucleo/browser/debug.php");				//DUMP de arrays, arboles y estructuras centrales
require_once("nucleo/browser/vinculador.php");			//Vinculos a otros ITEMS
require_once("nucleo/browser/hilo.php");				//Canal de comunicacion inter-ejecutable
require_once("nucleo/browser/interface/formateo.php"); 	//Funciones de formateo de columnas
require_once("nucleo/browser/interface/ei.php");		//Elementos de interface
require_once("nucleo/browser/logica.php");				//Elementos de logica
require_once("nucleo/lib/parseo.php");			       	//Funciones de parseo
//-------------------------------------------------------------------------

    http::headers_standart();//Antes de la sesion, si o si.
    //Control de sesion: Una sesion consiste en un USUARIO accediendo a un proyecto PROYECTO.
    //Determinar cual es el proyecto ACTUAL es un problema de la sesion (No de la solicitud)
    //Cargar al Usuario (Validarlo, controlar acceso; levantar su perfil de acceso y datos) tambien.
	$status_sesion = sesion::autorizar();
	//ei_arbol($status_sesion,"Status");
	$cronometro->marcar('SESION: Controlar STATUS SESION',"nucleo");
	if($status_sesion[0])	//------> Sesion VALIDA, creo una solicitud al item
	{
        //Incluyo el array de colores
        require_once("nucleo/browser/color/series/".apex_proyecto_estilo.".inc.php");// Array de COLORES
		$solicitud =& new solicitud_browser();
		try{
			$solicitud->procesar();	//Se llama a la ACTIVIDAD del ITEM
		}catch( Exception $e ){
			//Si una excepcion no fue atrapada en ningun lado, se atrapa aca!
			echo ei_mensaje("ATENCION, la excepcion no fue atrapada!");
			$solicitud->log->registrar_excepcion($e);
		}
		$solicitud->registrar();
		$solicitud->finalizar_objetos();
		//dump_session();
	}
	else					//------> Sesion INVALIDA: Mostrar pantalla de validacion con mensaje
	{
		$mensaje = $status_sesion[1];
		include("nucleo/browser/logon.php");
	}

//-------------------------------------------------------------------------
require_once("nucleo/acceso_fin.inc.php");
?>