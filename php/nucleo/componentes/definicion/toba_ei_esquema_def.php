<?php

class toba_ei_esquema_def extends toba_ei_def
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_objeto_esquema',
								'registros' => '1',
								'obligatorio' => false );
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);

		$sql['_info_esquema']['sql'] = "SELECT
												dirigido,
												formato,					
												modelo_ejecucion_cache,	
												ancho,					
												alto
									FROM	apex_objeto_esquema
									WHERE	objeto_esquema_proyecto='$proyecto' ";
		if ( isset($componente) ) {
			$sql['_info_esquema']['sql'] .= "	AND     objeto_esquema='$componente' ";	
		}
		$sql['_info_esquema']['sql'] .= ";";
		$sql['_info_esquema']['registros']='1';
		$sql['_info_esquema']['obligatorio']=true;
		return $sql;
	}
}
?>