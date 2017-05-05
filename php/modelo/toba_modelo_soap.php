<?php
/**
 * Clase que representa WS SOAP y su configuracion
 * @package Centrales
 * @subpackage Modelo
 */	
class toba_modelo_soap extends toba_modelo_elemento
{
	const ARCHIVO_CLIENTE = '/cliente.ini';
	const ARCHIVO_SERVER = '/servicio.ini';
	const ARCHIVO_CLAVE = '/privada.key';
	const ARCHIVO_CERT = '/publica.crt';
	const ARCHIVO_CERT_SERVER = '/cert_servidor.crt';
	
	const CARPETA_SOAP_BASE = '';
	const CARPETA_SOAP_PROVEEDOR = '/servicios_serv';
	const CARPETA_SOAP_CLIENTE = '/servicios_cli';
	const URL_BASE = '/servicios.php/';
	
	protected $proyecto;	
	protected $db;

	function __construct(toba_modelo_proyecto $proyecto)
	{		
		$this->proyecto = $proyecto;		
		$this->db = $this->proyecto->get_db();
	}
	
	static function path_clave_privada($proyecto)
	{
		return  $proyecto->get_dir_instalacion_proyecto(). self::ARCHIVO_CLAVE;	
	}
	
	static function path_clave_publica($proyecto)
	{
		return  $proyecto->get_dir_instalacion_proyecto(). self::ARCHIVO_CERT;
	}
	
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------//	
	/**
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $id_servicio
	 * @return toba_ini
	 */
	static function get_ini_server(toba_modelo_proyecto  $proyecto, $id_servicio='')
	{
		$directorio = self::get_dir_proveedor($proyecto->get_dir_instalacion_proyecto(), $id_servicio);
		$ini = new toba_ini($directorio.self::ARCHIVO_SERVER);			
		return $ini;
	}	

	/**
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $id_servicio
	 * @return toba_ini
	 */
	static function get_ini_cliente(toba_modelo_proyecto  $proyecto, $id_servicio='')
	{
		$directorio = self::get_dir_consumidor($proyecto->get_dir_instalacion_proyecto(), $id_servicio);
		$ini = new toba_ini($directorio. self::ARCHIVO_CLIENTE);			
		return $ini;
	}
	
	static function get_clave_privada(toba_modelo_proyecto $proyecto, $id_servicio='')
	{
		$directorio = self::get_dir_proveedor($proyecto->get_dir_instalacion_proyecto());
		$ini = new toba_ini($directorio.self::ARCHIVO_CLAVE);
		return $ini;
	}
	
	/**
	 * @param toba_modelo_proyecto $proyecto
	 * @return toba_ini
	 */
	static function get_ini_usuarios(toba_modelo_proyecto  $proyecto)
	{
	}
	
	//-----------------------------------------------------------------------------------------------------------------//
	/**
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $id_servicio
	 * @return boolean
	 */
	static function existe_ini_server(toba_modelo_proyecto  $proyecto, $id_servicio='')
	{
		$directorio = self::get_dir_proveedor($proyecto->get_dir_instalacion_proyecto(), $id_servicio);
		return file_exists($directorio . self::ARCHIVO_SERVER);
	}
	
	/**
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $id_servicio
	 * @return boolean
	 */
	static function existe_ini_cliente(toba_modelo_proyecto  $proyecto, $id_servicio='')
	{
		$directorio = self::get_dir_consumidor($proyecto->get_dir_instalacion_proyecto(), $id_servicio);
		return file_exists($directorio . self::ARCHIVO_CLIENTE);		
	}
	
	/**
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $id_servicio
	 * @return boolean
	 */
	static function existe_archivo_certificado(toba_modelo_proyecto $proyecto, $id_servicio='')
	{
		$directorio = $proyecto->get_dir_instalacion_proyecto();	
		return file_exists($directorio . self::ARCHIVO_CLAVE);
	}
	
	/**
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $id_servicio
	 * @return boolean
	 */
	static function existe_ini_usuarios(toba_modelo_proyecto  $proyecto, $id_servicio='')
	{
	}
	
