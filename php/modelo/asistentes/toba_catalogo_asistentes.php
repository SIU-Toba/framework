<?php

class toba_catalogo_asistentes
{
	static function buscar($tipo_plan, $id_plan_proyecto, $id_plan)
	{
		return toba_constructor::get_runtime( array('proyecto'=>$id_plan_proyecto, 
													'componente' => $id_plan),
												$tipo_plan );
	}
}
?>