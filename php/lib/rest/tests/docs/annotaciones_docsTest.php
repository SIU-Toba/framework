<?php

namespace rest\tests\docs;

use rest\docs\anotaciones_docs;

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

		$param_q1 = $params_query[0];

		// @param_query $juego string nombre del juego
		$this->assertEquals('query', $param_q1['paramType']);
		$this->assertEquals('juego', $param_q1['name']);
		$this->assertEquals('string', $param_q1['type']);
		$this->assertEquals('nombre del juego', $param_q1['description']);

		$params_body = $a->get_parametros_metodo($metodos[0], 'body');
		$this->assertEquals(1, count($params_body));

		//@param_body $limit integer Limitar a esta cantidad de registros
		$params_body1 = $params_body[0];
		$this->assertEquals('body', $params_body1['paramType']);
		$this->assertEquals('limit', $params_body1['name']);
		$this->assertEquals('integer', $params_body1['type']);
		$this->assertEquals('Limitar a esta cantidad de registros', $params_body1['description']);
	}

	public function testErroresMetodos()
	{
		$a = $this->getInstancia();
		$metodos = $a->get_metodos();
		$errores = $a->get_errores_metodo($metodos[0]);

		$this->assertEquals(2, count($errores));
		$this->assertEquals('404', $errores[0]['code']);
		$this->assertEquals('No se pudo encontrar a la persona', $errores[0]['message']);

		$this->assertEquals('400', $errores[1]['code']);
		$this->assertEmpty($errores[1]['message']);
	}
}
 