	//-----------------------------------------------------------------------------------------------------------------//
	/**
	 * @param string $path_origen
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $id_servicio
	 */
	static function cp_ini_cliente($path_origen, toba_modelo_proyecto  $proyecto, $id_servicio='')
	{
		$directorio =  self::get_dir_consumidor($proyecto->get_dir_instalacion_proyecto(), $id_servicio);
		toba_manejador_archivos::crear_arbol_directorios($directorio);
		copy($path_origen, $directorio. self::ARCHIVO_CLIENTE);
	}

	/**
	 * @param string $path_origen
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $id_servicio
	 */
	static function cp_ini_server($path_origen, toba_modelo_proyecto  $proyecto, $id_servicio='')
	{
		$directorio = self::get_dir_proveedor($proyecto->get_dir_instalacion_proyecto(), $id_servicio);
		toba_manejador_archivos::crear_arbol_directorios($directorio);
		copy($path_origen, $directorio. self::ARCHIVO_SERVER);
	}
	
	/**
	 * @param string $path_origen
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $id_servicio
	 */	
	static function cp_ini_usuarios($path_origen, toba_modelo_proyecto  $proyecto, $id_servicio='')
	{
	}
	
	//-----------------------------------------------------------------------------------------------------------------//
	/**
	 * @param string $path
	 * @param string $id_servicio
	 * @return string
	 */
	static function get_dir_proveedor($path, $id_servicio='')
	{		
		$dir = $path . self::CARPETA_SOAP_BASE  . self::CARPETA_SOAP_PROVEEDOR;
		if (trim($id_servicio) != '') { $dir = $dir . '/' . trim($id_servicio); }				
		return $dir;															//BC
	}
	
	/**
	 * @param string $path
	 * @param string $id_servicio
	 * @return string
	 */
	static function get_dir_consumidor($path, $id_servicio='')
	{
		$dir = $path . self::CARPETA_SOAP_BASE  . self::CARPETA_SOAP_CLIENTE;
		if (trim($id_servicio) != '') { $dir = $dir . '/' . trim($id_servicio); }		
		return $dir;															//BC		
	}	
	
	//------------------------------------------------------------------------------------------------------------------//
	/**
	 * @param string $dir
	 */
	static function crear_directorio_destino($dir, $id_proyecto='')
	{
		$dir_base = $dir . self::CARPETA_SOAP_BASE;
		$dir_consumidor = $dir_base . self::CARPETA_SOAP_CLIENTE;
		$dir_proveedor = $dir_base . self::CARPETA_SOAP_PROVEEDOR;
		
		toba_manejador_archivos::crear_arbol_directorios($dir_base);
		toba_manejador_archivos::crear_arbol_directorios($dir_consumidor);
		toba_manejador_archivos::crear_arbol_directorios($dir_proveedor);
	}	
	
	//------------------------------------------------------------------------------------------------------------------//
	/**
	 * Devuelve la URL basica de la api rest para el proyecto pasado por parametro
	 * @param toba_modelo_proyecto $proyecto
	 * @return string
	 */
	static function get_url_base($proyecto)
	{
		return $proyecto->get_url() . self::URL_BASE;
	}

	/**
	 * Devuelve el path donde esta la api rest del proyecto pasado por parametro
	 * @param toba_modelo_proyecto $proyecto
	 * @return string
	 */	
	static function get_dir_api_base($proyecto)
	{
		 //return $proyecto->get_dir() .'/php'. self::CARPETA_SOAP;
	}
	
	/**
	 * Devuelve el path donde esta la extension/personalizacion de api rest del proyecto pasado por parametro
	 * @param toba_modelo_proyecto $proyecto
	 * @return string
	 */	
	static function get_dir_api_personalizacion($proyecto)
	{
		//return $proyecto->get_dir_pers().'/php' . self::CARPETA_SOAP;		
	}
	
	
	//-----------------------------------------------------------------------------------------------------------------------//
	
