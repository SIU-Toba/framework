<?php

namespace rest\tests\lib;


use rest\lib\rest_filtro_sql;
use rest\rest;

class rest_filtro_sqlTest extends \PHPUnit_Framework_TestCase {

    protected $request;

    protected function setUp()
    {
        parent::setUp();
        $this->request = $this->get_mock_request();
    }


    public function testWhereIgual()
    {
        //$this->agregar_parametro_request($request, );


        $filtro = new rest_filtro_sql();
        $param = "nombre";
        $filtro->agregar_campo($param);

        $this->assertEquals("where", $filtro->get_sql_where());

    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function get_mock_request()
    {
        $r = new rest(array());
        $request = $this->getMockBuilder('rest\http\respuesta_rest')
            ->disableOriginalConstructor()
            ->getMock();
        return $request;
    }

    /**
     * @param $request
     */
    protected function agregar_parametro_request($request)
    {
        $request->expects($this->once())
            ->method('get')
            ->with($this->equalTo('something'))
            ->will($this->returnValue('recurso'));
    }
}
 