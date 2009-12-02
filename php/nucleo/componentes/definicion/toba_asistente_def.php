<?php

class toba_asistente_def implements toba_componente_definicion
{
	static protected $db;
	
	static function get_estructura()
	{
		$estructura[] = array( 	'tabla' => 'apex_molde_operacion',
								'registros' => '1',
								'obligatorio' => true );
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$proyecto = self::$db->quote($proyecto);
		if (isset($componente)) {
			$componente = self::$db->quote($componente);
		}			
		$sql['molde']['sql'] = "	SELECT	p.proyecto  		as				proyecto  				,
											p.molde				as				molde					,
											p.operacion_tipo	as				operacion_tipo			,
											p.nombre	        as  			nombre                  ,
											p.item		as				item					,
											p.carpeta_archivos	as				carpeta_archivos		,
											p.prefijo_clases	as				prefijo_clases,
											p.fuente			as				fuente,
											t.clase				as				clase				
						FROM	apex_molde_operacion p,
								apex_molde_operacion_tipo t
						WHERE	p.operacion_tipo = t.operacion_tipo
						AND		p.proyecto=$proyecto";
		if ( isset($componente) ) {
			$sql['molde']['sql'] .= "	AND		p.molde=$componente";	
		}
		$sql['molde']['sql'] .= "	ORDER BY molde;";
		$sql['molde']['registros']='1';	
		$sql['molde']['obligatorio']=true;
		return $sql;
	}
		
	static function get_vista_extendida_resumida($proyecto, $componente)
	{
		return self::get_vista_extendida($proyecto, $componente);
	}
	
	static function set_db($db)
	{
		self::$db = $db;
	}
}
?>