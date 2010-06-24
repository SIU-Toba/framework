<?php

class toba_ei_grafico_def extends toba_ei_def
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_objeto_grafico',
								'registros' => '1',
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
		$sql['_info_grafico']['sql'] = "SELECT *
										FROM	apex_objeto_grafico
										WHERE	objeto_grafico_proyecto=$proyecto";
		if ( isset($componente) ) {
			$sql['_info_grafico']['sql'] .= "	AND     objeto_grafico=$componente ";	
		}
		$sql['_info_grafico']['sql'] .= " ORDER BY objeto_grafico;";
		$sql['_info_grafico']['registros']='1';
		$sql['_info_grafico']['obligatorio']=true;
		return $sql;
	}
}
?>