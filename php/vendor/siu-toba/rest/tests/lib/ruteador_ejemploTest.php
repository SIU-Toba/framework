<?php

namespace SIUToba\rest\tests\lib;

use PHPUnit_Framework_MockObject_Mock;
use PHPUnit_Framework_TestCase;
use SIUToba\rest\lib\lector_recursos_archivo;
use SIUToba\rest\lib\rest_instanciador;
use SIUToba\rest\lib\ruteador;

class ruteador_ejemploTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ruteador
     */
    protected $ruteador;

    protected $lector_recursos;

    /**
     * @var rest_instanciador
     */
    protected $instanciador;

    protected $BASE_DIR;


    public function setUp()
    {
        $this->BASE_DIR = realpath(__DIR__ . '/../_ejemplo/rest');

        $this->instanciador = new rest_instanciador();

        $this->lector_recursos = new lector_recursos_archivo($this->BASE_DIR, 'recurso_');

        $this->ruteador = new ruteador($this->lector_recursos, $this->instanciador);
    }

    public function testError()
    {
        try {
            $this->ruteador->buscar_controlador('GET', 'z');
        } catch (\Exception $e) {
            $this->assertTrue(true);
            return;
        }
        $this->assertTrue(false);
    }

    public function testGetColeccion()
    {
        $this->ruteador->buscar_controlador('GET', 'a');


        $this->assertEquals($this->BASE_DIR . '/a/recurso_a.php', $this->instanciador->archivo);
        $this->assertEquals('get_list', $this->instanciador->accion);
        $this->assertEquals(array(), $this->instanciador->parametros);
    }

    public function testDeleteOne()
    {
        $this->ruteador->buscar_controlador('DELETE', 'a/id');

        $this->assertEquals($this->BASE_DIR . '/a/recurso_a.php', $this->instanciador->archivo);
        $this->assertEquals('delete', $this->instanciador->accion);
        $this->assertEquals(array('id'), $this->instanciador->parametros);
    }

    public function testGetSubrecursoEmbebido()
    {
        $this->ruteador->buscar_controlador('GET', 'a/id/b');

        $this->assertEquals($this->BASE_DIR . '/a/recurso_a.php', $this->instanciador->archivo);
        $this->assertEquals('get_b_list', $this->instanciador->accion);
        $this->assertEquals(array('id'), $this->instanciador->parametros);
    }

    public function testGetSubrecursoReal()
    {
        $this->ruteador->buscar_controlador('GET', 'a/ida/c');

        $this->assertEquals($this->BASE_DIR . '/a/c/recurso_c.php', $this->instanciador->archivo);
        $this->assertEquals('get_list', $this->instanciador->accion);
        $this->assertEquals(array('ida'), $this->instanciador->parametros);

        $this->ruteador->buscar_controlador('GET', 'a/ida/c/idc');

        $this->assertEquals($this->BASE_DIR . '/a/c/recurso_c.php', $this->instanciador->archivo);
        $this->assertEquals('get', $this->instanciador->accion);
        $this->assertEquals(array('ida', 'idc'), $this->instanciador->parametros);
    }

    public function testGetMontaje()
    {
        $this->ruteador->buscar_controlador('GET', 'montaje/d');

        $this->assertEquals($this->BASE_DIR . '/montaje/d/recurso_d.php', $this->instanciador->archivo);
        $this->assertEquals('get_list', $this->instanciador->accion);
        $this->assertEquals(array(), $this->instanciador->parametros);
    }

    public function testGetMontajeSubrec()
    {
        $this->ruteador->buscar_controlador('GET', 'montaje/d/id_d/b/id_b');

        $this->assertEquals($this->BASE_DIR . '/montaje/d/recurso_d.php', $this->instanciador->archivo);
        $this->assertEquals('get_b', $this->instanciador->accion);
        $this->assertEquals(array('id_d', 'id_b'), $this->instanciador->parametros);
    }

    public function testAliases()
    {

        $this->ruteador->buscar_controlador('GET', 'a/alias');

        $this->assertEquals($this->BASE_DIR . '/a/recurso_a.php', $this->instanciador->archivo);
        $this->assertEquals('get_list__alias', $this->instanciador->accion);
        $this->assertEquals(array(), $this->instanciador->parametros);
    }

    public function testGuiones()
    {

        $this->ruteador->buscar_controlador('GET', 'd-e/id-de');

        $this->assertEquals($this->BASE_DIR . '/d_e/recurso_d_e.php', $this->instanciador->archivo);
        $this->assertEquals('get', $this->instanciador->accion);
        $this->assertEquals(array('id-de'), $this->instanciador->parametros);


    }
    /*
        public function testQueryString()
        {
            $path = 'recurso?a=b';

            $recurso = array('recurso' => 'recurso', 'archivo' => 'recurso_clase');

            $parametros = array();
            $this->lector_recursos->expects($this->once())
                ->method('get_recurso')
                ->will($this->returnValue($recurso));
            $this->instanciador->expects($this->exactly(1))
                ->method('existe_metodo')
                ->will($this->onConsecutiveCalls(true));
            $rec = $this->ruteador->buscar_controlador('GET', $path);

            $this->assertEquals('recurso_clase', $rec->archivo);
            $this->assertEquals('get_list', $rec->accion);
            $this->assertEquals($parametros, $rec->parametros);
        }

        public function testGetRecurso()
        {
            $path = 'recurso/07';
            $parametros = array('07');

            $recurso = array('recurso' => 'recurso', 'archivo' => 'recurso_clase');

            $this->lector_recursos->expects($this->once())
                ->method('get_recurso')
                ->will($this->returnValue($recurso));
            $this->instanciador->expects($this->exactly(2))
                ->method('existe_metodo')
                ->will($this->onConsecutiveCalls(false, true));
            $rec = $this->ruteador->buscar_controlador('GET', $path);

            $this->assertEquals('recurso_clase', $rec->archivo);
            $this->assertEquals('get', $rec->accion);
            $this->assertEquals($parametros[0], $rec->parametros[0]);
        }

        public function testGetRecursoIdCero()
        {
            $path = 'recurso/0';
            $parametros = array('0');

            $recurso = array('recurso' => 'recurso', 'archivo' => 'recurso_clase');

            $this->lector_recursos->expects($this->once())
                ->method('get_recurso')
                ->will($this->returnValue($recurso));
            $this->instanciador->expects($this->exactly(2))
                ->method('existe_metodo')
                ->will($this->onConsecutiveCalls(false, true));
            $rec = $this->ruteador->buscar_controlador('GET', $path);

            $this->assertEquals('recurso_clase', $rec->archivo);
            $this->assertEquals('get', $rec->accion);
            $this->assertEquals($parametros[0], $rec->parametros[0]);
        }

        public function testGetSubrecursoPadre()
        {
            $path = 'padre/07/hijo';
            $parametros = array('07');

            $recurso = array('recurso' => 'padre', 'archivo' => 'padre_clase');

            $this->lector_recursos->expects($this->once())
                ->method('get_recurso')
                ->will($this->returnValue($recurso));
            $this->instanciador->expects($this->exactly(1))
                ->method('existe_metodo')
                ->will($this->onConsecutiveCalls(true));
            $rec = $this->ruteador->buscar_controlador('GET', $path);

            $this->assertEquals('padre_clase', $rec->archivo);
            $this->assertEquals('get_hijo_list', $rec->accion);
            $this->assertEquals($parametros, $rec->parametros);
        }

        public function testGetSubrecursoPadreComplejo()
        {
            $path = 'padre/07/hijo/12/nieto/abc';
            $parametros = array('07', '12', 'abc');

            $recurso = array('recurso' => 'hijo', 'archivo' => 'hijo_clase');

            $this->lector_recursos->expects($this->once())
                ->method('get_recurso')
                ->will($this->returnValue($recurso));
            $this->instanciador->expects($this->exactly(2))
                ->method('existe_metodo')
                ->will($this->onConsecutiveCalls(false, true));
            $rec = $this->ruteador->buscar_controlador('PUT', $path);

            $this->assertEquals('hijo_clase', $rec->archivo);
            $this->assertEquals('put_nieto', $rec->accion);
            $this->assertEquals($parametros, $rec->parametros);
        }


        public function testAliases()
        {
            $path = 'padre/alias';
            $parametros = array();

            $recurso = array('recurso' => 'padre', 'archivo' => 'padre_clase');

            $this->lector_recursos->expects($this->once())
                ->method('get_recurso')
                ->will($this->returnValue($recurso));

            $this->instanciador->expects($this->exactly(1))
                ->method('existe_metodo')
                ->will($this->onConsecutiveCalls(true));
    //            array('get($alias)', true),
    //            array('get_list__alias', true)

            $rec = $this->ruteador->buscar_controlador('GET', $path);

            $this->assertEquals('padre_clase', $rec->archivo);
            $this->assertEquals('get_list__alias', $rec->accion);
            $this->assertEquals($parametros, $rec->parametros);
        }

        public function testAliasesSubrec()
        {
            $path = 'padre/07/hijo/12/nieto/abc';
            $parametros = array('07', '12');

            $recurso = array('recurso' => 'hijo', 'archivo' => 'hijo_clase');

            $this->lector_recursos->expects($this->once())
                ->method('get_recurso')
                ->will($this->returnValue($recurso));

            $this->instanciador->expects($this->exactly(1))
                ->method('existe_metodo')
                ->will($this->onConsecutiveCalls(true));
    //            array('post_nieto($abc)', false),
    //            array('post_nieto_list__abc', true)

            $rec = $this->ruteador->buscar_controlador('POST', $path);

            $this->assertEquals('hijo_clase', $rec->archivo);
            $this->assertEquals('post_nieto_list__abc', $rec->accion);
            $this->assertEquals($parametros, $rec->parametros);
        }

        public function testError()
        {
            $path = 'recurso/07/';
            try {
                $this->ruteador->buscar_controlador('GET', $path);
            } catch (\Exception $e) {
                $this->assertTrue(true);

                return;
            }
            $this->assertTrue(false);
        }*/
}
