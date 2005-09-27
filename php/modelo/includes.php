<?
/*
require_once("nucleo/solicitud_especifica.php");	//Representa el pedido de un ITEM por un USUARIO
require_once("nucleo/lib/error.php");	    		//Error Handling
require_once("nucleo/lib/cronometro.php");          //Cronometrar ejecucion
require_once("nucleo/lib/monitor.php");	   			//Monitoreo general
require_once("nucleo/lib/encriptador.php");			//Encriptador
require_once("nucleo/lib/finalizar.php");			//Implementacion de DESTRUCTORES
require_once("nucleo/lib/mensaje.php");				//Modulo de mensajes parametrizables
require_once("nucleo/lib/cola_mensajes.php");		//Cola de mensajes utilizada durante la EJECUCION
require_once("instancias.php");                     //(NO SVN) Listado de INSTANCIAS...

*/
//require_once("nucleo/lib/db.php");		    		//Manejo de bases (utiliza abodb340)
require_once("nucleo/lib/varios.php");				//Funciones genericas (Manejo de paths, etc.)
require_once("nucleo/lib/sql.php");					//Libreria de manipulacion del SQL
require_once("nucleo/lib/excepcion_toba.php");		//Excepciones del TOBA
require_once("nucleo/lib/logger.php");				//Logger
require_once("nucleo/lib/asercion.php");       	   	//Aserciones
require_once("nucleo/toba.php");					//Obtencion de referencias a los objetos CENTRALES
?>