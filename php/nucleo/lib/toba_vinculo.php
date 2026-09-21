<?php

/**
 * Mantiene un vinculo especifico y brinda una api para poder manipularlo
 * @package Centrales
 * @jsdoc vinculador vinculador
 */
class toba_vinculo
{
    private $item;
    private $proyecto;
    private $parametros;// = array();
    private $opciones;// = array();
    private $target;
    private $popup = 0;
    private $popup_parametros = array();
    private $popup_parametros_validos = array('width','height','scrollbars','resizable');
    private $ajax = false;

    public function __construct($proyecto = null, $item = null, $popup = null, $opciones_popup = null)
    {
        $this->set_item($proyecto, $item);
        if (! is_null($popup) && $popup) {
            $this->activar_popup();
        }
        if (isset($opciones_popup)) {
            //Parseo del formato actual de definicion
            $temp = explode(',', $opciones_popup);
            $temp = array_map('trim', $temp);
            foreach ($temp as $opcion) {
                $o = explode(':', $opcion);
                $o = array_map('trim', $o);
                $popup_parametros[$o[0]] = $o[1];
            }
            $this->set_popup_parametros($popup_parametros);
        }
    }

    /**
     * Cambia la operación destino del vinculo
     */
    public function set_item($proyecto, $item)
    {
        $this->item = $item;
        $this->proyecto = $proyecto;
    }

    public function get_item()
    {
        return $this->item;
    }

    public function get_proyecto()
    {
        return $this->proyecto;
    }

    /**
     * Cambia los parametros de la URL generada por el vinculo
     */
    public function set_parametros($parametros)
    {
        $this->parametros = $parametros;
    }

    /**
     * Agrega parametros a la URL generada por el vinculo
     */
    public function agregar_parametro($clave, $valor)
    {
        $this->parametros[$clave] = $valor;
    }

    /**
     * Agrega a la URL generado un parámetro que carga automáticamente la zona de la operación destino del vínculo
     * @param mixed $editable Valor común de los items a cargar en la zona
     * @see toba_zona
     */
    public function set_editable_zona($editable)
    {
        $this->parametros[apex_hilo_qs_zona] = toba::vinculador()->variable_a_url($editable);
    }

    /**
     * Determina si el vinculo actual propaga el editable de la zona (si tiene zona y esta cargada)
     * @param boolean $propagar
     * @see toba_zona
     */
    public function set_propagar_zona($propagar = true)
    {
        $this->opciones['zona'] = $propagar;
    }

    public function get_parametros()
    {
        return $this->parametros;
    }

    public function set_opciones($datos)
    {
        $this->opciones = $datos;
    }

    public function get_opciones()
    {
        return $this->opciones;
    }

    public function agregar_opcion($clave, $valor)
    {
        $this->opciones[$clave] = $valor;
    }

    public function activar_popup($celda_popup = false)
    {
        $this->popup = 1;
        if ($celda_popup !== false) {
            $this->opciones['celda_memoria'] = 'popup';
        }
    }

    public function desactivar_popup()
    {
        $this->popup = 0;
    }

    public function estado_popup()
    {
        return $this->popup;
    }

    public function set_popup_parametros($parametros, $anexar = false)
    {
        if (! $anexar) {
            $this->popup_parametros = $parametros;
        } else {
            $this->popup_parametros = array_merge($parametros, $this->popup_parametros);
        }
    }

    public function set_popup_parametro($clave, $valor)
    {
        $this->popup_parametros[$clave] = $valor;
    }

    public function get_popup_parametros()
    {
        return $this->popup_parametros;
    }

    public function set_target($id)
    {
        $this->target = $id;
    }

    public function get_target()
    {
        return $this->target ?? '';
    }

    public function set_servicio($srv)
    {
        $this->opciones['servicio'] = $srv;
    }

    public function set_celda_memoria($celda)
    {
        $this->opciones['celda_memoria'] = $celda;
    }

    public function set_id_ventana_popup($id)
    {
        if ($this->popup === 1) {
            $this->opciones['nombre_ventana' ] = $id;
        }
    }

    public function get_id_ventana_popup()
    {
        if ($this->popup === 1 && isset($this->opciones['nombre_ventana'])) {
            return $this->opciones['nombre_ventana'];
        }
        return null;
    }


    /**
     * Cambia la forma de comunicar el vinculo, en lugar de hacerse cambiando la url, se comunica via ajax
     */
    public function set_ajax($es_ajax)
    {
        $this->ajax = $es_ajax;
    }

    /**
     * Retorna si el vínculo se comunica via ajax
     */
    public function get_ajax()
    {
        return $this->ajax;
    }
}
