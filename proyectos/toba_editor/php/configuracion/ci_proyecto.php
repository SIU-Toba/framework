<?php 
//--------------------------------------------------------------------
class ci_proyecto extends objeto_ci
{
	protected $iniciado = false;
	
	function evt__inicializar()
	{
		if(!$this->iniciado) {
			$this->dependencia('datos')->cargar(array('proyecto'=>editor::get_proyecto_cargado()));
		}	
	}

	function extender_objeto_js()
	{
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'iniciado';
		return $propiedades;
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- datos -------------------------------------------------------

	//---- form -------------------------------------------------------

	function evt__form__modificacion($datos)
	{
		$this->dependencia('datos')->set($datos);
		$this->dependencia('datos')->sincronizar();
	}

	function evt__form__carga()
	{
		return $this->dependencia('datos')->get();
	}
}
?>