<?
require_once("componente.php");

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
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//------------- Info base de la estructura ----------------
		$sql["info_estructura"]['sql'] = "SELECT	dt.tabla    as tabla,
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
			$sql["info_estructura"]['sql'] .= "	AND		objeto='$componente' ";	
		}
		$sql["info_estructura"]['sql'] .= ";";
		$sql["info_estructura"]['registros']='1';
		$sql["info_estructura"]['obligatorio']=true;
		//------------ Columnas ----------------
		$sql["info_columnas"]['sql'] = "SELECT	objeto_proyecto,
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
			$sql["info_columnas"]['sql'] .= "	AND		objeto='$componente' ";	
		}
		$sql["info_columnas"]['sql'] .= ";";
		$sql["info_columnas"]['registros']='n';
		$sql["info_columnas"]['obligatorio']=true;
		return $sql;
	}
	
	static function get_nombre_clase_info()
	{
		return 'info_datos_tabla';
	}
	
	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/runtime/persistencia';
	}
	
	static function get_tipo_abreviado()
	{
		return "Tabla";		
	}
}
?>