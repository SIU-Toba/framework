<?
require_once("componente.php");

class componente_datos_relacion extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_toba_datosrel';
		$estructura[2]['registros'] = '1';
		$estructura[2]['obligatorio'] = true;
		$estructura[3]['tabla'] = 'apex_toba_datosrel_asoc';
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
		$sql["info_estructura"]['sql'] = "SELECT	proyecto 	,	
													objeto      ,	
													debug		,	
													ap			,	
													ap_clase	,	
													ap_archivo		
										 FROM		apex_toba_datosrel
										 WHERE		proyecto='$proyecto' ";	
		if ( isset($componente) ) {
			$sql["info_estructura"]['sql'] .= "	AND		objeto='$componente' ";	
		}
		$sql["info_estructura"]['sql'] .= ";";
		$sql["info_estructura"]['registros']='1';
		$sql["info_estructura"]['obligatorio']=true;
		//------------ relaciones ----------------
		$sql["info_relaciones"]['sql'] = "SELECT	proyecto 		,
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
									 FROM		apex_toba_datosrel_asoc 
									 WHERE		proyecto = '$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_relaciones"]['sql'] .= "	AND		objeto='$componente' ";	
		}
		$sql["info_relaciones"]['sql'] .= ";";
		$sql["info_relaciones"]['registros']='n';
		$sql["info_relaciones"]['obligatorio']=false;
		$sql['info_dependencias'] = parent::get_vista_dependencias($proyecto, $componente);		
		return $sql;
	}
	
	static function get_nombre_clase_info()
	{
		return 'info_datos_relacion';
	}
	
	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/persistencia';
	}

	static function get_tipo_abreviado()
	{
		return "Relación";		
	}
}
?>