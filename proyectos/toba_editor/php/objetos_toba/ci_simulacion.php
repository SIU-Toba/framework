<?php

class ci_simulacion extends toba_ci
{
    protected $s__datos;

    public function ini()
    {
        $zona = toba::solicitud()->zona();
        if ($editable = $zona->get_editable()) {
            $componente['proyecto'] = $editable[0];
            $componente['componente'] = $editable[1];
        } else {
            throw new toba_error('Este item se utiliza desde la zona de objetos');
        }
        toba_editor::iniciar_contexto_proyecto_cargado();
        $this->agregar_dependencia('componente', $componente['proyecto'], $componente['componente']);
    }

    public function conf()
    {
        $this->pantalla()->agregar_dep('componente');
    }

    public function conf__componente($obj)
    {
        if ($obj instanceof toba_ei_formulario) {
            $this->configurar_form($obj);
        } elseif ($obj instanceof toba_ei_cuadro) {
            $this->configurar_cuadro($obj);
        } else {
            throw new toba_error('No es posible previsualizar el componente seleccionado.');
        }
    }

    public function configurar_form($obj)
    {
    }

    public function configurar_cuadro($obj)
    {
        $columnas = $obj->get_columnas();
        $estructura = $obj->get_estructura_datos();
        $muestra = array();
        foreach ($columnas as $id => $columna) {
            $muestra[$id] = $id;
        }
        foreach ($estructura as $columna) {
            $muestra[$columna] = $columna;
        }
        $obj->set_datos(array($muestra));
    }

    // Para manejar DAOS
    public function __call($metodo, $parametros)
    {
        return array();
    }

}
