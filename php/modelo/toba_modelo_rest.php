<?php
	
use SIUToba\SSLCertUtils\SSLCertUtils;
/**
 * Clase que representa  WS RESTy su configuracion
 * @package Centrales
 * @subpackage Modelo
 */
class toba_modelo_rest extends toba_modelo_elemento
{
	const CARPETA_REST_BASE = '/rest';
	const CARPETA_REST_PROVEEDOR = '/api_servidor';
	const CARPETA_REST_CLIENTE = '/api_cliente';
	
	const ARCHIVO_CLIENTE = '/cliente.ini';
	const ARCHIVO_USUARIOS = '/servidor_usuarios.ini';
	const ARCHIVO_SERVER = '/servidor.ini';
	
	const CARPETA_REST =  '/rest';	
	protected $proyecto;	
	protected $db;

	function __construct(toba_modelo_proyecto $proyecto)
	{		
		$this->proyecto = $proyecto;		
		$this->db = $this->proyecto->get_db();
	}

	/**
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $nombre_api
	 * @return toba_ini
	 */
	static function get_ini_server(toba_modelo_proyecto  $proyecto, $nombre_api='')
	{
		$directorio = self::get_dir_proveedor($proyecto->get_dir_instalacion_proyecto(), $nombre_api);
		$ini = new toba_ini($directorio.self::ARCHIVO_SERVER);			
		return $ini;
	}

	/**
	 * @param toba_modelo_proyecto $proyecto
	 * @return toba_ini
	 */
	static function get_ini_usuarios(toba_modelo_proyecto  $proyecto)
	{
		$directorio = self::get_dir_proveedor($proyecto->get_dir_instalacion_proyecto());
		$ini = new toba_ini($directorio.self::ARCHIVO_USUARIOS);
		return $ini;
	}


	/**
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $nombre_api
	 * @return toba_ini
	 */
	static function get_ini_cliente(toba_modelo_proyecto  $proyecto, $nombre_api='')
	{
		$directorio = self::get_dir_consumidor($proyecto->get_dir_instalacion_proyecto(), $nombre_api);
		$ini = new toba_ini($directorio. self::ARCHIVO_CLIENTE);			
		return $ini;
	}
	
	//-----------------------------------------------------------------------------------------------------------------//
	/**
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $nombre_api
	 * @return boolean
	 */
	static function existe_ini_server(toba_modelo_proyecto  $proyecto, $nombre_api='')
	{
		$directorio = self::get_dir_proveedor($proyecto->get_dir_instalacion_proyecto(), $nombre_api);
		return file_exists($directorio . self::ARCHIVO_SERVER);
	}
	
	/**
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $nombre_api
	 * @return boolean
	 */
	static function existe_ini_usuarios(toba_modelo_proyecto  $proyecto, $nombre_api='')
	{
		$directorio = self::get_dir_proveedor($proyecto->get_dir_instalacion_proyecto());
		return file_exists($directorio . self::ARCHIVO_USUARIOS);
	}
	
	/**
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $nombre_api
	 * @return boolean
	 */
	static function existe_ini_cliente(toba_modelo_proyecto  $proyecto, $nombre_api='')
	{
		$directorio = self::get_dir_consumidor($proyecto->get_dir_instalacion_proyecto(), $nombre_api);
		return file_exists($directorio . self::ARCHIVO_CLIENTE);		
	}
	
	//-----------------------------------------------------------------------------------------------------------------//
	/**
	 * @param string $path_origen
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $nombre_api
	 */
	static function cp_ini_cliente($path_origen, toba_modelo_proyecto  $proyecto, $nombre_api='')
	{
		$directorio =  self::get_dir_consumidor($proyecto->get_dir_instalacion_proyecto(), $nombre_api);
		toba_manejador_archivos::crear_arbol_directorios($directorio);
		copy($path_origen, $directorio. self::ARCHIVO_CLIENTE);
	}

