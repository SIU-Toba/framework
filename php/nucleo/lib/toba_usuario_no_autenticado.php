<?php
/**
 * Usuario especial que se usa para el acceso público al sistema
 * @package Seguridad
 * @subpackage TiposUsuario
 */
class toba_usuario_no_autenticado extends toba_usuario
{
	function __construct()
	{
		parent::__construct('no_autentificado');	
	}

	/**
	*	Retorna el identificador del usuario
	*/
	function get_id()
	{
		return 'no_autentificado';
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