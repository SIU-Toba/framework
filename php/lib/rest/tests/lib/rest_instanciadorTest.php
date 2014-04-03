<?php

namespace rest\tests\lib;

use rest\lib\rest_instanciador;

class rest_instanciadorTest extends \PHPUnit_Framework_TestCase
{


	function test_instanciacion_global()
	{
		$recurso = new rest_instanciador();

		$recurso->archivo = realpath(__DIR__."/../../../toba_fecha.php");

		$objeto = $recurso->get_instancia(false);

		$this->assertTrue(is_object($objeto), "No es un objeto");
		$this->assertTrue($objeto instanceof \toba_fecha);

		return $recurso;
	}

	function test_instanciacion_namespace()
	{
		$recurso = new rest_instanciador();

		$recurso->archivo = realpath(__DIR__."/../../lib/rest_instanciador.php");

		$objeto = $recurso->get_instancia(false);

		$this->assertTrue($objeto instanceof rest_instanciador);
	}

	/**
	 * @depends test_instanciacion_global
	 */
	function test_accion(rest_instanciador $recurso)
	{

		$recurso->accion = 'get_fecha_pantalla';

		$fecha = $recurso->ejecutar_accion();

		$this->assertTrue(is_string($fecha), "No ejecuta la accion");
	}


	/**
	 * @depends test_instanciacion_global
	 */
	function test_accion_parametros(rest_instanciador $recurso)
	{

		$ts = 12354;
		$recurso->accion = 'set_timestamp';
		$recurso->parametros = array($ts);

		$fecha = $recurso->ejecutar_accion();
		$objeto = $recurso->get_instancia();

		$this->assertTrue($objeto->get_fecha_desplazada(0) == $ts);
	}
}
