<?php

class odt_dr_asociac extends toba_datos_tabla
{
    /**
     * Valida que una relación tenga al menos un par de columnas asociadas
     */
    public function evt__validar_fila($fila)
    {
        $ok = true;
        if (isset($fila['padre_clave']) && isset($fila['hijo_clave'])) {
            if ($fila['padre_clave'] == '' || $fila['hijo_clave'] == '') {
                $ok = false;
            }
        } else {
            $ok = false;
        }
        if (!$ok) {
            $mensaje = 'Debe especificar al menos una asociación de columnas en la '.
                        "relación entre {$fila['padre_id']} y {$fila['hijo_id']}";
            throw new toba_error($mensaje);
        }
    }

}
