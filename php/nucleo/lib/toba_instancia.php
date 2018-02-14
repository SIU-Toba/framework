<?php
/**
 * Datos de ACCESO y AUDITORIA necesarios para el funcionamiento del nucleo.
 * Enmascara principalmente al archivo de configuración instancia.ini de la instancia actual
 * 
 * @package Centrales
 */
class toba_instancia
{
	static private $instancia;
	static protected $id;
	protected $id_solicitud;
	private $memoria;							//Referencia al segmento de $_SESSION asignado
		
	/**
	 * @return toba_instancia
	 */
	static function instancia($recargar=false)
	{
		if (!isset(self::$instancia) || $recargar) {
			self::$instancia = new toba_instancia($recargar);	
		}
		return self::$instancia;	
	}

	static function eliminar_instancia()
	{
		self::$instancia = null;
	}

	private function __construct($recargar)
	{
		$this->memoria =& toba::manejador_sesiones()->segmento_info_instancia();

		if(!$this->memoria || $recargar) {
			$this->memoria = $this->get_datos_instancia( self::get_id() );
		}
	}
	
	/**
	 * Retorna el contenido del archivo instancia.ini de la instancia 
	 */
	static function get_datos_instancia($id_instancia)
	{
		$archivo = toba::nucleo()->toba_instalacion_dir().'/i__' . $id_instancia . '/instancia.ini';
		if ( is_file( $archivo ) ) {
			return parse_ini_file( $archivo, true );
		} else {
			throw new toba_error("INFO_INSTANCIA: No se encuentra definido el archivo de inicializacion de la INSTANCIA: '".self::get_id()."' ('$archivo')");
		} 	
	}
	
	/**
	 * Retorna el id de la instancia actual
	 * La configuracion puede estar cono variable de entorno del servidor o una constante del PA
	 */
	static function get_id()
	{
		if ( ! isset(self::$id)) {
//			var_dump($_SERVER);
			if (defined('apex_pa_instancia')) {
				self::$id = apex_pa_instancia;
			} elseif (isset($_SERVER['TOBA_INSTANCIA'])) {
				self::$id = $_SERVER['TOBA_INSTANCIA'];
//			} elseif (isset($_SERVER['toba_instancia'])) {
//				self::$id = $_SERVER['toba_instancia'];
			} else {
				throw new toba_error("INFO_INSTANCIA: La INSTANCIA ACTUAL no se encuentra definida (no existe la variable de entorno TOBA_INSTANCIA ni la constante 'apex_pa_instancia')");
			}
		}		
		return self::$id;
	}
	
	/**
	 * Retorna un vinculo a la base de datos que forma parte de la instancia
	 * @return toba_db_postgres7
	 */
	function get_db()
	{
		if ( isset( $this->memoria['base'] ) ) {
			return toba_dba::get_db($this->memoria['base']);
		} else {
			throw new toba_error("INFO_INSTANCIA: El archivo de inicializacion de la INSTANCIA: '".self::$id."' no posee una BASE DEFINIDA");
		}
	}
	
	function get_schema_db()
	{
		$parametros = toba_dba::get_parametros_base($this->memoria['base']);
		if (isset($parametros['schema'])) {
			return $parametros['schema'];
		}
	}
	
	function get_schema_logs_toba()
	{
		return $this->get_schema_db(). '_logs';
	}
	
	/**
	 * @return toba_modelo_instancia
	 */
	protected function get_modelo_instancia()
	{
		$catalogo = toba_modelo_catalogo::instanciacion();
		$catalogo->set_db($this->get_db());
		return $catalogo->get_instancia($this->get_id());		
	}
	
