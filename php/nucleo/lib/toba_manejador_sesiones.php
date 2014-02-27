<?php
define("apex_sesion_qs_finalizar","fs");    	//SOLICITUD de finalizacion de sesion
define("apex_sesion_qs_cambio_proyecto","cps"); //SOLICITUD de cambio e proyecto: cerrar sesion y abrir nueva
define('apex_sesion_qs_cambio_pf', 'cpf');	//Solicitud de cambio de perfil funcional activo


/**
 * Maneja los segmentos de memoria y el proceso de creacion de sesiones
 * @package Seguridad
 */
class toba_manejador_sesiones
{
	static private $instanciacion;
	private $instancia;
	private $proyecto;
	private $sesion = null;
	private $usuario = null;
	protected $perfiles_funcionales_activos = array();
	private $autenticacion = null;
	private $contrasenia_vencida = false;
	
	/**
	 * @return toba_manejador_sesiones
	 */
	static function instancia()
	{
		if (!isset(self::$instanciacion)) {
			self::$instanciacion = new toba_manejador_sesiones();	
		}
		return self::$instanciacion;	
	}

	private function __construct()
	{
		define('TOBA_DIR', toba_nucleo::toba_dir());
		if (PHP_SAPI != 'cli') {
			if (session_id() != '') {
				throw new toba_error("Ya existe una sesi�n abierta, probablemente tenga activado session.auto_start = 1 en el php.ini");
			}

			session_name(toba::instalacion()->get_session_name());
			session_start();
		}
		$this->instancia = toba_instancia::get_id();
		$this->proyecto = toba_proyecto::get_id();
		if(!isset($_SESSION[TOBA_DIR]['nucleo'])) { //Primer acceso al sistema
			$_SESSION[TOBA_DIR]['nucleo']['inicio'] = time();
		}
	}

	//------------------------------------------------------------------
	//---  API para el manejo de sesiones ------------------------------
	//------------------------------------------------------------------

	/**
	 * Logueo a una instancia.
	 *	El parametro 3 es pasado como parametro al metodo conf__incial() de toba_sesion.
	 * 	Este metodo puede disparar una excepcion de tipo 'toba_error_autentificacion'
	 */
	function login($id_usuario, $clave=null, $datos_iniciales=null)
	{
		if ( $this->existe_sesion_activa() ) {
			toba::notificacion()->agregar('No es posible loguearse sobre una sesion abierta');
			throw new toba_reset_nucleo();
		}
		if ( $this->existe_usuario_activo() ) {
			$this->procesar_acceso_proyecto($datos_iniciales);
		}
		$this->autenticar($id_usuario, $clave, $datos_iniciales);
		if ($this->contrasenia_vencida) {	//Si se vencio la contrase�a del usuario redirecciono al item correspondiente
			$this->contrasenia_vencida = false;
			throw new  toba_error_login_contrasenia_vencida('La contrase�a actual del usuario ha caducado');
		}
				
		$this->procesar_acceso_instancia($id_usuario, $datos_iniciales);
		
		// Se recarga el nucleo, esta vez sobre una sesion activa.
		if (toba::nucleo()->solicitud_en_proceso()) {
			throw new toba_reset_nucleo('INICIAR SESION... recargando el nucleo.');
		}
	}

	/**
	 * Acceso de un usuario aninimo a la instancia.
	 */
	function login_anonimo($datos_iniciales=null)
	{
		if ( $this->existe_sesion_activa() ) {
			throw new toba_error('No es posible loguearse sobre una sesion abierta.');
		}
		if (!toba::proyecto()->get_parametro('usuario_anonimo')) {
			throw new toba_error('No esta definido el ID del usuario anonimo. No es posible iniciar la sesion.');
		}
		if (!toba::proyecto()->get_parametro('usuario_anonimo_grupos_acc')) {
			throw new toba_error('No esta definido el grupo de acceso del usuario anonimo. No es posible iniciar la sesion.');
		}
		$this->procesar_acceso_instancia(null, $datos_iniciales);
		// Se recarga el nucleo, esta vez sobre una sesion activa.
		if (toba::nucleo()->solicitud_en_proceso()) {
			throw new toba_reset_nucleo('INICIAR SESION... recargando el nucleo.');
		}
	}

