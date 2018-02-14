<?php

/**
 * Clase que mantiene información común a la actual instalación de toba
 * Enmascara principalmente al archivo de configuración instalacion.ini
 * 
 * @package Centrales
 */
class toba_instalacion
{
	static private $instancia;
	private $memoria;								//Referencia al segmento de $_SESSION asignado
	
	/**
	 * @return toba_instalacion
	 */
	static function instancia($recargar=false)
	{
		if (!isset(self::$instancia) || $recargar) {
			self::$instancia = new toba_instalacion($recargar);	
		}
		return self::$instancia;	
	}

	static function eliminar_instancia()
	{
		self::$instancia = null;
	}

	private function __construct($recargar)
	{
		$this->memoria =& toba_manejador_sesiones::segmento_info_instalacion();
		if(!$this->memoria || $recargar) {
			$archivo = toba::nucleo()->toba_instalacion_dir() . '/instalacion.ini';
			if (file_exists($archivo)) {
					$this->memoria = parse_ini_file( toba::nucleo()->toba_instalacion_dir() . '/instalacion.ini', true);
			} else {
					throw new toba_error('No fue posible encontrar la instalacion', "El archivo instalacion.ini no se encuentra en la ruta : $archivo" );
			}
		}
	}
	
	/**
	 * Retorna el nombre de la instalacion actual, cadena vacia si no esta seteado
	 * @return string
	 */
	function get_nombre()
	{
		if (isset($this->memoria['nombre'])) {
			return $this->memoria['nombre'];
		}
		return '';
	}
	
	/**
	 * Claves utilizadas para encriptar el querystring y cosas en la base
	 * @return Arreglo asociativo db=>, get=>
	 */
	function get_claves_encriptacion()
	{
		if (isset($this->memoria)) {
			$claves['db'] = $this->memoria['clave_db'];
			$claves['get'] = $this->memoria['clave_querystring'];
			return $claves;
		}
	}
	
	/**
	* Retorna el metodo de autenticacion toba|ldap|openid
	*/
	function get_tipo_autenticacion()
	{
		if (isset($this->memoria['autenticacion'])) {
			return $this->memoria['autenticacion'];
		} else {
			return 'toba';
		}
	}	
	
		
	/**
	* Retorna el metodo de autenticacion toba|ldap|openid
	*/
	function get_session_name()
	{
		if (isset($this->memoria['session_name'])) {
			return $this->memoria['session_name'];
		} else {
			return 'TOBA_SESSID';
		}
	}	
	
	/**
	 * Retorna un número que representa al grupo de trabajo y con el cual se indexaran los metadatos
	 * Pensado para poder hacer trabajos concurrentes entre grupos de trabajo geograficamente distribuidos
	 *
	 * @return integer
	 */
	function get_id_grupo_desarrollo()
	{
		if (isset($this->memoria)) {
			return $this->memoria['id_grupo_desarrollo'];
		}		
	}
	
	/**
	 * Retorna el comando que usa la instalación para editar archivos php en forma interactiva
	 */
	function get_editor_php() 
	{
		if (isset($this->memoria['editor_php'])) {
			return $this->memoria['editor_php'];
		}
	}
	
	/**
	 * La instalación trabaja con las librerías js comprimidas?
	 */
	function es_js_comprimido()
	{
		if (isset($this->memoria['js_comprimido'])) {
			return $this->memoria['js_comprimido'];
		} else {
			return false;	
		}
	}
	
	/**
	 * La instalación es una de produccion
	 */
	function es_produccion()
	{
		if (isset($this->memoria['es_produccion'])) {
			return $this->memoria['es_produccion'];
		} else {
			return false;	
		}
	}
	
	/**
	* Retorna true si la instalación esta vinculada con Arai-Usuarios
	*/
	function vincula_arai_usuarios()
	{
		if (isset($this->memoria['vincula_arai_usuarios'])) {
			return $this->memoria['vincula_arai_usuarios'];
		} else {
			return false;	
		}
	}

	function get_fonts_path()
	{
		if (isset($this->memoria['fonts_path'])) {
			return $this->memoria['fonts_path'];
		} else {
			return false;
		}
	}
	
	function arreglo_png_ie()
	{
		if (isset($this->memoria['arreglo_png_ie'])) {
			return $this->memoria['arreglo_png_ie'];
		} else {
			return true;	
		}		
	}
	
	/**
	 * Número de versión de Toba
	 */
	function get_numero_version()
	{
		return file_get_contents(toba_dir()."/VERSION");
	}

	/**
	 * Número de versión de Toba
	 * @return toba_version
	 */
	function get_version()
	{
		return new toba_version($this->get_numero_version());
	}
	
	/**
	 * Retorna el path de la instalación de toba
	 */
	function get_path()
	{
		return toba_nucleo::toba_dir();
	}	
	
	/**
	 * Retorna la ruta a la carpeta 'instalacion'
	 */
	function get_path_carpeta_instalacion()
	{
		return toba_nucleo::toba_instalacion_dir();
	}
	
	/**
	 * Retorna la URL base del runtime toba (donde esta el js, img y demas recursos globales a todos los proyectos)
	 * @return string
	 */			
	function get_url()
	{
		if (isset($this->memoria['url'])) {
			return $this->memoria['url'];
		}		
	}
	
	function get_datos_smtp($nombre_config = null)
	{
		if (! isset($this->memoria['smtp']) && is_null($nombre_config)) {
			throw new toba_error('Debe definir la entrada "smtp" el archivo instalacion/instalacion.ini');
		}
		$path_ini_smtp = toba::nucleo()->toba_instalacion_dir().'/smtp.ini';
		if (! file_exists($path_ini_smtp)) {
			throw new toba_error("No existe el archivo '$path_ini_smtp'");
		}
		$ini = new toba_ini($path_ini_smtp);
		$conf = (is_null($nombre_config)) ? $this->memoria['smtp']: $nombre_config;
		if (! $ini->existe_entrada($conf)) {
			throw new toba_error("No existe la entrada '$conf' el archivo '$path_ini_smtp'");
		}
		return $ini->get_datos_entrada($conf);
	}
	
	/**
	 * Retorna un path donde incluir archivos temporales, el path no es navegable
	 */
	function get_path_temp()
	{
		return toba_dir()."/temp";	
	}

	/**
	 * Retorna si se debe realizar el chequeo de revisiones de metadatos desde toba_editor.
	 * Se usa el parametro 'chequea_sincro_svn' 0|1
	 * @return boolean $chequea
	 */
	function chequea_sincro_svn()
	{
		$chequea = false;
		if (isset($this->memoria['chequea_sincro_svn'])) {
			$chequea = ($this->memoria['chequea_sincro_svn'] == '1');
		}
		return $chequea;
	}

	function get_xslfo_fop()
	{
		if (isset($this->memoria['xslfo']) && isset($this->memoria['xslfo']['fop']) && $this->memoria['xslfo']['fop'] != '') {
			return $this->memoria['xslfo']['fop'];
		}
		return false;
	}
	
	/**
	 * Devuelve un arreglo con las rutas a los certificados ssl
	 * @return array
	 */
	function get_configuracion_certificados_ssl()
	{
		$conf = array();
		if (isset($this->memoria['X509'])) {
			$conf =  $this->memoria['X509'];
		}
		return $conf;
	}
}
?>
