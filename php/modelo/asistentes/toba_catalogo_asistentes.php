<?php

class toba_catalogo_asistentes
{
	static function cargar_por_plan($id_plan_proyecto, $id_plan)
	{
		$tipo_plan = self::get_asistente_plan($id_plan_proyecto, $id_plan);
		return toba_constructor::get_runtime( array('proyecto'=>$id_plan_proyecto, 
													'componente' => $id_plan),
													$tipo_plan[0]['asistente'] );
	}
	
	static function get_asistente_plan($id_plan_proyecto, $id_plan)
	{	
		$sql = "SELECT 	t.clase 			as asistente 
				FROM 	apex_plan_operacion o,
						apex_plan_operacion_tipo t
				WHERE 	o.operacion_tipo = t.operacion_tipo
				AND		proyecto = '$id_plan_proyecto'
				AND		plan = '$id_plan';";
		return consultar_fuente($sql);
	}
}
?>