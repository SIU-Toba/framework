<?php

namespace rest\chulupi;

use kernel\nucleo\acceso_rest;
use rest\http\request_memoria;
use rest\rest;
use rest\seguridad\rest_usuario;

class rest_test_case extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var rest
	 */
	protected $app;
	protected $autenticador;
	protected $autorizador;

	/**
	 * @var \kernel\nucleo\acceso_rest
	 */
	protected $acceso;



	protected function ejecutar($metodo, $ruta, $get = array(), $post = array(), $headers = array())
	{
		if(strpos('?', $ruta) !== false){
			throw new \toba_error("Pasar los parametros del get en el tercer parámetro");
		}

		$app = $this->setupRest();
		$host =$this->acceso->url_rest() . $ruta;
		$this->mock_vista_no_escribir($app);
		$mock_request = new request_memoria($metodo, $host, $get, $post, $headers);
		$app->request = $mock_request;
		$app->procesar();
		return $app->response();
	}

	protected function setupRest()
	{
		if(!isset($this->app)){
			$this->acceso = new acceso_rest();
			$this->app = $this->acceso->get_app_rest();
		}
		return $this->app;
	}

	protected function mock_autenticador(rest_usuario $user, rest $app)
	{
		$this->autenticador = $this->getMockBuilder('rest\seguridad\proveedor_autenticacion')
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->autenticador
			->expects($this->any())
			->method('get_usuario')
			->will($this->returnValue($user));
		$app->autenticador = $this->autenticador;
	}

	protected function mock_autorizador($autorizar, rest $app)
	{
		$this->autorizador = $this->getMockBuilder('rest\seguridad\proveedor_autorizacion')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		$this->autorizador
			->expects($this->any())
			->method('tiene_acceso')
			//->with($this->equalTo($usuario))
			->will($this->returnValue($autorizar));
		$app->autorizador = $this->autorizador;
	}

	protected function mock_vista_no_escribir(rest $app)
	{
		$vista = $this->getMockBuilder('rest\http\vista_json')
			->disableOriginalConstructor()
			->getMock();
		$vista
			->expects($this->once())
			->method('escribir')
			->will($this->returnValue(''));
		$app->vista = $vista;
	}
}