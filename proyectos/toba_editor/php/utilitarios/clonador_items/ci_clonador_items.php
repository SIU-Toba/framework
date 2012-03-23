<?php 
//--------------------------------------------------------------------
class ci_clonador_items extends toba_ci
{
	protected $opciones;

	function ini()
	{
		if (! toba::zona()->cargada()) {
			throw new toba_error('La operación se debe invocar desde la zona de un item');
		}
	}
	
	//---- Eventos CI -------------------------------------------------------

	function evt__procesar()
	{
		list($proyecto_actual, $item_actual) = toba::zona()->get_editable();
		$id = array('proyecto' => $proyecto_actual, 'componente' => $item_actual);
		$info_item = toba_constructor::get_info($id, 'toba_item');
		$directorio = false;
		if ($this->opciones['con_subclases']) {
			$directorio = $this->opciones['carpeta_subclases'];
		}
		$nuevos_datos = array();
		$nuevos_datos['proyecto'] = $this->opciones['proyecto'];
		$nuevos_datos['padre_proyecto'] = $this->opciones['proyecto'];
		$nuevos_datos['padre'] = $this->opciones['carpeta'];
		$nuevos_datos['punto_montaje'] = $this->opciones['punto_montaje'];
		$nuevos_datos['pagina_tipo_proyecto'] = $this->opciones['pagina_tipo_proyecto'];
		$nuevos_datos['pagina_tipo'] = $this->opciones['pagina_tipo'];
		if (isset($this->opciones['anexo'])) {
			$nuevos_datos['anexo_nombre'] = $this->opciones['anexo'];	
		}
		$nuevos_datos['fuente_datos'] = $this->opciones['fuente'];
		$nuevos_datos['fuente_datos_proyecto'] = $this->opciones['proyecto'];
		$info_item->clonar($nuevos_datos, $directorio);
		toba::notificacion()->info('Clonado OK');
	}

	//---- opciones -------------------------------------------------------

	function get_pms($proyecto)
	{
		return toba_info_editores::get_pms($proyecto);
	}
	
	function get_tipos_pagina($proyecto)
	{
		return toba_info_editores::get_tipos_pagina($proyecto);
	}
	
	function evt__opciones__modificacion($datos)
	{
		$this->opciones = $datos;
	}

	function conf__opciones()
	{
		if (!isset($this->opciones)) {
			$this->opciones = array();
			list($proyecto_actual, $item_actual) = toba::zona()->get_editable();
			$id = array('proyecto' => $proyecto_actual, 'componente' => $item_actual);
			$sql = 'SELECT pagina_tipo_proyecto, pagina_tipo FROM apex_item WHERE proyecto = '.quote($proyecto_actual). ' AND item = '.quote($item_actual);
			$datos = toba::db()->consultar_fila($sql);		
			$this->opciones['proyecto']				= $proyecto_actual;
			$this->opciones['pagina_tipo_proyecto']	= $datos['pagina_tipo_proyecto'];
			$this->opciones['pagina_tipo']			= $datos['pagina_tipo'];
		}
		return $this->opciones;
	}

}

?>