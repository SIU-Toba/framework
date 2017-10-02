<?php
/**
 * Clase que representa los WS y su configuracion
 * @package Centrales
 * @subpackage Modelo
 */
class toba_modelo_servicio_web extends toba_modelo_elemento
{
	protected $proyecto;
	protected $identificador;
	protected $wsa = false;
	protected $rest = false;
	protected $db;
	protected $parametros_ws= array();
	
	function __construct(toba_modelo_proyecto $proyecto, $identificador, $tipo='soap')
	{		
		$this->proyecto = $proyecto;		
		$this->identificador = $identificador;
		$this->db = $this->proyecto->get_db();
		$this->rest = (strtolower($tipo) == 'rest');
	}

	function set_tipo_ws($tipo)
	{
		$this->rest = (strtolower($tipo) == 'rest');
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
	
	//--------------------------------------------------------------------------------------------------------------------//
	static function path_clave_privada(toba_modelo_proyecto $proyecto)
	{
		return toba_modelo_soap::path_clave_privada($proyecto);
	}
	
	static function path_clave_publica(toba_modelo_proyecto $proyecto)
	{
		return toba_modelo_soap::path_clave_publica($proyecto);
	}

	static function existe_archivo_certificado(toba_modelo_proyecto $proyecto)
	{
		return toba_modelo_soap::existe_archivo_certificado($proyecto);
	}
	
	static function get_ini_cliente(toba_modelo_proyecto $proyecto, $id_servicio)
	{
		return toba_modelo_soap::get_ini_cliente($proyecto, $id_servicio);
	}
	
	static function get_ini_server(toba_modelo_proyecto  $proyecto, $id_servicio)
	{
		return toba_modelo_soap::get_ini_server($proyecto, $id_servicio);
	}

	static function generar_id_entrada_cliente($headers)
	{
		$nombre = array();
		ksort($headers);
		foreach ($headers as $id => $valor) {
			$nombre[] = $id.'='.$valor;
		}
		//$nombre = implode(',', $nombre);
		return  implode(',', $nombre);
	}

	static function esta_activo(toba_modelo_proyecto $proyecto, $id_servicio)
	{
		return toba_modelo_soap::esta_activo($proyecto, $id_servicio);
	}
	
	//------------------------------------------------------------------------------------------------------------------//
	/**
	 * Crea un nuevo servicio web en base a los datos provistos a la clase
	 * @throws toba_error_def 
	 */
	function crear_servicio_web()
	{
		if (! $this->rest) {
			$soap = new toba_modelo_soap($this->proyecto);
			$soap->crear_servicio_web($this->identificador, $this->wsa, $this->parametros_ws);
		} 
	}
			
	/**
	 * Genera el archivo de documentacion correspondiente para el servicio y devuelve su nombre
	 * @param string $carpeta_doc
	 * @param boolean $forzar_reemplazo
	 * @return string 
	 */
	function generar_documentacion($carpeta_doc, $forzar_reemplazo = false)
	{
		if (! $this->rest) {
			$soap = new toba_modelo_soap($this->proyecto);
			return $soap->generar_documentacion($carpeta_doc, $forzar_reemplazo);
		}
	}

	/**
	 * Graba el archivo de configuracion del servicio dentro del directorio que luego sera enviado al cliente.
	 * @param array $directorio
	 * @param array $datos_cert
	 * @param string $url_sistema 
	 */
	function generar_configuracion_cliente($cert_servidor, $url_sistema, $cert_cli=null, $key_cli=null, $cert_pwd=null,$usr=null, $usr_pwd=null)
	{
		if (! $this->rest) {
			$soap = new toba_modelo_soap($this->proyecto);
			$soap->set_manejador_interface($this->manejador_interface);
			$soap->generar_configuracion_cliente($cert_servidor, $url_sistema, $this->get_id());
		} else {
			$rest = new toba_modelo_rest($this->proyecto);
			$rest->set_manejador_interface($this->manejador_interface);
			$rest->generar_configuracion_cliente($this->get_id(),$cert_servidor, $url_sistema, $cert_cli, $key_cli, $cert_pwd, $usr, $usr_pwd);
		}
	}
	
	/**
	 * Asocia el proyecto y servicio con el nombre del archivo que contiene la clave publica
	 * @param directorio
	 * @param array $headers
	 * @param cert_cliente
	 */
	function generar_configuracion_servidor($cert_cliente, $headers = array())
	{
		if (! $this->rest) {
			$soap = new toba_modelo_soap($this->proyecto);
			$soap->generar_configuracion_servidor($cert_cliente, $headers, $this->get_id());
		} else {
			$rest = new toba_modelo_rest($this->proyecto);
			$rest->generar_configuracion_servidor();
		}
	}

	/**
	 * Permite activar o desactivar un servicio web determinado (only  SOAP)
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $id_servicio
	 * @param smallint $estado 
	 */
	static function set_estado_activacion( toba_modelo_proyecto $proyecto, $id_servicio, $estado=0) 
	{
		toba_modelo_soap::set_estado_activacion($proyecto, $id_servicio, $estado);
	}

	/**
	 * Genera los archivos para los certificados del proyecto
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $directorio
	 * @throws toba_error_usuario 
	 */
	static function generar_certificados(toba_modelo_proyecto $proyecto, $directorio = null, $rest=false)
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
		
		$modelo =  'toba_modelo_soap';					//($rest) ? 'toba_modelo_rest' : cuando se defina cual es el path de las claves por ahora solo soap
		$out_key = $modelo::path_clave_privada($proyecto);
		$out_cert = $modelo::path_clave_publica($proyecto);
		$dir_sign = $out_key .'.sign';
			
		$cmd = "openssl req -x509 -nodes -days 2000 -newkey rsa:2048 -keyout $dir_sign -config $dir_inst/openssl.ini -out $out_cert";
		$exito = toba_manejador_procesos::ejecutar($cmd, $stdout, $stderr);
		if ($exito != '0') {
			throw new toba_error_usuario($stderr. "\n Asegurese tener instalados los binarios de OpenSSL y disponibles en el path. Para comprobar ejecute 'openssl version'");
		}
		
		$cmd = "openssl rsa -in $dir_sign -out $out_key";
		$exito = toba_manejador_procesos::ejecutar($cmd, $stdout, $stderr);
		if ($exito != '0') {
			throw new toba_error_usuario($stderr);
		}		
		unlink("$dir_sign");
	}	
}
?>
