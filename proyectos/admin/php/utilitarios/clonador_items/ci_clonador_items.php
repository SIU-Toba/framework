<?php 
//--------------------------------------------------------------------
class ci_clonador_items extends objeto_ci
{
	protected $opciones;

	function evt__inicializar()
	{
		$zona = toba::get_solicitud()->zona();
		$editable = toba::get_solicitud()->zona()->obtener_editable_propagado();
		if (! isset($editable)) {
			throw new excepcion_toba('La operación se debe invocar desde la zona de un item');
		}
	}
	
	
	//---- Eventos CI -------------------------------------------------------

	function evt__procesar()
	{
		$zona = toba::get_solicitud()->zona();
		$editable = $zona->obtener_editable_propagado();
		list($proyecto_actual, $item_actual) = $editable;
		$id = array('proyecto' => $proyecto_actual, 'componente' => $item_actual);
		$info_item = constructor_toba::get_info($id, 'item');
		$directorio = false;
		if ($this->opciones['con_subclases']) {
			$directorio = $this->opciones['carpeta_subclases'];
		}
		$nuevos_datos = array();
		$nuevos_datos['proyecto'] = $this->opciones['proyecto'];
		$nuevos_datos['padre_proyecto'] = $this->opciones['proyecto'];
		$nuevos_datos['padre'] = $this->opciones['carpeta'];
		if (isset($this->opciones['anexo'])) {
			$nuevos_datos['anexo_nombre'] = $this->opciones['anexo'];	
		}
		$nuevos_datos['fuente_datos'] = $this->opciones['fuente'];
		$nuevos_datos['fuente_datos_proyecto'] = $this->opciones['proyecto'];
		$info_item->clonar($nuevos_datos, $directorio);
	}

	//---- opciones -------------------------------------------------------

	function evt__opciones__modificacion($datos)
	{
		$this->opciones = $datos;
	}

	function evt__opciones__carga()
	{
		if (!isset($this->opciones)) {
			$this->opciones = array();
			$this->opciones['proyecto'] = editor::get_proyecto_cargado();	
		}
		return $this->opciones;
	}


}

?>