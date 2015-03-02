<?php

namespace SIUToba\rest\tests\docs;

use SIUToba\rest\docs\anotaciones_docs;

class annotaciones_docsTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @return anotaciones_docs
	 */
	protected function getInstancia()
	{
		return new anotaciones_docs('tests/docs/clase_anotada_ejemplo.php');
	}

	public function testConstructor()
	{
		$this->getInstancia();
		$this->assertTrue(true);
	}

	public function testDescripcionClase()
	{
		//la descripcion tiene espacios y algun chirimbolo raro,
		//saltos de linea-> se limipian espacios
		$a = $this->getInstancia();
		$this->assertTrue($a->get_descripcion_clase() == 'descripcion clase jk %%');
	}

	public function testGetMetodos()
	{
		$a = $this->getInstancia();
		$metodos = $a->get_metodos();

		$this->assertEquals('get', $metodos[0]['nombre']);
		$this->assertEquals('id_persona', $metodos[0]['parametros'][0]);
	}

	public function testParametrosMetodos()
	{
		$a = $this->getInstancia();
		$metodos = $a->get_metodos();
		$params_query = $a->get_parametros_metodo($metodos[0], 'query');

		$this->assertEquals(3, count($params_query));

//		$param_q1 = $params_query[0];

        $pq = $params_query[0];

		// @param_query $juego string nombre del juego
		$this->assertEquals('query', $pq['in']);
		$this->assertEquals('juego', $pq['name']);
		$this->assertEquals('string', $pq['type']);
		$this->assertEquals('nombre del juego', $pq['description']);

		$params_body = $a->get_parametros_metodo($metodos[0], 'body');
		$this->assertEquals(1, count($params_body));

		//@param_body $limit integer Limitar a esta cantidad de registros
		$params_body1 = $params_body[0];
		$this->assertEquals('body', $params_body1['in']);
		$this->assertEquals('limit', $params_body1['name']);
		$this->assertEquals('integer', $params_body1['type']);
		$this->assertEquals('Limitar a esta cantidad de registros', $params_body1['description']);
	}

	public function testRespuestasMetodos()
	{
		$a = $this->getInstancia();
		$metodos = $a->get_metodos();
		$respuestas = $a->get_respuestas_metodo($metodos[0]);


        $schema = array('type' => 'array',
                        'items' => array('$ref' => '#/definitions/Persona'));

		$this->assertEquals(3, count($respuestas));

        $this->assertArrayHasKey('200', $respuestas);
        $this->assertEquals('descripcion', $respuestas['200']['description']);
        $this->assertEquals($schema, $respuestas['200']['schema']);

        $this->assertArrayHasKey('404', $respuestas);
		$this->assertEquals('No se pudo encontrar a la persona', $respuestas['404']['description']);

        $this->assertArrayHasKey('400', $respuestas);
		$this->assertEmpty($respuestas['400']['description']);
	}
}
 