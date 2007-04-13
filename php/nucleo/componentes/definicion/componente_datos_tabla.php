<?php

class componente_datos_tabla extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_db_registros';
		$estructura[2]['registros'] = '1';
		$estructura[2]['obligatorio'] = true;
		$estructura[3]['tabla'] = 'apex_objeto_db_registros_col';
		$estructura[3]['registros'] = 'n';
		$estructura[3]['obligatorio'] = true;
		$estructura[4]['tabla'] = 'apex_objeto_db_registros_ext';
		$estructura[4]['registros'] = 'n';
		$estructura[4]['obligatorio'] = false;		
		$estructura[5]['tabla'] = 'apex_objeto_db_registros_ext_col';
		$estructura[5]['registros'] = 'n';
		$estructura[5]['obligatorio'] = false;		
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//------------- Info base de la estructura ----------------
		$sql["_info_estructura"]['sql'] = "SELECT	dt.tabla    as tabla,
											dt.alias          	as alias,
											dt.min_registros  	as min_registros,
											dt.max_registros  	as max_registros,
											dt.ap				as ap			,	
											dt.ap_clase			as ap_sub_clase	,	
											dt.ap_archivo	    as ap_sub_clase_archivo,
											dt.modificar_claves as ap_modificar_claves,
											ap.clase			as ap_clase,
											ap.archivo			as ap_clase_archivo
					 FROM		apex_objeto_db_registros as dt
				 				LEFT OUTER JOIN apex_admin_persistencia ap ON dt.ap = ap.ap
					 WHERE		objeto_proyecto='$proyecto' ";
		if ( isset($componente) ) {
			$sql["_info_estructura"]['sql'] .= "	AND		objeto='$componente' ";	
		}
		$sql["_info_estructura"]['sql'] .= ";";
		$sql["_info_estructura"]['registros']='1';
		$sql["_info_estructura"]['obligatorio']=true;
		//------------ Columnas ----------------
		$sql["_info_columnas"]['sql'] = "SELECT	objeto_proyecto,
						objeto 			,	
						col_id			,	
						columna			,	
						tipo			,	
						pk				,	
						secuencia		,
						largo			,	
						no_nulo			,	
						no_nulo_db		,
						externa
					 FROM		apex_objeto_db_registros_col 
					 WHERE		objeto_proyecto = '$proyecto' ";
		if ( isset($componente) ) {
			$sql["_info_columnas"]['sql'] .= "	AND		objeto='$componente' ";	
		}
		$sql["_info_columnas"]['sql'] .= ";";
		$sql["_info_columnas"]['registros']='n';
		$sql["_info_columnas"]['obligatorio']=true;
		
		//------------ Externas ----------------
		$sql["_info_externas"]['sql'] = "SELECT	objeto_proyecto,
						objeto 			,	
						externa_id		,	
						tipo			,	
						sincro_continua	,	
						metodo			,
						clase			,	
						include			,	
						sql
					 FROM		apex_objeto_db_registros_ext 
					 WHERE		objeto_proyecto = '$proyecto' ";
		if ( isset($componente) ) {
			$sql["_info_externas"]['sql'] .= "	AND		objeto='$componente' ";	
		}
		$sql["_info_externas"]['sql'] .= ";";
		$sql["_info_externas"]['registros']='n';
		$sql["_info_externas"]['obligatorio']=false;
		
		//------------ Externas ----------------
		$sql["_info_externas_col"]['sql'] = "SELECT	ext_col.objeto_proyecto,
						ext_col.objeto 			,	
						ext_col.externa_id		,	
						ext_col.es_resultado	,
						col.columna				
					 FROM	
					 		apex_objeto_db_registros_ext_col ext_col,
					 		apex_objeto_db_registros_col col
					 WHERE		
					 		ext_col.objeto_proyecto = '$proyecto' AND
					 		col.objeto_proyecto = '$proyecto' AND
					 		ext_col.col_id = col.col_id	
					 	";
		if ( isset($componente) ) {
			$sql["_info_externas_col"]['sql'] .= "	AND		ext_col.objeto='$componente' ";	
		}
		$sql["_info_externas_col"]['sql'] .= ";";
		$sql["_info_externas_col"]['registros']='n';
		$sql["_info_externas_col"]['obligatorio']=false;
				
		return $sql;
	}
	
	static function get_nombre_clase_info()
	{
		return 'info_datos_tabla';
	}
	
	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/persistencia';
	}
	
	static function get_tipo_abreviado()
	{
		return "Tabla";		
	}
}
?>