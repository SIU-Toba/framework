<?php

namespace rest\tests\lib;

use PHPUnit_Framework_MockObject_Mock;
use PHPUnit_Framework_TestCase;
use rest\rest;
use rest\lib\ruteador;


class ruteadorTest extends PHPUnit_Framework_TestCase{

	/**
	 * @var ruteador
	 */
	public $ruteador;


	/**
	 * @var PHPUnit_Framework_MockObject_Mock
	 */
	protected $lector_recursos;

	function setUp()
	{
		$this->lector_recursos =
			$this->getMockBuilder('rest\lib\lector_recursos_archivo')
					->disableOriginalConstructor()
					->getMock();
		$this->ruteador =new ruteador($this->lector_recursos);
	}

	function testGetColeccion()
	{
		$path = 'recurso';
		$parametros = array();
		$this->lector_recursos->expects($this->once())
			->method('get_recurso')
			->will($this->returnValue('recurso'));
		$rec = $this->ruteador->buscar_controlador('GET', $path);

		$this->assertEquals('recurso', $rec->clase);
		$this->assertEquals('get_list', $rec->accion);
		$this->assertEquals($parametros, $rec->parametros);
	}

	function testQueryString()
	{
		$path = 'recurso?a=b';
		$parametros = array();
		$this->lector_recursos->expects($this->once())
			->method('get_recurso')
			->will($this->returnValue('recurso'));
		$rec = $this->ruteador->buscar_controlador('GET', $path);

		$this->assertEquals('recurso', $rec->clase);
		$this->assertEquals('get_list', $rec->accion);
		$this->assertEquals($parametros, $rec->parametros);
	}

	function testGetRecurso()
	{
		$path = 'recurso/07';
		$parametros = array('07');

		$this->lector_recursos->expects($this->once())
			->method('get_recurso')
			->will($this->returnValue('recurso'));
		$rec = $this->ruteador->buscar_controlador('GET', $path);

		$this->assertEquals('recurso', $rec->clase);
		$this->assertEquals('get', $rec->accion);
		$this->assertEquals($parametros[0], $rec->parametros[0]);
	}

	function testGetSubrecursoPadre()
	{
		$path = 'padre/07/hijo';
		$parametros = array('07');

		$this->lector_recursos->expects($this->once())
			->method('get_recurso')
			->will($this->returnValue('padre'));
		$rec = $this->ruteador->buscar_controlador('GET', $path);

		$this->assertEquals('padre', $rec->clase);
		$this->assertEquals('get_hijo_list', $rec->accion);
		$this->assertEquals($parametros, $rec->parametros);
	}

    function testGetSubrecursoPadreComplejo()
    {
        $path = 'padre/07/hijo/12/nieto/abc';
        $parametros = array('07', '12', 'abc');

        $this->lector_recursos->expects($this->once())
            ->method('get_recurso')
            ->will($this->returnValue('hijo'));
        $rec = $this->ruteador->buscar_controlador('PUT', $path);

        $this->assertEquals('hijo', $rec->clase);
        $this->assertEquals('put_nieto', $rec->accion);
        $this->assertEquals($parametros, $rec->parametros);
    }

    function testGetAliases()
    {
        //VER como testear, ya que usa al instanciador para ver si existe el metodo, y no se puede mockear
        //en este caso existe el metodo put_nieto_abc($padre, $hijo)
//        $path = 'padre/07/hijo/12/nieto/abc';
//        $parametros = array('07', '12');
//
//        $this->lector_recursos->expects($this->once())
//            ->method('get_recurso')
//            ->will($this->returnValue('hijo'));
//        $rec = $this->ruteador->buscar_controlador('PUT', $path);
//
//        $this->assertEquals('hijo', $rec->clase);
//        $this->assertEquals('put_nieto_abc', $rec->accion);
//        $this->assertEquals($parametros, $rec->parametros);

    }

	function testError()
	{
		$path = 'recurso/07/';
		try{
			$this->ruteador->buscar_controlador('GET', $path);
		}catch (\Exception $e){
			$this->assertTrue(true);
			return;
		}
		$this->assertTrue(false);
	}

}