	/**
	 * Salida de la sesion creada desde un proyecto
	 */
	function logout($observaciones=null)
	{
		unset($_SESSION['openid']);
		if ( !($this->existe_sesion_activa() && $this->existe_usuario_activo()) ) {
			throw new toba_error('No existe una sesion de la cual desloguearse.');
		}
		$this->procesar_salida_proyecto($observaciones);

		//Mando a hacer el logout para los tipos de autenticacion externos.
		$this->get_autenticacion()->logout();
		
		// Se recarga el nucleo, esta vez sobre una sesion INACTIVA.
		if (toba::nucleo()->solicitud_en_proceso()) {
			throw new toba_reset_nucleo('FINALIZAR SESION... recargando el nucleo.');
		}
	}
	
	/**
	*	Entrada a un proyecto desde la operaci�n de inicializacion de sesion
	*/
	function iniciar_sesion_proyecto($datos_iniciales)
	{
		//Login anonimo de sesiones extendidas.
		if ( ! toba::proyecto()->get_parametro('requiere_validacion') ) {
			$this->login_anonimo($datos_iniciales);
		}		
		$this->procesar_acceso_proyecto($datos_iniciales);			
		if (toba::nucleo()->solicitud_en_proceso()) {
			throw new toba_reset_nucleo('INICIAR SESION PROYECTO... recargando el nucleo.');
		}
	}
	
	/**
	 * Delega la autenticaci�n del proyecto a un objeto 
	 * @param toba_autenticable $autenticacion Objeto responsable de la autenticacion
	 */
	function set_autenticacion(toba_autenticable $autenticacion)
	{
		$this->autenticacion = $autenticacion;
	}
	
	function get_autenticacion() {
		if (!isset($this->autenticacion)) {
			switch (toba::instalacion()->get_tipo_autenticacion()) {
				case 'ldap':
					$this->set_autenticacion(new toba_autenticacion_ldap());
					break;
				case 'openid':
					$this->set_autenticacion(new toba_autenticacion_openid());
					break;
				case 'cas':
					$this->set_autenticacion(new toba_autenticacion_cas());
					break;
				case 'saml':

					$this->set_autenticacion(new toba_autenticacion_saml());
					break;				
				default:												//tipo == 'toba'
					$this->set_autenticacion(new toba_autenticacion_basica());
					break;					
			}
		}		
		return $this->autenticacion;
	}

	//------------------------------------------------------------------
	//---  Informacion GENENRAL  ---------------------------------------
	//------------------------------------------------------------------

	/**
	* Devuelve true si existe un usuario logueado en la instancia
	*/
	function existe_usuario_activo($instancia=null)
	{
		$instancia = isset($instancia) ? $instancia : $this->instancia;
		return isset($_SESSION[TOBA_DIR]['instancias'][$instancia]['id_usuario']);
	}
	
