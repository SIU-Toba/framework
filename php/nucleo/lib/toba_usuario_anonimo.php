<?php
/**
 * Usuario ANONIMO
 * @package Centrales
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
	
	function get_grupo_acceso()
	{
		return toba::proyecto()->get_grupo_acceso_usuario_anonimo();
	}
}
?>