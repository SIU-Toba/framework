<?php

class toba_ei_codigo_def extends toba_ei_def
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_objeto_codigo',
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
		$sql['_info_codigo']['sql'] = "SELECT *
										FROM	apex_objeto_codigo
										WHERE	objeto_codigo_proyecto=$proyecto";
		if ( isset($componente) ) {
			$sql['_info_codigo']['sql'] .= "	AND     objeto_codigo=$componente ";
		}
		$sql['_info_codigo']['sql'] .= " ORDER BY objeto_codigo;";
		$sql['_info_codigo']['registros']='1';
		$sql['_info_codigo']['obligatorio']=true;
		return $sql;
	}
}
?>