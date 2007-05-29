<?php

class toba_datos_relacion_def extends toba_componente_def
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_datos_rel';
		$estructura[2]['registros'] = '1';
		$estructura[2]['obligatorio'] = true;
		$estructura[3]['tabla'] = 'apex_objeto_datos_rel_asoc';
		$estructura[3]['registros'] = 'n';
		$estructura[3]['obligatorio'] = true;
		$estructura[4]['tabla'] = 'apex_objeto_dependencias';
		$estructura[4]['registros'] = 'n';
		$estructura[4]['obligatorio'] = false;
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//------------- Info base de la estructura ----------------
		$sql["_info_estructura"]['sql'] = "SELECT	proyecto 	,	
													objeto      ,	
													debug		,	
													ap			,	
													ap_clase	,	
													ap_archivo		
										 FROM		apex_objeto_datos_rel
										 WHERE		proyecto='$proyecto' ";	
		if ( isset($componente) ) {
			$sql["_info_estructura"]['sql'] .= "	AND		objeto='$componente' ";	
		}
		$sql["_info_estructura"]['sql'] .= ";";
		$sql["_info_estructura"]['registros']='1';
		$sql["_info_estructura"]['obligatorio']=true;
		//------------ relaciones ----------------
		$sql["_info_relaciones"]['sql'] = "SELECT	proyecto 		,
												objeto 		    ,
												asoc_id			,
											--	identificador   ,
												padre_proyecto	,
												padre_objeto	,
												padre_id		,
												padre_clave		,
												hijo_proyecto	,
												hijo_objeto		,
												hijo_id			,
												hijo_clave		,
												cascada			,
												orden			
									 FROM		apex_objeto_datos_rel_asoc 
									 WHERE		proyecto = '$proyecto' ";
		if ( isset($componente) ) {
			$sql["_info_relaciones"]['sql'] .= "	AND		objeto='$componente' ";	
		}
		$sql["_info_relaciones"]['sql'] .= ";";
		$sql["_info_relaciones"]['registros']='n';
		$sql["_info_relaciones"]['obligatorio']=false;
		$sql['_info_dependencias'] = parent::get_vista_dependencias($proyecto, $componente);		
		return $sql;
	}

	static function get_vista_extendida_resumida($proyecto, $componente)
	{
		$estructura = self::get_vista_extendida($proyecto, $componente);
		unset($estructura['_info_relaciones']);
		return $estructura;
	}

	static function get_tipo_abreviado()
	{
		return "Relación";		
	}
}
?>