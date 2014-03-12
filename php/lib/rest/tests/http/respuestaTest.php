<?php

namespace rest\tests\http;

use rest\http\respuesta;

class respuestaTest extends \PHPUnit_Framework_TestCase
{


	/**
	 * @expectedException rest\lib\rest_error_interno
	 */
	public function testFinalizarError()
	{
		$r = new respuesta();
		$r->finalizar(); //no se seteo la respuesta
	}


	public function testFinalizarValidacionVacio()
	{
		$r = new respuesta("data", 204);
		$r->finalizar();
		$this->assertEmpty($r->get_data());

		$r = new respuesta("data", 304);
		$r->finalizar();
		$this->assertEmpty($r->get_data());
	}
}
 