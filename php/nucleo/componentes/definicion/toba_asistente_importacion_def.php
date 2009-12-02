<?php

class toba_asistente_importacion_def extends toba_asistente_def
{
 	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_molde_operacion_importacion',
								'registros' => '1',
								'obligatorio' => true );
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
		$sql['molde_importacion']['sql'] = "SELECT	proyecto							,
													molde								,
													origen_item							,
													origen_proyecto	
							 FROM		apex_molde_operacion_importacion 
							 WHERE	proyecto=$proyecto ";	
		if ( isset($componente) ) {
			$sql['molde_importacion']['sql'] .= "	AND		molde=$componente ";	
		}
		$sql['molde_importacion']['sql'] .= " ORDER BY molde;";
		$sql['molde_importacion']['registros']='1';
		$sql['molde_importacion']['obligatorio']=true;
		return $sql;
	}
}
?>