<?php

class toba_asistente_abms_def extends toba_asistente_def
{
 	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_molde_operacion_abms',
								'registros' => '1',
								'obligatorio' => true );
		$estructura[] = array( 	'tabla' => 'apex_molde_operacion_abms_fila',
								'registros' => 'n',
								'obligatorio' => false );
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//------------- abms ----------------
		$sql['molde_abms']['sql'] = "SELECT			proyecto  							,
													molde								,
													tabla								,
													gen_usa_filtro						,
													gen_separar_pantallas				,
													cuadro_eof							,
													cuadro_id							,
													cuadro_eliminar_filas				,
													cuadro_datos_origen					,
													cuadro_datos_origen_ci_sql			,
													cuadro_datos_origen_php_include		,
													cuadro_datos_origen_php_clase		,
													cuadro_datos_origen_php_metodo		,
													datos_tabla_validacion				,
													apdb_pre							
							 FROM		apex_molde_operacion_abms 
							 WHERE	proyecto='$proyecto' ";	
		if ( isset($componente) ) {
			$sql['molde_abms']['sql'] .= "	AND		molde='$componente' ";	
		}
		$sql['molde_abms']['sql'] .= ";";
		$sql['molde_abms']['registros']='1';
		$sql['molde_abms']['obligatorio']=true;
		//------------ Columnas ----------------
		$sql['molde_abms_fila']['sql'] = "SELECT	proyecto  							,
													molde								,
													fila								,
													orden								,
													columna        						,
													etiqueta       						,
													en_cuadro							,
													en_form								,
													en_filtro							,
													dt_tipo_dato						,
													dt_largo,
													dt_secuencia,
													dt_pk,
													elemento_formulario					,
													ef_desactivar_modificacion			,
													ef_procesar_javascript				,
													ef_carga						,
													ef_carga_ci_sql				,
													ef_carga_php_include			,
													ef_carga_php_clase			,
													ef_carga_php_metodo			
										 FROM		apex_molde_operacion_abms_fila
										 WHERE	proyecto = '$proyecto' ";
		if ( isset($componente) ) {
			$sql['molde_abms_fila']['sql'] .= "	AND		molde='$componente' ";
		}
		$sql['molde_abms_fila']['sql'] .= " ORDER BY fila;";
		$sql['molde_abms_fila']['registros']='n';
		$sql['molde_abms_fila']['obligatorio']=true;
		return $sql;
	}
}
?>