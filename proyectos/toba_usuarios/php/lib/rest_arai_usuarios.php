<?php

use GuzzleHttp\Psr7;
//use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * ACCESO y MANEJO de los servicios REST de ARAI-Usuarios.
 * 
 */
class rest_arai_usuarios
{
	static private $instancia;
	protected $cliente;
		
	/**
	 * Metodo para instanciar la clase rest_arai_usuarios
	 * @param boolean $recargar Flag que determina si recarga la instancia
	 * @return rest_arai_usuarios
	 */
	static function instancia($recargar=false)
	{
		if (!isset(self::$instancia) || $recargar) {
			self::$instancia = new rest_arai_usuarios($recargar);	
		}
		return self::$instancia;	
	}

	/**
	*	Eliminar la instancia actual
	*/
	static function eliminar_instancia()
	{
		self::$instancia = null;
	}

	/**
	*	Constructor de la clase rest_arai_usuarios
	*	@param boolean $recargar Flag que determina si recarga la instancia
	*/
	private function __construct($recargar)
	{
		$this->cliente = $this->get_cliente_rest();
	}
	
	//-----------------------------------------------------------------------------------
	//---- Auxiliares -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	/**
	 * Ver http://docs.guzzlephp.org/en/latest/docs.html
	 * @return GuzzleHttp\Client
	 */
	protected function get_cliente_rest()
	{
		try {
			$opciones = array();
			$cliente = toba::servicio_web_rest('rest_arai_usuarios', $opciones);
			return $cliente->guzzle();
		} catch (toba_error $e) {
			throw new toba_error_usuario("Hay un problema de configuracion del cliente REST. Por favor asegurese de configurarlo correctamente en el archivo cliente.ini.\n<br/><br/>Mensaje: " . $e->get_mensaje());
		}
	}
	
	private function manejar_excepcion_request(RequestException $e)
	{
		$msg = $e->getMessage() . PHP_EOL . Psr7\str($e->getRequest()) . PHP_EOL;
		if ($e->hasResponse()) {
			$msg .= Psr7\str($e->getResponse()) . PHP_EOL;
		}
		throw new toba_error($msg);
	}
	
	public function get_usuarios($filtro=array(), $excluir_aplicacion = null)
	{
		$query = $datos = array();
		$url = 'usuarios';		
		if (! is_null($excluir_aplicacion)) {
			$query['excluir_aplicacion'] = $excluir_aplicacion;
		}
		
		if (isset($filtro) && is_array($filtro)) {
			foreach ($filtro as $id => $campo) {
				$valor = (is_array($campo['valor'])) ? $campo['valor']['desde'] . ';' . $campo['valor']['hasta'] : $campo['valor'];
				$query[$id] = $campo['condicion'] . ';' . $valor;
			}
		}		
		try {
			// obtengo la respuesta
			$response = $this->cliente->get($url, array('query' => $query));
			$datos = rest_decode($response->getBody()->__toString());			
			foreach($datos as $clave => $dato) {
				$datos[$clave]['nombre_apellido'] = $dato['nombre'] . ' ' . $dato['apellido'];
			}			
		} catch (RequestException $e) {
			$this->manejar_excepcion_request($e);
		} catch (Exception $e) {
			throw new toba_error($e);
		}
		return $datos;		
	}
	
	public function get_usuario($identificador)
	{
		try {
			$url = "usuarios/$identificador";
			// obtengo la respuesta
			$response = $this->cliente->get($url);
			$datos = rest_decode($response->getBody()->__toString());			
			if (! empty($datos)) {
				$datos['nombre_apellido'] = $datos['nombre'] . ' ' . $datos['apellido'];
			}
		} catch (RequestException $e) {
			$this->manejar_excepcion_request($e);
		} catch (Exception $e) {
			throw new toba_error($e);
		}
		return $datos;		
	}
	
	public function get_cuenta($identificador_aplicacion, $cuenta)
	{
		try {
			$url = "aplicaciones/$identificador_aplicacion/cuentas/$cuenta";

			// obtengo la respuesta
			$response = $this->cliente->get($url);
			$datos = rest_decode($response->getBody()->__toString());	
		} catch (RequestException $e) {
			if ($e->getCode() == 404) { return array();	}
			$this->manejar_excepcion_request($e);
		} catch (Exception $e) {
			throw new toba_error($e);
		}
		return $datos;	
	}
	
	public function agregar_cuenta($identificador_aplicacion, $datos_cuenta)
	{
		try {
			$url = "aplicaciones/$identificador_aplicacion/cuentas";
			
			// obtengo la respuesta
			$response = $this->cliente->post($url, array('body' => rest_encode($datos_cuenta)));
			$datos = rest_decode($response->getBody()->__toString());			
		} catch (RequestException $e) {
			$this->manejar_excepcion_request($e);
		} catch (Exception $e) {
			throw new toba_error($e);
		}
		return $datos;		
	}
	
	public function eliminar_cuenta($identificador_aplicacion, $cuenta)
	{
		try {
			$url = "aplicaciones/$identificador_aplicacion/cuentas/$cuenta";
			
			// obtengo la respuesta
			$this->cliente->delete($url);
		} catch (RequestException $e) {
			$this->manejar_excepcion_request($e);
		} catch (Exception $e) {
			throw new toba_error($e);
		}
	}
	
	public function get_nombre_apellido_usuario($identificador)
	{
		$datos = $this->get_usuario($identificador);
		if (isset($datos) && !empty($datos)) {
			return $datos['nombre_apellido'];
		} else {
			return '';
		}
	}
	
	public function get_identificador_x_aplicacion_cuenta($identificador_aplicacion, $cuenta) 
	{
		$datos = $this->get_cuenta($identificador_aplicacion, $cuenta);
		if (isset($datos) && !empty($datos)) {
			return $datos['identificador_usuario'];
		} else {
			return null;
		}
	}
	
}
?>
