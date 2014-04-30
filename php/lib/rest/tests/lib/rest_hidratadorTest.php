<?php

namespace rest\tests\lib;

use rest\lib\rest_hidratador;

class rest_hidratadorTest extends \PHPUnit_Framework_TestCase
{


	public function testPasamanos()
	{
		$campos = array('a');
		$datos = array('a' => 10, 'b' => 2);
		$obj = rest_hidratador::hidratar_fila($campos, $datos);

		$this->assertEquals(1, count($obj));
		$this->assertEquals(10, $obj['a']);
	}

	public function testAlias()
	{
		$campos = array('a' => 'b');
		$datos = array('a' => 10, 'b' => 2);
		$obj = rest_hidratador::hidratar_fila($campos, $datos);

		$this->assertEquals(1, count($obj));
		$this->assertEquals(10, $obj['b']);
	}

	public function testObjeto()
	{
		$campos = array('a' => array('a' => 'id',
		                             'n' => 'nombre'),
			'b'
		);

		$datos = array('a' => 10, 'n' => 'xx', 'b' => 2, 'z' => 4);
		$obj = rest_hidratador::hidratar_fila($campos, $datos);

		$this->assertEquals(2, count($obj));
		$this->assertEquals(2, $obj['b']);
		$this->assertEquals(10, $obj['a']['id']);
		$this->assertEquals('xx', $obj['a']['nombre']);
	}


	public function testMerge()
	{
		$campos = array('a' => array('a' => 'id',
		                             'n' => 'nombre'),
			'b'
		);
		$merge = array('id_fila' => 'b',
		               'grupos'  => array(
			               'g' => array('z' => 'x')));

		$datos = array(
			array('a' => 10, 'n' => 'xx', 'b' => 2, 'z' => 4),
			array('a' => 10, 'n' => 'xx', 'b' => 2, 'z' => 5),
			array('a' => 10, 'n' => 'xx', 'b' => 2, 'z' => 6)
		);

		$obj = rest_hidratador::hidratar($campos, $datos, $merge);

		$exp = array(
			array('a' => array(
						'id'     => 10,
						'nombre' => 'xx',
					),
				'b' => 2,
				'g' =>	array(
						array('x' => 4),
						array('x' => 5),
						array('x' => 6),
					),
			),
		);

		$this->assertEquals($obj, $exp);
	}
}
 