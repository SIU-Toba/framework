<?php

php_referencia::instancia()->agregar(__FILE__);

class ci_solapas extends toba_ci
{
    protected $pasadas_por_solapa;

    public function ini()
    {
        $this->pasadas_por_solapa = array('1' => 0, '2' => 0, '3' => 0);
        $this->set_propiedades_sesion(array('pasadas_por_solapa'));
    }

    /**
    *	El cuadro se carga con la cantidad de pasadas producidas hasta el momento
    */
    public function conf__cuadro_pasadas()
    {
        return array($this->pasadas_por_solapa);
    }

    /**
    *	Se registran la cantidad de pasadas cuando se entra a una solapa
    */
    public function evt__1__entrada()
    {
        $this->pasadas_por_solapa[1]++;
    }

    public function evt__2__entrada()
    {
        $this->pasadas_por_solapa[2]++;
    }

    public function evt__3__entrada()
    {
        $this->pasadas_por_solapa[3]++;
    }


}
