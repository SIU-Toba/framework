<?php

require_once 'lib/rest/toba/rest_test_case.php';

class personasTest extends \rest\toba\rest_test_case {

    public function testAlgo(){
        $user = new \rest\seguridad\rest_usuario();
        $user->set_usuario('user');
        $this->mock_autenticador($user);
        $host = "/toba_referencia/trunk/";

        $this->ejecutar('GET', $host.'rest/personas');

        $this->assertTrue(true);
    }

} 