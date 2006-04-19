<?php
require_once("componente_ei.php");

class componente_ei_esquema extends componente_ei
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[3]['tabla'] = 'apex_objeto_esquema';
		$estructura[3]['registros'] = '1';
		$estructura[3]['obligatorio'] = false;
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);

		$sql['info_esquema']['sql'] = "SELECT
												dirigido,
												formato,					
												modelo_ejecucion_cache,	
												ancho,					
												alto
									FROM	apex_objeto_esquema
									WHERE	objeto_esquema_proyecto='$proyecto' ";
		if ( isset($componente) ) {
			$sql['info_esquema']['sql'] .= "	AND     objeto_esquema='$componente' ";	
		}
		$sql['info_esquema']['sql'] .= ";";
		$sql['info_esquema']['registros']='1';
		$sql['info_esquema']['obligatorio']=true;
		return $sql;
	}
	
	static function get_tipo_abreviado()
	{
		return "Esquema";		
	}
}
?>