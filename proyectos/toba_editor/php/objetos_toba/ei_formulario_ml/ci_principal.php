<?php

require_once('objetos_toba/ci_editores_toba.php');

class ci_principal extends ci_editores_toba
{
    protected $clase_actual = 'toba_ei_formulario_ml';

    public function ini()
    {
        parent::ini();
        $ef = toba::memoria()->get_parametro('ef');
        //¿Se selecciono un ef desde afuera?
        if (isset($ef)) {
            $this->set_pantalla(2);
            $this->dependencia('efs')->seleccionar_ef($ef);
        }
    }

    public function evt__procesar()
    {
        //---Se valida si tiene agregar/quitar en php que tenga un evento implicito
        $agrega_filas = $es_js = $this->get_entidad()->tabla('prop_basicas')->get_columna('filas_agregar');
        $es_js = $this->get_entidad()->tabla('prop_basicas')->get_columna('filas_agregar_online');
        $hay_implicito = $this->get_dbr_eventos()->hay_evento_implicito_maneja_datos();
        if ($agrega_filas && ! $es_js && !$hay_implicito) {
            toba::notificacion()->agregar('Se ha seleccionada <strong>Agregar/Quitar líneas en el server</strong>
				pero no se ha definido ningún evento implícito que maneje datos.<br><br>
				Para que este comportamiento funcione debe generar el 
				[wiki:Referencia/Eventos#Modelos modelo de eventos] <em>Básico</em> en la solapa
				de Eventos', 'info');
        } elseif (! $this->get_dbr_eventos()->hay_evento_maneja_datos()) {
            toba::notificacion()->agregar('El formulario no posee evento que <strong>maneje datos</strong>,
				esto implica que los datos no viajaran del cliente al servidor.<br><br>
				Para que este comportamiento funcione debe generar algún 
				[wiki:Referencia/Eventos#Modelos modelo de eventos] en la solapa
				de Eventos', 'info');

        }
        parent::evt__procesar();
    }

    //*******************************************************************
    //*****************  PROPIEDADES BASICAS  ***************************
    //*******************************************************************

    public function conf__prop_basicas()
    {
        $datos = $this->get_entidad()->tabla('prop_basicas')->get();
        $datos['posicion_botonera'] = $this->get_entidad()->tabla('base')->get_columna('posicion_botonera');
        return $datos;
    }

    public function evt__base__modificacion($datos)
    {
        $this->get_entidad()->tabla('base')->set($datos);
    }

    public function evt__prop_basicas__modificacion($datos)
    {
        if (! $datos['filas_ordenar']) {
            $datos['columna_orden'] = '';
        }
        $this->get_entidad()->tabla('base')->set_columna_valor('posicion_botonera', $datos['posicion_botonera']);
        unset($datos['posicion_botonera']);
        $this->get_entidad()->tabla('prop_basicas')->set($datos);

    }

    //*******************************************************************
    //** Dialogo con el CI de EFs  **************************************
    //*******************************************************************

    public function evt__2__salida()
    {
        $this->dependencia('efs')->limpiar_seleccion();
    }

    public function get_dbr_efs()
    {
        return $this->get_entidad()->tabla('efs');
    }


    //*******************************************************************
    //** Dialogo con el CI de EVENTOS  **********************************
    //*******************************************************************

    public function get_eventos_estandar($modelo)
    {
        return toba_ei_formulario_ml_info::get_lista_eventos_estandar($modelo);
    }

    public function evt__3__salida()
    {
        $this->dependencia('eventos')->limpiar_seleccion();
    }

    public function get_dbr_eventos()
    {
        return $this->get_entidad()->tabla('eventos');
    }
}