	/**
	* Devuelve true si existe una sesion para el proyecto actual
	*/
	function existe_sesion_activa($proyecto=null)
	{
		$proyecto = isset($proyecto) ? $proyecto : $this->proyecto;
		return isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$proyecto]['info_sesion']['id']);
	}

	/**
	* Devuelve el ID de la sesion actual. Este es el ID utilizado por toba para registrar la sesion en la base.
	*/
	function get_id_sesion($proyecto=null)
	{
		$proyecto = isset($proyecto) ? $proyecto : $this->proyecto;
		if ($this->existe_sesion_activa($proyecto)) {
			return $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$proyecto]['info_sesion']['id'];
		}
	}

	/**
	* Devuelve el ID del usuario logueado a la instancia actual.
	*/
	function get_id_usuario_instancia()
	{
		if ($this->existe_usuario_activo()) {
			return $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['id_usuario'];
		}
	}

	/**
	* Devuelve la cantidad de instancias cargadas (con o sin usuarios activos)
	*/
	function get_cantidad_instancias_cargadas()
	{
		return count($_SESSION[TOBA_DIR]['instancias']);
	}

	/**
	* Devuelve la cantidad de instancias que poseen un usuario activo
	*/
	function get_cantidad_instancias_activas()
	{
		$x = 0;
		if(isset($_SESSION[TOBA_DIR]['instancias']) && is_array($_SESSION[TOBA_DIR]['instancias'])) {
			foreach( array_keys($_SESSION[TOBA_DIR]['instancias']) as $instancia ) {
				if ($this->existe_usuario_activo($instancia)) $x++;
			}
		}
		return $x;
	}

	/**
	* Devuelve la cantidad de proyectos cargadas (con o sin sesiones)
	*/
	function get_cantidad_proyectos_cargados()
	{
		return count($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos']);
	}

	/**
	* Devuelve la cantidad de proyectos que poseen una sesion abierta
	*/
	function get_cantidad_proyectos_activos()
	{
		$x = 0;
		if(isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos']) &&
			is_array($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'])) {
			foreach( array_keys($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos']) as $proyecto ) {
				if ($this->existe_sesion_activa($proyecto)) $x++;
			}
		}
		return $x;
	}

	/**
	* Devuelve la cantidad de proyectos que poseen una sesion abierta
	*/
	function get_proyectos_activos()
	{
		$proyectos = array();
		if(isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos']) &&
			is_array($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'])) {
			foreach( array_keys($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos']) as $proyecto ) {
				if ($this->existe_sesion_activa($proyecto)) {
					$proyectos[] = $proyecto;
				}
			}
		}
		return $proyectos;
	}


	/**
	*	Responde si un proyecto puntual fue cargado
	*/
	function existe_proyecto_cargado($proyecto)
	{
		return isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$proyecto]);
	}
	
	/**
	*	Determina los perfiles funcionales pertenecientes del usuario actual
	*/
	function get_perfiles_funcionales()
	{
		if( (toba_editor::modo_prueba() && ( ! toba_editor::acceso_recursivo() ) ) ) {
			return toba_editor::get_perfiles_funcionales_previsualizacion();
		} else {
			return $this->usuario()->get_perfiles_funcionales();
		}
	}	
	
	
	/**
	*	Determina los ids de restricciones funcionales pertenecientes al usuario actual segun sus perfiles activos
	*/
	function get_restricciones_funcionales()
	{
		return toba::usuario()->get_restricciones_funcionales($this->get_perfiles_funcionales_activos());
	}	
		
	/**
	 * Retorna los perfiles funcionales activos en la sesi�n actual
	 * @return array
	 */
	function get_perfiles_funcionales_activos()
	{
		return $this->perfiles_funcionales_activos;
	}
	
	/**
	 * Activa un subconjunto de los perfiles funcionales propios del usuario actual
	 * @param array $activos
	 */
	function set_perfiles_funcionales_activos($activos)
	{
		$perfiles = $this->get_perfiles_funcionales();		
		//Validacion
		foreach ($activos as $perfil) {
			if (! in_array($perfil, $perfiles)) {
				throw new toba_error_seguridad("Se esta intentando activar el perfil '$perfil' y el mismo no pertenece al usuario actual");
			}
		}
		return $this->perfiles_funcionales_activos = $activos;
	}	
	

	/**
	* @deprecated Desde 1.5 usar get_perfiles_funcionales
	*/
	function get_grupos_acceso()
	{
		return $this->get_perfiles_funcionales();

	}

	/**
	*	Determina el perfil de datos del usuario actual
	*/
	function get_perfil_datos()
	{
		if( (toba_editor::modo_prueba() && ( ! toba_editor::acceso_recursivo() ) ) ) {
			return toba_editor::get_perfil_datos_previsualizacion();
		} else {
			return $this->usuario()->get_perfil_datos();
		}
	}

	function verificar_cambio_perfil_activo() 
	{
		if (isset($_POST[apex_sesion_qs_cambio_pf]) && toba::proyecto()->permite_cambio_perfiles()) {
			$perfil_solicitado = trim($_POST[apex_sesion_qs_cambio_pf]);
			if ($perfil_solicitado != apex_ef_no_seteado) {
				toba::logger()->debug('Seteando el perfil '. $perfil_solicitado . ' como activo para el proyecto ' . $this->proyecto);
				$this->set_perfiles_funcionales_activos(array($perfil_solicitado));											
			} else {
				toba::logger()->debug('Seteando como activos para el proyecto los perfiles originales definidos.');
				$this->set_perfiles_funcionales_activos($this->get_perfiles_funcionales());
			}
			toba::memoria()->set_dato('usuario_perfil_funcional_seleccionado', $perfil_solicitado);
		}
	}
	
	//-----------  Acceso a los objetos USUARIO y SESION  ------------------

	/**
	 * @return toba_usuario
	 */
	function usuario()
	{
		if( $this->existe_usuario_activo() && $this->existe_sesion_activa() ) {
			if (!is_object($this->usuario)) {
				toba::logger()->crit("MANEJADOR de SESIONES. Error de consistencia interna, 
										la sesion y el usuario estan marcados como activos, 
										pero la propiedad 'usuario' no se encuentra seteada");
				return new toba_usuario_no_autenticado();
			} else {
				return $this->usuario;
			}
		} else {
			return new toba_usuario_no_autenticado();
		}
	}

	/**
	 * @return toba_sesion
	 */
	function sesion()
	{
		if (php_sapi_name() === 'cli') {
			//Mock para la consola??
		} else {
			if ($this->existe_sesion_activa()) {
				return $this->sesion;
			} else {
				toba::logger()->notice('ATENCION: No existe una sesion activa en la instancia.');
				return null;
			}
		}
	}

	//------------------------------------------------------------------
	//---  ADMINISTRACION interna de USUARIOS y SESIONES  --------------
	//------------------------------------------------------------------

	/**
	*	El nucleo inicializa el manejador.
	*/
	function iniciar()
	{
		if( $this->existe_sesion_activa() ) {			//--> Hay una sesion para el proyecto actual
			$this->cargar_contexto();
			try {
				$this->control_finalizacion_sesion();
				$this->registrar_activacion_sesion();
			} catch ( toba_error $e ) {
				toba::logger()->debug('P�rdida de sesi�n: '. $e->getMessage());
				$this->logout($e->getMessage());
				$this->comprobar_acceso_anonimo();
			}
		} elseif( $this->existe_usuario_activo() ) {	//--> Hay un usuario en la instancia, pero no logueado al proyecto actual
			//Inicializacion de la sesion del proyecto
			if ( $this->sesion_esta_extendida() ) {
				// Tiene metodo de inicializacion?
				$metodo_inicializacion = 'ini__automatica';
				if( method_exists($this->get_sesion_proyecto(), $metodo_inicializacion) ) {
					try {
						$datos_iniciales = $this->get_sesion_proyecto()->$metodo_inicializacion();
						$this->procesar_acceso_proyecto($datos_iniciales);
						toba::logger()->debug("MANEJADOR de SESIONES: Incializacion automatica de la sesion del proyecto. DATOS: " . var_export($datos_iniciales,true),'toba');
						return;
					} catch ( toba_error_ini_sesion $e ) {
						//La inicializacion automatica fallo, no pasa nada malo. Se prueba con item de inicializacion
						toba::logger()->notice("MANEJADOR de SESIONES: Fallo la inicializacion automatica de la sesion del proyecto. " . $e->getMessage() ,'toba');
					}
				} 
				// Tiene item de inicializacion
				if ( $this->sesion_posse_item_inicializacion() ) {
					//Apunto al nucleo al item de inicializacion de sesion
					$item[0] = toba::proyecto()->get_id();
					$item[1] = toba::proyecto()->get_parametro('item_set_sesion');
					toba::memoria()->set_item_solicitado($item);
				} else {
					$this->procesar_acceso_proyecto();
				}
			} else {
				//La sesion no esta extendida
				$this->procesar_acceso_proyecto();
			}
		} else {										//--> ** Entrada INICIAL **
			$this->comprobar_acceso_anonimo();
		}
		if ( $this->modo_previsualizacion() ) {
			$this->activar_editor();
		}		
	}
	
	private function comprobar_acceso_anonimo()
	{
		// Si el proyecto no requiere autentificacion disparo una sesion anonima
		if ( ! toba::proyecto()->get_parametro('requiere_validacion') ) {
			//Si la sesion esta extendida, aunque sea anonima necesita inicializacion.
			if ( $this->sesion_posse_item_inicializacion() ) {
				//Apunto al nucleo al item de inicializacion de sesion
				$item[0] = toba::proyecto()->get_id();
				$item[1] = toba::proyecto()->get_parametro('item_set_sesion');
				toba::memoria()->set_item_solicitado($item);
			} else {
				$this->login_anonimo();
			}
		}
	}

	/**
	* El nucleo finaliza el manejador. Se persiste el usuario y la sesion
	*/
	function finalizar()
	{
		//ei_arbol($_SESSION, 'sesion', null, true);
		if( $this->existe_sesion_activa() ) {
			$this->guardar_contexto();
		}
	}

	//------------------------------------------------------------------

	/**
	*	Se crea el usuario y la sesion en la instancia
	*/
	private function procesar_acceso_instancia($id_usuario=null, $datos_iniciales=null)
	{
		try {
			//$this->borrar_segmento_proyecto();//Potencialmente utilizado en items publicos
			$this->cargar_usuario($id_usuario);
			$this->registar_usuario();
			$this->abrir_sesion($datos_iniciales);
			$this->guardar_contexto();
		} catch ( toba_error $e ) {
			unset($this->usuario);
			unset($this->sesion);
			$this->borrar_segmento_instancia();
			throw $e;
		}		
	}

	/**
	*	Se accede a un proyecto directamente
	*/
	private function procesar_acceso_proyecto($datos_iniciales=null)
	{
		if ( $this->existe_sesion_activa() ) {
			toba::notificacion()->agregar('No es posible cargar un proyecto que ya se encuentra abierto');
		}
		if ( ! $this->existe_usuario_activo() ) {
			//Por si el acceso desde el item de inicializacion de sesion esta mal
			throw new toba_error('ERROR: El acceso directo a un proyecto solo puede solicitarse si existe un usuario logueado.');
		}
		try {
			$this->cargar_usuario( $this->get_id_usuario_instancia() );
			$this->abrir_sesion($datos_iniciales);
			$this->guardar_contexto();
		} catch ( toba_error $e ) {
			unset($this->usuario);
			unset($this->sesion);
			$this->borrar_segmento_proyecto();
			throw $e;
		}
	}

	/**
	*	Salida de un proyecto. Dispara en cascada la salida de la instancia y la instalacion si es necesario.
	*/
	private function procesar_salida_proyecto($observaciones=null)
	{
		$this->cerrar_sesion($observaciones);
		$this->borrar_segmento_proyecto();
		$this->borrar_segmento_instancia();
		if ($this->get_cantidad_instancias_cargadas()==0) {
			$this->borrar_segmento_instalacion();
		}
		//$this->desregistrar_usuario();
		/*
		if ($this->get_cantidad_proyectos_cargados()==0) {
			// El proyecto era el unico abierto, intento restaurar la memoria completamente
			$this->borrar_segmento_instancia();
			if ($this->get_cantidad_instancias_cargadas()==0) {
				$this->borrar_segmento_instalacion();
			}
		} else {
			// Si no quedan proyectos con sesiones en la instancia, hay que desregistrar el usuario
			if ($this->get_cantidad_proyectos_activos()==0) {
				$this->desregistrar_usuario();
			}
		}
		*/
	}
	
	private function cargar_usuario($id_usuario=null)
	{
		$msg_error = "No es posible cargar el usuario '$id_usuario'. El mismo no posee un perfil funcional definido para el proyecto '{$this->proyecto}'.";
		if (isset($id_usuario)) {
			toba::logger()->debug("Cargando USUARIO '$id_usuario' en el proyecto '{$this->proyecto}'",'toba');
			$this->usuario = $this->get_usuario_proyecto($id_usuario);
		} else {
			toba::logger()->debug("Cargando USUARIO ANONIMO en el proyecto '{$this->proyecto}'",'toba');
			$this->usuario = new toba_usuario_anonimo('anonimo');
		}
		// Se controla que el usuario tenga usuario un perfil funcional v�lido para el proyecto actual
		$grupos_acceso = $this->usuario->get_perfiles_funcionales();
		if ( ! $grupos_acceso ) { 
			throw new toba_error($msg_error);
		}
		$this->perfiles_funcionales_activos = $grupos_acceso;
	}

	private function registar_usuario()
	{
		$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['inicio'] = time();
		$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['id_usuario'] = $this->usuario->get_id();
	}
	
	private function abrir_sesion($datos_iniciales=null, $tipo='normal')
	{
		$id = toba::instancia()->get_id_sesion();
		toba::logger()->debug("Crear SESION '$id' en el proyecto '{$this->proyecto}'",'toba');
		toba::instancia()->abrir_sesion( $id, $this->usuario->get_id(), $this->proyecto );
		$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['info_sesion']['id'] = $id;
		$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['info_sesion']['inicio'] = time();
		$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['info_sesion']['tipo'] = $tipo;
		toba::logger()->debug('Se creo la SESION [usuario: ' . $this->usuario->get_id() . ' ]', 'toba');
		$this->sesion = $this->get_sesion_proyecto();
		$this->sesion->conf__inicial($datos_iniciales);
		$this->registrar_activacion_sesion();
	}

	private function registrar_activacion_sesion()
	{
		//toba::logger()->debug('Registro activacion sesion','toba');
		$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['info_sesion']['ultimo_acceso'] = time();
		$this->sesion->conf__activacion();
	}

	/**
	*	Controla el cierre de la sesion.
	*/
	private function control_finalizacion_sesion()
	{
		//El usuario solicito cerrar la sesion 
		if ( isset($_GET[apex_sesion_qs_finalizar])&&($_GET[apex_sesion_qs_finalizar]==1) ) {
			throw new toba_error_usuario('Finalizada por el usuario');
		}
		// Controlo el tiempo de no interaccion
		$ventana = toba::proyecto()->get_parametro('sesion_tiempo_no_interac_min');
		if($ventana != 0){ // 0 implica desactivacion
			$ultimo_acceso = $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['info_sesion']['ultimo_acceso'];
			$tiempo_desconectado = ((time()-$ultimo_acceso)/60);//Tiempo desde el ultimo REQUEST
			if ( $tiempo_desconectado >= $ventana){
				toba::notificacion("Usted ha permanecido mas de $ventana minutos sin interactuar 
							con el servidor. Por razones de seguridad su sesion ha sido eliminada. 
							Por favor vuelva a registrarse si desea continuar utilizando el sistema.
							Disculpe las molestias ocasionadas.");
				throw new toba_error_autorizacion("Se exedio la ventana temporal ($ventana m.)");
			}
		}
		// Controlo el tiempo maximo de sesion
		$maximo = toba::proyecto()->get_parametro('sesion_tiempo_maximo_min');
		if($maximo != 0){ // 0 implica desactivacion
			$inicio_sesion = $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['info_sesion']['inicio'];
			$tiempo_total = ((time()-$inicio_sesion)/60);//Tiempo desde que se inicio la sesion
			if ( $tiempo_total >= $maximo){
				toba::notificacion("Se ha superado el tiempo de sesion permitido ($maximo minutos)
							Por favor vuelva a registrarse si desea continuar utilizando el sistema.
							Disculpe las molestias ocasionadas.");
				throw new toba_error_autorizacion("Se exedio el tiempo maximo de sesion ($maximo m.)");
			}
		}
		
		//--Controla que la autenticacion siga presente (caso CAS y SAML)
		$this->get_autenticacion()->verificar_logout();
	}	

	private function cerrar_sesion($observaciones=null)
	{
		$this->sesion->conf__final();
		toba::instancia()->cerrar_sesion($this->get_id_sesion(), $observaciones);
		if (PHP_SAPI != 'cli') {
			session_regenerate_id(true);
		}
	}

	private function desregistrar_usuario()
	{
		$usuario = $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['id_usuario'];
		toba::logger()->debug("Desregistrando el usuario '$usuario' de la instancia '{$this->instancia}'",'toba');
		unset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['inicio']);
		unset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['id_usuario']);
	}
	
	/**
	*	Permite controlar desde un proyecto la vida de la sesion del otro a bajo nivel.
	*	Para salir del normalmente del proyecto actual, usar 'logout()' 
	*	Este metodo fuerza la eliminacion directa y esta pensado para que lo consuma el proyecto toba_editor.
	*/
	function abortar_sesion_proyecto($proyecto, $obs=null)
	{
		$id = $this->get_id_sesion($proyecto);
		toba::logger()->debug("Abortando la sesion '$id' del proyecto '$proyecto'.",'toba');
		toba::instancia()->cerrar_sesion($id, $obs);
		$this->borrar_segmento_proyecto($proyecto);
	}

	
	function recargar_info_proyecto($proyecto)
	{
		if ($proyecto == toba_proyecto::get_id()) {
			toba_proyecto::eliminar_instancia();
		}
		$info =& toba::manejador_sesiones()->segmento_info_proyecto($proyecto);
		$info = null;
	}
	
	static function recargar_info_instalacion()
	{
		toba_instalacion::instancia(true);
	}
	
	static function recargar_info_instancia()
	{
		toba_instancia::instancia(true);
	}
	
	
	//------------------------------------------------------------------
	
	private function guardar_contexto()
	{
		if( !isset($this->usuario) || !isset($this->sesion) ) {
			throw new toba_error('MANEJADOR de SESIONES: No es posible guardar el contexto.');
		}
		$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['usuario'] = serialize($this->usuario);
		$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['perfiles_funcionales_activos'] = serialize($this->perfiles_funcionales_activos);
		$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['sesion'] = serialize($this->sesion);
	}
	
	private function cargar_contexto()
	{
		if( !isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['usuario']) ||
			!isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['sesion']) ) {
			throw new toba_error('MANEJADOR de SESIONES: Error interno. No es posible cargar la sesion solicitada.');
		}
		//Cargo las clases de los archivos serializados
		//SESION
		$subclase = toba::proyecto()->get_parametro('sesion_subclase');
		$archivo = toba::proyecto()->get_parametro('sesion_subclase_archivo');
		if( $subclase && $archivo ) {
			require_once($archivo);
		}
		//USUARIO
		$subclase = toba::proyecto()->get_parametro('usuario_subclase');
		$archivo = toba::proyecto()->get_parametro('usuario_subclase_archivo');
		if( $subclase && $archivo ) {
			require_once($archivo);
		}
		$this->usuario = unserialize($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['usuario']);
		$this->sesion = unserialize($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['sesion']);
		$this->perfiles_funcionales_activos = unserialize($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['perfiles_funcionales_activos']);
	}

	//------------------------------------------------------------------

	
	
	private function get_usuario_proyecto($id_usuario)
	{
		$subclase = toba::proyecto()->get_parametro('usuario_subclase');
		$archivo = toba::proyecto()->get_parametro('usuario_subclase_archivo');
		if( $subclase && $archivo ) {
			require_once($archivo);
			return new $subclase($id_usuario);
		} else {
			return new toba_usuario_basico($id_usuario);
		}
	}
	
	public function invocar_autenticar($id_usuario, $clave, $datos_iniciales) {
		return $this->invocar_metodo_usuario('autenticar', array($id_usuario, $clave, $datos_iniciales) );		
	}
	
	private function invocar_metodo_usuario($metodo, $parametros)
	{
		$subclase = toba::proyecto()->get_parametro('usuario_subclase');
		$archivo = toba::proyecto()->get_parametro('usuario_subclase_archivo');
		if( $subclase && $archivo ) {
			require_once($archivo);
			$clase = $subclase;
		} else {
			$clase = 'toba_usuario_basico';
		}		
		$estado = call_user_func_array( array($clase,$metodo), $parametros );
		return $estado;
	}

	private function get_sesion_proyecto()
	{
		$subclase = toba::proyecto()->get_parametro('sesion_subclase');
		$archivo = toba::proyecto()->get_parametro('sesion_subclase_archivo');
		if( $subclase && $archivo ) {
			require_once($archivo);
			return new $subclase();
		} else {
			return new toba_sesion();
		}
	}

	private function sesion_esta_extendida()
	{
		return ( 	toba::proyecto()->get_parametro('sesion_subclase') &&
					toba::proyecto()->get_parametro('sesion_subclase_archivo') );
	}	

	private function sesion_posse_item_inicializacion()
	{
		return ( 	toba::proyecto()->get_parametro('item_set_sesion') );
	}	

	private function sesion_posse_metodo_inicializacion()
	{
		return ( 	toba::proyecto()->get_parametro('item_set_sesion') );
	}	

	//------------------------------------------------------------------
	//---  Relacion con el EDITOR --------------------------------------
	//------------------------------------------------------------------

	/**
	*	Indica si el proyecto esta ejecutandose en modo previsualizacion
	*/
	private function modo_previsualizacion()
	{
		$segmento =& $this->segmento_editor();
		return ( isset($segmento) && isset($segmento['proyecto']) && ($segmento['proyecto'] == $this->proyecto) );
	}

	/**
	*	Se encarga de inicializar a toba_editor en los proyectos ejecutados en modo previsualizacion
	*	(la inicializacion esta en la activacion de la sesion del editor y no se invocaba 
	*		cuando el proyecto estaba previsualizaciondose)
	*/
	private function activar_editor()
	{
		if( $this->proyecto != toba_editor::get_id() ) {
			toba_editor::referenciar_memoria();
		}
	}

	//------------------------------------------------------------------
	//---  Manejo de la autentificacion --------------------------------
	//------------------------------------------------------------------

	/**
	*	Lleva a cabo la autentificacion. 
	*	Los fallos se registran con excepciones, en funcionamiento normal no devulve ningun valor
	*/
	private function autenticar($id_usuario, $clave=null, $datos_iniciales=null)
	{
		if (toba::proyecto()->get_parametro('validacion_debug')) {
			//Es autologin, no se autentica
			return;
		}
		if (!isset($clave)) {
			throw new toba_error_autenticacion('Es necesario ingresar la clave.');
		}
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		if ($this->invocar_metodo_usuario('es_ip_rechazada',array($ip))) {
			throw new toba_error('La IP esta bloqueada. Contactese con el administrador');
		}
		if ($this->invocar_metodo_usuario('es_usuario_bloqueado', array($id_usuario))) {
			throw new toba_error('El usuario se encuentra bloqueado. Cont�ctese con el administrador');
		}
		// Disparo la autenticacion
		if ($this->get_autenticacion() != null) {
			$estado = $this->autenticacion->autenticar($id_usuario, $clave, $datos_iniciales);
		} else {
			throw new toba_error_seguridad('No existe la autenticaci�n propuesta');
		}
		if (!$estado) {
			$error = 'La combinaci�n usuario/clave es incorrecta';
			$this->invocar_metodo_usuario('registrar_error_login', array($id_usuario, $ip, $error));
			$cant_max_intentos = toba::proyecto()->get_parametro('validacion_intentos');
			if (isset($cant_max_intentos)) {				
				$bloquear_usuario = (toba::proyecto()->get_parametro('validacion_bloquear_usuario') == '1');
				$lanzar_excepcion = (toba::proyecto()->get_parametro('validacion_bloquear_usuario') == '2');
				//Bloqueo el Usuario o IP si la cantidad de intentos supera los esperados dentro de la ventana temporal establecida
				$ventana_temporal = toba::proyecto()->get_parametro('validacion_intentos_min');
				if ( $bloquear_usuario || $lanzar_excepcion) {
					$intentos = $this->invocar_metodo_usuario('get_cantidad_intentos_usuario_en_ventana_temporal',array($id_usuario, $ventana_temporal));
				}else{
					$intentos = $this->invocar_metodo_usuario('get_cantidad_intentos_en_ventana_temporal',array($ip, $ventana_temporal));
				}
				$supero_tope_intentos_en_ventana = $intentos > $cant_max_intentos;
				if ( $supero_tope_intentos_en_ventana ) {
					if ($bloquear_usuario) {
						$this->invocar_metodo_usuario('bloquear_usuario',array($id_usuario));
						throw new toba_error_autenticacion("$error. Ha superado el l�mite de inicios de sesion. El usuario ha sido bloqueado.");
					}elseif ($lanzar_excepcion) {
						throw new toba_error_autenticacion_intentos("$error. Ha superado el l�mite de inicios de sesion.|$intentos");
					} else {
						$this->invocar_metodo_usuario('bloquear_ip',array($ip));
						throw new toba_error_autenticacion("$error. La IP ha sido bloqueada.");
					}
				}
			}
			throw new toba_error_autenticacion($error);
		}
		if ($this->get_autenticacion() != null) {
			$this->contrasenia_vencida = $this->autenticacion->verificar_clave_vencida($id_usuario);
		} else {
			throw new toba_error_seguridad('No existe la autenticaci�n propuesta');
		}
	}

	//------------------------------------------------------------------
	//---  ESPACIOS de MEMORIA  ----------------------------------------
	//------------------------------------------------------------------

	static function & segmento_info_instalacion()
	{
		if (!isset($_SESSION[TOBA_DIR]['instalacion'])) {
			$_SESSION[TOBA_DIR]['instalacion'] = array();
		}
		return $_SESSION[TOBA_DIR]['instalacion'];
	}

	function & segmento_info_instancia()
	{
		if (!isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['info'])) {
			$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['info'] = array();
		}
		return $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['info'];
	}

	function & segmento_datos_instancia()
	{
		if (!isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['datos_globales'])) {
			$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['datos_globales'] = array();
		}
		return $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['datos_globales'];
	}
	
	function & segmento_editor()
	{
		if (!isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['editor'])) {
			$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['editor'] = array();
		}
		return $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['editor'];
	}

	function & segmento_info_proyecto($proyecto = null)
	{
		if (! isset($proyecto)) {
			$proyecto = $this->proyecto;	
		}
		if(!isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$proyecto]['info'])) {
			$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$proyecto]['info'] = array();
		}
		return $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$proyecto]['info'];
	}

	function & segmento_memoria_proyecto()
	{
		if(!isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['memoria'])) {
			$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['memoria'] = array();
		}
		return $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['memoria'];
	}

	function & segmento_memoria_puntos_control()
	{
		if(!isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['puntos_control'])) {
			$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['puntos_control'] = array();
		}
		return $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['puntos_control'];
	}

	//----  Borrado de los espacios de memoria  ---------------------------------------------

	function borrar_segmento_instalacion()
	{
		unset($_SESSION[TOBA_DIR]['instalacion']);	
		toba_instalacion::eliminar_instancia();
		toba::logger()->debug('BORRAR segmento memoria INSTALACION','toba');
	}

	function borrar_segmento_instancia()
	{
		unset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]);
		toba_instancia::eliminar_instancia();
		toba::logger()->debug('BORRAR segmento memoria INSTANCIA: ' . $this->instancia,'toba');
	}

	function borrar_segmento_editor()
	{
		unset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['editor']);
		toba::logger()->debug('BORRAR segmento memoria EDITOR','toba');
	}

	function borrar_segmento_proyecto($proyecto=null)
	{
		if(!isset($proyecto)) $proyecto = $this->proyecto;
		unset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$proyecto]);
		toba_proyecto::eliminar_instancia();
		toba_memoria::eliminar_instancia();
		toba::logger()->debug('BORRAR segmento memoria PROYECTO: ' . $proyecto ,'toba');
	}
}
?>
