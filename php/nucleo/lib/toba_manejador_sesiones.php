<?php
	
define("apex_sesion_qs_finalizar","fs");    	//SOLICITUD de finalizacion de sesion
define("apex_sesion_qs_cambio_proyecto","cps"); //SOLICITUD de cambio e proyecto: cerrar sesion y abrir nueva
define('apex_sesion_qs_cambio_pf', 'cpf');	//Solicitud de cambio de perfil funcional activo
define('apex_sesion_qs_cambio_usuario', 'alcu'); //Solicitud de cambio de usuario via App Launcher


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
	protected $perfiles_datos_activos;
	private $autenticacion = null;
	private $contrasenia_vencida = false;
	private $_usuarios_posibles=array();
	
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
	
	static function verificar_directorio_toba()
	{
	    if (!defined('TOBA_DIR')) {
	        define('TOBA_DIR', toba_nucleo::toba_dir());
	    }
	}

	private function __construct()
	{
		if (!defined('TOBA_DIR')) {
		    define('TOBA_DIR', toba_nucleo::toba_dir());
		}
		if (PHP_SAPI != 'cli') {
			if (session_id() != '') {
				throw new toba_error("Ya existe una sesión abierta, probablemente tenga activado session.auto_start = 1 en el php.ini");
			}
			if (! toba_nucleo::instancia()->es_acceso_rest()) {
				session_name(toba::instalacion()->get_session_name());
				session_start();
			}
		}
		$this->instancia = toba_instancia::get_id();
		$this->proyecto = toba_proyecto::get_id();
		
		if(session_status() == PHP_SESSION_ACTIVE && !isset($_SESSION[TOBA_DIR]['nucleo'])) { //Primer acceso al sistema
			$_SESSION[TOBA_DIR]['nucleo']['inicio'] = time();		
		}
	}
	
	static function enviar_csrf_hidden()
	{
		$tm = toba::memoria();
		if ($tm->existe_dato_operacion(apex_sesion_csrt)) {
			$valor = $tm->get_dato_operacion(apex_sesion_csrt);
			echo toba_form::hidden(apex_sesion_csrt, $valor);
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
		if ($this->contrasenia_vencida) {	//Si se vencio la contraseña del usuario redirecciono al item correspondiente
			$this->contrasenia_vencida = false;
			throw new  toba_error_login_contrasenia_vencida('La contraseña actual del usuario ha caducado');
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
	 * Realiza un cambio de usuario en runtime
	 * @param string $actual  ID del usuario actual
	 * @param string $nuevo  ID hasheado del usuario al cual cambiar
	 * @param mixed $datos_iniciales Array de datos para inicializar la sesion [opcional]
	 * @throws toba_error_seguridad
	 */
	function cambio_usuario($actual, $nuevo, $datos_iniciales=null)
	{
		//verificar usuario actual
		if ($actual != toba::usuario()->get_id()) {
			throw new toba_error_seguridad('El usuario actual no es el especificado');			
		}
		//Verificar que el usuario nuevo esta en la lista de posibles fijada por el app_launcher
		$mapeo = $this->recuperar_mapeo_usuarios($nuevo , $this->_usuarios_posibles);
		if (empty($mapeo)) {
			throw new toba_error_seguridad('Es intentando acceder a un usuario no valido' );
		}
		//Si todo va bien.
		$this->procesar_salida_proyecto("Logout por cambio de usuario");			//Redirije a la pantalla de login, quizas hay que hacer algo distinto por ejemplo, no borrar la sesion
		$this->procesar_acceso_instancia($mapeo, $datos_iniciales);
	}	
		
	/**
	*	Entrada a un proyecto desde la operación de inicializacion de sesion
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
	 * Delega la autenticación del proyecto a un objeto 
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
				case 'saml_onelogin':
					$this->set_autenticacion(new toba_autenticacion_saml_onelogin());
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
	 * Retorna los perfiles funcionales activos en la sesión actual
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
	
	/**
	*	Determina el perfil de datos del usuario actual
	* @deprecated 3.0.0
	* @see toba_manejador_sesiones::get_perfiles_datos
     	*/
	function get_perfil_datos()
	{
		if( (toba_editor::modo_prueba() && ( ! toba_editor::acceso_recursivo() ) ) ) {
			return toba_editor::get_perfil_datos_previsualizacion();
		} else {
			return $this->usuario()->get_perfil_datos();
		}
	}

	/**
	 * Determina los perfiles de datos del usuario actual
	 * @return array
	 */
	function get_perfiles_datos()
	{
		if( (toba_editor::modo_prueba() && ( ! toba_editor::acceso_recursivo() ) ) ) {
			return toba_editor::get_perfiles_datos_previsualizacion();
		} else {
			return $this->usuario()->get_perfiles_datos();
		}		
	}	
	
	function get_perfil_datos_activo()
	{
		if (! isset($this->perfiles_datos_activos)) {
			$this->perfiles_datos_activos = $this->get_perfiles_datos();
		}
		return $this->perfiles_datos_activos;
	}
	
	/**
	* @deprecated 3.0.0
	* @see toba_manejador_sesiones::set_perfiles_datos_activos()
	*/
	function set_perfil_datos_activo($id_perfil)
	{
		if (! is_array($id_perfil)) {
			if (! is_null($id_perfil)) {
				$this->set_perfiles_datos_activos(array($id_perfil));
			}
		} 
	}
	
	/**
	 * Fija cuales son los perfiles de datos activos para el usuario actual, chequea contra configurados en base
	 * @param array $perfiles
	 * @throws toba_error_seguridad
	 */
	function set_perfiles_datos_activos($perfiles)
	{
		if (is_array($perfiles) && ! empty($perfiles)) {
			$disponibles = $this->get_perfiles_datos();									//Controlo que no sea algun perfil que este por fuera de los asignados al usuario
			$df = array_diff($perfiles, $disponibles);
			 if (! empty($df)) {
				throw new toba_error_seguridad('Alguno de los perfiles de datos seteado no es valido');
			}			
			$this->perfiles_datos_activos = $perfiles;
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
				$this->control_cambio_usuario();				
			} catch ( toba_error $e ) {
				toba::logger()->debug('Pérdida de sesión: '. $e->getMessage());
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
					toba::memoria()->set_item_solicitado($item, false);					//No genera token xq implica redireccion interna que el usuario no puede ver
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
				toba::memoria()->set_item_solicitado($item, false);						//No genera token xq implica redireccion interna que el usuario no puede ver
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
		// Se piden los perfiles funcionales del usuario.
		$this->perfiles_funcionales_activos = $this->usuario->get_perfiles_funcionales();
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

	private function control_cambio_usuario()
	{
		$param = toba::memoria()->get_parametro(apex_sesion_qs_cambio_usuario);
		if (! is_null($param)) {			
			$id = $this->usuario()->get_id();
			$this->cambio_usuario($id, $param);
		}
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
		$proyecto = toba::proyecto()->get_id();
		$ventana = toba_parametros::get_session_no_interaccion($proyecto);
		if($ventana != 0) { // 0 implica desactivacion
			$ultimo_acceso = $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['info_sesion']['ultimo_acceso'];
			$tiempo_desconectado = ((time()-$ultimo_acceso)/60);//Tiempo desde el ultimo REQUEST
			if ( $tiempo_desconectado >= $ventana) {
				toba::notificacion("Usted ha permanecido mas de $ventana minutos sin interactuar 
							con el servidor. Por razones de seguridad su sesion ha sido eliminada. 
							Por favor vuelva a registrarse si desea continuar utilizando el sistema.
							Disculpe las molestias ocasionadas.");
				throw new toba_error_autorizacion("Se exedio la ventana temporal ($ventana m.)");
			}
		}
		// Controlo el tiempo maximo de sesion
		 $maximo = toba_parametros::get_session_tiempo_maximo($proyecto);
		if($maximo != 0){ // 0 implica desactivacion
			$inicio_sesion = $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['info_sesion']['inicio'];
			$tiempo_total = ((time()-$inicio_sesion)/60);//Tiempo desde que se inicio la sesion
			if ( $tiempo_total >= $maximo) {
				toba::notificacion("Se ha superado el tiempo de sesion permitido ($maximo minutos)
							Por favor vuelva a registrarse si desea continuar utilizando el sistema.
							Disculpe las molestias ocasionadas.");
				throw new toba_error_autorizacion("Se exedio el tiempo maximo de sesion ($maximo m.)");
			}
		}
		
		//--Controla que la autenticacion siga presente (caso CAS y SAML), no se hace para proyectos sin login
		if (toba::proyecto()->get_parametro('requiere_validacion') ) {
			$this->get_autenticacion()->verificar_logout();
		}
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
		$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['perfiles_datos_activos'] = serialize($this->perfiles_datos_activos);
		$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['usuarios_posibles'] = serialize($this->_usuarios_posibles);
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
		$this->cargar_clase_sesion();
		
		//USUARIO
		$this->cargar_clase_usuario();
		
		$this->usuario = unserialize($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['usuario']);
		$this->sesion = unserialize($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['sesion']);
		$this->perfiles_funcionales_activos = unserialize($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['perfiles_funcionales_activos']);
		$this->perfiles_datos_activos = unserialize($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['perfiles_datos_activos']);
		$this->_usuarios_posibles = unserialize($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['usuarios_posibles']);
	}

	//------------------------------------------------------------------
	private function cargar_clase_sesion()
	{
		$subclase = toba::proyecto()->get_parametro('sesion_subclase');
		$archivo = toba::proyecto()->get_parametro('sesion_subclase_archivo');
		if (trim($archivo) != '' && trim($subclase) != '') {
			$pm = toba::proyecto()->get_parametro('pm_sesion');
			toba_cargador::cargar_clase_archivo($pm, $archivo, toba::proyecto()->get_id());			
		}
	}
	
	private function cargar_clase_usuario()
	{
		$archivo = toba::proyecto()->get_parametro('usuario_subclase_archivo');
		$subclase = toba::proyecto()->get_parametro('usuario_subclase');	
		if (trim($archivo) != '' && trim($subclase) != '') {
			$pm = toba::proyecto()->get_parametro('pm_usuario');
			toba_cargador::cargar_clase_archivo($pm, $archivo, toba::proyecto()->get_id());			
		} 
	}
			
	private function get_usuario_proyecto($id_usuario)
	{	
		$subclase = toba::proyecto()->get_parametro('usuario_subclase');			
		if (trim($subclase) == '') {
			$subclase = 'toba_usuario_basico';	
		} else {
			$this->cargar_clase_usuario();
		}
		return new $subclase($id_usuario);		
	}
	
	public function invocar_autenticar($id_usuario, $clave, $datos_iniciales) {
		return $this->invocar_metodo_usuario('autenticar', array($id_usuario, $clave, $datos_iniciales) );		
	}
	
	private function invocar_metodo_usuario($metodo, $parametros)
	{
		$subclase = toba::proyecto()->get_parametro('usuario_subclase');	
		if (trim($subclase)  == '') {
			$subclase = 'toba_usuario_basico';	
		} else {
			$this->cargar_clase_usuario();
		}
		$estado = call_user_func_array( array($subclase, $metodo), $parametros );
		return $estado;
	}

	private function get_sesion_proyecto()
	{
		$subclase = toba::proyecto()->get_parametro('sesion_subclase');
		if (trim($subclase)  == '') {
			$subclase = 'toba_sesion';
		} else {
			$this->cargar_clase_sesion();
		}
		return new $subclase();
	}

	private function sesion_esta_extendida()
	{
		return ( toba::proyecto()->get_parametro('sesion_subclase') &&
					toba::proyecto()->get_parametro('sesion_subclase_archivo'));
	}	

	private function sesion_posse_item_inicializacion()
	{
		return ( toba::proyecto()->get_parametro('item_set_sesion'));
	}	

	private function sesion_posse_metodo_inicializacion()
	{
		return ( toba::proyecto()->get_parametro('item_set_sesion'));
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
			throw new toba_error('El usuario se encuentra bloqueado. Contáctese con el administrador');
		}
		// Disparo la autenticacion
		if ($this->get_autenticacion() != null) {
			$estado = $this->autenticacion->autenticar($id_usuario, $clave, $datos_iniciales);
		} else {
			throw new toba_error_seguridad('No existe la autenticación propuesta');
		}
		if (!$estado) {
			$error = 'La combinación usuario/clave es incorrecta';
			$this->invocar_metodo_usuario('registrar_error_login', array($id_usuario, $ip, $error));
			$proyecto = toba::proyecto()->get_id();
			$cant_max_intentos = toba_parametros::get_intentos_validacion($proyecto);
			if (isset($cant_max_intentos)) {
				$accion_pedida = toba_parametros::get_debe_bloquear_usuario($proyecto);
				$bloquear_usuario = ($accion_pedida == '1'); 
				$lanzar_excepcion =  ($accion_pedida == '2'); 
				//Bloqueo el Usuario o IP si la cantidad de intentos supera los esperados dentro de la ventana temporal establecida
				$ventana_temporal = toba_parametros::get_ventana_intentos($proyecto);
				if ( $bloquear_usuario || $lanzar_excepcion) {
					$intentos = $this->invocar_metodo_usuario('get_cantidad_intentos_usuario_en_ventana_temporal',array($id_usuario, $ventana_temporal));
				}else{
					$intentos = $this->invocar_metodo_usuario('get_cantidad_intentos_en_ventana_temporal',array($ip, $ventana_temporal));
				}
				$supero_tope_intentos_en_ventana = $intentos > $cant_max_intentos;
				if ( $supero_tope_intentos_en_ventana ) {
					if ($bloquear_usuario) {
						$this->invocar_metodo_usuario('bloquear_usuario',array($id_usuario));
						throw new toba_error_autenticacion("$error. Ha superado el límite de inicios de sesion. El usuario ha sido bloqueado.");
					}elseif ($lanzar_excepcion) {
						throw new toba_error_autenticacion_intentos("$error. Ha superado el límite de inicios de sesion.|$intentos");
					} else {
						$this->invocar_metodo_usuario('bloquear_ip',array($ip));
						throw new toba_error_autenticacion("$error. La IP ha sido bloqueada.");
					}
				}
			}
			throw new toba_error_autenticacion($error);
		}
		if ($this->get_autenticacion() != null) {											//Recupero la lista de cuentas alternativas para el usuario y genero el mapeo interno
			$this->contrasenia_vencida = $this->get_autenticacion()->verificar_clave_vencida($id_usuario);
			$usr_posibles = $this->get_autenticacion()->get_lista_cuentas_posibles();
			if (! empty($usr_posibles)) {
				$usr_posibles = $this->get_descripcion_cuentas_usuario($usr_posibles);
				$this->_usuarios_posibles = $this->generar_mapeo_usuarios($usr_posibles);
			}
		} else {
			throw new toba_error_seguridad('No existe la autenticación propuesta');
		}
	}

	//---------------------------------------------------------------------------------------------
	//		CUENTAS DE USUARIO VALIDAS
	//---------------------------------------------------------------------------------------------
	/**
	 * Devuelve un arreglo conteniendo las cuentas alternativas del usuario (si es que existen)
	 * Incluye ademas el usuario actual bajo el indice 'usuario_actual'
	 * @return array  Formato array('usuario_actual' => 'id', array('id_base' =>id, 'descripcion' => 'nombre'))
	 */
	function get_cuentas_disponibles()
	{
		$rs = array();
		if (! empty($this->_usuarios_posibles)) {
			$rs = array_values($this->_usuarios_posibles);
			$rs['usuario_actual'] = $this->_usuarios_posibles[$this->usuario()->get_id()]['id_base'];
		}
		return $rs;
	}
	
	/**
	 * Genera un mapeo interno de los ids de usuario y arma un arreglo con el id hasheado y el nombre
	 * @param array $lista_usuarios
	 * @return array
	 * @ignore
	 */
	protected function generar_mapeo_usuarios($lista_usuarios)
	{
		$resultado = array();
		$hasher = new toba_hash();
		foreach($lista_usuarios as $usr) {
			$hs = $hasher->hash($usr['id']);
			$resultado[$usr['id']] = array('id_base'  => base64_encode($hs), 'descripcion' => $usr['nombre']);
		}
		return $resultado;
	}
	
	/**
	 * Devuelve el id original del usuario encontrado o un arreglo vacio
	 * @param string $id ID hasheado del usuario
	 * @param array $lista Mapeo de usuarios generado por la funcion anterior
	 * @return mixed
	 * @ignore
	 */
	protected function recuperar_mapeo_usuarios($id, $lista)		
	{
		$resultado = array();
		foreach($lista as $clave => $valores) {
			if ($id == $valores['id_base']) {
				$resultado = $clave;
			}
		}		
		return $resultado;
	}	
	
	/**
	 * Recupera las descripciones de las cuentas de usuario via una subclase del proyecto o toba_usuario_basico
	 * @param array $cuentas Arreglo de cuentas a recuperar las descripciones
	 * @return array El formato debe ser array(array('id' => id, 'nombre' => nombre))
	 * @ignore
	 */
	protected function get_descripcion_cuentas_usuario($cuentas)
	{
		$subclase = toba::proyecto()->get_parametro('usuario_subclase');			
		if (trim($subclase) == '') {
			$subclase = 'toba_usuario_basico';	
		} else {
			$this->cargar_clase_usuario();
		}
		
		$rs = $subclase::recuperar_descripcion_cuentas($cuentas);
		return $rs;
	}	
	
	//------------------------------------------------------------------
	//---  ESPACIOS de MEMORIA  ----------------------------------------
	//------------------------------------------------------------------

	static function & segmento_info_instalacion()
	{
		self::verificar_directorio_toba();
		if (!isset($_SESSION[TOBA_DIR]['instalacion'])) {
			$_SESSION[TOBA_DIR]['instalacion'] = array();
		}
		return $_SESSION[TOBA_DIR]['instalacion'];
	}

	function & segmento_info_instancia()
	{
		self::verificar_directorio_toba();
		if (!isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['info'])) {
			$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['info'] = array();
		}
		return $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['info'];
	}

	function & segmento_datos_instancia()
	{
		self::verificar_directorio_toba();
		if (!isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['datos_globales'])) {
			$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['datos_globales'] = array();
		}
		return $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['datos_globales'];
	}
	
	function & segmento_editor()
	{
		self::verificar_directorio_toba();
		if (!isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['editor'])) {
			$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['editor'] = array();
		}
		return $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['editor'];
	}

	function & segmento_info_proyecto($proyecto = null)
	{
		self::verificar_directorio_toba();
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
		self::verificar_directorio_toba();
		if(!isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['memoria'])) {
			$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['memoria'] = array();
		}
		return $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['memoria'];
	}

	function & segmento_memoria_puntos_control()
	{
		self::verificar_directorio_toba();
		if(!isset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['puntos_control'])) {
			$_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['puntos_control'] = array();
		}
		return $_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$this->proyecto]['puntos_control'];
	}

	//----  Borrado de los espacios de memoria  ---------------------------------------------

	function borrar_segmento_instalacion()
	{
		self::verificar_directorio_toba();
		unset($_SESSION[TOBA_DIR]['instalacion']);	
		toba_instalacion::eliminar_instancia();
		toba::logger()->debug('BORRAR segmento memoria INSTALACION','toba');
	}

	function borrar_segmento_instancia()
	{
		self::verificar_directorio_toba();
		unset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]);
		toba_instancia::eliminar_instancia();
		toba::logger()->debug('BORRAR segmento memoria INSTANCIA: ' . $this->instancia,'toba');
	}

	function borrar_segmento_editor()
	{
		self::verificar_directorio_toba();
		unset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['editor']);
		toba::logger()->debug('BORRAR segmento memoria EDITOR','toba');
	}

	function borrar_segmento_proyecto($proyecto=null)
	{
		self::verificar_directorio_toba();
		if(!isset($proyecto)) $proyecto = $this->proyecto;
		unset($_SESSION[TOBA_DIR]['instancias'][$this->instancia]['proyectos'][$proyecto]);
		toba_proyecto::eliminar_instancia();
		toba_memoria::eliminar_instancia();
		toba::logger()->debug('BORRAR segmento memoria PROYECTO: ' . $proyecto ,'toba');
	}
}
?>
