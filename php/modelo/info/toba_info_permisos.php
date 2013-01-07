<?php

class toba_info_permisos
{
	static function get_lista_permisos($condiciones = null)
	{
		if (isset($condiciones['proyecto'])) {
			$proyecto = $condiciones['proyecto'];
		} else {
			$proyecto = toba_contexto_info::get_proyecto();			
		}
		$db =  toba_contexto_info::get_db();
		$where = '';
		if ($condiciones != null) {
			if (isset($condiciones['nombre'])) {
				$nombre = $db->quote("%{$condiciones['nombre']}%");
				$where .= "AND	nombre ILIKE  $nombre";
			}
			if (isset($condiciones['descripcion'])) {
				$descr = $db->quote("%{$condiciones['descripcion']}%");
				$where .= "AND	descripcion ILIKE $descr ";
			}
		}

		$sql = "SELECT 	
					permiso,
					nombre,
					descripcion
				FROM apex_permiso 
				WHERE 
					proyecto = ".$db->quote($proyecto)."
					$where
				ORDER BY nombre, descripcion
		";
		return toba_contexto_info::get_db()->consultar($sql);
	}
	
	static function get_perfiles_funcionales($proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();	
		}
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT proyecto, usuario_grupo_acc, nombre
				FROM apex_usuario_grupo_acc
				WHERE 
					proyecto = $proyecto
				ORDER BY nombre, usuario_grupo_acc
		";
		return toba_contexto_info::get_db()->consultar($sql);
	}	
	
	static function get_perfiles_funcionales_miembros($proyecto, $perfil, $conexion = null)
	{
		if (! isset($conexion)) {
			$conexion = toba_contexto_info::get_db();
		}
		$proyecto = $conexion->quote($proyecto);
		$perfil = $conexion->quote($perfil);
		$sql = "SELECT usuario_grupo_acc_pertenece
				FROM apex_usuario_grupo_acc_miembros
				WHERE 
						proyecto = $proyecto
					AND	usuario_grupo_acc = $perfil
				ORDER BY usuario_grupo_acc_pertenece
		";
		return $conexion->consultar($sql);
	}	
		
	
	/**
	 * Retorna un listado de perfiles funcionales de los cuales este perfil puede ser miembro
	 */
	static function get_perfiles_funcionales_pueden_ser_miembros($proyecto, $perfil=null)
	{
		$where = '';
		if (isset($perfil)) {
			$perfil = toba_contexto_info::get_db()->quote($perfil);
			$where .= "AND usuario_grupo_acc != $perfil
			
			";
		}
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT proyecto, usuario_grupo_acc, nombre
				FROM apex_usuario_grupo_acc
				WHERE 
						proyecto = $proyecto
					$where
				ORDER BY nombre
		";
		return toba_contexto_info::get_db()->consultar($sql);
	}	
	
	/**
	 * @deprecated Desde 1.5 Usar get_perfiles_funcionales
	 */
	static function get_grupos_acceso($proyecto=null)
	{
		return self::get_perfiles_funcionales($proyecto);
	}
	
	static function get_perfiles_datos($proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();	
		}
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT proyecto, usuario_perfil_datos, nombre
				FROM apex_usuario_perfil_datos
				WHERE 
					proyecto = $proyecto
				ORDER BY nombre
		";
		return toba_contexto_info::get_db()->consultar($sql);
	}
	
	static function get_lista_usuarios($proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = toba_contexto_info::get_proyecto();	
		}
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT u.usuario as usuario, u.nombre as nombre
				FROM apex_usuario u, apex_usuario_proyecto up
				WHERE u.usuario = up.usuario
				AND up.proyecto = $proyecto;";
		return toba_contexto_info::get_db()->consultar($sql);
	}	

	static function get_restricciones_proyecto($proyecto)
	{
		$proyecto = toba_contexto_info::get_db()->quote($proyecto);
		$sql = "SELECT 	proyecto,
						restriccion_funcional,
						descripcion,
						((SELECT COUNT(*) FROM apex_restriccion_funcional_ef		WHERE restriccion_funcional = rf.restriccion_funcional) +
						(SELECT COUNT(*) FROM apex_restriccion_funcional_pantalla	WHERE restriccion_funcional = rf.restriccion_funcional) +
						(SELECT COUNT(*) FROM apex_restriccion_funcional_evt		WHERE restriccion_funcional = rf.restriccion_funcional) +
						(SELECT COUNT(*) FROM apex_restriccion_funcional_ei		 	WHERE restriccion_funcional = rf.restriccion_funcional) +
						(SELECT COUNT(*) FROM apex_restriccion_funcional_filtro_cols WHERE restriccion_funcional = rf.restriccion_funcional) +
						(SELECT COUNT(*) FROM apex_restriccion_funcional_cols	 	WHERE restriccion_funcional = rf.restriccion_funcional)) as cant_resticciones
						
				FROM 	apex_restriccion_funcional as rf
				WHERE 	proyecto = $proyecto
				ORDER BY descripcion
		";
		return toba_contexto_info::get_db()->consultar($sql);
	}	
	
	function get_lista_restricciones_proyecto()
	{
		$proyecto = quote($this->s__proyecto);
		$sql = "SELECT
					restriccion_funcional as restriccion,
					descripcion
				FROM
					apex_restriccion_funcional
				WHERE
					proyecto = $proyecto
				ORDER BY descripcion
				";
		return toba::db()->consultar($sql);
	}	
	

	
}


?>
