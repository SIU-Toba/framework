<?php
class ci_puntos_control extends toba_ci
{

	function conf__cuadro($componente)
	{
		return toba_info_editores::get_puntos_control();
	}

	function evt__agregar()
	{
		$this->set_pantalla('pant_edicion');
		$this->evento('baja')->anular();
	}

	function evt__cancelar()
	{
		$this->dep('relacion')->resetear();
		$this->controlador->set_pantalla('pant_listado');
	}

	function evt__aceptar()
	{
		try
		{
			$r = $this->dep('relacion');
			$r->sincronizar();
			$r->resetear();
			$this->controlador->set_pantalla('pant_listado');
		} catch(toba_error $e) {
			toba::notificacion()->agregar('Error procesando');
			toba::logger()->error($e->getMessage());
		}
	}

	function evt__baja()
	{
		$pos = $this->dep('relacion')->tabla('apex_ptos_control')->get_cursor();
		$this->dep('relacion')->tabla('apex_ptos_ctrl_param')->eliminar_todo();
		$this->dep('relacion')->tabla('apex_ptos_control')->eliminar($pos);
		$this->evt__aceptar();
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$seleccion['proyecto'] = toba_editor::get_proyecto_cargado();      
		$this->dep('relacion')->cargar($seleccion);

		$this->set_pantalla('pant_edicion');
	}

	function conf__detalle($componente)
	{
		return $this->dep('relacion')->tabla('apex_ptos_control')->get();
	}

	function evt__detalle__modificacion($datos)
	{
		$datos['proyecto'] = toba_editor::get_proyecto_cargado();
		$this->dep('relacion')->tabla('apex_ptos_control')->set($datos);
	}

	function conf__parametros($componente)
	{
		$componente->colapsar();
		return $this->dep('relacion')->tabla('apex_ptos_ctrl_param')->get_filas();
	}

	function evt__parametros__modificacion($datos)
	{
		foreach ($datos as $key => $value) {
			$datos[$key]['proyecto'] = toba_editor::get_proyecto_cargado();
		}

		$this->dep('relacion')->tabla('apex_ptos_ctrl_param')->procesar_filas($datos);
	}

	function conf__controles($componente)
	{
		return $this->dep('relacion')->tabla('ptos_control_ctrl')->get_filas();
	}

	function evt__controles__modificacion($datos)
	{
		foreach ($datos as $key => $value) {
			$datos[$key]['proyecto'] = toba_editor::get_proyecto_cargado();
		}
		$this->dep('relacion')->tabla('ptos_control_ctrl')->procesar_filas($datos);
	}
}
?>