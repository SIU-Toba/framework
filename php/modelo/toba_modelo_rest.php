<?php
	
class toba_modelo_rest extends toba_modelo_elemento
{
	const CARPETA_REST_BASE = '/rest';
	const CARPETA_REST_PROVEEDOR = '/api_servidor';
	const CARPETA_REST_CLIENTE = '/api_cliente';
	
	const ARCHIVO_CLIENTE = '/cliente.ini';
	const ARCHIVO_USUARIOS = '/servidor_usuarios.ini';
	const ARCHIVO_SERVER = '/servidor.ini';
	
	protected $proyecto;	

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
		$directorio = self::get_dir_proveedor($proyecto->get_dir_instalacion_proyecto(). '/rest', $nombre_api);
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
}
?>
