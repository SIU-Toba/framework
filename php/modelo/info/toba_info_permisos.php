<?php

class toba_info_permisos
{
	static function get_lista_permisos($condiciones = null)
	{
		$where = '';
		if ($condiciones != null) {
			if (isset($condiciones['nombre'])) {
				$where .= "AND	nombre ILIKE '%{$condiciones['nombre']}%' ";
			}
			if (isset($condiciones['descripcion'])) {
				$where .= "AND	descripcion ILIKE '%{$condiciones['descripcion']}%' ";
			}
		}
		$sql = "SELECT 	
					permiso,
					nombre,
					descripcion
				FROM apex_permiso 
				WHERE 
					proyecto = '". toba_contexto_info::get_proyecto() ."'
					$where
				ORDER BY nombre, descripcion
		";
		return toba_contexto_info::get_db()->consultar($sql);
	}
	
	static function get_grupos_acceso($proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();	
		}
		$sql = "SELECT proyecto, usuario_grupo_acc, nombre
				FROM apex_usuario_grupo_acc
				WHERE 
					proyecto = '$proyecto'
				ORDER BY nombre
		";
		return toba_contexto_info::get_db()->consultar($sql);
	}
	
	static function get_perfiles_datos($proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();	
		}
		$sql = "SELECT proyecto, usuario_perfil_datos, nombre
				FROM apex_usuario_perfil_datos
				WHERE 
					proyecto = '$proyecto'
				ORDER BY nombre
		";
		return toba_contexto_info::get_db()->consultar($sql);
	}
	
	static function get_lista_usuarios($proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();	
		}
		$sql = "SELECT u.usuario as usuario, u.nombre as nombre
				FROM apex_usuario u, apex_usuario_proyecto up
				WHERE u.usuario = up.usuario
				AND up.proyecto = '$proyecto';";
		return toba_contexto_info::get_db()->consultar($sql);
	}	

}


?>