	/**
	 * @param string $path_origen
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $nombre_api
	 */
	static function cp_ini_server($path_origen, toba_modelo_proyecto  $proyecto, $nombre_api='')
	{
		$directorio = self::get_dir_proveedor($proyecto->get_dir_instalacion_proyecto(), $nombre_api);
		toba_manejador_archivos::crear_arbol_directorios($directorio);
		copy($path_origen, $directorio. self::ARCHIVO_SERVER);
	}
	
	/**
	 * @param string $path_origen
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $nombre_api
	 */	
	static function cp_ini_usuarios($path_origen, toba_modelo_proyecto  $proyecto, $nombre_api='')
	{
		$directorio = self::get_dir_proveedor($proyecto->get_dir_instalacion_proyecto());
		copy($path_origen, $directorio. self::ARCHIVO_USUARIOS);
	}
	
	//-----------------------------------------------------------------------------------------------------------------//
	/**
	 * @param string $path
	 * @param string $nombre_api
	 * @return string
	 */
	static function get_dir_proveedor($path, $nombre_api='')
	{		
		$dir = $path . self::CARPETA_REST_BASE;
		if (file_exists($path . self::CARPETA_REST_PROVEEDOR)) {							//Nueva estructura de directorios a partir de 2.8
			$dir = $path . self::CARPETA_REST_PROVEEDOR;				
			if (trim($nombre_api) != '') { $dir = $dir . '/' . trim($nombre_api); }				
		}
		return $dir;															//BC
	}
	
	/**
	 * @param string $path
	 * @param string $nombre_api
	 * @return string
	 */
	static function get_dir_consumidor($path, $nombre_api='')
	{
		$dir = $path . self::CARPETA_REST_BASE;
		if (file_exists($path . self::CARPETA_REST_CLIENTE)) {							//Nueva estructura de directorios a partir de 2.8
			$dir = $path . self::CARPETA_REST_CLIENTE;				
		}
		if (trim($nombre_api) != '') { $dir = $dir . '/' . trim($nombre_api); }						
		
		return $dir;															//BC		
	}	
	
	//------------------------------------------------------------------------------------------------------------------//
	/**
	 * @param string $dir
	 */
	static function crear_directorio_destino($dir, $id_proyecto='')
	{
		$dir_base = $dir . self::CARPETA_REST_BASE;
		/*$dir_consumidor = $dir . self::CARPETA_REST_CLIENTE;
		$dir_proveedor = $dir . self::CARPETA_REST_PROVEEDOR;*/
		
		toba_manejador_archivos::crear_arbol_directorios($dir_base);
		/*toba_manejador_archivos::crear_arbol_directorios($dir_consumidor);
		toba_manejador_archivos::crear_arbol_directorios($dir_proveedor);*/
	}	
	
	static function inicializar_archivos_config_servidor($proyecto, $id_proyecto)
	{
		if (! self::existe_ini_server($proyecto, $id_proyecto)) {
			self::cp_ini_server(toba_dir(). '/php/modelo/var/rest_servidor.ini', $proyecto, $id_proyecto);
		}		
		if (! self::existe_ini_usuarios($proyecto, $id_proyecto)) {
			self::cp_ini_usuarios(toba_dir(). '/php/modelo/var/rest_servidor_usuarios.ini', $proyecto, $id_proyecto);
		}
	}
	
	static function inicializar_archivos_config_cliente($proyecto, $id_servicio)
	{
		if (! self::existe_ini_cliente($proyecto, $id_servicio)) {
			self::cp_ini_cliente(toba_dir(). '/php/modelo/var/rest_cliente.ini', $proyecto, $id_servicio);
		}
	}
	
	//------------------------------------------------------------------------------------------------------------------//
	/**
	 * Devuelve la URL basica de la api rest para el proyecto pasado por parametro
	 * @param toba_modelo_proyecto $proyecto
	 * @return string
	 */
	static function get_url_base($proyecto)
	{
		return $proyecto->get_url() . self::CARPETA_REST;
	}

	/**
	 * Devuelve el path donde esta la api rest del proyecto pasado por parametro
	 * @param toba_modelo_proyecto $proyecto
	 * @return string
	 */	
	static function get_dir_api_base($proyecto)
	{
		 return $proyecto->get_dir() .'/php'. self::CARPETA_REST;
	}
	
