<?php

namespace rest\toba;

use rest\http\request_memoria;
use rest\rest;
use rest\seguridad\rest_usuario;

class rest_test_case extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var rest
	 */
	protected static $app;
	protected $autenticador;
	protected $autorizador;

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();
		$tr = new \toba_rest();
		$app = $tr->instanciar_libreria_rest();
		$tr->configurar_libreria_rest($app);
		self::$app = $app;
	}

	protected function mock_autenticador(rest_usuario $user)
	{
		$this->autenticador = $this->getMockBuilder('rest\seguridad\proveedor_autenticacion')
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->autenticador
			->expects($this->once())
			->method('get_usuario')
			->will($this->returnValue($user));
		self::$app->autenticador = $this->autenticador;
	}

	protected function mock_autorizador($autorizar)
	{
		$this->autorizador = $this->getMockBuilder('rest\seguridad\proveedor_autorizacion')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		$this->autorizador
			->expects($this->once())
			->method('tiene_acceso')
			//->with($this->equalTo($usuario))
			->will($this->returnValue($autorizar));
		self::$app->autorizador = $this->autorizador;
	}

	protected function mock_vista_no_escribir($app)
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

	public function ejecutar($metodo, $ruta, $get = array(), $post = array(), $headers = array())
	{
		$app = self::$app;
		$this->mock_vista_no_escribir($app);
		$mock_request = new request_memoria($metodo, $ruta, $get, $post, $headers);
		$app->request = $mock_request;
		$app->procesar();
	}
}