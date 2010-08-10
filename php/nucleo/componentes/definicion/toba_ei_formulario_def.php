<?php

class toba_ei_formulario_def extends toba_ei_def
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_objeto_ut_formulario',
								'registros' => '1',
								'diff_clave' => 'objeto_ut_formulario',
								'obligatorio' => false );
		$estructura[] = array( 	'tabla' => 'apex_objeto_ei_formulario_ef',
								'registros' => 'n',
								'obligatorio' => false,
								'diff_clave' => 'identificador',
								'diff_excluir' => array(
									'objeto_ei_formulario_fila'
								));
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		
		$proyecto = self::$db->quote($proyecto);
		if (isset($componente)) {
			$componente = self::$db->quote($componente);
		}		
				
		//Formulario
		$sql["_info_formulario"]['sql'] = "SELECT	auto_reset as	auto_reset,						
										ancho 						as ancho,
										ancho_etiqueta				as ancho_etiqueta,
										expandir_descripcion		as expandir_descripcion,
										no_imprimir_efs_sin_estado	as no_imprimir_efs_sin_estado,
										resaltar_efs_con_estado		as resaltar_efs_con_estado,
										template					as template,
										template_impresion  as template_impresion
								FROM	apex_objeto_ut_formulario
								WHERE	objeto_ut_formulario_proyecto=$proyecto";
		if ( isset($componente) ) {
			$sql['_info_formulario']['sql'] .= "	AND		objeto_ut_formulario=$componente ";	
		}
		$sql['_info_formulario']['sql'] .= " ORDER BY objeto_ut_formulario;";
		$sql['_info_formulario']['registros']='1';
		$sql['_info_formulario']['obligatorio']=true;
		//EF
		$sql["_info_formulario_ef"]['sql'] = "SELECT	
										ef.*,
										con.clase as carga_consulta_php_clase,
										con.archivo as carga_consulta_php_archivo
								FROM	apex_objeto_ei_formulario_ef ef
											LEFT OUTER JOIN apex_consulta_php con ON
												(ef.objeto_ei_formulario_proyecto = con.proyecto AND
													ef.carga_consulta_php = con.consulta_php) 
								WHERE	ef.objeto_ei_formulario_proyecto=$proyecto";
		if ( isset($componente) ) {
			$sql['_info_formulario_ef']['sql'] .= "	AND		objeto_ei_formulario=$componente ";	
		}
		$sql['_info_formulario_ef']['sql'] .= " AND	(desactivado=0	OR	desactivado	IS	NULL)
								ORDER	BY	orden;";
		$sql['_info_formulario_ef']['registros']='n';
		$sql['_info_formulario_ef']['obligatorio']=true;
		return $sql;
	}
}
?>