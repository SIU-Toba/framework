<?
#-------------------------------------------------------------------------------
#----<  Inicio el ENTORNO  >----------------------------------------------------
#-------------------------------------------------------------------------------
require_once("nucleo/solicitud_especifica.php");	//Representa el pedido de un ITEM por un USUARIO
require_once("nucleo/lib/error.php");	    		//Error Handling
require_once("nucleo/lib/cronometro.php");          //Cronometrar ejecucion
require_once("nucleo/lib/monitor.php");	   			//Monitoreo general
require_once("nucleo/lib/db.php");		    		//Manejo de bases (utiliza abodb340)
require_once("nucleo/lib/encriptador.php");			//Encriptador
require_once("nucleo/lib/varios.php");				//Funciones genericas (Manejo de paths, etc.)
require_once("nucleo/lib/sql.php");					//Libreria de manipulacion del SQL
require_once("nucleo/lib/finalizar.php");			//Implementacion de DESTRUCTORES
require_once("nucleo/lib/excepcion_toba.php");		//Excepciones del TOBA
require_once("nucleo/lib/logger.php");				//Logger
require_once("nucleo/lib/mensaje.php");				//Modulo de mensajes parametrizables
require_once("nucleo/lib/cola_mensajes.php");		//Cola de mensajes utilizada durante la EJECUCION
require_once("nucleo/lib/asercion.php");       	   	//Aserciones
require_once("nucleo/toba.php");					//Obtencion de referencias a los objetos CENTRALES
require_once("instancias.php");                     //(NO SVN) Listado de INSTANCIAS...
//require_once("configuracion.php");                  //(NO SVN) Configuracion de la instalacion
//-------------------------------------------------------------------------
define("apex_buffer_clave","x_buffer_clave");		//Clave interna de los BUFFERS


	#-[1]- Creacion del cronometro y encriptador
	$cronometro =& new cronometro();	//Creo el cronometro;
	$encriptador =& new encriptador();	//Creo el encriptador;
    
    if(isset($instancia[apex_pa_instancia]))
    {
		#-[2]- Abro la conexion a la INSTANCIA
		try{
			abrir_base("instancia",$instancia[apex_pa_instancia]);
		}catch(exception $e){
			echo "No es posible conectarse a la instancia.";
			//if(apex_pa_validacion_debug) echo "<pre>"; print_r($instancia[apex_pa_instancia]);
			die();
		}
    }else{
        die("No hay una entrada para " . apex_pa_instancia . " en el archivo de instancias.");
    }
	$cronometro->marcar("INICIO: Abrir la conexion a la INSTANCIA","nucleo");

################################################################################
?>