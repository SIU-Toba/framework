<?php

class toba_proyecto_implementacion
{

	//-------------------------------------------------------------
	//------   PERFIL DE DATOS     --------------------------------
	//-------------------------------------------------------------

	/**
	*	Devuelve el perfil de datos de un usuario
	*/
	function get_perfiles_datos($proyecto)
	{
		$sql = "SELECT 	usuario_perfil_datos as 		perfil_datos,
						nombre as 						nombre
					FROM apex_usuario_perfil_datos
					WHERE proyecto = '$proyecto';";
		return toba::instancia()->get_db()->consultar($sql);
	}
		
	/**
	*	Devuelve el perfil de datos de un usuario
	*/
	function get_perfil_datos($usuario, $proyecto)
	{
		$sql = "SELECT up.usuario_perfil_datos as 		perfil_datos
					FROM apex_usuario_proyecto_perfil_datos up
					WHERE up.usuario = '$usuario'
					AND up.proyecto = '$proyecto';";
		$datos = toba::instancia()->get_db()->consultar_fila($sql);
		if(!$datos) return null;
		return $datos['perfil_datos'];
	}
	

	/**
	*	Devuelve las restricciones correspondientes al perfil de datos del usuario
	*/
	function get_perfil_datos_restricciones($proyecto, $perfil)
	{
		$sql = "SELECT 	d.usuario_perfil_datos as 		perfil_datos,
						d.dimension as 					dimension,
						d.clave as 						clave,
						di.fuente_datos as				fuente_datos
					FROM  	apex_usuario_perfil_datos_dims d,
							apex_dimension di
					WHERE 	d.dimension = di.dimension
						ANd	d.proyecto = di.proyecto
						AND d.usuario_perfil_datos = '$perfil'
						AND d.proyecto = '$proyecto'";
		return toba::instancia()->get_db()->consultar($sql);
	}

	//-------------------------------------------------------------
	//------   RESTRICCIONES FUNCIONALES  -------------------------
	//-------------------------------------------------------------
	
	/**
	 * Retorna los ids de las restricciones funcionales que aplican a un conjunto de grupos de acceso
	 */
	function get_restricciones_funcionales($grupos, $proyecto)
	{
		$sql_grupos = "'".implode("', '", $grupos)."'";
		$sql = "SELECT 	rf.restriccion_funcional
			FROM 
				apex_grupo_acc_restriccion_funcional rf
			WHERE
					rf.usuario_grupo_acc IN($sql_grupos)
				AND	proyecto = '$proyecto'
		";
		$datos = toba::instancia()->get_db()->consultar($sql);
		return aplanar_matriz($datos, 'restriccion_funcional');
	}

	function get_rf_pantallas($proyecto, $item, $rf)
	{
		$sql_rf = implode(', ', $rf);
		$sql = "SELECT 
					pantalla, 
					objeto_ci,
					no_visible
				FROM apex_restriccion_funcional_pantalla
				WHERE
						proyecto = '$proyecto'
					AND	restriccion_funcional IN ($sql_rf)
					AND	item = '$item'
		";
		return toba::instancia()->get_db()->consultar($sql);
	}
	
	function get_rf_eis($proyecto, $item, $rf)
	{
		$sql_rf = implode(', ', $rf);
		$sql = "SELECT 
					objeto, 
					no_visible
				FROM apex_restriccion_funcional_ei
				WHERE
						proyecto = '$proyecto'
					AND	restriccion_funcional IN ($sql_rf)
					AND	item = '$item'
		";
		return toba::instancia()->get_db()->consultar($sql);
	}
	
	function get_rf_eventos($proyecto, $item, $rf)
	{
		$sql_rf = implode(', ', $rf);
		$sql = "SELECT 
					evento_id, 
					no_visible
				FROM apex_restriccion_funcional_evt
				WHERE
						proyecto = '$proyecto'
					AND	restriccion_funcional IN ($sql_rf)
					AND	item = '$item'
		";
		return toba::instancia()->get_db()->consultar($sql);
	}

	
	function get_rf_cuadro_cols($proyecto, $item, $rf)
	{
		$sql_rf = implode(', ', $rf);
		$sql = "SELECT 
					objeto_cuadro,
					objeto_cuadro_col,
					no_visible
				FROM apex_restriccion_funcional_cols
				WHERE
						proyecto = '$proyecto'
					AND	restriccion_funcional IN ($sql_rf)
					AND	item = '$item'
		";
		return toba::instancia()->get_db()->consultar($sql);
	}	
	
	function get_rf_form_efs($proyecto, $item, $rf)
	{
		$sql_rf = implode(', ', $rf);
		$sql = "SELECT 
					objeto_ei_formulario,
					objeto_ei_formulario_fila,
					no_visible,
					no_editable
				FROM apex_restriccion_funcional_ef
				WHERE
						proyecto = '$proyecto'
					AND	restriccion_funcional IN ($sql_rf)
					AND	item = '$item'
		";
		return toba::instancia()->get_db()->consultar($sql);		
	}
	
	function get_rf_filtro_cols($proyecto, $item, $rf)
	{
		$sql_rf = implode(', ', $rf);
		$sql = "SELECT 
					objeto_ei_filtro,
					objeto_ei_filtro_col,
					no_visible
				FROM apex_restriccion_funcional_filtro_cols
				WHERE
						proyecto = '$proyecto'
					AND	restriccion_funcional IN ($sql_rf)
					AND	item = '$item'
		";
		return toba::instancia()->get_db()->consultar($sql);		
	}	
	
}

?>