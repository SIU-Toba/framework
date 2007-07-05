<?php

class toba_asistente_def implements toba_componente_definicion
{
	static function get_estructura()
	{
		$estructura[] = array( 	'tabla' => 'apex_plan_operacion',
								'registros' => '1',
								'obligatorio' => true );
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql['plan']['sql'] = "	SELECT	p.proyecto  		as				proyecto  				,
											p.plan				as				plan					,
											p.operacion_tipo	as				operacion_tipo			,
											p.nombre	        as  			nombre                  ,
											p.carpeta_item		as				carpeta_item			,
											p.carpeta_archivos	as				carpeta_archivos		,
											t.clase				as				clase				
						FROM	apex_plan_operacion p,
								apex_plan_operacion_tipo t
						WHERE	p.operacion_tipo = t.operacion_tipo
						AND		p.proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['plan']['sql'] .= "	AND		p.plan='$componente';";	
		}
		$sql['plan']['registros']='1';	
		$sql['plan']['obligatorio']=true;
		return $sql;
	}
		
	static function get_vista_extendida_resumida($proyecto, $componente)
	{
		return self::get_vista_extendida($proyecto, $componente);
	}
}
?>