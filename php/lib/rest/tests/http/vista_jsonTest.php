<?php
/**
 * Created by IntelliJ IDEA.
 * User: alejandro
 * Date: 2/18/14
 * Time: 4:48 PM
 */

namespace rest\tests\http;

use rest\http\respuesta_rest;
use rest\http\vista_json;

class vista_jsonTest extends \PHPUnit_Framework_TestCase
{


	public function testJsonSimple()
	{
		$data = array('hola' => 'mundo');
		$r = new respuesta_rest($data);
		$v = new vista_json($r);

		$data2 = json_decode($v->get_cuerpo(), true);
		$this->assertEquals($data, $data2);
	}

	public function testJsonEmpty()
	{
		$data = array();
		$r = new respuesta_rest($data);
		$v = new vista_json($r);

		$data2 = json_decode($v->get_cuerpo(), true);
		$this->assertEquals($data, $data2);

		$data = null;
		$r = new respuesta_rest($data);
		$v = new vista_json($r);

		$data2 = json_decode($v->get_cuerpo(), true);
		$this->assertEquals($data, $data2);
	}
}
 