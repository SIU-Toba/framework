<?
define("apex_solicitud_tipo","soap");	
//-------------------------------------------------------------------------
require_once("nucleo/acceso_inicio.inc.php");  	//Configuracion comun a todas las solicitudes
//-------------------------------------------------------------------------
require_once("nucleo/lib/comunicador_soap.php");		//Comunicacion entre instancias
require_once("nucleo/browser/interface/ei.php");		//Elementos de interface
//-------------------------------------------------------------------------
define("apex_qs_separador","||");				//Separador utilizado para diferenciar campos de valores compuestos
define("apex_hilo_qs_item","ai");				//ITEM de catalogo a solicitar
//-------------------------------------------------------------------------

	try
	{
		$solicitud =& new solicitud_soap();
		//$solicitud->cargar_info_objetos();
		$solicitud->procesar();
		//$solicitud->registrar();
	}catch( Exception $e ){
		//Si una excepcion no fue atrapada en ningun lado, se atrapa aca!
		debug_print_backtrace();
	}

//-------------------------------------------------------------------------
require_once("nucleo/acceso_fin.inc.php");
?>