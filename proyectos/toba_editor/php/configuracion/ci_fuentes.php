<?php 

class ci_fuentes extends objeto_ci
{
	function evt__inicializar()
	{
		if ($editable = toba::get_zona()->get_editable()){
			$clave['proyecto'] = $editable[0];
			$clave['fuente_datos'] = $editable[1];
			$this->dependencia('datos')->cargar($clave);
		}			
	}

	function get_lista_bases()
	{
		$bases = dba::get_bases_definidas();
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

	function evt__form__modificacion($datos)
	{
		$datos['proyecto'] = editor::get_proyecto_cargado();
		$this->dependencia('datos')->set($datos);
	}

	function conf__form()
	{
		return $this->dependencia('datos')->get();
	}
}
?>