	/**
	 * Retorna el path absoluto de un proyecto perteneciente a esta instancia
	 */
	function get_path_proyecto($proyecto)
	{
		//incluyo el archivo de informacion basica de la INSTANCIA
		if ($proyecto == 'toba') {
			return toba::instalacion()->get_path();	
		} elseif (isset($this->memoria[$proyecto]['path'])) {
			$path = $this->memoria[$proyecto]['path'];
			if (substr($path, 0, 1) == '.') {
				return realpath(toba_dir().'/'.$this->memoria[$proyecto]['path']);
			} else {
				return $this->memoria[$proyecto]['path'];
			}
		} else {
			return toba_dir() . "/proyectos/" . $proyecto;
		}
	}

	function get_path_proyecto_pers($proyecto)
	{
		$path_proyecto = $this->get_path_proyecto($proyecto);
		return $path_proyecto . '/' . toba_personalizacion::dir_personalizacion;
	}
	
	function get_path_ini()
	{
		$id_instancia = toba::instancia()->get_id();
		return toba::nucleo()->toba_instalacion_dir(). '/'.toba_modelo_instancia::dir_prefijo.$id_instancia . '/'. toba_modelo_instancia::toba_instancia;
	}
	
	static function get_path_instalacion_proyecto($proyecto)
	{
		$id_instancia = toba::instancia()->get_id();
		return toba::nucleo()->toba_instalacion_dir() . '/' . toba_modelo_instancia::dir_prefijo. $id_instancia . '/' . toba_modelo_instancia::prefijo_dir_proyecto . $proyecto;	
	}
	
	function get_directiva_compilacion($proyecto)
	{
		if (isset($this->memoria[$proyecto]['metadatos_compilados'])) {
			return $this->memoria[$proyecto]['metadatos_compilados'];
		}
		return null;
	}
	
	function get_largo_minimo_password()
	{
		if (isset($this->memoria['pwd_largo_minimo'])) {
			return $this->memoria['pwd_largo_minimo'];
		} else {
			return apex_pa_pwd_largo_minimo;
		}
	}
	
	function get_parametro_seccion_proyecto($proyecto, $parametro) 
	{
		if (isset($this->memoria[$proyecto][$parametro])) {
			return $this->memoria[$proyecto][$parametro];
		}
		return null;
	}
	
	//----------------------------------------------------------------
	// DATOS
	//----------------------------------------------------------------

	//------------------------- LOG aplicacion -------------------------------------

	function get_id_solicitud()
	{
		if (! isset($this->id_solicitud)) {
			$sql = "SELECT nextval('{$this->get_schema_logs_toba()}.apex_solicitud_seq'::text) as id;";	
			$rs = $this->get_db()->consultar($sql);
			if (empty($rs)) {
				throw new toba_error('No es posible generar un ID para la solicitud');
			}
			$this->id_solicitud = $rs[0]['id'];
		}
		return $this->id_solicitud;
	}

	private function existe_solicitud($id)
	{
		$sql = "SELECT  '1'  FROM {$this->get_schema_logs_toba()}.apex_solicitud WHERE solicitud = :solicitud;";
		$rs = $this->get_db()->sentencia($sql, array('solicitud' => $id));
		return (! empty($rs));
	}
	
	function registrar_solicitud($id, $proyecto, $item, $tipo_solicitud)
	{
		//Evita que se intente registrar 2 veces la misma solicitud
		if (! $this->existe_solicitud($id)) {
			$sql = "INSERT INTO {$this->get_schema_logs_toba()}.apex_solicitud (proyecto, solicitud, solicitud_tipo, item_proyecto, item)	
					VALUES (:proyecto, :solicitud, :solicitud_tipo,:item_proyecto, :item);";	

			$parametros = array(
				'proyecto' => $proyecto,
				'solicitud' => $id,
				'solicitud_tipo' => $tipo_solicitud,
				'item_proyecto' => $proyecto,
				'item' => $item
			);
			$this->get_db()->sentencia($sql, $parametros);
		}
	}

