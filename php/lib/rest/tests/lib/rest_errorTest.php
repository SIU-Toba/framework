<?php

namespace rest\tests\lib;

use rest\http\respuesta_rest;
use rest\lib\rest_error;

class rest_errorTest extends \PHPUnit_Framework_TestCase
{


	public function testInicializacion()
	{

		$status = 400;
		$mensaje = "mi mensaje";
		$arreglo = array('hola' => 'mundo');
		$error = new rest_error($status, $mensaje, $arreglo);

		$this->assertEquals($mensaje, $error->getMessage());
		$this->assertEquals($status, $error->getCode());
		$this->assertEquals($arreglo, $error->get_datalle());
	}

	public function testConfiguracionRespuesta()
	{

		$status = 400;
		$mensaje = "mi mensaje";
		$arreglo = array('hola' => 'mundo');
		$error = new rest_error($status, $mensaje, $arreglo);
		$r = new respuesta_rest();
		$error->configurar_respuesta($r);

		$data = $r->get_data();
		$this->assertEquals($mensaje, $data['descripcion']);
		$this->assertEquals($status, $r->get_status());
		$this->assertEquals($arreglo, $data['detalle']);
	}

	public function testConfiguracionRespuestaSinDetalle()
	{
		$status = 400;
		$mensaje = "mi mensaje";
		$arreglo = array();
		$error = new rest_error($status, $mensaje, $arreglo);
		$r = new respuesta_rest();
		$error->configurar_respuesta($r);

		$data = $r->get_data();
		$this->assertArrayNotHasKey('detalle', $data);

		$error = new rest_error($status, $mensaje);
		$error->configurar_respuesta($r);
		$data = $r->get_data();
		$this->assertArrayNotHasKey('detalle', $data);
	}
}
 