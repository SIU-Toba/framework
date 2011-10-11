<?php
/**
 * @package Componentes
 * @subpackage Negocio
 */
abstract class toba_servicio_web extends toba_componente
{

	final function __construct($id)
	{
		parent::__construct($id);
		// Cargo las dependencias
		foreach( $this->_lista_dependencias as $dep){
			$this->cargar_dependencia($dep);
			$this->_dependencias[$dep]->set_controlador($this, $dep);
			$this->dep($dep)->inicializar();
		}		
	}

	function get_opciones()
	{
		return array();
	}
	
	
	/**
	 * Rutea WSF hacia la extensión
	 */
	function __call($nombre, $argumentos)
	{
		$metodo = substr($nombre, 1);
		$mensaje_entrada = new toba_servicio_web_mensaje($argumentos[0]);
		
		//Aca puedo obtener los headers para el metodo y tambien verificar que la firma es correcta
		$headers = $this->obtener_headers($mensaje_entrada);
		if ($this->servicio_con_firma()) {
			$this->verificar_firma($headers, $mensaje_entrada->get_payload());		
		}
		
		$mensaje_salida = $this->$metodo($mensaje_entrada, $headers);
		if (isset($mensaje_salida)) {
			return $mensaje_salida->wsf();
		} else {
			return;
		}
	}
	
	/**
	 * Devuelve un arreglo con los datos de los headers
	 * @param WSMessage $mensaje
	 * @return array
	 */
	function obtener_headers($mensaje)
	{
		$headers = array();
		$datos = $mensaje->wsf()->outputHeaders;
		foreach($datos as $encabezado) {		
			$pila[] = simplexml_load_string($encabezado->str);
		}
		while(! empty($pila)) {
			$elemento = array_shift($pila);
			foreach($elemento->children() as $hijo) {
				$pila[] = $hijo;				
			}			
			if ($elemento->count() == 0) {						//Si es una hoja obtengo el valor							
				$name = $elemento->getName();
				$value = (string) $elemento;				
				$headers[$name] = $value;
			}
		}
		return $headers;
	}
	
	/**
	 * Verifica la firma del mensaje completo
	 * @param array $headers
	 * @param string $contenido_mensaje 
	 */
	protected function verificar_firma($headers, $contenido_mensaje )
	{
		//Recuperar la firma calculada en el cliente
		if (! empty($headers)) {
			if (isset($headers['firma'] )) {			
				$firma_original = base64_decode($headers['firma']);
				unset($headers['firma']); 

				//Ahora verifico la firma
				$clave_necesaria = $this->get_clave_publica($headers);		
				$data = $contenido_mensaje. implode('',$headers);

				$pub_key_id = openssl_get_publickey('file://'.$clave_necesaria);
				if (openssl_verify($data, $firma_original, $pub_key_id) != 1) {
					throw new toba_error('El mensaje no es válido o no fue posible procesar su firma correctamente');
				}
			} else {
				throw new toba_error('El mensaje no viene firmado, se anula el pedido');
			}
		}
	}	
	
	protected function servicio_con_firma()
	{
		$va_firmado = true;
		$id_servicio = $this->id[1];	//El nombre del servicio lo obtengo del identificador del item
		$ini = toba_modelo_instalacion::get_archivo_configuracion_servicios_web();
		if (! is_null($ini) && $ini->existe_entrada($id_servicio)) {
			$datos = $ini->get($id_servicio, 'firmado', 1, false );		//Por defecto firmo el mensaje (desactivar explicitamente)
			$va_firmado = ($datos == '1'); 
		}
		return $va_firmado;
	}		
	
	/**
	 * Devuelve la ruta al archivo que contiene la clave publica
	 * @param array $headers
	 * @return string
	 */
	protected function get_clave_publica($headers)
	{
		return null;
		
		//TODO: Aca tendria que recuperarse el nombre del archivo de acuerdo al grupo de datos que viene en los headers		
		//$ini = toba_modelo_instalacion::get_archivo_configuracion_servicios_web();
		return toba::nucleo()->toba_instalacion_dir(). '/servicios/id_rsa.pub';
	}		
}
?>