	/**
	 * Permite activar o desactivar un servicio web determinado
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $id_servicio
	 * @param smallint $estado 
	 */
	static function set_estado_activacion( toba_modelo_proyecto $proyecto, $id_servicio, $estado=0) 
	{
		$ini = self::get_ini_server($proyecto, $id_servicio);
		if (! $ini->existe_entrada('general', 'activo')) {
			$ini->agregar_entrada('general', array('activo' => $estado));
		} else {
			$ini->set_datos_entrada('general', array('activo' => $estado));
		}		
		$ini->guardar();
	}
	
	
	/**
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $id_servicio
	 * @return boolean
	 */
	static function esta_activo(toba_modelo_proyecto $proyecto, $id_servicio)
	{
		$activo = false;
		$ini = self::get_ini_server($proyecto, $id_servicio);
		if (isset($ini) && $ini->existe_entrada('general', 'activo')) {
			$activo = ($ini->get('general', 'activo') == '1');
		}
		return $activo;
	}	
	
	/**
	 * @param array $headers
	 * @return string
	 */
	static function generar_id_entrada_cliente($headers)
	{
		$nombre = array();
		ksort($headers);
		foreach ($headers as $id => $valor) {
			$nombre[] = $id.'='.$valor;
		}
		$nombre = implode(',', $nombre);
		return $nombre;
	}
	
	/**
	 * 
	 * @param sting $identificador
	 * @param smallint $wsa
	 * @param array $parametros_ws
	 * @throws toba_error_def
	 */
	function crear_servicio_web($identificador, $wsa, $parametros_ws)
	{
		$sqls = array();
		
		//Creo el registro para el servicio web
		$proy_id = $this->db->quote($this->proyecto->get_id());
		$ws_id = $this->db->quote($identificador);		
		$wsa = ($wsa) ? '1': '0';
		$sqls[] = "INSERT INTO apex_servicio_web (proyecto, servicio_web, param_wsa) VALUES ($proy_id, $ws_id, $wsa);";
		
		//Agrego los parametros de configuracion al servicio
		if (is_array($parametros_ws)) {
			foreach($parametros_ws as $key =>$param) {
				$clave = $this->db->quote($key);
				$valor = $this->db->quote($param);
				$sqls[] = "INSERT INTO apex_servicio_web_param (proyecto, servicio_web, parametro, valor) VALUES ($proy_id, $ws_id, $clave, $valor);";
			}
		}
		
		try {
			$this->db->abrir_transaccion();			
			$this->db->ejecutar($sqls);
			$this->db->cerrar_transaccion();
		} catch (toba_error_db $e) {
			$this->db->abortar_transaccion();
			throw new toba_error_def('Los datos provistos para la creacion del servicio no son correctos');
		}
	}
		
	/**
	 * Graba el archivo de configuracion del servicio dentro del directorio que luego sera enviado al cliente.
	 * @param array $directorio
	 * @param array $datos_cert
	 * @param string $url_sistema 
	 */
	function generar_configuracion_cliente($cert_servidor, $url_sistema, $id_servicio)
	{
		$this->crear_directorio_destino($this->proyecto->get_dir_instalacion_proyecto());
		$config = self::get_ini_cliente($this->proyecto, $id_servicio);
		if (! is_null($url_sistema)) {
			$config->agregar_entrada('conexion', array('to' => $url_sistema));
		}		
		if (! $config->existe_entrada('certificado', 'clave_cliente')) {
			$cert = array();		
			$cert['clave_cliente'] = $this->path_clave_privada();	//Se utiliza la clave de todo el proyecto
			$cert['cert_cliente'] = $this->path_clave_publica();//Se utiliza el cert de todo el proyecto
		} else {
			$cert = $config->get_datos_entrada("certificado");  //Mantiene clave y cert actuales del cliente
		}

		//Guarda el certificado del servidor
		$directorio = $this->get_dir_consumidor($this->proyecto->get_dir_instalacion_proyecto(), $id_servicio);
		copy($cert_servidor, $directorio. self::ARCHIVO_CERT_SERVER);
		$cert['cert_servidor'] = '.'. self::ARCHIVO_CERT_SERVER;
						
		$config->agregar_entrada('certificado', $cert);
		$config->guardar();
	}
	
