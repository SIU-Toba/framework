<?php

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

/**
 * ACCESO y MANEJO de los servicios REST de ARAI-Usuarios.
 * para la API v1
 */
class api_usuarios_1 implements InterfaseApiUsuarios
{
	static private $instancia;
	protected $cliente;
		
	static function instancia($cliente)
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new api_usuarios_1($cliente);	
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

	protected function __construct($cliente)
	{
		$this->cliente = $cliente;
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
        return $this->cliente;
	}
	
	protected function manejar_excepcion_request(RequestException $e)
	{
		$msg = $e->getMessage() . PHP_EOL . Psr7\str($e->getRequest()) . PHP_EOL;
		if ($e->hasResponse()) {
			$msg .= Psr7\str($e->getResponse()) . PHP_EOL;
		}
		toba_logger::instancia()->error($msg);
		throw new toba_error(toba::escaper()->escapeJs($msg));
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
		} catch (RequestException | \Exception $e) {
            toba_logger::instancia()->var_dump(['filtro' => $filtro, 'exluir_app' => $excluir_aplicacion]);
			$this->manejar_excepcion_request($e);
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
		} catch (RequestException | \Exception $e) {
            toba_logger::instancia()->var_dump(['id' => $identificador]);            
			$this->manejar_excepcion_request($e);
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
			throw new toba_error(toba::escaper()->escapeJs($e));
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
			throw new toba_error(toba::escaper()->escapeJs($e));
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
			throw new toba_error(toba::escaper()->escapeJs($e));
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