	function actualizar_solicitud_cronometro($id, $proyecto)
	{
		$tiempo = toba::cronometro()->tiempo_acumulado();		
		$sql = "UPDATE {$this->get_schema_logs_toba()}.apex_solicitud SET tiempo_respuesta = :tiempo_respuesta
				WHERE	proyecto =  :proyecto AND solicitud = :solicitud;";
		
		$parametros = array(
			'proyecto' => $proyecto,
			'solicitud' => $id,
			'tiempo_respuesta' => $tiempo						
		);
		$this->get_db()->sentencia($sql, $parametros);
	}
	
	
	function registrar_solicitud_observaciones( $proyecto, $id, $tipo, $observacion )
	{		
		$sql = "INSERT INTO {$this->get_schema_logs_toba()}.apex_solicitud_observacion (proyecto, solicitud, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, observacion)	
				VALUES (:proyecto, :solicitud, :solicitud_obs_tipo_proyecto,:solicitud_obs_tipo, :observacion);";	

		$parametros = array(
			'proyecto' => $proyecto,
			'solicitud' => $id,
			'solicitud_obs_tipo' => $tipo[0],
			'solicitud_obs_tipo_proyecto' => $tipo[1],
			'observacion' => $observacion
		);
		$this->get_db()->sentencia($sql, $parametros);		
	}

	function registrar_solicitud_browser($proyecto, $id, $sesion_proyecto, $sesion, $ip)
	{
		$sql = "INSERT INTO {$this->get_schema_logs_toba()}.apex_solicitud_browser (solicitud_proyecto, solicitud_browser, proyecto, sesion_browser, ip)	
				VALUES (:solicitud_proyecto, :solicitud_browser, :proyecto, :sesion_browser, :ip);";	

		$parametros = array(
			'solicitud_proyecto' => $proyecto,
			'solicitud_browser' => $id,
			'proyecto' => $sesion_proyecto,
			'sesion_browser' => $sesion,
			'ip' => $ip
		);
		$this->get_db()->sentencia($sql, $parametros);		
	}

	function registrar_solicitud_consola($proyecto, $id, $usuario, $llamada)
	{
		$sql = "INSERT INTO {$this->get_schema_logs_toba()}.apex_solicitud_consola (proyecto, solicitud_consola, usuario, llamada) 
				VALUES (:proyecto,:toba_solicitud_consola, :usuario, :llamada);";
		$parametros = array(
			'proyecto' => $proyecto,
			'toba_solicitud_consola' => $id,
			'usuario' => $usuario,
			'llamada' => $llamada
		);
		$this->get_db()->sentencia($sql, $parametros);		
	}

	function registrar_marca_cronometro($proyecto, $solicitud, $marca, $nivel, $texto, $tiempo)
	{
		$sql = "INSERT INTO {$this->get_schema_logs_toba()}.apex_solicitud_cronometro(proyecto, solicitud, marca, nivel_ejecucion, texto, tiempo) 
				VALUES (:proyecto, :solicitud, :marca, :nivel_ejecucion, :texto, :tiempo);";
		$parametros = array(
			'proyecto' => $proyecto,
			'solicitud' => $solicitud,
			'marca' => $marca,
			'nivel_ejecucion' => $nivel,
			'texto' => $texto,
			'tiempo' => $tiempo
		);
		$this->get_db()->sentencia($sql, $parametros);		
	}

	function registrar_solicitud_web_service($proyecto, $solicitud, $metodo, $ip)
	{
		$sql = "INSERT INTO {$this->get_schema_logs_toba()}.apex_solicitud_web_service (proyecto, solicitud, metodo, ip)
				VALUES (:proyecto, :solicitud, :metodo, :ip);"; 
		$parametros = array(
			'proyecto' => $proyecto,
			'solicitud' => $solicitud,
			'metodo' => $metodo,
			'ip' => $ip
		);		
		$this->get_db()->sentencia($sql, $parametros);		
	}
	
	
	//------------------ Relacion entre PROYECTOS --------------------------
	
	/**
	 * Retorna las urls de los proyectos actualmente incluídos en la instancia
	 */
	function get_url_proyectos($proys)
	{
		$salida = array();
		foreach ($proys as $pro) {
			$salida[$pro] = $this->get_url_proyecto($pro);
		}
		return $salida;
	}
	
