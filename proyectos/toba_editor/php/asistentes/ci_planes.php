<?php 
class ci_planes extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- Inicializacion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function ini()
	{
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
		$plan = $this->get_info_plan('toba_editor',1,'toba_plan_operacion_abms');
		$plan->mostrar();
	}

	function evt__cancelar()
	{
	}

	function get_info_plan($proyecto, $plan, $tipo)
	{
		return toba_constructor::get_runtime( array('proyecto'=>$proyecto, 'componente' => $plan ),$tipo);
	}
}

?>