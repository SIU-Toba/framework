<?php

class test_ejemplo_caso extends toba_test
{
    public static function get_descripcion()
    {
        return "Ejemplo de caso de test";
    }

    public function test_algo()
    {
        $this->AssertEqual(1 + 1, 2);
    }

}
