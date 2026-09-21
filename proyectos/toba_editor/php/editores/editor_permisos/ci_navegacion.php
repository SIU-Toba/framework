<?php

class ci_navegacion extends toba_ci
{
    protected $seleccion;
    protected $filtro = array();

    public function ini()
    {
        $props = array('seleccion', 'filtro');
        $this->set_propiedades_sesion($props);
    }

    /**
     * @return toba_datos_relacion
     */
    public function get_relacion()
    {
        return $this->dependencia('datos');
    }

    public function conf__listado()
    {
        $filtro = (isset($this->filtro)) ? $this->filtro : array();
        return toba_info_permisos::get_lista_permisos($filtro);
    }

    public function evt__listado__seleccion($id)
    {
        $this->seleccion = $id;
        $this->get_relacion()->cargar($this->seleccion);
        $this->set_pantalla('edicion');
    }


    public function evt__filtro__filtrar($datos)
    {
        $this->filtro = $datos;
    }

    public function conf__filtro()
    {
        if (isset($this->filtro)) {
            return $this->filtro;
        }
    }

    public function evt__filtro__cancelar()
    {
        unset($this->filtro);
    }

    public function evt__agregar()
    {
        $this->set_pantalla('edicion');
    }

    public function evt__cancelar()
    {
        $this->get_relacion()->resetear();
        parent::evt__cancelar();
        $this->set_pantalla('seleccion');
    }

    public function evt__guardar()
    {
        $this->get_relacion()->tabla('permiso')->set_columna_valor('proyecto', toba_editor::get_proyecto_cargado());
        $this->get_relacion()->sincronizar();
        $this->evt__cancelar();
    }

    public function evt__eliminar()
    {
        $this->get_relacion()->eliminar();
        $this->set_pantalla('seleccion');
    }

}
