<?php
require_once("componente_ei_formulario.php");	//Ancestro de todos los	OE

class componente_ei_formulario_ml extends componente_ei_formulario
{
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		//Formulario
		$sql["_info_formulario"]['sql'] = "SELECT	auto_reset as	auto_reset,
										scroll as 					scroll,					
										ancho as					ancho,
										alto as						alto,
										filas as					filas,
										filas_agregar as			filas_agregar,
										filas_agregar_online as 	filas_agregar_online,
										filas_ordenar as			filas_ordenar,
										filas_numerar as 			filas_numerar,
										columna_orden as 			columna_orden,
										analisis_cambios		as	analisis_cambios
								FROM	apex_objeto_ut_formulario
								WHERE	objeto_ut_formulario_proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['_info_formulario']['sql'] .= "	AND		objeto_ut_formulario='$componente' ";	
		}
		$sql['_info_formulario']['sql'] .= ";";
		$sql['_info_formulario']['registros']='1';
		$sql['_info_formulario']['obligatorio']=true;
		//EF
		$sql["_info_formulario_ef"]['sql'] = "SELECT	*,
										estilo as					columna_estilo
								FROM	apex_objeto_ei_formulario_ef
								WHERE	objeto_ei_formulario_proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['_info_formulario_ef']['sql'] .= "	AND		objeto_ei_formulario='$componente' ";	
		}
		$sql['_info_formulario_ef']['sql'] .= " AND	(desactivado=0	OR	desactivado	IS	NULL)
								ORDER	BY	orden;";
		$sql['_info_formulario_ef']['registros']='n';
		$sql['_info_formulario_ef']['obligatorio']=false;
		return $sql;
	}

	static function get_nombre_clase_info()
	{
		return 'info_ei_formulario_ml';
	}
	
	static function get_tipo_abreviado()
	{
		return "Form. ML";		
	}
}
?>