<?php

namespace SIUToba\TobaUsuarios\lib;

/**
 * Interface minima a implementar para la operacion del ABM de Usuarios
 */
interface InterfaseApiUsuarios
{
    public function get_usuarios($filtro=array(), $excluir_aplicacion = null);

    public function get_usuario($identificador);

    public function get_cuenta($identificador_aplicacion, $cuenta);

    public function agregar_cuenta($identificador_aplicacion, $datos_cuenta);

    public function eliminar_cuenta($identificador_aplicacion, $cuenta);

    public function get_nombre_apellido_usuario($identificador);

    public function get_identificador_x_aplicacion_cuenta($identificador_aplicacion, $cuenta);
}
