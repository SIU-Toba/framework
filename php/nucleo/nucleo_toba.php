<?php
//require_once('nucleo/browser/clases/interfaces.php');
require_once("nucleo/lib/error.php");	    				//Error Handling
require_once("nucleo/lib/cronometro.php");          		//Cronometrar ejecucion
require_once("nucleo/lib/monitor.php");	   					//Monitoreo general
require_once("nucleo/lib/db.php");		    				//Manejo de bases (utiliza abodb340)
require_once("nucleo/lib/encriptador.php");					//Encriptador
require_once("nucleo/lib/varios.php");						//Funciones genericas (Manejo de paths, etc.)
require_once("nucleo/lib/sql.php");							//Libreria de manipulacion del SQL
require_once("nucleo/lib/excepcion_toba.php");				//Excepciones del TOBA
require_once("nucleo/lib/logger.php");						//Logger
require_once("nucleo/lib/mensaje.php");						//Modulo de mensajes parametrizables
require_once("nucleo/lib/cola_mensajes.php");				//Cola de mensajes utilizada durante la EJECUCION
require_once("nucleo/lib/asercion.php");       	   			//Aserciones
require_once("nucleo/lib/permisos.php");					//Administrador de permisos particulares
require_once("nucleo/browser/recurso.php");					//Obtencion de imágenes de la aplicación
require_once("nucleo/componentes/constructor_toba.php");	//Constructor de componentes
require_once("nucleo/componentes/cargador_toba.php");		//Cargador de componentes
require_once("nucleo/componentes/catalogo_toba.php");		//Catalogo de componentes

/**
	Servicios independientes del tipo de solicitud
		- Creacion de componentes internos

	salidas no HTML: toba::get_logger()->ocultar();
*/
class nucleo_toba
{
	static private $instancia;
	private $solicitud;
	private $medio_acceso;
	
	private function __construct()
	{
		toba::get_cronometro();
	}
	
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new nucleo_toba();	
		}
		return self::$instancia;	
	}	
	
	function acceso_web()
	{
		try {
			require_once("nucleo/solicitud.php");
			require_once("nucleo/browser/http.php");				//Genera Encabezados de HTTP
			require_once("nucleo/browser/sesion.php");				//Control de sesiones HTTP
			require_once("nucleo/browser/usuario_http.php");		//Validador de usuarios
		    http::headers_standart();//Antes de la sesion, si o si.
			sesion::autorizar(); //Antes del HTML si o si
			toba::get_cronometro()->marcar('SESION: Controlar STATUS SESION',"nucleo");
			$item = toba::get_hilo()->obtener_item_solicitado();
			$this->solicitud = solicitud::get_solicitud($item[0], $item[1]);
			$this->procesar();
		} catch (excepcion_toba_login $e) {
			global $mensaje;
			$mensaje = $e->getMessage();
			try {
					$pro = apex_pa_proyecto;	
					$candidato = toba_dir() ."'/proyectos/$pro/php/logon.php'";
					if (file_exists($candidato)) {
						include($candidato);
					}else{
						include("nucleo/browser/logon.php");
					}//IF					
			}catch(exception $e){
				//No hay una conexion, salta el combo del logon
				echo ei_mensaje('No es posible INGRESAR al sistema: ' . $e->getMessage());		
			}
		}		
	}

	function acceso_consola()
	{
		try {
			define("apex_solicitud_tipo","consola");                //Establezco el tipo de solicitud		
			require_once("nucleo/solicitud_consola.php");
			$this->solicitud = new solicitud_consola();
			$this->procesar();
			exit( $this->solicitud->obtener_estado_proceso() );
		} catch (excepcion_toba $e) {
			ei_mensaje($e->getMessage());
		}		
	}
	
	function acceso_wddx()
	{
		define("apex_solicitud_tipo","wddx");                //Establezco el tipo de solicitud				
		require_once("nucleo/solicitud_wddx.php");
		$this->solicitud = new solicitud_wddx();
		$this->procesar();
	}
	
	function get_solicitud()
	{
		return $this->solicitud;	
	}
	
	function procesar()
	{
		toba::get_db("instancia");
		try{
			//Si el proyecto no es toba, incluyo el archivo de inicializacion
			if (toba::get_hilo()->obtener_proyecto() != 'toba') {
				//Invoco el archivo de INICIALIZACION del proyecto
				include_once("inicializacion.php");
			}
			$this->solicitud->procesar();	//Se llama a la ACTIVIDAD del ITEM
			$this->solicitud->registrar();
			$this->solicitud->finalizar_objetos();
		}catch( Exception $e ){
			toba::get_logger()->crit($e);
		}
		toba::get_logger()->guardar();
		//ATENCION!: dba::cerrar_bases();
	}
}
?>