	/**
	 * Devuelve el path donde esta la extension/personalizacion de api rest del proyecto pasado por parametro
	 * @param toba_modelo_proyecto $proyecto
	 * @return string
	 */	
	static function get_dir_api_personalizacion($proyecto)
	{
		return $proyecto->get_dir_pers().'/php' . self::CARPETA_REST;		
	}
		
	function generar_configuracion_cliente($id_servicio, $cert_CA, $url, $cert_cli, $key_cli, $cert_pwd, $usr, $usr_pwd)
	{		
		//Intento crear la carpeta de destino de configuraciones por si no esta.
		try {
			self::crear_directorio_destino($this->proyecto->get_dir_instalacion_proyecto());
		} catch (Exception $e) {
			$this->manejador_interface->mensaje("No se pudo crear la carpeta para las configuraciones de la api REST");
			die;
		}		
		//Intento copiar el archivo de configuracion del cliente
		try {
			self::inicializar_archivos_config_cliente($this->proyecto, $id_servicio);
		} catch (Exception $e) {
			$this->manejador_interface->mensaje("No se pudo crear la carpeta para el servicio $id_servicio en la carpeta de la instancia");
			die;
		}	
		
		$ini = self::get_ini_cliente($this->proyecto, $id_servicio);
		$datos = array();
		if ($ini->existe_entrada('conexion')) {					//Recupero los datos de la entrada o la genero vacia
			$datos = $ini->get('conexion');
		} else {
			$ini->agregar_entrada('conexion', $datos);
		}
		//Averiguo que datos debo guardar
		if (! is_null($url)) { $datos['to'] = $url; }
		if (! is_null($usr)) {
			$datos['auth_usuario'] = $usr;
			$datos['auth_password'] = $usr_pwd;
			$datos['auth_tipo'] = 'digest';
		} elseif (! is_null($cert_cli)) {
			$datos['auth_tipo'] =  'ssl';
			$datos['cert_file'] = $cert_cli;
			$datos['key_file'] = $key_cli;
			if (! is_null($cert_pwd)) {$datos['cert_pwd'] =  $cert_pwd;}
			if (! is_null($cert_CA)) { $datos['ca_cert'] = $cert_CA;}
		}
		
		$ini->set_datos_entrada('conexion', $datos);
		$ini->guardar();
	}
	
	function generar_configuracion_servidor($usr, $usr_pwd, $cert_file=null, $tipo_auth='digest' )
	{
		$id_proyecto = $this->proyecto->get_id();
		//--- Servidor (se considera el nombre del proyecto como nombre de api por defecto)
		self::inicializar_archivos_config_servidor($this->proyecto, $id_proyecto);
		
		$ini_server = self::get_ini_server($this->proyecto, $id_proyecto);
		if (! $ini_server->existe_entrada('autenticacion')) {
			$ini_server->agregar_entrada('autenticacion', $tipo_auth);
		} else {
			$ini_server->set_datos_entrada('autenticacion', $tipo_auth);
		}
		if (! $ini_server->existe_entrada('settings')) {
			$ini_server->agregar_entrada('settings', array('formato_respuesta' => 'json' , 'url_protegida' => '/.*/'));							//Pongo los parametros default
		}
		
		$ini_usr = self::get_ini_usuarios($this->proyecto);
		$datos = array();		
		if (! is_null($usr) && $tipo_auth != 'ssl') {
			$datos = array('password' => $usr_pwd);
		} elseif (! is_null($cert_file)) {
			//Hay que recuperar el CN y el Fingerprint
			$util = new SSLCertUtils();
			$util->loadCertFromFile($cert_file);
			$usr = $util->getCN();
			$datos = array('fingerprint' => $util->getFingerprint());
		}
		
		if (! is_null($usr) &&  ! empty($datos)) {
			if (! $ini_usr->existe_entrada($usr)) {
				$ini_usr->agregar_entrada($usr, $datos);
			} else {
				$data_existente = $ini_usr->get($usr);
				$ini_usr->set_datos_entrada($usr, array_merge($data_existente, $datos));
			}
		}
		$ini_server->guardar();
		$ini_usr->guardar();
	}	
}
?>
