<?php

class toba_ei_formulario_ml_def extends toba_ei_formulario_def
{
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		
		$proyecto = self::$db->quote($proyecto);
		if (isset($componente)) {
			$componente = self::$db->quote($componente);
		}		
				
		//Formulario
		$sql["_info_formulario"]['sql'] = "SELECT	auto_reset as	auto_reset,
										scroll as 					scroll,					
										ancho as					ancho,
										alto as						alto,
										filas as					filas,
										filas_agregar as			filas_agregar,
										filas_agregar_online as 	filas_agregar_online,
										filas_agregar_abajo as		filas_agregar_abajo,
										filas_agregar_texto as		filas_agregar_texto,
										filas_borrar_en_linea as 	filas_borrar_en_linea,
										filas_ordenar_en_linea as 	filas_ordenar_en_linea,
										filas_ordenar as			filas_ordenar,
										filas_numerar as 			filas_numerar,
										columna_orden as 			columna_orden,
										analisis_cambios		as	analisis_cambios
								FROM	apex_objeto_ut_formulario
								WHERE	objeto_ut_formulario_proyecto=$proyecto";
		if ( isset($componente) ) {
			$sql['_info_formulario']['sql'] .= "	AND		objeto_ut_formulario=$componente ";	
		}
		$sql['_info_formulario']['sql'] .= " ORDER BY objeto_ut_formulario;";
		$sql['_info_formulario']['registros']='1';
		$sql['_info_formulario']['obligatorio']=true;
		//EF
		$sql["_info_formulario_ef"]['sql'] = "SELECT	ef.*,
										estilo as					columna_estilo,
										con.clase as carga_consulta_php_clase,
										con.archivo as carga_consulta_php_archivo
								FROM	apex_objeto_ei_formulario_ef ef
											LEFT OUTER JOIN apex_consulta_php con ON
												(ef.objeto_ei_formulario_proyecto = con.proyecto AND
													ef.carga_consulta_php = con.consulta_php) 
								WHERE	objeto_ei_formulario_proyecto=$proyecto";
		if ( isset($componente) ) {
			$sql['_info_formulario_ef']['sql'] .= "	AND		objeto_ei_formulario=$componente ";	
		}
		$sql['_info_formulario_ef']['sql'] .= " AND	(desactivado=0	OR	desactivado	IS	NULL)
								ORDER	BY	orden;";
		$sql['_info_formulario_ef']['registros']='n';
		$sql['_info_formulario_ef']['obligatorio']=false;
		return $sql;
	}
}
?>