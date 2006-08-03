<?php
//GET
define("apex_sesion_qs_finalizar","fs");    	//SOLICITUD de finalizacion de sesion
define("apex_sesion_qs_cambio_proyecto","cps"); //SOLICITUD de cambio e proyecto: cerrar sesion y abrir nueva


class sesion_toba
{
	static private $instancia;
	protected $ventana_validacion = 60;

	static function instancia($clase='sesion_toba')
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new $clase();	
		}
		return self::$instancia;	
	}

	protected function __construct(){}	
	
	function activa()
	{
		return isset($_SESSION['toba']['id']);
	}
	
	function controlar_estado_activacion()
	{
		if ( $this->activa() ) {

			// Controlo si se solicito el final de la sesion
			if ($this->controlar_fin_sesion()) {
				$this->finalizar();
				toba::get_hilo()->set_item_solicitado(null);//Fuerza a buscar el item inicial
				return false;
			}

			// Controlo si tiene un grupo de acceso dentro del proyecto actual
			$this->get_grupo_acceso();

			// Controlo el tiempo de no interaccion
			$ventana = info_proyecto::instancia()->get_parametro('sesion_tiempo_no_interac_min');
			if($ventana != 0){ // 0 implica desactivacion
				$tiempo_desconectado = ((time()-$_SESSION['toba']["ultimo_acceso"])/60);//Tiempo desde el ultimo REQUEST
				if ( $tiempo_desconectado >= $ventana){
					toba::get_cola_mensajes("Usted ha permanecido mas de $ventana minutos sin interactuar 
								con el servidor. Por razones de seguridad su sesion ha sido eliminada. 
								Por favor vuelva a registrarse si desea continuar utilizando el sistema.
								Disculpe las molestias ocasionadas.");
					$this->finalizar("Se exedio la ventana temporal ($ventana m.)");					
					return false;
				}
			}
			// Controlo el tiempo maximo de sesion
			$maximo = info_proyecto::instancia()->get_parametro('sesion_tiempo_maximo_min');
			if($maximo != 0){ // 0 implica desactivacion
				$tiempo_total = ((time()-$_SESSION['toba']["inicio"])/60);//Tiempo desde el ultimo REQUEST
				if ( $tiempo_total >= $maximo){
					toba::get_cola_mensajes("Se ha superado el tiempo de sesion permitido ($maximo minutos)
								Por favor vuelva a registrarse si desea continuar utilizando el sistema.
								Disculpe las molestias ocasionadas.");
					$this->finalizar("Se exedio el tiempo maximo de sesion ($maximo m.)");
					return false;
				}
			}
			
			//Ventana de actualizacion para el usuario
			$this->conf__actualizar_sesion();

			//Guardo el momento del acceso
			$_SESSION['toba']["ultimo_acceso"]=time();
			return true;
		} else {
			//__falta: puerta de USUARIO ANONIMO
			return false;
		}
		//Guardo el momento del ultimo request
	}
	
	protected function controlar_fin_sesion()
	{
		return isset($_GET[apex_sesion_qs_finalizar])&&($_GET[apex_sesion_qs_finalizar]==1);
	}	
	
	function iniciar($usuario, $clave=null)
	{
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		try {
			if (info_instancia::es_ip_rechazada($ip)) {
				throw new excepcion_toba('La IP esta bloqueada. Contactese con el administrador');
			}
			$proyecto = info_proyecto::get_id();
			toba::get_usuario()->cargar($usuario, $clave);
			$grupo_acceso = toba::get_usuario()->get_grupo_acceso();

			//Creo la sesion
			$id = info_instancia::get_id_sesion();
			info_instancia::abrir_sesion(	$id, toba::get_usuario()->get_id(), $proyecto );
			$_SESSION['toba']["id"] = $id;
			$_SESSION['toba']["apex_pa_ID"] = apex_pa_ID; //Punto de acceso utilizado para abrir la sesion
			$_SESSION['toba']["inicio"]=time();
			//PATHs
			$_SESSION['toba']["path"] = toba_dir();
			$_SESSION['toba']["path_php"] = $_SESSION['toba']["path"]. "/php";
			toba::get_logger()->debug('Se creo la SESION [usuario: ' . $_SESSION['toba']["usuario"]['id'] . ' ]', 'toba');
			
			$this->conf__inicio($usuario);
			
			//Si la solicitud ya esta en proceso, reinicio forzando la recarga del nucleo
			if (toba::get_nucleo()->solicitud_en_proceso()) {
				throw new excepcion_reset_nucleo('INICIAR... recargando el nucleo.');
			}
		} catch ( excepcion_toba_login $e ) {
			//Registro la falla de login			
			info_instancia::registrar_error_login($usuario, $ip, $e->getMessage());
			$cant_max_intentos = info_proyecto::instancia()->get_parametro('validacion_intentos');
			if (isset($cant_max_intentos)) {
				//Bloqueo la IP si la cantidad de intentos supera los esperados dentro de la ventana temporal establecida
				$ventana_temporal = info_proyecto::instancia()->get_parametro('validacion_intentos_min');
				if (! isset($ventana_temporal)) {
					$ventana_temporal = $this->ventana_validacion;
				}
				$intentos = info_instancia::get_cantidad_intentos_en_ventana_temporal($ip, $ventana_temporal);
				$supero_tope_intentos_en_ventana = $intentos > $cant_max_intentos;
				if ( $supero_tope_intentos_en_ventana ) {
					info_instancia::bloquear_ip($ip);
				}
			}
			throw new excepcion_toba_login($e->getMessage());
		}
	}

	function finalizar($observaciones="")
	//Cierra una sesion de la aplicacion
	{
		if (isset($_SESSION['toba']["id"]))
		{
			$this->conf__fin();
			//Cierro la sesion de la base
			info_instancia::cerrar_sesion($_SESSION['toba']["id"], $observaciones);
			unset($_SESSION['toba']["id"]);
			unset($_SESSION['toba']["apex_pa_ID"]);
			unset($_SESSION['toba']["inicio"]);
			unset($_SESSION['toba']["path"]);
			unset($_SESSION['toba']["path_php"]);
			unset($_SESSION['toba']["ultimo_acceso"]);
			editor::limpiar_memoria();
			info_instalacion::limpiar_memoria();
			info_instancia::limpiar_memoria();
			info_proyecto::limpiar_memoria();
			//Existieron archivos temporales asociados a la sesion, los elimino...
			if (isset($_SESSION['toba']["archivos"])) {
				foreach($_SESSION['toba']["archivos"] as $archivo) {
					//SI puedo ubicar los archivos los elimino
					if(is_file($archivo)){
						unlink($archivo);
					}
				}
			}
			//session_unset();
			//session_destroy();
			//Si la solicitud ya esta en proceso, reinicio forzando la recarga del nucleo
			if (toba::get_nucleo()->solicitud_en_proceso()) {
				throw new excepcion_reset_nucleo('FINALIZAR... recargando el nucleo.');
			}
		}
	}

	protected function conf__inicio($usuario) {}
	
	protected function conf__fin() {}
	
	protected function conf__actualizar_sesion() {}
	
	function get_grupo_acceso()
	{
		if( editor::modo_prueba() && (info_proyecto::get_id() != editor::get_id())) {
			return editor::get_grupo_acceso_previsualizacion();
		} else {
			return toba::get_usuario()->get_grupo_acceso();		
		}
	}
	
	function get_id()
	{
		return $_SESSION['toba']['id'];
	}

	//---------------------------------------------------------------
	//	Controles extendidos aplicables al usuario
	//---------------------------------------------------------------

	protected function validar_vencimiento($vencimiento)
	{
		if ($vencimiento != ""){
			if (date("Y-m-d") <= $vencimiento){	
				return array(true,"Validacion vencimiento OK!");
			}else{
				return $this->error_login(0,"El fecha de vigencia del usuario ha caducado.");
			}	
		}else{
			return array(true,"No hay fecha de vencimiento");
		}
	}

	protected function validar_dia($dias)
	{
		if ($dias != ""){
			$dias = decbin($dias);
			if ($dias[date("w")] == 1){
				return array(true,"Validacion dia OK!");
			}else{
				return $this->error_login(0,"No posee permisos para ingresar al sistema los dias '" . date("l") . "'.");
			}
		}else{
			return array(true,"No hay restricciones de dia");
		}
	}

	protected function validar_ip($ip)
	{		
		if ($ip!=""){
			if ($ip == $_SERVER["REMOTE_ADDR"]){
				return array(true,"Validacion IP OK!");
			}else{
				return $this->error_login(0,"No posee autorización para ingresar desde la IP : ".$_SERVER["REMOTE_ADDR"].".");
			}	
		}else{
			return array(true,"No hay restricciones de IP");
		}
	}	

	protected function validar_horario($hora_entrada,$hora_salida)
	{
		if (($hora_entrada != "") && ($hora_salida != "")){
			if (($hora_entrada < date("H:i")) && ($hora_salida > date("H:i"))){
				return array(true,"Validacion Franja horaria OK!");
			}else{
				return $this->error_login(0,"No posee autorización para ingresar a las ".date("H:i")."hs . Su franja horaria es : ".$hora_entrada."-".$hora_salida.".");
			}	
		}else{
			return array(true,"No hay restricciones de horario");
		}
	}
}
?>