	/**
	 * Retorna las url asociada a un proyecto particular de la instancia
	 */	
	function get_url_proyecto($proy)
	{
		if (isset($this->memoria[$proy]['url'])) {
			return $this->memoria[$proy]['url'];
		} elseif (toba::proyecto()->get_id() == $proy && isset($_SERVER['TOBA_PROYECTO_ALIAS'])) {
			//---Es el actual y hay una directiva en el ALIAS
			return '/'.$_SERVER['TOBA_PROYECTO_ALIAS'];
		} else {
			return '/'.$proy;
		}
	}
    
    function get_url_proyecto_pers($proy)
	{
		if (isset($this->memoria[$proy]['url_pers'])) {
			return $this->memoria[$proy]['url_pers'];
		} elseif (toba::proyecto()->get_id() == $proy && isset($_SERVER['TOBA_PROYECTO_ALIAS'])) {
			//---Es el actual y hay una directiva en el ALIAS
			return '/'.$_SERVER['TOBA_PROYECTO_ALIAS'];
		} else {
			return '/'.$proy.'_pers';
		}
	}
	/**
	 * Retorna la lista de proyectos a los cuales el usuario actual puede ingresar
	 */
	function get_proyectos_accesibles($refrescar=false)
	{
		if ($refrescar || ! isset($this->memoria['proyectos_accesibles'])) {
			$usuario = $this->get_db()->quote(toba::usuario()->get_id());
			$sql = "SELECT 		p.proyecto, 
	    						p.descripcion_corta
	    				FROM 	apex_proyecto p,
	    						apex_usuario_proyecto up
	    				WHERE 	p.proyecto = up.proyecto
						AND  	listar_multiproyecto = 1 
						AND		up.usuario = $usuario
						ORDER BY orden;";
			$this->memoria['proyectos_accesibles'] =
					 $this->get_db()->consultar($sql, toba_db_fetch_num);
		}
		return $this->memoria['proyectos_accesibles'];
	}
	
	function get_id_proyectos()
	{
		$ids = array();
		foreach (explode(',', $this->memoria['proyectos']) as $id) {
			$ids[] = trim($id);
		}
		return $ids;
	}

	//--------------------------------------------------------------------------
	//-------------------- LOGIN USUARIOS --------------------------------------------
	//--------------------------------------------------------------------------

	/**
	 * Retorna la información cruda de un usuario, tal como está en la base de datos
	 * Para hacer preguntas del usuario actual utilizar toba::usuario()->
	 *
	 * @see toba_usuario
	 */
	function get_info_usuario($usuario)
	{
		$rs = array();
		if (trim($usuario) != '') {$rs = $this->get_info_usuarios(array($usuario));}
		if(empty($rs)){
			throw new toba_error("El usuario $usuario no existe.");
		}
		return $rs[0];
	}
	
	/**
	 * Retorna la información cruda de un grupo de usuarios, tal como está en la base de datos
	 * @see toba_usuario
	 */
	function get_info_usuarios($usuarios)
	{
		$rs = array();
		if (! empty($usuarios)) {
			$usuario_list = $this->get_db()->quote($usuarios);
			$sql = 'SELECT	usuario as							id,
							nombre as							nombre,
							email as							email,
							parametro_a as						parametro_a,
							parametro_b as 						parametro_b,
							parametro_c as						parametro_c
					FROM 	apex_usuario u
					WHERE	usuario IN ('. implode(',' , $usuario_list) . ')';
			$rs = $this->get_db()->consultar($sql);
		}
		return $rs;
	}	

