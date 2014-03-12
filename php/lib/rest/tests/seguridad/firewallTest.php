<?php

namespace rest\tests\seguridad;

use rest\seguridad\firewall;

class firewallTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $autenticador;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $autorizador;

	protected function get_instancia_ruta($pattern = '#.*#')
	{
		$this->autenticador = $this->getMockBuilder('rest\seguridad\proveedor_autenticacion')
			->disableOriginalConstructor()
			->getMockForAbstractClass();
		$this->autorizador = $this->getMockBuilder('rest\seguridad\proveedor_autorizacion')
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		return new firewall($this->autenticador, $this->autorizador, $pattern);
	}

	protected function get_instancia_manejar($usuario = NULL, $tiene_acceso = true)
	{
		$f = $this->get_instancia_ruta();

		$this->autenticador
			->expects($this->once())
			->method('get_usuario')
			->will($this->returnValue($usuario));

		$this->autorizador
			->expects($this->once())
			->method('tiene_acceso')
			->with($this->equalTo($usuario))
			->will($this->returnValue($tiene_acceso));

		return $f;
	}

	public function testManejaRuta()
	{
		$f = $this->get_instancia_ruta('#.*#');
		$this->assertTrue($f->maneja_ruta('/'));
		$this->assertTrue($f->maneja_ruta('//-.fadsfjasdfjo/7//'));

		$f = $this->get_instancia_ruta('#^/admin.*#');
		$this->assertFalse($f->maneja_ruta('/'));
		$this->assertFalse($f->maneja_ruta('admin/'));
		$this->assertFalse($f->maneja_ruta('/aDmin/'));

		$this->assertTrue($f->maneja_ruta('/admin'));
		$this->assertTrue($f->maneja_ruta('/admin/fsdi/87/fas?hola'));
	}

	public function testAutenticarOk()
	{
		$f = $this->get_instancia_manejar(null, true);
		$usuario = $f->manejar('/', null);
		$this->assertEquals(null, $usuario);
	}

	/**
	 * @expectedException rest\seguridad\autenticacion\rest_error_autenticacion
	 */
	public function atestAutenticarError()
	{
		$f = $this->get_instancia_manejar(null, false);
		$usuario = $f->manejar('/', null);
		$this->assertEquals(null, $usuario);
	}

	/**
	 * @expectedException rest\seguridad\autorizacion\rest_error_autorizacion
	 */
	public function testAutorizarError()
	{
		$f = $this->get_instancia_manejar('usuario', false);
		$usuario = $f->manejar('/', null);
		$this->assertEquals(null, $usuario);
	}
}
 