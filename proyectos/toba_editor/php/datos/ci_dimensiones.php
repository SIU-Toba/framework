<?php

class ci_dimensiones extends toba_ci
{
	protected $carga_ok;

	function ini()
	{
		if ($editable = toba::zona()->get_editable()) {
			$clave['proyecto'] = toba_editor::get_proyecto_cargado();
			$clave['dimension'] = $editable[1];
			$this->carga_ok = $this->dependencia('datos')->cargar($clave);
		}			
	}

	function conf()
	{
		if(!$this->carga_ok) {
			$this->pantalla()->eliminar_evento('eliminar');
			$this->pantalla()->eliminar_tab('pant_elementos');
		}
	}

	//---- Eventos CI -------------------------------------------------------

	function evt__guardar()
	{
		$this->dependencia('datos')->sincronizar();
		$clave = $this->dependencia('datos')->tabla('dimension')->get_clave_valor(0);
		$zona = toba::solicitud()->zona();
		if (! $zona->cargada()) {
			$zona->cargar(array_values($clave));
		}
		$this->carga_ok = true;
		admin_util::refrescar_barra_lateral();
	}

	function evt__eliminar()
	{
		$this->dependencia('datos')->eliminar_todo();
		toba::solicitud()->zona()->resetear();
		$this->carga_ok = false;
		admin_util::refrescar_barra_lateral();
	}
	
	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	function evt__formulario__modificacion($datos)
	{
		$datos['proyecto'] = toba_editor::get_proyecto_cargado();
		$this->dependencia('datos')->tabla('dimension')->set($datos);
	}

	function conf__formulario()
	{
		$datos = $this->dependencia('datos')->tabla('dimension')->get();
		return $datos;
	}

	function get_tablas($fuente)
	{
		return toba::db($fuente['fuente_datos'], toba_editor::get_proyecto_cargado())->get_lista_tablas();
	}

	//--- Muestra los elementos de la dimension

	function conf__elementos()
	{
		$datos = $this->dependencia('datos')->tabla('dimension')->get();
		$id = explode(',',$datos['col_id']);
		$desc = explode(',',$datos['col_desc']);
		$sql = "SELECT " . implode(' || ',$id) . " as clave, " 
						. implode(' || ',$desc) . " as descripcion
				FROM {$datos['tabla']}
				ORDER BY descripcion";
		$datos = toba::db($datos['fuente_datos'])->consultar($sql);
		return $datos;
	}

}

?>