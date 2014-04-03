<?php

namespace rest\tests\lib;

use PHPUnit_Framework_MockObject_Mock;
use PHPUnit_Framework_TestCase;
use rest\lib\ruteador;

class ruteadorTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var ruteador
	 */
	public $ruteador;

	/**
	 * @var PHPUnit_Framework_MockObject_Mock
	 */
	protected $lector_recursos;

	protected $instanciador;

	function setUp()
	{
		$this->instanciador =
			$this->getMockBuilder('rest\lib\rest_instanciador')
				->disableOriginalConstructor()
				->getMock();
		$this->lector_recursos =
			$this->getMockBuilder('rest\lib\lector_recursos_archivo')
				->disableOriginalConstructor()
				->getMock();
		$this->ruteador = new ruteador($this->lector_recursos, $this->instanciador);
	}

	function testGetColeccion()
	{
		$path = 'recurso';

		$recurso = array('recurso' => 'recurso', 'archivo' => 'recurso_clase');

		$parametros = array();
		$this->lector_recursos->expects($this->once())
			->method('get_recurso')
			->will($this->returnValue($recurso));

		$this->instanciador->expects($this->exactly(1))
			->method('existe_metodo')
			->will($this->onConsecutiveCalls(true));

		$rec = $this->ruteador->buscar_controlador('GET', $path);

		$this->assertEquals('recurso_clase', $rec->archivo);
		$this->assertEquals('get_list', $rec->accion);
		$this->assertEquals($parametros, $rec->parametros);
	}

	function testQueryString()
	{
		$path = 'recurso?a=b';

		$recurso = array('recurso' => 'recurso', 'archivo' => 'recurso_clase');

		$parametros = array();
		$this->lector_recursos->expects($this->once())
			->method('get_recurso')
			->will($this->returnValue($recurso));
		$this->instanciador->expects($this->exactly(1))
			->method('existe_metodo')
			->will($this->onConsecutiveCalls(true));
		$rec = $this->ruteador->buscar_controlador('GET', $path);

		$this->assertEquals('recurso_clase', $rec->archivo);
		$this->assertEquals('get_list', $rec->accion);
		$this->assertEquals($parametros, $rec->parametros);
	}

	function testGetRecurso()
	{
		$path = 'recurso/07';
		$parametros = array('07');

		$recurso = array('recurso' => 'recurso', 'archivo' => 'recurso_clase');

		$this->lector_recursos->expects($this->once())
			->method('get_recurso')
			->will($this->returnValue($recurso));
		$this->instanciador->expects($this->exactly(2))
			->method('existe_metodo')
			->will($this->onConsecutiveCalls(false, true));
		$rec = $this->ruteador->buscar_controlador('GET', $path);

		$this->assertEquals('recurso_clase', $rec->archivo);
		$this->assertEquals('get', $rec->accion);
		$this->assertEquals($parametros[0], $rec->parametros[0]);
	}

	function testGetRecursoIdCero()
	{
		$path = 'recurso/0';
		$parametros = array('0');

		$recurso = array('recurso' => 'recurso', 'archivo' => 'recurso_clase');

		$this->lector_recursos->expects($this->once())
			->method('get_recurso')
			->will($this->returnValue($recurso));
		$this->instanciador->expects($this->exactly(2))
			->method('existe_metodo')
			->will($this->onConsecutiveCalls(false, true));
		$rec = $this->ruteador->buscar_controlador('GET', $path);

		$this->assertEquals('recurso_clase', $rec->archivo);
		$this->assertEquals('get', $rec->accion);
		$this->assertEquals($parametros[0], $rec->parametros[0]);
	}

	function testGetSubrecursoPadre()
	{
		$path = 'padre/07/hijo';
		$parametros = array('07');

		$recurso = array('recurso' => 'padre', 'archivo' => 'padre_clase');

		$this->lector_recursos->expects($this->once())
			->method('get_recurso')
			->will($this->returnValue($recurso));
		$this->instanciador->expects($this->exactly(1))
			->method('existe_metodo')
			->will($this->onConsecutiveCalls(true));
		$rec = $this->ruteador->buscar_controlador('GET', $path);

		$this->assertEquals('padre_clase', $rec->archivo);
		$this->assertEquals('get_hijo_list', $rec->accion);
		$this->assertEquals($parametros, $rec->parametros);
	}

	function testGetSubrecursoPadreComplejo()
	{
		$path = 'padre/07/hijo/12/nieto/abc';
		$parametros = array('07', '12', 'abc');

		$recurso = array('recurso' => 'hijo', 'archivo' => 'hijo_clase');

		$this->lector_recursos->expects($this->once())
			->method('get_recurso')
			->will($this->returnValue($recurso));
		$this->instanciador->expects($this->exactly(2))
			->method('existe_metodo')
			->will($this->onConsecutiveCalls(false, true));
		$rec = $this->ruteador->buscar_controlador('PUT', $path);

		$this->assertEquals('hijo_clase', $rec->archivo);
		$this->assertEquals('put_nieto', $rec->accion);
		$this->assertEquals($parametros, $rec->parametros);
	}

	function testAliases()
	{
		$path = 'padre/alias';
		$parametros = array();

		$recurso = array('recurso' => 'padre', 'archivo' => 'padre_clase');

		$this->lector_recursos->expects($this->once())
			->method('get_recurso')
			->will($this->returnValue($recurso));

		$this->instanciador->expects($this->exactly(1))
			->method('existe_metodo')
			->will($this->onConsecutiveCalls(true));
//            array('get($alias)', true),
//            array('get_list__alias', true)

		$rec = $this->ruteador->buscar_controlador('GET', $path);

		$this->assertEquals('padre_clase', $rec->archivo);
		$this->assertEquals('get_list__alias', $rec->accion);
		$this->assertEquals($parametros, $rec->parametros);
	}

	function testAliasesSubrec()
	{
		$path = 'padre/07/hijo/12/nieto/abc';
		$parametros = array('07', '12');

		$recurso = array('recurso' => 'hijo', 'archivo' => 'hijo_clase');


		$this->lector_recursos->expects($this->once())
			->method('get_recurso')
			->will($this->returnValue($recurso));

		$this->instanciador->expects($this->exactly(1))
			->method('existe_metodo')
			->will($this->onConsecutiveCalls(true));
//            array('post_nieto($abc)', false),
//            array('post_nieto_list__abc', true)

		$rec = $this->ruteador->buscar_controlador('POST', $path);

		$this->assertEquals('hijo_clase', $rec->archivo);
		$this->assertEquals('post_nieto_list__abc', $rec->accion);
		$this->assertEquals($parametros, $rec->parametros);
	}

	function testError()
	{
		$path = 'recurso/07/';
		try {
			$this->ruteador->buscar_controlador('GET', $path);
		} catch (\Exception $e) {
			$this->assertTrue(true);
			return;
		}
		$this->assertTrue(false);
	}
}