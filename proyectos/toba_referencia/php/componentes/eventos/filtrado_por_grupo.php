<?php

php_referencia::instancia()->agregar(__FILE__);

class filtrado_por_grupo extends toba_ci
{
    public function evt__limpiar()
    {
        $this->dep('formulario')->set_grupo_eventos_activo(null);
    }

    public function evt__i()
    {
        $this->dep('formulario')->set_grupo_eventos_activo('i');
        $this->dep('cuadro')->set_grupo_eventos_activo('i');
        $this->pantalla()->set_grupo_eventos_activo('i');
    }

    public function evt__ii()
    {
        $this->dep('formulario')->set_grupo_eventos_activo('ii');
        $this->dep('cuadro')->set_grupo_eventos_activo('ii');
        $this->pantalla()->set_grupo_eventos_activo('ii');
    }

    public function conf__cuadro()
    {
        $datos[0]['id'] = 'A';
        $datos[0]['desc'] = 'Desc. A';
        $datos[1]['id'] = 'B';
        $datos[1]['desc'] = 'Desc. B';
        return $datos;
    }

}
