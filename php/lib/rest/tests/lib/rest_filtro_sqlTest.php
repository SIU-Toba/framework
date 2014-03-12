<?php

namespace rest\tests\lib;

use rest\lib\rest_filtro_sql;
use rest\rest;

class rest_filtro_sqlTest extends \PHPUnit_Framework_TestCase
{


	protected $request;

	/**
	 * @var rest_filtro_sql
	 */
	protected $filtro;

	protected function setUp()
	{
		parent::setUp();
		$this->request = $this->get_mock_request();
	}

	/**
	 * @expectedException rest\lib\rest_error
	 */
	public function testParametroIncorrecto()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->agregar_parametro_request($param, 'no_existe;pepe');
		$this->assertEquals("nombre = pepe", trim($this->filtro->get_sql_where()));
	}


	public function testParametroInexistente()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->request->expects($this->once())
			->method('get')
			->with($this->equalTo($param))
			->will($this->returnValue(''));

		$this->assertEquals("1 = 1", trim($this->filtro->get_sql_where()));
	}

	public function testLimit()
	{
		$this->request->expects($this->exactly(2))
			->method('get')
			->with($this->logicalOr($this->equalTo('limit'), $this->equalTo('page')))
			->will($this->returnCallback(function ($param) {
				if ($param == 'limit') {
					return 5;
				} else {
					return 1;
				}
			}));

		$this->assertEquals("LIMIT 5 OFFSET 0", trim($this->filtro->get_sql_limit()));
	}

	public function testLimitSinPage()
	{
		$this->request->expects($this->exactly(2))
			->method('get')
			->with($this->logicalOr($this->equalTo('limit'), $this->equalTo('page')))
			->will($this->returnCallback(function ($param) {
				if ($param == 'limit') {
					return 5;
				} else {
					return '';
				}
			}));

		$this->assertEquals("LIMIT 5", trim($this->filtro->get_sql_limit()));
	}

	public function testLimitSinLimit()
	{
		$this->request->expects($this->exactly(1))
			->method('get')
			->with($this->logicalOr($this->equalTo('limit'), $this->equalTo('page')))
			->will($this->returnCallback(function ($param) {
				if ($param == 'limit') {
					return '';
				} else {
					return 10;
				}
			}));

		$this->assertEquals("", trim($this->filtro->get_sql_limit()));
	}


	public function testOrderBy()
	{

		$this->filtro->agregar_campo('nombre');
		$this->filtro->agregar_campo('apellido');

		$this->agregar_parametro_request('order', '+apellido,-nombre');

		$this->assertEquals("ORDER BY apellido ASC, nombre DESC", trim($this->filtro->get_sql_order_by()));
	}

	public function testOrderByVacio()
	{

		$this->filtro->agregar_campo('nombre');
		$this->filtro->agregar_campo('apellido');
//        $this->agregar_parametro_request('order', '+apellido,-"and=1=1');

		$this->assertEquals("", trim($this->filtro->get_sql_order_by()));
	}

	/**
	 * @expectedException rest\lib\rest_error
	 */
	public function testOrderByInvalido()
	{

		$this->filtro->agregar_campo('nombre');
		$this->filtro->agregar_campo('apellido');

		$this->agregar_parametro_request('order', '+apellido,-"and=1=1');

		$this->assertEquals("ORDER BY apellido ASC, nombre DESC", trim($this->filtro->get_sql_order_by()));
	}

	/**
	 * @expectedException rest\lib\rest_error
	 */
	public function testLimitError()
	{
		$this->request->expects($this->exactly(2))
			->method('get')
			->with($this->logicalOr($this->equalTo('limit'), $this->equalTo('page')))
			->will($this->returnCallback(function ($param) {
				if ($param == 'limit') {
					return 5;
				} else {
					return 0;
				}
			}));

		$this->assertEquals("LIMIT 5 OFFSET 0", trim($this->filtro->get_sql_limit()));
	}


	public function testWhereIgual()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->agregar_parametro_request($param, 'es_igual_a;pepe');
		$this->assertEquals("nombre = pepe", trim($this->filtro->get_sql_where()));
	}

	public function testWhereDistinto()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->agregar_parametro_request($param, 'es_distinto_de;pepe');
		$this->assertEquals("nombre <> pepe", trim($this->filtro->get_sql_where()));
	}

	public function testWhereEntre()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->agregar_parametro_request($param, 'entre;pepe;juan');
		$this->assertEquals("nombre BETWEEN pepe AND juan", trim($this->filtro->get_sql_where()));
	}

	public function testWhereMayorQue()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->agregar_parametro_request($param, 'es_mayor_que;pepe');
		$this->assertEquals("nombre > pepe", trim($this->filtro->get_sql_where()));
	}

	public function testWhereMayorIgualQue()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->agregar_parametro_request($param, 'es_mayor_igual_que;pepe');
		$this->assertEquals("nombre >= pepe", trim($this->filtro->get_sql_where()));
	}


	public function testWhereMenorQue()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->agregar_parametro_request($param, 'es_menor_que;pepe');
		$this->assertEquals("nombre < pepe", trim($this->filtro->get_sql_where()));
	}

	public function testWhereMenorIgualQue()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->agregar_parametro_request($param, 'es_menor_igual_que;pepe');
		$this->assertEquals("nombre <= pepe", trim($this->filtro->get_sql_where()));
	}

	public function testWhereDesde()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->agregar_parametro_request($param, 'desde;pepe');
		$this->assertEquals("nombre >= pepe", trim($this->filtro->get_sql_where()));
	}

	public function testWhereHasta()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->agregar_parametro_request($param, 'hasta;pepe');
		$this->assertEquals("nombre <= pepe", trim($this->filtro->get_sql_where()));
	}

	public function testWhereContiene()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->agregar_parametro_request($param, 'contiene;pepe');
		$this->assertEquals("nombre ILIKE %pepe%", trim($this->filtro->get_sql_where()));
	}

	public function testWhereNOContiene()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->agregar_parametro_request($param, 'no_contiene;pepe');
		$this->assertEquals("nombre NOT ILIKE %pepe%", trim($this->filtro->get_sql_where()));
	}

	public function testWhereComienza()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->agregar_parametro_request($param, 'comienza_con;pepe');
		$this->assertEquals("nombre ILIKE pepe%", trim($this->filtro->get_sql_where()));
	}

	public function testWhereTermina()
	{
		$param = "nombre";
		$this->filtro->agregar_campo($param);
		$this->agregar_parametro_request($param, 'termina_con;pepe');
		$this->assertEquals("nombre ILIKE %pepe", trim($this->filtro->get_sql_where()));
	}


	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	protected function get_mock_request()
	{
		$r = new rest(array());

		$this->filtro = $this->getMockBuilder('rest\lib\rest_filtro_sql')
			->disableOriginalConstructor()
			->setMethods(array('quote'))
			->getMock();

		$request = $this->getMockBuilder('rest\http\respuesta_rest')
			->disableOriginalConstructor()
			->getMock();
		$r->container['request'] = $request;

		return $request;
	}


	protected function agregar_parametro_request($param, $valor)
	{
		$this->request->expects($this->once())
			->method('get')
			->with($this->equalTo($param))
			->will($this->returnValue($valor));

		$this->filtro->expects($this->any())
			->method('quote')
			->will($this->returnArgument(0));
	}
}
 