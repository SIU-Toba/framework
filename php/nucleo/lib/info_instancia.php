<?
/**
*	Datos de ACCESO y AUDITORIA necesarios para el funcionamiento del nucleo.
*/
class info_instancia
{
	static private $instancia;
	static protected $id;
	
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new info_instancia();	
		}
		return self::$instancia;	
	}

	private function __construct()
	{
		if(!isset($_SESSION['toba']['instancia'])) {
			//incluyo el archivo de informacion basica de la INSTANCIA
			$_SESSION['toba']['instancia'] = self::get_info_instancia( self::get_id() );
		}
	}
	
	function get_info_instancia($id_instancia)
	{
		$archivo = toba_dir() . '/instalacion/i__' . $id_instancia . '/instancia.ini';
		if ( is_file( $archivo ) ) {
			return parse_ini_file( $archivo, true );
		} else {
			throw new excepcion_toba("INFO_INSTANCIA: No se encuentra definido el archivo de inicializacion de la INSTANCIA: '".self::get_id()."' ('$archivo')");
		} 	
	}
	
	/**
	 * Retorna el id de la instancia actual
	 * La configuracion puede estar cono variable de entorno del servidor o una constante del PA
	 */
	static function get_id()
	{
		if ( ! isset(self::$id)) {
			if (isset($_SERVER['TOBA_INSTANCIA'])) {
				self::$id = $_SERVER['TOBA_INSTANCIA'];
			} elseif (defined('apex_pa_instancia')) {
				self::$id = apex_pa_instancia;
			} else {
				throw new excepcion_toba("INFO_INSTANCIA: La INSTANCIA ACTUAL no se encuentra definida (no exite la variable de entorno TOBA_INSTANCIA ni la constante 'apex_pa_instancia')");
			}
		}		
		return self::$id;
	}
	
	function limpiar_memoria()
	{
		unset($_SESSION['toba']['instancia']);
		self::$instancia = null;
	}

	static function get_db()
	{
		if ( isset( $_SESSION['toba']['instancia']['base'] ) ) {
			return dba::get_db($_SESSION['toba']['instancia']['base']);
		} else {
			throw new excepcion_toba("INFO_INSTANCIA: El archivo de inicializacion de la INSTANCIA: '".self::$id."' no posee una BASE DEFINIDA");
		}
	}
	
	static function get_path_proyecto($proyecto)
	{
		//incluyo el archivo de informacion basica de la INSTANCIA
		if (isset($_SESSION['toba']['instancia'][$proyecto]['path'])) {
			return $_SESSION['toba']['instancia'][$proyecto]['path'];
		} else {
			return toba_dir() . "/proyectos/" . $proyecto;
		}
	}

	//----------------------------------------------------------------
	// DATOS
	//----------------------------------------------------------------

	//------------------------- SESION -------------------------------------
	
	static function get_id_sesion()
	{
		$sql = "SELECT nextval('apex_sesion_browser_seq'::text) as id;";
		$rs = self::get_db()->consultar($sql);
		if(empty($rs)){
			throw new excepcion_toba("No es posible recuperar el ID de la sesion.");
		}
		return $rs[0]['id'];	
	}
	
	static function abrir_sesion($sesion, $usuario, $proyecto)
	{
		$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : null;
		$sql = "INSERT INTO apex_sesion_browser (sesion_browser,usuario,ip,proyecto,php_id) VALUES ('$sesion','$usuario','".$ip."','$proyecto','".session_id()."');";
		self::get_db()->ejecutar($sql);
	}
	
	static function cerrar_sesion($sesion, $observaciones = null)
	{
		if(isset($observaciones)){
			$sql = "UPDATE apex_sesion_browser SET egreso = current_timestamp, observaciones='$observaciones' WHERE sesion_browser = '$sesion';";
		}else{
			$sql = "UPDATE apex_sesion_browser SET egreso = current_timestamp WHERE sesion_browser = '$sesion';";
		}		
		self::get_db()->ejecutar($sql);
	}

	//------------------------- USUARIOS -------------------------------------
	
	static function get_info_usuario($usuario)
	{
		$sql = "SELECT	usuario as							id,
						nombre as							nombre,
						hora_salida as						hora_salida,
						solicitud_registrar as				sol_registrar,
						solicitud_obs_tipo_proyecto as		sol_obs_tipo_proyecto,
						solicitud_obs_tipo as				sol_obs_tipo,
						solicitud_observacion as			sol_obs,
						parametro_a as						parametro_a,
						parametro_b as 						parametro_b,
						parametro_c as						parametro_c
				FROM 	apex_usuario u
				WHERE	usuario = '$usuario'";
		$rs = self::get_db()->consultar($sql);
		if(empty($rs)){
			throw new excepcion_toba("El usuario '$usuario' no existe.");
		}
		return $rs[0];
	}

	static function get_info_autenticacion($usuario)
	{
		$sql = "SELECT clave, autentificacion FROM apex_usuario WHERE usuario='$usuario'";
		$rs = self::get_db()->consultar($sql);
		if(empty($rs)){
			//throw new excepcion_toba("El usuario '$usuario' no existe.");
			return array();
		}
		return $rs[0];
	}

	static function get_lista_usuarios()
	{
		$sql = "SELECT 	u.usuario as usuario, 
						u.nombre as nombre
				FROM 	apex_usuario u, apex_usuario_proyecto p
				WHERE 	u.usuario = p.usuario
				AND		p.proyecto = '".info_proyecto::get_id()."'
				ORDER BY 1;";
		return self::get_db()->consultar($sql);	
	}

	static function get_grupo_acceso($usuario, $proyecto)
	{
		$sql = "SELECT	up.usuario_grupo_acc as 				grupo_acceso,
						up.usuario_perfil_datos as 				perfil_datos,
						ga.nivel_acceso as						nivel_acceso
				FROM 	apex_usuario_proyecto up,
						apex_usuario_grupo_acc ga
				WHERE	up.usuario_grupo_acc = ga.usuario_grupo_acc
				AND		up.proyecto = ga.proyecto
				AND		up.usuario = '$usuario'
				AND		up.proyecto = '$proyecto';";
		return self::get_db()->consultar($sql);
	}
	
	/**
	*	Devuelve la lista de items a los que el usuario puede acceder
	*/
	static function get_vinculos_posibles($usuario)
	{
		$sql = "SELECT	i.proyecto as proyecto,
						i.item as item
				FROM	apex_item i,
						apex_usuario_grupo_acc_item ui,
						apex_usuario_proyecto up
				WHERE	(i.carpeta <> 1 OR i.carpeta IS NULL)
				AND		ui.item = i.item
				AND		ui.proyecto = i.proyecto
				AND		ui.usuario_grupo_acc = up.usuario_grupo_acc
                AND     ui.proyecto = up.proyecto
                AND     up.usuario = '$usuario';";
		return self::get_db()->consultar($sql);
	}

	//------------------------- LOG aplicacion -------------------------------------

	static function get_id_solicitud()
	{
		$sql = "SELECT	nextval('apex_solicitud_seq'::text) as id;";	
		$rs = self::get_db()->consultar($sql);
		if (empty($rs)) {
			throw new excepcion_toba('No es posible generar un ID para la solicitud');
		}
		return $rs[0]['id'];
	}

	static function registrar_solicitud($id, $proyecto, $item, $tipo_solicitud)
	{
		$tiempo = toba::get_cronometro()->tiempo_acumulado();
		$sql = "INSERT	INTO apex_solicitud (proyecto, solicitud, solicitud_tipo, item_proyecto, item, tiempo_respuesta)	
				VALUES ('$proyecto','$id','$tipo_solicitud','$proyecto','$item','$tiempo');";	
		self::get_db()->ejecutar($sql);
	}

	static function registrar_solicitud_observaciones( $id, $tipo, $observacion )
	{
		$sql = "INSERT	INTO apex_solicitud_observacion (solicitud,solicitud_obs_tipo_proyecto,solicitud_obs_tipo,observacion) 
				VALUES ('$id','{$tipo[0]}','{$tipo[1]}','".addslashes($observacion)."');";
		self::get_db()->ejecutar($sql);
	}

	static function registrar_solicitud_browser($id, $sesion, $ip)
	{
		$sql = "INSERT INTO apex_solicitud_browser (solicitud_browser, sesion_browser, ip) VALUES ('$id','$sesion','$ip');";
		self::get_db()->ejecutar($sql);
	}

	static function registrar_solicitud_consola($id, $usuario, $llamada)
	{
		$sql = "INSERT INTO apex_solicitud_consola (solicitud_consola, usuario, llamada) VALUES ('$id','$usuario','$llamada');";
		self::get_db()->ejecutar($sql);
	}

	static function log_sistema($tipo,$mensaje)
	{
		$mensaje = addslashes($mensaje);
		$sql = "INSERT INTO apex_log_sistema(usuario,log_sistema_tipo,observaciones) VALUES ('". $usuario . "','$tipo','$mensaje')";
		self::get_db()->ejecutar($sql);
	}

	//-------------------- PROYECTOS  ----------------------------
	
	static function get_url_proyectos($proys)
	{
		$salida = array();
		foreach ($proys as $pro) {
			//incluyo el archivo de informacion basica de la INSTANCIA
			if (isset($_SESSION['toba']['instancia'][$pro]['url'])) {
				$salida[$pro] = $_SESSION['toba']['instancia'][$pro]['url'];
			} else {
				$salida[$pro] = '/'.$pro;
			}
		}
		return $salida;
	}
	
	static function get_proyectos_accesibles($refrescar=false)
	{
		if ($refrescar || ! isset($_SESSION['toba']['instancia']['proyectos_accesibles'])) {
			$usuario = toba::get_hilo()->obtener_usuario();
			$sql = "SELECT 		p.proyecto, 
	    						p.descripcion_corta
	    				FROM 	apex_proyecto p,
	    						apex_usuario_proyecto up
	    				WHERE 	p.proyecto = up.proyecto
						AND  	listar_multiproyecto = 1 
						AND		up.usuario = '$usuario'
						ORDER BY orden;";
			$_SESSION['toba']['instancia']['proyectos_accesibles'] =
					 self::get_db()->consultar($sql, toba_db_fetch_num);
		}
		return $_SESSION['toba']['instancia']['proyectos_accesibles'];
	}

	//-------------------- Bloqueo de IPs en LOGIN  ----------------------------

	static function es_ip_rechazada($ip)
	{
		$sql = "SELECT '1' FROM apex_log_ip_rechazada WHERE ip='$ip'";
		$rs = self::get_db()->consultar($sql);
		if ( empty($rs)) {
			return false;
		}
		return true;
	}
	
	static function registrar_error_login($usuario, $ip, $texto)
	{
		$sql = "INSERT INTO apex_log_error_login(usuario,clave,ip,gravedad,mensaje) 
				VALUES ('$usuario',NULL,'$ip','1','$texto')";
		self::get_db()->ejecutar($sql);
	}

	static function get_cantidad_intentos_en_ventana_temporal($ip, $ventana_temporal)
	{
		$sql = "SELECT count(*) as total FROM apex_log_error_login WHERE ip='$ip' AND (gravedad > 0) AND ((now()-momento) < '$ventana_temporal min')";
		$rs = self::get_db()->consultar($sql);
		return $rs[0]['total'];
	}

	static function bloquear_ip($ip)
	{
		$sql = "INSERT INTO apex_log_ip_rechazada (ip) VALUES ('$ip')";
		self::get_db()->ejecutar($sql);
	}
}
?>
