<?php
/**
 * Created by IntelliJ IDEA.
 * User: alejandro
 * Date: 2/18/14
 * Time: 12:39 PM
 */

namespace rest\tests\http;

use rest\http\respuesta_rest;

class respuesta_restTest extends \PHPUnit_Framework_TestCase
{


	public function testGet()
	{
		$data = array(1);
		$r = new respuesta_rest();
		$r->get($data);

		$this->assertEquals(200, $r->get_status());
		$this->assertEquals($data, $r->get_data());
	}

	/**
	 * @expectedException rest\lib\rest_error
	 */
	public function testGetNotFound()
	{
		$data = false;
		$r = new respuesta_rest();
		$r->get($data);
	}

	/**
	 * @expectedException rest\lib\rest_error
	 */
	public function testNotFound()
	{
		$r = new respuesta_rest();
		$r->not_found("mje");
	}

	public function testPutOK()
	{
		$errores = false;
		$r = new respuesta_rest();
		$r->put();
		$this->assertEquals(204, $r->get_status());
		$this->assertEmpty($r->get_data());
	}

	public function testDeleteOK()
	{
		$r = new respuesta_rest();
		$r->delete();
		$this->assertEquals(204, $r->get_status());
		$this->assertEmpty($r->get_data());
	}


	public function testRedirect()
	{
		$r = new respuesta_rest();
		$r->redirect('hola');
		$this->assertArrayHasKey('Location', $r->headers);
		$this->assertEquals($r->headers['Location'], 'hola');
	}

	public function testErrorNegocio(){
		$r = new respuesta_rest();
		$error = array('error' => 'e');
		$r->error_negocio($error);
		$this->assertEquals(400, $r->get_status());
		$this->assertEquals($error, $r->get_data());
	}
}
 