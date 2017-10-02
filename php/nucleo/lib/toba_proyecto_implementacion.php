<?php

class toba_proyecto_implementacion
{

	//-------------------------------------------------------------
	//------   PERFIL DE DATOS     --------------------------------
	//-------------------------------------------------------------

	/**
	*	Devuelve la lista de perfiles de datos del proyecto
	*/
	static function get_perfiles_datos($proyecto)
	{
		$db = toba::instancia()->get_db();
		$proyecto = $db->quote($proyecto);
		$sql = "SELECT 	usuario_perfil_datos as 		perfil_datos,
						nombre as 						nombre
					FROM apex_usuario_perfil_datos
					WHERE proyecto = $proyecto
		";
		return $db->consultar($sql);
	}

	/**
	*	Devuelve la definicion de un perfil de datos
	*/
	static function get_info_perfiles_datos($proyecto, $perfil)
	{
		$db = toba::instancia()->get_db();
		$proyecto = $db->quote($proyecto);		
		$perfil = $db->quote($perfil);
		$sql = "SELECT 	*
					FROM apex_usuario_perfil_datos
					WHERE proyecto = $proyecto
					AND	usuario_perfil_datos = $perfil
		";
		return $db->consultar_fila($sql);
	}
		
	/**
	*	Devuelve el perfil de datos de un usuario
	* @deprecated 3.0.0
	* @see toba_proyecto_implementacion::get_perfiles_datos_usuario()
	*/	 	
	static function get_perfil_datos($usuario, $proyecto)
	{
		$datos = self::get_perfiles_datos_usuario($usuario, $proyecto);
		if(empty($datos)) {
			$result = null;
		} else {
			$anx = current($datos);
			$result = $anx['perfil_datos'];
		}			
		return $result;
	}
	
	/**
	 * Devuelve los perfiles de datos del usuario en el proyecto
	 * @param string $usuario
	 * @param string $proyecto
	 * @return array
	 */
	static function get_perfiles_datos_usuario($usuario, $proyecto)
	{
		$db = toba::instancia()->get_db();
		$proyecto = $db->quote($proyecto);		
		$usuario = $db->quote($usuario);		
		$sql = "SELECT up.usuario_perfil_datos as 		perfil_datos
					FROM apex_usuario_proyecto_perfil_datos up
					WHERE up.usuario = $usuario
					AND up.proyecto = $proyecto ";
		$datos = $db->consultar($sql);
		return $datos;
	}

	/**
	*	Devuelve las restricciones correspondientes al perfil de datos del usuario
	* @param string $proyecto
	* @param mixed $perfil  Id de perfil o arreglo de ids.
	* @return array
	*/
	static function get_perfil_datos_restricciones($proyecto, $perfil)
	{
		$db = toba::instancia()->get_db();
		$proyecto = $db->quote($proyecto);		
		$perfil = $db->quote($perfil);		
		$sql = "SELECT 	d.usuario_perfil_datos as 		perfil_datos,
						d.dimension as 					dimension,
						d.clave as 						clave,
						di.fuente_datos as				fuente_datos
					FROM  	apex_usuario_perfil_datos_dims d,
							apex_dimension di
					WHERE 	d.dimension = di.dimension
						ANd	d.proyecto = di.proyecto						
						AND d.proyecto = $proyecto
		";
		if (is_array($perfil)) {
			$sql .= 'AND d.usuario_perfil_datos IN (' . implode(',', $perfil) . ')';
		} else {
			$sql .= "AND d.usuario_perfil_datos = $perfil";
		}		
		
		return $db->consultar($sql);
	}

	//-------------------------------------------------------------
	//------   RESTRICCIONES FUNCIONALES  -------------------------
	//-------------------------------------------------------------
	
