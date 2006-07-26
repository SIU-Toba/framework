<?php 

class ci_grupo extends objeto_ci
{
	function evt__inicializar()
	{
		$zona = toba::get_solicitud()->zona();
		if ($editable = $zona->get_editable()){
			$clave['proyecto'] = $editable[0];
			$clave['usuario_grupo_acc'] = $editable[1];
			$this->dependencia('datos')->cargar($clave);
		}			
	}

	//---- Eventos CI -------------------------------------------------------

	function evt__guardar()
	{
		$this->dependencia('datos')->sincronizar();
		$clave = $this->dependencia('datos')->get_clave_valor(0);
		toba::get_solicitud()->zona()->cargar_editable($clave);
	}

	function evt__eliminar()
	{
		$this->dependencia('datos')->eliminar_todo();
		toba::get_solicitud()->zona()->resetear();
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	function evt__formulario__modificacion($datos)
	{
		$datos['proyecto'] = editor::get_proyecto_cargado();
		$this->dependencia('datos')->set($datos);
	}

	function evt__formulario__carga()
	{
		return $this->dependencia('datos')->get();
	}
}
?>