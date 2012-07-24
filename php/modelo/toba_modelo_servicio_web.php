<?php

class toba_modelo_servicio_web extends toba_modelo_elemento
{
	protected $proyecto;
	protected $identificador;
	protected $wsa = false;
	protected $db;
	protected $parametros_ws= array();
	
	function __construct(toba_modelo_proyecto $proyecto, $identificador)
	{		
		$this->proyecto = $proyecto;		
		$this->identificador = $identificador;
		$this->db = $this->proyecto->get_db();
	}
	
	function get_id()
	{
		return $this->identificador;
	}	
	
	/**
	 * Permite especificar si el servicio utiliza WS addressing
	 * @param boolean $estado 
	 */
	function set_ws_addressing($estado)
	{
		$this->wsa = $estado;
	}
	
	/**
	 * Permite agregar parametros de configuracion al servicio web
	 * @param array $parametros  Arreglo asociativo clave=valor
	 */
	function agregar_parametros($parametros=array())
	{
		$this->parametros_ws = $parametros;		
	}
	
	/**
	 * Crea un nuevo servicio web en base a los datos provistos a la clase
	 * @throws toba_error_def 
	 */
	function crear_servicio_web()
	{
		$sqls = array();
		
		//Creo el registro para el servicio web
		$proy_id = $this->db->quote($this->proyecto->get_id());
		$ws_id = $this->db->quote($this->identificador);		
		$wsa = ($this->wsa) ? '1': '0';
		$sqls[] = "INSERT INTO apex_servicio_web (proyecto, servicio_web, param_wsa) VALUES ($proy_id, $ws_id, $wsa);";
		
		//Agrego los parametros de configuracion al servicio
		if (is_array($this->parametros_ws)) {
			foreach($this->parametros_ws as $key =>$param) {
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
	 * Genera los archivos para los certificados del proyecto
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $directorio
	 * @throws toba_error_usuario 
	 */
	static function generar_certificados(toba_modelo_proyecto $proyecto, $directorio = null)
	{
		//Si no se pasa directorio de salida, asigno por defecto el de instalacion del proyecto
		if (is_null($directorio)) {
			$directorio = $proyecto->get_dir_instalacion_proyecto();			
		}		
		
		//Busco en el directorio de la instalacion de Toba el archivo de configuracion para openssl
		$dir_inst = $proyecto->get_instalacion()->get_dir();		
		if (! file_exists($dir_inst.'/openssl.ini')) {
			throw new toba_error_usuario("No existe el archivo '$dir_inst/openssl.ini'. Necesita copiarlo de la carpeta toba/php/modelo/var");
		}
		
		$cmd = "openssl req -x509 -nodes -days 20000 -newkey rsa:1024 -keyout $directorio/privada.key.sign -config $dir_inst/openssl.ini -out $directorio/publica.crt";
		$exito = toba_manejador_archivos::ejecutar($cmd, $stdout, $stderr);
		if ($exito != '0') {
			$this->manejador_interface->error($stderr);
			throw new toba_error_usuario("Asegurese tener instalados los binarios de OpenSSL y disponibles en el path. Para comprobar ejecute 'openssl version'");
		}
		
		$cmd = "openssl rsa -in $directorio/privada.key.sign -out $directorio/privada.key";
		$exito = toba_manejador_archivos::ejecutar($cmd, $stdout, $stderr);
		if ($exito != '0') {
			throw new toba_error_usuario($stderr);
		}		
		unlink("$directorio/privada.key.sign");
	}
	
	/**
	 * Genera el archivo de documentacion correspondiente para el servicio y devuelve su nombre
	 * @param string $carpeta_doc
	 * @param boolean $forzar_reemplazo
	 * @return string 
	 */
	function generar_documentacion($carpeta_doc, $forzar_reemplazo = false)
	{
		$prefijo = "http://localhost";
		$sufijo = "/servicios.php/";
		if ($forzar_reemplazo || !file_exists($carpeta_doc.'/wsdl-viewer.xsl')) {
			copy(toba_dir(). '/php/modelo/var/wsdl-viewer.xsl', $carpeta_doc.'/wsdl-viewer.xsl');
		}
		if ($forzar_reemplazo || !file_exists($carpeta_doc.'/wsdl-viewer.css')) {		
			copy(toba_dir(). '/php/modelo/var/wsdl-viewer.css', $carpeta_doc.'/wsdl-viewer.css');
		}
		$include = '<?xml-stylesheet type="text/xsl" href="wsdl-viewer.xsl"?>';
		$search = '"utf-8"?>';
		$this->get_manejador_interface()->mensaje("Servicio: ".$this->identificador);			
		$url = $prefijo.$this->proyecto->get_url().$sufijo.$this->identificador."?wsdl2";
		$wsdl = file_get_contents($url);
		$wsdl = str_replace($search, $search.$include, $wsdl);
		$file = $this->identificador.".wsdl.xml";
		file_put_contents($carpeta_doc."/".$file, $wsdl);
		return $file;
	}

	/**
	 * Graba el archivo de configuracion del servicio dentro del directorio que luego sera enviado al cliente.
	 * @param array $directorio
	 * @param array $datos_cert
	 * @param string $url_sistema 
	 */
	function generar_configuracion_cliente($directorio, $cert_servidor, $url_sistema)
	{
		$config = new toba_ini($directorio . '/cliente.ini');
		if ($url_sistema != null) {
			$config->agregar_entrada("conexion", array('to' => $url_sistema));
		}
		if (! $config->existe_entrada('certificado', 'clave_cliente')) {
			$cert = array();
			$cert['clave_cliente'] = "../../privada.key";	//Se utiliza la clave de todo el proyecto
			$cert['cert_cliente'] = "../../publica.crt";	//Se utiliza el cert de todo el proyecto
		} else {
			$cert = $config->get_datos_entrada("certificado");  //Mantiene clave y cert actuales del cliente
		}

		//Guarda el certificado del servidor
		copy($cert_servidor, $directorio."/cert_servidor.crt");
		$cert['cert_servidor'] = "./cert_servidor.crt";
		$config->agregar_entrada('certificado', $cert);
				
		if (! empty($datos_cert)) {
			$config->agregar_entrada('certificado', $datos_cert);
		}
		$config->guardar();		
	}
	
	/**
	 * Asocia el proyecto y servicio con el nombre del archivo que contiene la clave publica
	 * @param directorio
	 * @param array $headers
	 * @param cert_cliente
	 */
	function generar_configuracion_servidor($directorio, $headers=array(), $cert_cliente)
	{
		$config = new toba_ini($directorio . '/servicio.ini');
		if (! $config->existe_entrada("certificado")) {
			$cert = array();
			$cert['clave_servidor'] = "../../privada.key";	//Se utiliza la clave de todo el proyecto
			$cert['cert_servidor'] = "../../publica.crt";	//Se utiliza el cert de todo el proyecto
			$config->agregar_entrada('certificado', $cert);
		}
		
		//Armo ID de cliente
		$nombre = array();
		ksort($headers);
		foreach ($headers as $id => $valor) {
			$nombre[] = $id.'='.$valor;
		}
		$nombre = implode(',', $nombre);

		//Guarda el certificado del cliente
		$nombre_archivo = toba_manejador_archivos::nombre_valido(str_replace("=", "_", $nombre));
		copy($cert_cliente, $directorio."/$nombre_archivo.crt");
		
		$datos = array();
		$datos['archivo'] = "./$nombre_archivo.crt";
		$datos['fingerprint'] = sha1(toba_servicio_web::decodificar_certificado($directorio."/$nombre_archivo.crt"));
		$config->agregar_entrada($nombre, $datos);
		
		$config->guardar();
	}
}
?>