	/**
	 * Retorna los ids de las restricciones funcionales que aplican a un conjunto de grupos de acceso
	 */
	static function get_restricciones_funcionales($grupos, $proyecto)
	{
		if (empty($grupos)) {
			return array();
		}
		$db = toba::instancia()->get_db();
		$proyecto = $db->quote($proyecto);	
		$grupos = $db->quote($grupos); 
		$sql_grupos = implode(", ", $grupos);
		$sql = "SELECT 	rf.restriccion_funcional
			FROM 
				apex_grupo_acc_restriccion_funcional rf
			WHERE
					rf.usuario_grupo_acc IN($sql_grupos)
				AND	proyecto = $proyecto
		";
		$datos = $db->consultar($sql);
		return aplanar_matriz($datos, 'restriccion_funcional');
	}

	static function get_rf_pantallas($proyecto, $item, $rf)
	{
		$db = toba::instancia()->get_db();
		$proyecto = $db->quote($proyecto);			
		$item = $db->quote($item);
		$rf = $db->quote($rf);
		$sql_rf = implode(', ', $rf);
		$sql = "SELECT 
					pantalla, 
					objeto_ci,
					no_visible
				FROM apex_restriccion_funcional_pantalla
				WHERE
						proyecto = $proyecto
					AND	restriccion_funcional IN ($sql_rf)
					AND	item = $item
		";
		return $db->consultar($sql);
	}
	
	static function get_rf_eis($proyecto, $item, $rf)
	{
		$db = toba::instancia()->get_db();
		$proyecto = $db->quote($proyecto);			
		$item = $db->quote($item);
		$rf = $db->quote($rf);		
		$sql_rf = implode(', ', $rf);
		$sql = "SELECT 
					objeto, 
					no_visible
				FROM apex_restriccion_funcional_ei
				WHERE
						proyecto = $proyecto
					AND	restriccion_funcional IN ($sql_rf)
					AND	item = $item
		";
		return $db->consultar($sql);
	}
	
	static function get_rf_eventos($proyecto, $item, $rf)
	{
		$db = toba::instancia()->get_db();
		$proyecto = $db->quote($proyecto);			
		$item = $db->quote($item);
		$rf = $db->quote($rf);		
		$sql_rf = implode(', ', $rf);
		$sql = "SELECT 
					evento_id, 
					no_visible
				FROM apex_restriccion_funcional_evt
				WHERE
						proyecto = $proyecto
					AND	restriccion_funcional IN ($sql_rf)
					AND	item = $item
		";
		return $db->consultar($sql);
	}

	
	static function get_rf_cuadro_cols($proyecto, $item, $rf)
	{
		$db = toba::instancia()->get_db();
		$proyecto = $db->quote($proyecto);			
		$item = $db->quote($item);
		$rf = $db->quote($rf);				
		$sql_rf = implode(', ', $rf);
		$sql = "SELECT 
					objeto_cuadro,
					objeto_cuadro_col,
					no_visible
				FROM apex_restriccion_funcional_cols
				WHERE
						proyecto = $proyecto
					AND	restriccion_funcional IN ($sql_rf)
					AND	item = $item
		";
		return $db->consultar($sql);
	}	
	
	static function get_rf_form_efs($proyecto, $item, $rf)
	{
		$db = toba::instancia()->get_db();
		$proyecto = $db->quote($proyecto);			
		$item = $db->quote($item);
		$rf = $db->quote($rf);				
		$sql_rf = implode(', ', $rf);
		$sql = "SELECT 
					objeto_ei_formulario,
					objeto_ei_formulario_fila,
					no_visible,
					no_editable
				FROM apex_restriccion_funcional_ef
				WHERE
						proyecto = $proyecto
					AND	restriccion_funcional IN ($sql_rf)
					AND	item = $item
		";
		return $db->consultar($sql);		
	}
	
	static function get_rf_filtro_cols($proyecto, $item, $rf)
	{
		$db = toba::instancia()->get_db();
		$proyecto = $db->quote($proyecto);			
		$item = $db->quote($item);
		$rf = $db->quote($rf);				
		$sql_rf = implode(', ', $rf);
		$sql = "SELECT 
					objeto_ei_filtro,
					objeto_ei_filtro_col,
					no_visible
				FROM apex_restriccion_funcional_filtro_cols
				WHERE
						proyecto = $proyecto
					AND	restriccion_funcional IN ($sql_rf)
					AND	item = $item
		";
		return $db->consultar($sql);		
	}	
	
}

?>