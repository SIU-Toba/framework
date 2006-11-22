<?php
define("apex_sesion_qs_finalizar","fs");    	//SOLICITUD de finalizacion de sesion
define("apex_sesion_qs_cambio_proyecto","cps"); //SOLICITUD de cambio e proyecto: cerrar sesion y abrir nueva

/**
 * Representa la sesión del usuario en la aplicacion. En su ciclo de vida presenta tres momentos:
 *  - Inicio, donde se producen validaciones (generalmente despues de un login)
 *  - Nudo o refresco, donde se valida que no haya excedido el tiempo de no-interaccion, etc
 *  - Finalizacion. Se borra toda la informacion de la sesion en memoria
 * 
 * Cabe aclarar que el sentido de la sesion es orientada al ciclo de vida del usuario en este proyecto y no
 * sobre el  $_SESSION, siendo éste un contenedor de información manejado en bajo nivel por lo que llamamos memoria
 * @see toba_memoria
 * 
 * @package Centrales
 */
class toba_sesion
{
	static private $instancia;
	protected $ventana_validacion = 60;

	static function instancia($clase='toba_sesion')
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new $clase();	
		}
		return self::$instancia;	
	}

	protected function __construct(){}	

	/**
	 * Hay una sesion iniciada?
	 */
	function activa()
	{
		return isset($_SESSION['toba']['id']);
	}

	/**
	 * Refresca la sesion, esto se produce con una sesion activa() una vez por pedido de página
	 */
	function controlar_estado_activacion()
	{
		if ( $this->activa() ) {

			// Controlo si se solicito el final de la sesion
			if ($this->controlar_fin_sesion()) {
				$this->finalizar();
				toba::memoria()->set_item_solicitado(null);//Fuerza a buscar el item inicial
				return false;
			}

			// Controlo si tiene un grupo de acceso dentro del proyecto actual
			$this->get_grupo_acceso();

			// Controlo el tiempo de no interaccion
			$ventana = toba::proyecto()->get_parametro('sesion_tiempo_no_interac_min');
			if($ventana != 0){ // 0 implica desactivacion
				$tiempo_desconectado = ((time()-$_SESSION['toba']["ultimo_acceso"])/60);//Tiempo desde el ultimo REQUEST
				if ( $tiempo_desconectado >= $ventana){
					toba::notificacion("Usted ha permanecido mas de $ventana minutos sin interactuar 
								con el servidor. Por razones de seguridad su sesion ha sido eliminada. 
								Por favor vuelva a registrarse si desea continuar utilizando el sistema.
								Disculpe las molestias ocasionadas.");
					$this->finalizar("Se exedio la ventana temporal ($ventana m.)");					
					return false;
				}
			}
			// Controlo el tiempo maximo de sesion
			$maximo = toba::proyecto()->get_parametro('sesion_tiempo_maximo_min');
			if($maximo != 0){ // 0 implica desactivacion
				$tiempo_total = ((time()-$_SESSION['toba']["inicio"])/60);//Tiempo desde el ultimo REQUEST
				if ( $tiempo_total >= $maximo){
					toba::notificacion("Se ha superado el tiempo de sesion permitido ($maximo minutos)
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
	}
	
	protected function controlar_fin_sesion()
	{
		return isset($_GET[apex_sesion_qs_finalizar])&&($_GET[apex_sesion_qs_finalizar]==1);
	}	
	
	/**
	 * Intenta iniciar la sesion de un par usuario/clave
	 * @return Excepcion toba_error si no valida
	 */
	function iniciar($usuario, $clave=null)
	{
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		try {
			if (toba_instancia::es_ip_rechazada($ip)) {
				throw new toba_error('La IP esta bloqueada. Contactese con el administrador');
			}
			$proyecto = toba_proyecto::get_id();
			toba::usuario()->cargar($usuario, $clave);
			$grupo_acceso = toba::usuario()->get_grupo_acceso();

			//Creo la sesion
			$id = toba_instancia::get_id_sesion();
			if (php_sapi_name() !== 'cli') {
				toba_instancia::abrir_sesion(	$id, toba::usuario()->get_id(), $proyecto );
			}
			$_SESSION['toba']["id"] = $id;
			$_SESSION['toba']['proyecto_inicial'] = $proyecto;
			$_SESSION['toba']["apex_pa_ID"] = apex_pa_ID; //Punto de acceso utilizado para abrir la sesion
			$_SESSION['toba']["inicio"]=time();
			//PATHs
			$_SESSION['toba']["path"] = toba_dir();
			$_SESSION['toba']["path_php"] = $_SESSION['toba']["path"]. "/php";
			toba::logger()->debug('Se creo la SESION [usuario: ' . $_SESSION['toba']["usuario"]['id'] . ' ]', 'toba');
			
			$this->conf__inicio($usuario);
			
			//Si la solicitud ya esta en proceso, reinicio forzando la recarga del nucleo
			if (toba::nucleo()->solicitud_en_proceso()) {
				throw new toba_reset_nucleo('INICIAR... recargando el nucleo.');
			}
		} catch ( toba_error_login $e ) {
			//Registro la falla de login			
			toba_instancia::registrar_error_login($usuario, $ip, $e->getMessage());
			$cant_max_intentos = toba::proyecto()->get_parametro('validacion_intentos');
			if (isset($cant_max_intentos)) {
				//Bloqueo la IP si la cantidad de intentos supera los esperados dentro de la ventana temporal establecida
				$ventana_temporal = toba::proyecto()->get_parametro('validacion_intentos_min');
				if (! isset($ventana_temporal)) {
					$ventana_temporal = $this->ventana_validacion;
				}
				$intentos = toba_instancia::get_cantidad_intentos_en_ventana_temporal($ip, $ventana_temporal);
				$supero_tope_intentos_en_ventana = $intentos > $cant_max_intentos;
				if ( $supero_tope_intentos_en_ventana ) {
					toba_instancia::bloquear_ip($ip);
				}
			}
			throw new toba_error_login($e->getMessage());
		}
	}

	/**
	 * Cierra una sesion
	 * @return Excepcion toba_reset_nucleo si hay una solicitud en curso
	 */
	function finalizar($observaciones="")
	{
		if (isset($_SESSION['toba']["id"]))
		{
			$this->conf__fin();
			//Cierro la sesion de la base
			toba_instancia::cerrar_sesion($_SESSION['toba']["id"], $observaciones);
			unset($_SESSION['toba']["id"]);
			unset($_SESSION['toba']["apex_pa_ID"]);
			unset($_SESSION['toba']["inicio"]);
			unset($_SESSION['toba']["path"]);
			unset($_SESSION['toba']["path_php"]);
			unset($_SESSION['toba']["ultimo_acceso"]);
			toba_editor::limpiar_memoria();
			toba_instalacion::limpiar_memoria();
			toba_instancia::limpiar_memoria();
			toba_proyecto::limpiar_memoria();
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
			if (toba::nucleo()->solicitud_en_proceso()) {
				throw new toba_reset_nucleo('FINALIZAR... recargando el nucleo.');
			}
		}
	}

	/**
	 * Ventana de inicializacion del contexto de ejecucion del proyecto en el pedido de página actual
	 * (antiguo inicializacion.php)
	 * @ventana
	 */
	function iniciar_contexto() {}	

	/**
	 * Ventana de extensión del inicio de la sesion de un usuario
	 * @ventana
	 */
	protected function conf__inicio($usuario) {}

	/**
	 * Ventana de extensión del fin de la sesión actual
	 * @ventana
	 */	
	protected function conf__fin() {}

	/**
	 * Ventana de extensión de la refresco o actualización de la sesión actual
	 * @ventana
	 */		
	protected function conf__actualizar_sesion() {}
	
	/**
	 * Retorna el grupo de acceso de la sesion actual
	 * @see toba_usuario::get_grupo_acceso()
	 */
	function get_grupo_acceso()
	{
		if( toba_editor::modo_prueba() && (toba_proyecto::get_id() != toba_editor::get_id())) {
			return toba_editor::get_grupo_acceso_previsualizacion();
		} else {
			return toba::usuario()->get_grupo_acceso();		
		}
	}
	
	function get_id()
	{
		return $_SESSION['toba']['id'];
	}

	function get_proyecto()
	{
		return $_SESSION['toba']['proyecto_inicial'];
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