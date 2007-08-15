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
		$archivo = toba_dir() . '/instalacion/i__' . $id_instancia . '/instancia.ini';
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
			if (defined('apex_pa_instancia')) {
				self::$id = apex_pa_instancia;
			} elseif (isset($_SERVER['TOBA_INSTANCIA'])) {
				self::$id = $_SERVER['TOBA_INSTANCIA'];
			} else {
				throw new toba_error("INFO_INSTANCIA: La INSTANCIA ACTUAL no se encuentra definida (no exite la variable de entorno TOBA_INSTANCIA ni la constante 'apex_pa_instancia')");
			}
		}		
		return self::$id;
	}
	
	/**
	 * Retorna un vinculo a la base de datos que forma parte de la instancia
	 * @return toba_db
	 */
	function get_db()
	{
		if ( isset( $this->memoria['base'] ) ) {
			return toba_dba::get_db($this->memoria['base']);
		} else {
			throw new toba_error("INFO_INSTANCIA: El archivo de inicializacion de la INSTANCIA: '".self::$id."' no posee una BASE DEFINIDA");
		}
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

	//----------------------------------------------------------------
	// DATOS
	//----------------------------------------------------------------

	//------------------------- SESION -------------------------------------
	
	function get_id_sesion()
	{
		$sql = "SELECT nextval('apex_sesion_browser_seq'::text) as id;";
		$rs = $this->get_db()->consultar($sql);
		if(empty($rs)){
			throw new toba_error("No es posible recuperar el ID de la sesion.");
		}
		return $rs[0]['id'];
	}
	
	function abrir_sesion($sesion, $usuario, $proyecto)
	{
		$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : null;
		$sql = "INSERT INTO apex_sesion_browser (sesion_browser,usuario,ip,proyecto,php_id) VALUES ('$sesion','$usuario','".$ip."','$proyecto','".session_id()."');";
		$this->get_db()->ejecutar($sql);
	}
	
	function cerrar_sesion($sesion, $observaciones = null)
	{
		if(isset($observaciones)){
			$sql = "UPDATE apex_sesion_browser SET egreso = current_timestamp, observaciones='$observaciones' WHERE sesion_browser = '$sesion';";
		}else{
			$sql = "UPDATE apex_sesion_browser SET egreso = current_timestamp WHERE sesion_browser = '$sesion';";
		}		
		$this->get_db()->ejecutar($sql);
	}

	//---------------------- LOGIN USUARIOS -------------------------------------
	
	/**
	 * Retorna la información cruda de un usuario, tal como está en la base de datos
	 * Para hacer preguntas del usuario actual utilizar toba::usuario()->
	 *
	 * @see toba_usuario
	 */
	function get_info_usuario($usuario)
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
		$rs = $this->get_db()->consultar($sql);
		if(empty($rs)){
			throw new toba_error("El usuario '$usuario' no existe.");
		}
		return $rs[0];
	}

	function get_info_autenticacion($usuario)
	{
		$sql = "SELECT clave, autentificacion FROM apex_usuario WHERE usuario='$usuario'";
		$rs = $this->get_db()->consultar($sql);
		if(empty($rs)){
			//throw new toba_error("El usuario '$usuario' no existe.");
			return array();
		}
		return $rs[0];
	}
	
	/**
	*	Devuelve los grupos de acceso de un usuario para un proyecto
	*/
	function get_grupos_acceso($usuario, $proyecto)
	{
		$sql = "SELECT	up.usuario_grupo_acc as 				grupo_acceso
				FROM 	apex_usuario_proyecto up,
						apex_usuario_grupo_acc ga
				WHERE	up.usuario_grupo_acc = ga.usuario_grupo_acc
				AND		up.proyecto = ga.proyecto
				AND		up.usuario = '$usuario'
				AND		up.proyecto = '$proyecto';";
		$datos = $this->get_db()->consultar($sql);
		if($datos){
			$grupos = array();
			foreach($datos as $dato) {
				$grupos[] = $dato['grupo_acceso'];
			}
			return $grupos;
		} else {
			return array();
		}
	}
	
	/**
	*	Utilizada en el login automatico
	*/
	function get_lista_usuarios()
	{
		$sql = "SELECT 	DISTINCT 
						u.usuario as usuario, 
						u.nombre as nombre
				FROM 	apex_usuario u, apex_usuario_proyecto p
				WHERE 	u.usuario = p.usuario
				AND		p.proyecto = '".toba_proyecto::get_id()."'
				ORDER BY 1;";
		return $this->get_db()->consultar($sql);	
	}

	//------------------------- LOG aplicacion -------------------------------------

	function get_id_solicitud()
	{
		$sql = "SELECT	nextval('apex_solicitud_seq'::text) as id;";	
		$rs = $this->get_db()->consultar($sql);
		if (empty($rs)) {
			throw new toba_error('No es posible generar un ID para la solicitud');
		}
		return $rs[0]['id'];
	}

	function registrar_solicitud($id, $proyecto, $item, $tipo_solicitud)
	{
		$tiempo = toba::cronometro()->tiempo_acumulado();
		$sql = "INSERT	INTO apex_solicitud (proyecto, solicitud, solicitud_tipo, item_proyecto, item, tiempo_respuesta)	
				VALUES ('$proyecto','$id','$tipo_solicitud','$proyecto','$item','$tiempo');";	
		$this->get_db()->ejecutar($sql);
	}

	function registrar_solicitud_observaciones( $proyecto, $id, $tipo, $observacion )
	{
		$sql = "INSERT	INTO apex_solicitud_observacion (proyecto, solicitud, solicitud_obs_tipo_proyecto, solicitud_obs_tipo, observacion) 
				VALUES ('$proyecto', '$id','{$tipo[0]}','{$tipo[1]}','".addslashes($observacion)."');";
		$this->get_db()->ejecutar($sql);
	}

	function registrar_solicitud_browser($proyecto, $id, $sesion_proyecto, $sesion, $ip)
	{
		$sql = "INSERT INTO apex_solicitud_browser (solicitud_proyecto, solicitud_browser, proyecto, sesion_browser, ip) VALUES ('$proyecto','$id','$sesion_proyecto','$sesion','$ip');";
		$this->get_db()->ejecutar($sql);
	}

	function registrar_solicitud_consola($proyecto, $id, $usuario, $llamada)
	{
		$sql = "INSERT INTO apex_toba_solicitud_consola (proyecto, toba_solicitud_consola, usuario, llamada) VALUES ('$proyecto','$id','$usuario','$llamada');";
		$this->get_db()->ejecutar($sql);
	}

	function registrar_marca_cronometro($proyecto, $solicitud, $marca, $nivel, $texto, $tiempo)
	{
		$sql = "INSERT INTO apex_solicitud_cronometro(proyecto, solicitud, marca, nivel_ejecucion, texto, tiempo) VALUES ('$proyecto','$solicitud','$marca','$nivel','$texto','$tiempo');";
		$this->get_db()->ejecutar($sql);
	}

	function log_sistema($tipo,$mensaje)
	{
		$sql = "INSERT INTO apex_log_sistema(usuario,log_sistema_tipo,observaciones) VALUES ('". $usuario . "','$tipo','".addslashes($mensaje)."')";
		$this->get_db()->ejecutar($sql);
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

	/**
	 * Retorna la lista de proyectos a los cuales el usuario actual puede ingresar
	 */
	function get_proyectos_accesibles($refrescar=false)
	{
		if ($refrescar || ! isset($this->memoria['proyectos_accesibles'])) {
			$usuario = toba::usuario()->get_id();
			$sql = "SELECT 		p.proyecto, 
	    						p.descripcion_corta
	    				FROM 	apex_proyecto p,
	    						apex_usuario_proyecto up
	    				WHERE 	p.proyecto = up.proyecto
						AND  	listar_multiproyecto = 1 
						AND		up.usuario = '$usuario'
						ORDER BY orden;";
			$this->memoria['proyectos_accesibles'] =
					 $this->get_db()->consultar($sql, toba_db_fetch_num);
		}
		return $this->memoria['proyectos_accesibles'];
	}

	//-------------------- Bloqueo de IPs en LOGIN  ----------------------------

	function es_ip_rechazada($ip)
	{
		$sql = "SELECT '1' FROM apex_log_ip_rechazada WHERE ip='$ip'";
		$rs = $this->get_db()->consultar($sql);
		if ( empty($rs)) {
			return false;
		}
		return true;
	}
	
	function registrar_error_login($usuario, $ip, $texto)
	{
		$texto = addslashes($texto);
		$sql = "INSERT INTO apex_log_error_login(usuario,clave,ip,gravedad,mensaje) 
				VALUES ('$usuario',NULL,'$ip','1','$texto')";
		$this->get_db()->ejecutar($sql);
	}

	function get_cantidad_intentos_en_ventana_temporal($ip, $ventana_temporal=null)
	{
		$sql = "SELECT count(*) as total FROM apex_log_error_login WHERE ip='$ip' AND (gravedad > 0)";
		if (isset($ventana_temporal)) {
			$sql .= " AND ((now()-momento) < '$ventana_temporal min')";
		}
		$rs = $this->get_db()->consultar($sql);
		return $rs[0]['total'];
	}
	
	function bloquear_ip($ip)
	{
		try {
			$sql = "INSERT INTO apex_log_ip_rechazada (ip) VALUES ('$ip')";
			$this->get_db()->ejecutar($sql);
		} catch ( toba_error $e ) {
			//La ip ya esta rechazada	
		}
	}
}
?>