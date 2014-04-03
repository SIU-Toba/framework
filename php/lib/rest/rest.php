<?php

namespace rest;

use Exception;
use rest\docs\controlador_docs;
use rest\http\request;
use rest\http\respuesta_rest;
use rest\http\vista_json;
use rest\http\vista_respuesta;
use rest\http\vista_xml;
use rest\lib\lector_recursos_archivo;
use rest\lib\logger;
use rest\lib\logger_vacio;
use rest\lib\rest_error;
use rest\lib\rest_error_interno;
use rest\lib\rest_instanciador;
use rest\lib\ruteador;
use rest\lib\Set;
use rest\seguridad\autenticacion\rest_error_autenticacion;
use rest\seguridad\autorizacion\autorizacion_anonima;
use rest\seguridad\autorizacion\rest_error_autorizacion;
use rest\seguridad\firewall;
use rest\seguridad\proveedor_autorizacion;
use rest\seguridad\rest_usuario;

/**
 * @property lector_recursos_archivo lector_recursos
 * @property request request
 * @property respuesta_rest $response
 * @property vista_respuesta vista
 * @property ruteador router
 * @property logger logger
 * @property firewall firewall
 * @property mixed settings
 * @property rest_usuario usuario
 * @property proveedor_autorizacion autorizador
 */
class rest
{

	protected static $instancia;

	/**
	 * @var \rest\lib\Set
	 */
	public $container;


	/**
	 * @return rest
	 */
	public static function app()
	{
		return self::$instancia;
	}

	/**
	 * @return request
	 */
	public static function request()
	{
		return self::$instancia->request;
	}

	/**
	 * @return respuesta_rest
	 */
	public static function response()
	{
		return self::$instancia->response;
	}

	/**
	 * Si el usuario es null, es acceso anonimo
	 * @return rest_usuario
	 */
	public static function usuario()
	{
		return self::$instancia->usuario;
	}


	/**
	 * Settings default - Se pueden cambiar en el constructor
	 * @return array
	 */
	public static function get_default_settings()
	{
		return array(
			'formato_respuesta'  => 'json',
			'encoding'           => 'utf-8', //latin1
			'path_controladores' => '/',
			'prefijo_controladores' => 'recurso_',
			'url_api'            => '/api',
			'prefijo_api_docs'   => 'api-docs',
			'url_protegida'      => '/.*/',

			//DEBUG
			'debug'              => false,
			// HTTP
			'http.version'       => '1.1'
		);
	}

	function __construct($settings = array())
	{
		self::$instancia = $this;
		$this->autoload();

		$this->container = new Set();
		$this->container['settings'] = array_merge(static::get_default_settings(), $settings);

		// Request default
		$this->container->singleton('request', function ($c) {
			$req = new request();
			$req->set_encoding_datos($c['settings']['encoding']);
			return $req;
		});

		// Resoyesta default
		$this->container->singleton('response', function ($c) {
			$respuesta = new respuesta_rest();
			$respuesta->set_encoding_datos($c['settings']['encoding']);
			return $respuesta;
		});

		// Ruteador default
		$this->container->singleton('router', function ($c) {
			$r = new ruteador($c->lector_recursos, new rest_instanciador());
			return $r;
		});

		// Proveedor de autenticacion --> SE DEBE INDICAR UNO EXTERNAMEN
		$this->container->singleton('autenticador', function ($c) {
			throw new rest_error_interno("Se debe indicar un autenticador que provea los usuarios del negocio");
		});

		// Proveedor de autorizacion
		$this->container->singleton('autorizador', function ($c) {
			$autorizador = new autorizacion_anonima();
			return $autorizador;
		});

		// Firewall default
		$this->container->singleton('firewall', function ($c) {
			$autorizador = new firewall($c->autenticador, $c->autorizador, $c->settings['url_protegida']);
			return $autorizador;
		});

		// Logger
		$this->container->singleton('logger', function ($c) {
			return new logger_vacio();
		});

		$this->container->singleton('lector_recursos', function ($c) {
			return new lector_recursos_archivo(
				$c['settings']['path_controladores'],
				$c['settings']['prefijo_controladores']);
		});

		$this->container->singleton('controlador_documentacion', function ($c) {
			return new controlador_docs(
				$c['settings']['path_controladores'],
				$c['settings']['url_api']
			);
		});

		// Vistas default
		$this->container->singleton('vista', function ($c) {
			$formato = $c['settings']['formato_respuesta'];
			$respuesta = $c['response'];
			switch ($formato) {
				case 'json':
					return new vista_json($respuesta);
				case 'xml':
					return new vista_xml($respuesta);
			}
		});
	}

