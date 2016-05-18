<?php
/**
 * Usuario especial que se usa para el acceso público al sistema
 * @package Seguridad
 * @subpackage TiposUsuario
 */
class toba_usuario_no_autenticado extends toba_usuario
{

    const NO_AUTENTICADO = 'no_autentificado';

	function __construct()
	{
		parent::__construct(self::NO_AUTENTICADO);	
	}

	/**
	*	Retorna el identificador del usuario
	*/
	function get_id()
	{
		return self::NO_AUTENTICADO;
	}

	/**
	*	Retorna el nombre del usuario
	*/
	function get_nombre()
	{
		return 'Usuario no autentificado';
	}
}
?>