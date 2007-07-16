<?php

class toba_catalogo_asistentes
{
	/**
	*	Carga un asistente a partir de un plan de generacion
	*/
	static function cargar_por_plan($id_plan_proyecto, $id_plan)
	{
		$tipo_plan = self::get_asistente_plan($id_plan_proyecto, $id_plan);
		$datos = toba_cargador::instancia()->get_metadatos_extendidos( array('proyecto'=>$id_plan_proyecto, 
																'componente' => $id_plan),
																$tipo_plan );
		$clase = $datos['plan']['clase'];
		return new $clase($datos);
	}
	
	static function get_asistente_plan($id_plan_proyecto, $id_plan)
	{	
		$sql = "SELECT 	t.clase 			as asistente 
				FROM 	apex_plan_operacion o,
						apex_plan_operacion_tipo t
				WHERE 	o.operacion_tipo = t.operacion_tipo
				AND		proyecto = '$id_plan_proyecto'
				AND		plan = '$id_plan';";
		$temp = consultar_fuente($sql);
		if($temp) {
			return $temp[0]['asistente'];
		} else {
			throw new toba_error('El plan solicitado no existe.');	
		}
	}

	static function get_ci_plan($id_plan_proyecto, $id_plan)
	{	
		$sql = "SELECT 	t.ci 				as ci
				FROM 	apex_plan_operacion o,
						apex_plan_operacion_tipo t
				WHERE 	o.operacion_tipo = t.operacion_tipo
				AND		proyecto = '$id_plan_proyecto'
				AND		plan = '$id_plan';";
		$temp = consultar_fuente($sql);
		if($temp) {
			return $temp[0]['ci'];
		} else {
			throw new toba_error('El plan solicitado no existe.');	
		}
	}
}
?>