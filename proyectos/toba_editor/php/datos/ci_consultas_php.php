<?php 
class ci_consultas_php extends toba_ci
{
	protected $carga_ok;
	
	function ini()
	{
		if ($editable = toba::zona()->get_editable()) {
			$clave['proyecto'] = toba_editor::get_proyecto_cargado();
			$clave['consulta_php'] = $editable[1];
			$this->dependencia('datos')->cargar($clave);
			$this->carga_ok = $this->dependencia('datos')->cargar($clave);
		}	
	}

	function conf()
	{
		if(!$this->carga_ok) {
			$this->pantalla()->eliminar_evento('eliminar');
		}	
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__guardar()
	{
		$this->dependencia('datos')->sincronizar();
		$clave = $this->dependencia('datos')->get_clave_valor(0);
		$zona = toba::solicitud()->zona();
		if (! $zona->cargada()) {
			$zona->cargar(array_values($clave));
		}
		admin_util::refrescar_barra_lateral();
	}

	function evt__eliminar()
	{
		$this->dependencia('datos')->eliminar_todo();
		toba::solicitud()->zona()->resetear();
		admin_util::refrescar_barra_lateral();
	}

	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------


	function evt__form__modificacion($datos)
	{
		$datos['proyecto'] = toba_editor::get_proyecto_cargado();
		$this->dependencia('datos')->set($datos);
	}

	function conf__form()
	{
		$datos = $this->dependencia('datos')->get();
		return $datos;
	}
}

?>