<?php

namespace SIUToba\rest\seguridad\autenticacion;

/**
 * Retorna los api_keys de los usuarios.
 * @package SIUToba\rest\seguridad\autenticacion
 */
interface usuarios_api_key
{
	function get_usuario_api_key($api_key);
}