	function procesar()
	{
		$this->logger->debug("Iniciando el pedido");

		$method = $this->request->get_method();
		$url = $this->get_url_relativa();
		$url = ltrim($url, '/');

		$this->logger->debug("Procesando URL '/$url'");

		$partes_url = explode('/', $url);

		try {
			$this->controlar_acceso($url);

			if ($partes_url[0] == $this->settings['prefijo_api_docs']) {
				$this->mostrar_documentacion($url);
			} else {
				$recurso = $this->router->buscar_controlador($method, $url);
				$this->logger->debug("Controlador encontrado {$recurso->archivo} :: {$recurso->accion} (" . implode(',', $recurso->parametros) . ")");
				$recurso->ejecutar_accion();
			}
		} catch (rest_error_autenticacion $ex) {
			$ex->configurar_respuesta($this->response);
			$this->logger->info("Excepcion de Autenticacion. Autenticar y reintentar");
			$this->logger->var_dump($this->response);
		} catch (rest_error_autorizacion $ex) {
			$ex->configurar_respuesta($this->response);
			$this->logger->info("Error de Autorizacion.");
		} catch (rest_error $ex) {
			// Excepciones controladas, partel del flujo normal de la API
			$ex->configurar_respuesta($this->response);
			$this->logger->info("La api retornó un error. Status: " . $this->response->get_status());
			$this->logger->var_dump($this->response->get_data());
		} catch (Exception $ex) {
			// Excepcion del codigo del proyecto - Error de programación, no tiene que entrar aca en el flujo normal
			$this->logger->error("Error al ejecutar el pedido. " . $ex->getMessage());
			$this->logger->error($ex->getTraceAsString());
			if ($this->config('debug')) {
				throw $ex;
			} else {
				$error = new rest_error(500, "Error Interno en el servidor");
				$error->configurar_respuesta($this->response);
			}
		}
		$this->response->finalizar();
		$this->vista->escribir();
		$this->logger->debug("Pedido finalizado");
		if ($this->config('debug')) {
			$this->logger->var_dump($this->response);
		}
	}


	/**
	 * @param $ruta
	 * @throws rest_error_autorizacion si el firewall denega el acceso
	 */
	protected function controlar_acceso($ruta)
	{
		$this->logger->debug("Iniciando Autenticacion");
		if ($this->firewall->maneja_ruta($ruta)) {
			$this->logger->debug("Pedido capturado por el firewall");
			$usuario = $this->firewall->manejar($ruta, $this->request);
			$this->loggear_acceso_ok($usuario);
			$this->usuario = $usuario;
		} else {
			$this->logger->info("El firwall no controla acceso a $ruta");
		}
	}

	private function get_url_relativa()
	{
		$uri = $this->request->get_request_uri();
		$url = strtok($uri, '?');
		$url_api = $this->settings['url_api'];

		if (substr($url, 0, strlen($url_api)) == $url_api) {
			return substr($url, strlen($url_api));
		}
		throw new rest_error_interno
		("Este controlador no está configurado para manejar esta URL. La url es: '$uri', la url de la API es '$url_api'");
	}


	protected function autoload()
	{
		include_once 'bootstrap.php';
//		if (file_exists(__DIR__ . '/vendor/autoload.php')) {
//			require 'vendor/autoload.php';
//		} else {
//			die("Falta correr 'composer install' en php/lib/rest");
//		}
		bootstrap::registerAutoloader();
	}

	public function config($clave)
	{
		return $this->settings[$clave];
	}

	public function __get($name)
	{
		return $this->container[$name];
	}

	public function __set($name, $value)
	{
		$this->container[$name] = $value;
	}

	public function __isset($name)
	{
		return isset($this->container[$name]);
	}

	public function __unset($name)
	{
		unset($this->container[$name]);
	}

	/**
	 * @param $usuario
	 */
	protected function loggear_acceso_ok($usuario)
	{
		if ($usuario != null) {
			$this->logger->debug("Usuario '{$usuario->get_usuario()}' autenticado y autorizado");
		} else {
			$this->logger->debug("Usuario autorizado anonimamente");
		}
	}

	/**
	 * @param $url
	 */
	protected function mostrar_documentacion($url)
	{
		$this->logger->debug("Iniciando documentacion");
		$controlador = $this->controlador_documentacion;
		$url = strstr($url, '/');
		$controlador->get_documentacion($url);
	}
}