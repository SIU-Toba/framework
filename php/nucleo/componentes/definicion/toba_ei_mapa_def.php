<?php

class toba_ei_mapa_def extends toba_ei_def
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_objeto_mapa',
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
		$sql['_info_mapa']['sql'] = "SELECT *
									FROM	apex_objeto_mapa
									WHERE	objeto_mapa_proyecto=$proyecto";
		if ( isset($componente) ) {
			$sql['_info_mapa']['sql'] .= "	AND     objeto_mapa=$componente ";	
		}
		$sql['_info_mapa']['sql'] .= " ORDER BY objeto_mapa;";
		$sql['_info_mapa']['registros']='1';
		$sql['_info_mapa']['obligatorio']=true;
		return $sql;
	}
}
?>