	function get_info_autenticacion($usuario)
	{
		try {
			$sql = 'SELECT clave, 
						  autentificacion, 
						  CASE WHEN (vencimiento IS NOT NULL) THEN
							(now()::date > vencimiento::date)::integer		/*Verifico si la clave esta vencida*/
						  ELSE
						         0
						  END  as clave_vencida,
						  forzar_cambio_pwd
				     FROM apex_usuario 
			              WHERE usuario = :usuario';
			$id = $this->get_db()->sentencia_preparar($sql);
			$rs = $this->get_db()->sentencia_consultar($id, array('usuario'=>$usuario));
			if(!empty($rs))	return $rs[0];
		} catch (toba_error_db $e ) {
			toba::logger()->info($e->getMessage());
			throw new toba_error('Error recuperando información');
		}
	}

	function get_pregunta_secreta($usuario)
	{
		try {
			$sql = 'SELECT pregunta, respuesta FROM apex_usuario_pregunta_secreta WHERE activa = 1 AND usuario = :usuario';
			$id = $this->get_db()->sentencia_preparar($sql);
			$rs = $this->get_db()->sentencia_consultar($id, array('usuario' => $usuario));
			if (!empty($rs)) return $rs[0];
		} catch (toba_error_db $e) {
			toba::logger()->info($e->getMessage());
			throw new toba_error('Error recuperando información');
		}
	}
			
	function get_lista_claves_usadas($usuario, $periodo_tiempo=null, $no_repetidas=null)
	{	
		$params = array('usuario' => $usuario);		
		$sql = 'SELECT clave, algoritmo FROM apex_usuario_pwd_usados WHERE usuario = :usuario '; 		
		if (! is_null($periodo_tiempo)) {
			$sql .= ' AND (current_date - fecha_cambio)::integer <= :periodo';			
			$params['periodo'] = $periodo_tiempo;
		}
		if (! is_null($no_repetidas)) {
			$sql .= ' ORDER BY fecha_cambio DESC  LIMIT ' . $no_repetidas;
		}
		try {
			$id = $this->get_db()->sentencia_preparar($sql);
			$rs = $this->get_db()->sentencia_consultar($id, $params);
			return $rs; 
		} catch (toba_error_db $e) {
			toba::logger()->info($e->getMessage());
			throw new toba_error('Error recuperando información');
		}
	}
	
	/**
	 * @ignore
	 * @param string $usuario
	 * @param string $proyecto
	 * @return array
	 */
	function get_datos_perfiles_funcionales_usuario_proyecto($usuario, $proyecto)
	{
		$db = $this->get_db();
		$usuario = $db->quote($usuario);
		$proyecto_quote = $db->quote($proyecto);
		$sql = "SELECT	up.usuario_grupo_acc as  grupo_acceso,
						(SELECT COUNT(*) FROM apex_usuario_grupo_acc_miembros mie WHERE mie.usuario_grupo_acc = up.usuario_grupo_acc) as cant_membresias,
						ga.nombre
				FROM 	apex_usuario_proyecto up,
						apex_usuario_grupo_acc ga
				WHERE	up.usuario_grupo_acc = ga.usuario_grupo_acc
				AND		up.proyecto = ga.proyecto
				AND		up.usuario = $usuario
				AND		up.proyecto = $proyecto_quote
				ORDER BY ga.usuario_grupo_acc ASC;";
		return $db->consultar($sql);
	}
		
	/**
	*	Retorna los perfiles funcionales que tiene asociado un usuario a un proyecto
	*	@return $value	Retorna un array de grupos de acceso
	*/	
	function get_perfiles_funcionales($usuario, $proyecto) 
	{
		$datos = $this->get_datos_perfiles_funcionales_usuario_proyecto($usuario, $proyecto);
		if($datos){
			$grupos = array();
			foreach($datos as $dato) {
				 $grupo = $dato['grupo_acceso'];
				 $grupos[] = $grupo;				 					 
				 if ($dato['cant_membresias'] > 0) {
				 	$grupos = array_merge($grupos, toba::proyecto()->get_perfiles_funcionales_asociados($grupo));
				 }
			}
			$grupos = array_unique($grupos);
			return $grupos;
		} else {
			return array();
		}		
	}
	
	
	