	/**
	 * Asocia el proyecto y servicio con el nombre del archivo que contiene la clave publica
	 * @param directorio
	 * @param array $headers
	 * @param cert_cliente
	 */
	function generar_configuracion_servidor($cert_cliente, $headers = array(), $id_servicio='')
	{
		$this->crear_directorio_destino($this->proyecto->get_dir_instalacion_proyecto());
		$config = self::get_ini_server($this->proyecto, $this->get_id());
		if (! $config->existe_entrada('certificado')) {
			$cert = array();
			$cert['clave_servidor'] = $this->path_clave_privada();	//Se utiliza la clave de todo el proyecto
			$cert['cert_servidor'] =  $this->path_clave_publica();		//Se utiliza el cert de todo el proyecto
			$config->agregar_entrada('certificado', $cert);
		}
		
		//Armo ID de cliente
		$nombre = self::generar_id_entrada_cliente($headers);

		//Guarda el certificado del cliente
		$directorio = $this->get_dir_proveedor($this->proyecto->get_dir_instalacion_proyecto(), $id_servicio);
		$nombre_archivo = toba_manejador_archivos::nombre_valido(str_replace('=', '_', $nombre));
		copy($cert_cliente, $directorio."/$nombre_archivo.crt");
		
		$datos = array();
		$datos['archivo'] = "./$nombre_archivo.crt";
		$contenido_certificado = file_get_contents($datos['archivo']);
		$datos['fingerprint'] = toba_firma_digital::certificado_get_fingerprint($contenido_certificado);
		if (! $config->existe_entrada($nombre)) {
			$config->agregar_entrada($nombre, $datos);
		} else {
			$config->set_datos_entrada($nombre, $datos);
		}
		
		if (! $config->existe_entrada('general', 'activo')) {
			$config->agregar_entrada('general', array('activo' => '0'));		//Desactivo el WS por defecto
		}
		$config->guardar();
	}
	
	/**
	 * Genera el archivo de documentacion correspondiente para el servicio y devuelve su nombre
	 * @param string $carpeta_doc
	 * @param boolean $forzar_reemplazo
	 * @return string 
	 */
	function generar_documentacion($carpeta_doc, $forzar_reemplazo = false)
	{
		$prefijo = toba_http::get_nombre_servidor();
		if (is_null($prefijo)) {		
			$prefijo = 'http://localhost';
		}
		$url = $prefijo.$this->proyecto->get_url().self::URL_BASE.$this->identificador.'?wsdl2';
		
		if ($forzar_reemplazo || !file_exists($carpeta_doc.'/wsdl-viewer.xsl')) {
			copy(toba_dir(). '/php/modelo/var/wsdl-viewer.xsl', $carpeta_doc.'/wsdl-viewer.xsl');
		}
		if ($forzar_reemplazo || !file_exists($carpeta_doc.'/wsdl-viewer.css')) {		
			copy(toba_dir(). '/php/modelo/var/wsdl-viewer.css', $carpeta_doc.'/wsdl-viewer.css');
		}
		
		$include = '<?xml-stylesheet type="text/xsl" href="wsdl-viewer.xsl"?>';
		$search = '"utf-8"?>';
		$this->get_manejador_interface()->mensaje('Servicio: '.$this->identificador);	
				
		$wsdl = file_get_contents($url);
		$wsdl = str_replace($search, $search.$include, $wsdl);
		$file = $this->identificador.'.wsdl.xml';
		file_put_contents($carpeta_doc.'/'.$file, $wsdl);
		return $file;
	}
	
	function get_encabezado_doc($proyecto)
	{
		$html = "<html><head>
		<link href='wsdl-viewer.css' rel='stylesheet' type='text/css' media='screen'/>
		</head>
		<body>
		<div id='header'>
		<h1>{$proyecto->get_id()}</h1><h2>Documentación Servicios Web</h2>
		</div>
		<div id='inner_box'><div class='page'>
		<ul>";
		return $html;
	}
	
	function get_pie_doc()
	{
		return  "</ul></div></div></body></html>";
	}
}
?>