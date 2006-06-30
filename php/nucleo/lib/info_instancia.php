<?
/**
*	Datos de ACCESO y AUDITORIA necesarios para el funcionamiento del nucleo.
*/
class info_instancia
{
/*
	static private $instancia;
	
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new info_instalacion();	
		}
		return self::$instancia;	
	}

	private function __construct()
	{
		$_SESSION['toba']['instancia'] = parse_ini_file( toba_dir() . '/instalacion/instalacion.ini');
	}
	
	function limpiar_memoria()
	{
		unset($_SESSION['toba']['instancia']);
	}
*/
	//----------------------------------------------------------------
	// DATOS
	//----------------------------------------------------------------

	static function get_db()
	{
		return toba::get_db('instancia');
	}

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
		$sql = "INSERT INTO apex_sesion_browser (sesion_browser,usuario,ip,proyecto,php_id) VALUES ('$sesion','$usuario','".$_SERVER["REMOTE_ADDR"]."','$proyecto','".session_id()."');";
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

	//------------------------- LOG aplicacion -------------------------------------

	static function get_id_solicitud()
	{
		$sql = "SELECT	nextval('apex_solicitud_seq'::text) as id;";	
		$rs = self::get_db('instancia')->consultar($sql);
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
		self::get_db('instancia')->ejecutar($sql);
	}

	static function registrar_solicitud_observaciones( $id, $tipo, $observacion )
	{
		$sql = "INSERT	INTO apex_solicitud_observacion (solicitud,solicitud_obs_tipo_proyecto,solicitud_obs_tipo,observacion) 
				VALUES ('$id','{$tipo[0]}','{$tipo[1]}','".addslashes($observacion)."');";
		self::get_db('instancia')->ejecutar($sql);
	}

	static function registrar_solicitud_browser($id, $sesion, $ip)
	{
		$sql = "INSERT INTO apex_solicitud_browser (solicitud_browser, sesion_browser, ip) VALUES ('$id','$sesion','$ip');";
		self::get_db('instancia')->ejecutar($sql);
	}

	static function registrar_solicitud_consola($id, $usuario, $llamada)
	{
		$sql = "INSERT INTO apex_solicitud_consola (solicitud_consola, usuario, llamada) VALUES ('$id','$usuario','$llamada');";
		self::get_db('instancia')->ejecutar($sql);
	}

	static function log_sistema($tipo,$mensaje)
	{
		$mensaje = addslashes($mensaje);
		$sql = "INSERT INTO apex_log_sistema(usuario,log_sistema_tipo,observaciones) VALUES ('". $usuario . "','$tipo','$mensaje')";
		self::get_db('instancia')->ejecutar($sql);
	}

	//-------------------- Bloqueo de IPs en LOGIN  ----------------------------

	static function control_ip_rechazada()
	{
		$sql = "SELECT '1' FROM apex_log_ip_rechazada WHERE ip='{$_SERVER["REMOTE_ADDR"]}'";
		$rs = self::get_db()->consultar($sql);
		if (! empty($rs)) {
			return array(0,"Ha sido bloqueado el acceso desde la maquina '{$_SERVER["REMOTE_ADDR"]}'. Por favor cont�ctese con el <a href='mailto:".apex_pa_administrador."'>Administrador</a>.");
		}		
	}
	
	static function registrar_error_login()
	{
		global $ADODB_FETCH_MODE, $db;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if($gravedad>0){
			$sql = "INSERT INTO apex_log_error_login(usuario,clave,ip,gravedad,mensaje) 
					VALUES ('".$this->usuario."','".$this->clave."','".$_SERVER["REMOTE_ADDR"]."','$gravedad','$texto')";
			$rs	= $db['instancia'][apex_db_con]->Execute($sql);
		}
		$sql = "SELECT count(*) as total FROM apex_log_error_login WHERE ip='{$_SERVER["REMOTE_ADDR"]}' AND (gravedad > 0) AND ((now()-momento) < '" . apex_pa_validacion_ventana_intentos . " min')";
		$rs	  = $db['instancia'][apex_db_con]->Execute($sql);
		if ($rs->fields[0] < apex_pa_validacion_intentos){
			return array (false,$texto." Quedan " . (apex_pa_validacion_intentos - $rs->fields[0]) . " intentos antes de bloquear la IP.");
		}else{//Se supero la cantidad de intentos
			self::bloquear_ip();
			return array (false,$texto. " La IP {$_SERVER["REMOTE_ADDR"]} ha sido bloqueada. Por favor cont�ctese con el Administrador.");
		}			
	}

	static function bloquear_ip()
	{
		global $db;
		$sql = "INSERT INTO apex_log_ip_rechazada(ip) VALUES ('".$_SERVER["REMOTE_ADDR"]."')";
		$db['instancia'][apex_db_con]->execute($sql);
	}
}
?>