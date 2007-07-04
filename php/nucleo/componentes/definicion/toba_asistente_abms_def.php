<?php

class toba_asistente_abms_def extends toba_asistente_def
{
 	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_plan_operacion_abms',
								'registros' => '1',
								'obligatorio' => true );
		$estructura[] = array( 	'tabla' => 'apex_plan_operacion_abms_fila',
								'registros' => 'n',
								'obligatorio' => false );
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//------------- abms ----------------
		$sql['_info_abms']['sql'] = "SELECT			proyecto  							,
													plan								,
													tabla								,
													gen_usa_filtro						,
													gen_separar_pantallas				,
													cuadro_eof							,
													cuadro_id							,
													cuadro_eliminar_filas				,
													cuadro_datos_origen					,
													cuadro_datos_origen_ci_sql			,
													cuadro_datos_orgien_php_archivo		,
													cuadro_datos_orgien_php_clase		,
													cuadro_datos_orgien_php_metodo		,
													datos_tabla_validacion				,
													apdb_pre							
							 FROM		apex_plan_operacion_abms 
							 WHERE	proyecto='$proyecto' ";	
		if ( isset($componente) ) {
			$sql['_info_abms']['sql'] .= "	AND		plan='$componente' ";	
		}
		$sql['_info_abms']['sql'] .= ";";
		$sql['_info_abms']['registros']='1';
		$sql['_info_abms']['obligatorio']=true;
		//------------ Columnas ----------------
		$sql['_info_abms_fila']['sql'] = "SELECT	proyecto  							,
													plan								,
													fila								,
													orden								,
													columna        						,
													etiqueta       						,
													en_cuadro							,
													en_form								,
													en_filtro							,
													elemento_formulario					,
													ef_desactivar_modificacion			,
													ef_procesar_javascript				,
													ef_datos_origen						,
													ef_datos_origen_ci_sql				,
													ef_datos_orgien_php_archivo			,
													ef_datos_orgien_php_clase			,
													ef_datos_orgien_php_metodo			
										 FROM		apex_plan_operacion_abms_fila
										 WHERE	proyecto = '$proyecto' ";
		if ( isset($componente) ) {
			$sql['_info_abms_fila']['sql'] .= "	AND		plan='$componente' ";
		}
		$sql['_info_abms_fila']['sql'] .= " ORDER BY fila;";
		$sql['_info_abms_fila']['registros']='n';
		$sql['_info_abms_fila']['obligatorio']=true;
		return $sql;
	}
}
?>