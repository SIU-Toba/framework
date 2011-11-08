<?php

class toba_servicio_web_cliente
{
	static protected $ini;	
	protected $wsf;
	protected $opciones;
	protected $id_servicio;
	
	/**
	 * @return toba_servicios_web
	 */
	static function conectar($id_servicio, $opciones=array())
	{
		$info = toba::proyecto()->get_info_servicios_web_acc($id_servicio);
		$opciones_ini = $info['parametros'];
		
		//-- Lee opciones predefinidas del .ini
		if (! isset(self::$ini)) {
			self::$ini = toba_modelo_instalacion::get_archivo_configuracion_servicios_web();
			if (! is_null(self::$ini)) {
				if (self::$ini->existe_entrada($id_servicio)) {
					$opciones_ini = array_merge($opciones_ini, self::$ini->get_datos_entrada($id_servicio));			
				}		
			}
		}
		
		//Convierte todos los '1' de texto en true
		foreach (array_keys($opciones_ini) as $id_opcion) {
			if ($opciones_ini[$id_opcion] === '1' || $opciones_ini[$id_opcion] === 1) {
				$opciones_ini[$id_opcion] = true;
			}
		}		

		//-- Mezcla con las opciones recibidas y crea el objeto
		$opciones = array_merge($opciones_ini, $opciones);
		if (! isset($opciones['to'])) {
			throw new toba_error_def("Debe indicar la URL destino en el campo 'to'");			
		}		
		toba::logger()->debug("Invocando servicio $id_servicio. Opciones:<br>". var_export($opciones, true));
		$servicio = new toba_servicio_web_cliente($opciones, $id_servicio);
		return $servicio;
	}
	
	function __construct($opciones, $id_servicio) 
	{
		$this->opciones = $opciones;
		$this->id_servicio = $id_servicio;
		$this->wsf = new WSClient($this->opciones);
	}
	
	/**
	 * @return WSClient
	 */
	function wsf()
	{
		return $this->wsf;	
	}
		
	/**
	 * Envia un mensaje al servicio web y espera la respuesta
	 * @param toba_servicio_web_mensaje $mensaje
	 * @return toba_servicio_web_mensaje
	 */
	function request(toba_servicio_web_mensaje $mensaje)
	{
		try {
			//Antes de hacer el request, mando a firmar el mensaje con la clave privada.
			$this->sign($mensaje);
			$message = $this->wsf->request($mensaje->wsf());
			return new toba_servicio_web_mensaje($message);
		} catch (WSFault $fault) {
			throw new toba_error_comunicacion($fault->__toString(), $this->opciones, $this->wsf->getLastResponseHeaders());
		} catch (Exception $e) {
			throw new toba_error_comunicacion($e->getMessage(), $this->opciones, $this->wsf->getLastResponseHeaders());			
		}
	}
	
	function send(toba_servicio_web_mensaje $mensaje)
	{
		try {
			$this->wsf->send($mensaje->wsf());
		} catch (WSFault $fault) {
			throw new toba_error_comunicacion($fault->__toString(), $this->opciones, $this->wsf->getLastResponseHeaders());
		} catch (Exception $e) {
			throw new toba_error_comunicacion($e->getMessage(), $this->opciones, $this->wsf->getLastResponseHeaders());			
		}
	}

	/**.
	 *  Dispara el firmado del mensaje 
	 *  @ignore
	 */
	protected function sign(toba_servicio_web_mensaje $mensaje)
	{
		$archivo = $this->get_clave_privada();
		if (! is_null($archivo)) {
			$mensaje->firmar_mensaje($archivo);	
		}
	}

	/**
	 * Obtiene los headers por defecto que se deben agregar al mensaje
	 * Los mismos estan fijados en el archivo de configuracion para el servicio
	 * @return array(WSHeader) 
	 */
	protected function get_headers_configuracion()
	{
		$headers = array();
		$id_servicio = $this->id_servicio;
		$directorio = toba::nucleo()->toba_instalacion_dir(). '/servicios/'. $id_servicio;		//Directorio perteneciente al servicio
		
		//Obtener el archivo con la configuracion de headers
		$archivo = $directorio . '/headers.ini';
		if (toba_manejador_archivos::existe_archivo_en_path($archivo)) {
			$ini_headers = new toba_ini($archivo);
			$datos = $ini_headers->get('headers', null, null);
			if (! is_null($datos)) {									//Agregar un headers por cada valor disponible
				foreach($datos as $klave => $valor) {
					$headers[] = new WSHeader(array('name' => $klave, 'data' => $valor));					
				}
			}
		}
		return $headers;
	}
	
	/**
	 * Devuelve la ruta al archivo que contiene la clave privada
	 * @return string 
	 */
	function get_clave_privada()
	{	
		//Esto deberia salir del archivo existente dentro del directorio del servicio web
		$clave = null;
		$directorio = toba::nucleo()->toba_instalacion_dir(). '/servicios/'. $this->id_servicio;		//Directorio perteneciente al servicio		
		$ini_conf = new toba_ini($directorio . '/servicio.ini');
		if (! is_null($ini_conf) && $ini_conf->existe_entrada('RSA')) {
			$aux = $ini_conf->get_datos_entrada('RSA');
			$clave = $directorio. '/' . $aux['privada'];
		}				
		return $clave;
	}
	
	/**
	 * Devuelve la ruta al archivo que contiene la clave publica
	 * @return string 
	 */
	function get_clave_publica()
	{
		$clave = null;
		$directorio = toba::nucleo()->toba_instalacion_dir(). '/servicios/'. $this->id_servicio;		//Directorio perteneciente al servicio		
		$ini_conf = new toba_ini($directorio . '/servicio.ini');
		if (! is_null($ini_conf) &&  $ini_conf->existe_entrada('RSA')) {
			$aux = $ini_conf->get_datos_entrada('RSA');			
			$clave = $directorio .  '/' . $aux['public'];
		}		
		return $clave;
	}
		
	function get_conf_comunicacion()
	{
		
	}
}
?>