	/**
	* @deprecated Usar get_perfiles_funcionales
	*/
	function get_grupos_acceso($usuario, $proyecto)
	{
		return $this->get_perfiles_funcionales($usuario, $proyecto);
	}
	
	/**
	*	Utilizada en el login automatico
	*/
	function get_lista_usuarios($proyecto=null)
	{
		if (! isset($proyecto)) {
			$proyecto = toba_proyecto::get_id();
		}
		$proyecto = $this->get_db()->quote($proyecto);
		$sql = "SELECT 	DISTINCT 
						u.usuario as usuario, 
						u.nombre as nombre
				FROM 	apex_usuario u, apex_usuario_proyecto p
				WHERE 	u.usuario = p.usuario
				AND		p.proyecto = $proyecto
				ORDER BY 1;";
		return $this->get_db()->consultar($sql);	
	}

	//-------------------- Bloqueo de IPs en LOGIN  ----------------------------

	function es_ip_rechazada($ip)
	{
		$sql = "SELECT '1' FROM {$this->get_schema_logs_toba()}.apex_log_ip_rechazada WHERE ip = :ip";
		$id = $this->get_db()->sentencia_preparar($sql);
		$rs = $this->get_db()->sentencia_consultar($id, array('ip'=>$ip));
		if ( empty($rs)) {
			return false;
		}
		return true;
	}
	
	function registrar_error_login($usuario, $ip, $texto)
	{
		$sql = "INSERT INTO {$this->get_schema_logs_toba()}.apex_log_error_login(usuario,clave,ip,gravedad,mensaje) VALUES ( :usuario, NULL, :ip,'1',:texto)";
		try {
			$id = $this->get_db()->sentencia_preparar($sql);
			$this->get_db()->sentencia_ejecutar($id, array('usuario'=>$usuario,'ip'=>$ip,'texto'=>$texto));
		} catch ( toba_error_db $e) {
			throw new toba_error('Error');
		}
	}

	function bloquear_ip($ip)
	{
		try {
			$sql = "INSERT INTO {$this->get_schema_logs_toba()}.apex_log_ip_rechazada (ip) VALUES (:ip)";
			$id = $this->get_db()->sentencia_preparar($sql);
			$this->get_db()->sentencia_ejecutar($id, array('ip'=>$ip));
		} catch ( toba_error $e ) {
			//La ip ya esta rechazada	
		}
	}
	
	function get_cantidad_intentos_en_ventana_temporal($ip, $ventana_temporal=null)
	{
		$sql = "SELECT count(*) as total FROM {$this->get_schema_logs_toba()}.apex_log_error_login WHERE ip = :ip AND (gravedad > 0)";
		$parametros['ip'] = $ip;
		if (isset($ventana_temporal)) {
			$sql .= " AND ((now()-momento) < :ventana_temporal)";
			$parametros['ventana_temporal'] = $ventana_temporal . ' min';
		}
		try {
			$id = $this->get_db()->sentencia_preparar($sql);
			$rs = $this->get_db()->sentencia_consultar($id, $parametros);
			return $rs[0]['total'];
		} catch ( toba_error_db $e) {
			throw new toba_error('Error!');
		}
	}
	
	//-------------------- Bloqueo de Usuarios en LOGIN  ----------------------------
	
	
	function get_cantidad_intentos_usuario_en_ventana_temporal($usuario, $ventana_temporal=null)
	{
		$sql = "SELECT count(*) as total FROM {$this->get_schema_logs_toba()}.apex_log_error_login WHERE usuario = :usuario AND (gravedad > 0)";
		$parametros['usuario'] = $usuario;
		if (isset($ventana_temporal)) {
			$sql .= " AND ((now()-momento) < :ventana_temporal)";
			$parametros['ventana_temporal'] = $ventana_temporal . ' min';
		}
		try {
			$id = $this->get_db()->sentencia_preparar($sql);
			$rs = $this->get_db()->sentencia_consultar($id, $parametros);
			return $rs[0]['total'];
		} catch ( toba_error_db $e) {
			throw new toba_error('Error!');
		}
	}
	
