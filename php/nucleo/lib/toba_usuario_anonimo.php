<?php
/**
 * Usuario especial que se usa para el acceso anónimo al sistema
 * @package Seguridad
 * @subpackage TiposUsuario
 */
class toba_usuario_anonimo extends toba_usuario
{
	function get_id()
	{
		return toba::proyecto()->get_parametro('usuario_anonimo');
	}

	function get_nombre()
	{
		return toba::proyecto()->get_parametro('usuario_anonimo_desc');
	}
	
	function get_perfiles_funcionales()
	{
		return toba::proyecto()->get_perfiles_funcionales_usuario_anonimo();
	}
}
?>