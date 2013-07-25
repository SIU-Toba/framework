<?php

class toba_ei_firma_def extends toba_ei_def
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_objeto_ei_firma',
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
		$sql['_info_firma']['sql'] = "SELECT *
										FROM	apex_objeto_ei_firma
										WHERE	objeto_ei_firma_proyecto=$proyecto";
		if ( isset($componente) ) {
			$sql['_info_firma']['sql'] .= "	AND     objeto_ei_firma=$componente ";
		}
		$sql['_info_firma']['sql'] .= " ORDER BY objeto_ei_firma;";
		$sql['_info_firma']['registros']='1';
		$sql['_info_firma']['obligatorio']=true;
		return $sql;
	}
}
?>