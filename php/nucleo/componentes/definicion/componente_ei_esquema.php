<?php
require_once("componente_ei.php");

class componente_ei_esquema extends componente_ei
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[4]['tabla'] = 'apex_objeto_esquema';
		$estructura[4]['registros'] = '1';
		$estructura[4]['obligatorio'] = false;
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
	
	static function get_tipo_abreviado()
	{
		return "Esquema";		
	}

	static function get_nombre_clase_info()
	{
		return 'info_ei_esquema';
	}
}
?>