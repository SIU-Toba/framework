<?php 

class ci_fuentes extends toba_ci
{
	protected $carga_ok;

	function ini()
	{
		if ($editable = toba::zona()->get_editable()) {
			$clave['proyecto'] = toba_editor::get_proyecto_cargado();
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

	function get_lista_bases()
	{
		$bases = toba_dba::get_bases_definidas();
		$datos = array();
		$orden = 0;
		foreach($bases as $base => $descripcion) {
			$datos[$orden]['id'] = $base;
			$datos[$orden]['nombre'] = $base .' --- '. $descripcion['base'] .'@'. $descripcion['profile'];
			$orden++;
		}
		return $datos;
	}

	//---- Eventos CI -------------------------------------------------------

	function evt__guardar()
	{
		$this->dependencia('datos')->sincronizar();
		$clave = $this->dependencia('datos')->get_clave_valor(0);
		$zona = toba::solicitud()->zona();
		if (! $zona->cargada()) {
			$zona->cargar(array_values($clave));
		}
	}

	function evt__eliminar()
	{
		$this->dependencia('datos')->eliminar_todo();
		toba::solicitud()->zona()->resetear();
	}
	
	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	function evt__form__modificacion($datos)
	{
		$datos['proyecto'] = toba_editor::get_proyecto_cargado();
		$this->dependencia('datos')->set($datos);
	}

	function conf__form()
	{
		return $this->dependencia('datos')->get();
	}
}
?>