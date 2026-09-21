<?php

require_once('configuracion/ci_abm_basico.php');

class ci_mensajes_componentes extends ci_abm_basico
{
    protected $s__filtro;

    public function ini()
    {
        if (! toba::zona()->cargada()) {
            throw new toba_error('Necesita seleccionar un componente.');
        }
    }

    public function get_id_objeto()
    {
        $editable = toba::zona()->get_editable();
        return $editable[1];
    }

    public function get_datos_listado()
    {
        $clausulas = array();
        if (isset($this->s__filtro)) {
            $clausulas = $this->dep('filtro')->get_sql_clausulas();
        }
        $clausulas[] = 'objeto_proyecto = '. quote(toba_contexto_info::get_proyecto());
        $clausulas[] = 'objeto = '. quote($this->get_id_objeto());
        return toba_info_editores::get_mensajes_objeto_filtrados($clausulas);
    }


    //-----------------------------------------------------------------------------------
    //---- Eventos ----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    public function evt__agregar()
    {
        $this->set_pantalla('pant_edicion');
    }

    //-----------------------------------------------------------------------------------
    //---- filtro -----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    public function conf__filtro(toba_ei_filtro $filtro)
    {
        if (isset($this->s__filtro)) {
            $filtro->set_datos($this->s__filtro);
        }
    }

    public function evt__filtro__filtrar($datos)
    {
        $this->s__filtro = $datos;
    }

    public function evt__filtro__cancelar()
    {
        unset($this->s__filtro);
    }

    //-----------------------------------------------------------------------------------
    //---- formulario -------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    public function evt__formulario__alta($datos)
    {
        $datos['objeto'] = $this->get_id_objeto();
        $datos['objeto_proyecto'] = toba_editor::get_proyecto_cargado();
        parent::evt__formulario__alta($datos);
        $this->set_pantalla('pant_inicial');
    }

    public function evt__formulario__baja()
    {
        parent::evt__formulario__baja();
        $this->set_pantalla('pant_inicial');
    }

    public function evt__formulario__modificacion($datos)
    {
        parent::evt__formulario__modificacion($datos);
        $this->set_pantalla('pant_inicial');
    }

    public function evt__formulario__cancelar()
    {
        parent::evt__formulario__cancelar();
        $this->set_pantalla('pant_inicial');
    }

    //-----------------------------------------------------------------------------------
    //---- cuadro -----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    public function evt__cuadro__seleccion($seleccion)
    {
        $this->dependencia('datos')->cargar($seleccion);
        $this->set_pantalla('pant_edicion');
    }

}
