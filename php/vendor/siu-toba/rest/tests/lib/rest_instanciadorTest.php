<?php

namespace SIUToba\rest\tests\lib;

use ClassA;
use SIUToba\rest\lib\rest_instanciador;

class rest_instanciadorTest extends \PHPUnit_Framework_TestCase
{
    public function test_instanciacion_global()
    {
        $recurso = new rest_instanciador();

        $recurso->archivo = realpath(__DIR__."/ClassA.php");

        $objeto = $recurso->get_instancia(true);

        $this->assertTrue(is_object($objeto), "No es un objeto");
        $this->assertTrue($objeto instanceof ClassA);

        return $recurso;
    }

    public function test_instanciacion_namespace()
    {
        $recurso = new rest_instanciador();

        $recurso->archivo = realpath(__DIR__."/../../src/SIUToba/rest/lib/rest_instanciador.php");

        $objeto = $recurso->get_instancia(false);

        $this->assertTrue($objeto instanceof rest_instanciador);
    }

    /**
     * @depends test_instanciacion_global
     */
    public function test_accion(rest_instanciador $recurso)
    {
        $recurso->accion = 'metodo';

        $resultado = $recurso->ejecutar_accion();

        $this->assertEquals("Exito", $resultado, "No ejecuta la accion");
    }

    /**
     * @depends test_instanciacion_global
     */
    public function test_accion_parametros(rest_instanciador $recurso)
    {
        $ts = 12354;
        $recurso->accion = 'metodoEco';
        $recurso->parametros = array($ts);

        $resultado = $recurso->ejecutar_accion();

        $this->assertEquals($ts, $resultado);
    }
}
