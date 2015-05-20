<?php

namespace SIUToba\rest\lib;


class modelo_recursos
{
    /**
     * Recibe un arreglo de modelos.
     *
     * @param $models
     *
     * @return array
     */
    public function to_swagger($models)
    {
        $out = array();
        foreach ($models as $id_modelo => $m) {
            $nuevo = $this->to_swagger_modelo($id_modelo, $m);

            $out[$id_modelo] = $nuevo;
        }

        return $out;
    }

    protected function to_swagger_modelo($id, $modelo_in)
    {
        $required = array();
        $properties = array();

        foreach ($modelo_in as $campo => $def) {
            $this->get_property($properties, $campo, $def);
        }

//		$required[] = $campo;
        return $nuevo = array(
            'id' => $id,
            'required' => array_values($required),
            'properties' => $properties,
        );
    }

    protected function get_property(&$properties, $campo, $def)
    {
        $property = array();
        if (is_numeric($campo)) { //solo el campo  0=> nombre
            $campo = $def;
            $def = array();
        }

        //TODO, hacer mas modelos para representar estos subrecursos?
        if (isset($def['_compuesto'])) {
            $def = array('type' => $campo); //lo muestro asi por ahora
        }

        //	Defaults para los campos
        if (!isset($def['type'])) {
            $def['type'] = 'string';
        }

        //paso derecho los campos no especiales
        foreach ($def as $k => $campo_def) {
            if (strpos($k, '_') !== 0) {
                $property[$k] = $campo_def;
            }
        }
        $properties[$campo] = $property;
    }
}