	function bloquear_usuario($usuario)
	{
		try {
			$sql = "UPDATE apex_usuario SET bloqueado = 1 WHERE usuario = :usuario";
			$id = $this->get_db()->sentencia_preparar($sql);
			$this->get_db()->sentencia_ejecutar($id, array('usuario'=>$usuario));
		} catch ( toba_error $e ) {
			//el usuario ya esta bloqueado
		}
	}
	
	function es_usuario_bloqueado($usuario)
	{
		$sql = "SELECT '1' FROM apex_usuario WHERE usuario = :usuario AND bloqueado = 1";
		$id = $this->get_db()->sentencia_preparar($sql);
		$rs = $this->get_db()->sentencia_consultar($id, array('usuario'=>$usuario));
		if ( empty($rs)) {
			return false;
		}
		return true;
	}
	
	//--------------------------------------------------------------------------
	//------------------------- SESION -------------------------------------
	//--------------------------------------------------------------------------
	
	function get_id_sesion()
	{
		$sql = "SELECT nextval('{$this->get_schema_logs_toba()}.apex_sesion_browser_seq'::text) as id;";
		$rs = $this->get_db()->consultar($sql);
		if(empty($rs)){
			throw new toba_error("No es posible recuperar el ID de la sesion.");
		}
		return $rs[0]['id'];
	}
	
	function abrir_sesion($sesion, $usuario, $proyecto)
	{
		$sql = "INSERT INTO {$this->get_schema_logs_toba()}.apex_sesion_browser(sesion_browser, usuario, ip, proyecto, php_id) 
				VALUES (:sesion_browser, :usuario, :ip, :proyecto, :php_id);";
		$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : null;		
		$parametros = array(
			'sesion_browser' => $sesion,
			'usuario' => $usuario,
			'ip' => $ip,
			'proyecto' => $proyecto,
			'php_id' => session_id(),
		);
		$this->get_db()->sentencia($sql, $parametros);		
	}
	
	function cerrar_sesion($sesion, $observaciones = null)
	{
		$db = $this->get_db();
		$sesion = $db->quote($sesion);
		if (isset($observaciones)){
			$observaciones = $db->quote($observaciones);
			$sql = "UPDATE {$this->get_schema_logs_toba()}.apex_sesion_browser SET egreso = current_timestamp, observaciones=$observaciones WHERE sesion_browser = $sesion;";
		}else{
			$sql = "UPDATE {$this->get_schema_logs_toba()}.apex_sesion_browser SET egreso = current_timestamp WHERE sesion_browser = $sesion;";
		}		
		$db->ejecutar($sql);
	}

	//--------------------------------------------------------------------------
	//------------------------ Administracion de USUARIOS
	//--------------------------------------------------------------------------

	/**
	 *	Crea un nuevo usuario en la instancia 
	 *
	 * @param string $usuario
	 * @param string $nombre
	 * @param string $clave
	 * @param array $atributos asociativo campo => valor
	 */
	function agregar_usuario( $usuario, $nombre, $clave , $atributos=array())
	{
		$this->get_modelo_instancia()->agregar_usuario($usuario, $nombre, $clave, null, $atributos);
	}
	
	function vincular_usuario( $proyecto, $usuario, $perfil_acceso, $perfil_datos=array(), $set_previsualizacion=true )
	{
		$this->get_modelo_instancia()->get_proyecto($proyecto)->vincular_usuario($usuario, $perfil_acceso, $perfil_datos, $set_previsualizacion);
	}	

	function desbloquear_usuario($usuario)
	{
		$sql = "UPDATE apex_usuario SET bloqueado = 0 WHERE usuario = :usuario";
		$id = $this->get_db()->sentencia_preparar($sql);
		$this->get_db()->sentencia_ejecutar($id, array('usuario'=>$usuario));
	}	
}
?>