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
		$proyecto = self::$db->quote($proyecto);
		if (isset($componente)) {
			$componente = self::$db->quote($componente);
		}		
		//------------- abms ----------------
		$sql['molde_abms']['sql'] = "SELECT			proyecto  							,
													molde								,
													tabla								,
													gen_usa_filtro						,
													gen_separar_pantallas				,
													cuadro_eof							,
													cuadro_id							,
													filtro_comprobar_parametros,
													cuadro_forzar_filtro		,
													cuadro_eliminar_filas				,
													cuadro_carga_origen					,
													cuadro_carga_sql			,
													cuadro_carga_php_include		,
													cuadro_carga_php_clase		,
													cuadro_carga_php_metodo		,
													datos_tabla_validacion				,
													apdb_pre							
							 FROM		apex_molde_operacion_abms 
							 WHERE	proyecto=$proyecto ";	
		if ( isset($componente) ) {
			$sql['molde_abms']['sql'] .= "	AND		molde=$componente ";	
		}
		$sql['molde_abms']['sql'] .= "ORDER BY molde;";
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
													filtro_operador	,
													cuadro_estilo 	,
													cuadro_formato 	,
													dt_tipo_dato						,
													dt_largo,
													dt_secuencia,
													dt_pk,
													elemento_formulario					,
													ef_desactivar_modificacion			,
													ef_procesar_javascript				,
													ef_obligatorio						,
													ef_carga_origen,
													ef_carga_sql				,
													ef_carga_tabla,
													ef_carga_php_include			,
													ef_carga_php_clase			,
													ef_carga_php_metodo			,
													ef_carga_col_clave,
													ef_carga_col_desc
										 FROM		apex_molde_operacion_abms_fila
										 WHERE	proyecto = $proyecto ";
		if ( isset($componente) ) {
			$sql['molde_abms_fila']['sql'] .= "	AND		molde=$componente ";
		}
		$sql['molde_abms_fila']['sql'] .= " ORDER BY orden;";
		$sql['molde_abms_fila']['registros']='n';
		$sql['molde_abms_fila']['obligatorio']=true;
		return $sql;
	}
}
?>