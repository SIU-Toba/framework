<?php

class toba_catalogo_asistentes
{
	/**
	*	Carga un asistente a partir de un molde de generacion
	*/
	static function cargar_por_molde($id_molde_proyecto, $id_molde)
	{
		$tipo_molde = self::get_asistente_molde($id_molde_proyecto, $id_molde);
		$datos = toba_cargador::instancia()->get_metadatos_extendidos( array('proyecto'=>$id_molde_proyecto, 
																'componente' => $id_molde),
																$tipo_molde );
		$clase = $datos['molde']['clase'];
		return new $clase($datos);
	}
	
	static function get_asistente_molde($id_molde_proyecto, $id_molde)
	{	
		$sql = "SELECT 	t.clase 			as asistente 
				FROM 	apex_molde_operacion o,
						apex_molde_operacion_tipo t
				WHERE 	o.operacion_tipo = t.operacion_tipo
				AND		proyecto = '$id_molde_proyecto'
				AND		molde = '$id_molde';";
		$temp = consultar_fuente($sql);
		if($temp) {
			return $temp[0]['asistente'];
		} else {
			throw new toba_error('El molde solicitado no existe.');	
		}
	}

	static function get_ci_molde($id_molde_proyecto, $id_molde)
	{	
		$sql = "SELECT 	t.ci 				as ci
				FROM 	apex_molde_operacion o,
						apex_molde_operacion_tipo t
				WHERE 	o.operacion_tipo = t.operacion_tipo
				AND		proyecto = '$id_molde_proyecto'
				AND		molde = '$id_molde';";
		$temp = consultar_fuente($sql);
		if($temp) {
			return $temp[0]['ci'];
		} else {
			throw new toba_error('El molde solicitado no existe.');	
		}
	}
}
?>