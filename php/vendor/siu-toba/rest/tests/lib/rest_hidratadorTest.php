<?php

namespace SIUToba\rest\tests\lib;

use SIUToba\rest\lib\rest_hidratador;

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
		$campos = array('b' => array('_mapeo' => 'a'));
		$datos = array('a' => 10, 'b' => 2);
		$obj = rest_hidratador::hidratar_fila($campos, $datos);

		$this->assertEquals(1, count($obj));
		$this->assertEquals(10, $obj['b']);
	}

	public function testObjeto()
	{
		$campos = array(
            'a' => array( '_compuesto' =>
                            array('id' =>  array('_mapeo' => "a"),
                                  'nombre' => array('_mapeo' => "n"))
                   ),
			'b'
		);

		$datos = array('a' => 10, 'n' => 'xx', 'b' => 2, 'z' => 4);
		$obj = rest_hidratador::hidratar_fila($campos, $datos);

//        (
//        [a] => 10
//        [b] => 2
//        )

		$this->assertEquals(2, count($obj));
		$this->assertEquals(2, $obj['b']);
		$this->assertEquals(10, $obj['a']['id']);
		$this->assertEquals('xx', $obj['a']['nombre']);
	}


	public function testMerge()
	{
        $campos = array(
            'a' => array( '_compuesto' =>
                array('id' =>  array('_mapeo' => "a"), //es el _id!
                    'nombre' => array('_mapeo' => "n"))
            ),
            'b' => array('_id'),
            //g es compuesto, y ademas se agrupa
            'g' => array( '_agrupado_por' => 'b',
                          '_compuesto' => array('x' => array('_mapeo' => 'z'))
            )
        );
		$datos = array(
			array('a' => 10, 'n' => 'xx', 'b' => 2, 'z' => 4),
			array('a' => 10, 'n' => 'xx', 'b' => 2, 'z' => 5),
			array('a' => 10, 'n' => 'xx', 'b' => 2, 'z' => 6)
		);

		$obj = rest_hidratador::hidratar($campos, $datos);


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

		$this->assertEquals($exp, $obj);
	}
}
 