<?php

namespace rest\seguridad\autenticacion;

/**
 * Retorna los passswords de los usuarios. Esta clase está pensada para http digest,
 * que requiere si o si el password plano para poder pasarlo por su algoritmo.
 * USAR SOLO SI NO ES POSIBLE PASAR LA CONEXION POR HTTPS, sino usar basic.
 * @package rest\seguridad\autenticacion
 */
interface usuarios_password
{

	/**
	 * Dado el username, retorna el password para ser comparado.
	 * @param $usuario
	 * @return mixed string\null. El password o NULL si el usuario no existe
	 */
	function get_password($usuario);
}