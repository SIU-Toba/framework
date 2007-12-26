<?php 
class ci_importar_operacion extends toba_ci
{
	protected $s__datos;
	
	function get_catalogo_operaciones()
	{
		$salida = array();
		$salida[] = array(
			'proyecto' => 'toba_referencia',
			'item' => '1000182',
			'nombre' => 'Consulta de Auditoría'
		);
		return $salida;
	}
	
	//---- form_origen ------------------------------------------------------------------

	function evt__form_origen__modificacion($datos)
	{
		$this->s__datos = $datos;	
	}

	function conf__form_origen(toba_ei_formulario $form)
	{
		$form->set_datos($this->s__datos);
		
	}
	
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__importar()
	{
		$id = array('proyecto' => $this->s__datos['proyecto'], 'componente' => $this->s__datos['item']);
		$info_item = toba_constructor::get_info($id, 'toba_item');
		$proyecto = toba_editor::get_proyecto_cargado();
		$nuevos_datos = array();
		$nuevos_datos['proyecto'] = $proyecto;
		$nuevos_datos['padre_proyecto'] = $proyecto;
		$nuevos_datos['padre'] = $this->s__datos['carpeta'];
		$nuevos_datos['fuente_datos'] = $this->s__datos['fuente'];
		$nuevos_datos['fuente_datos_proyecto'] = $proyecto;
		$nuevo_item = $info_item->clonar($nuevos_datos, $this->s__datos['carpeta_archivos']);
		toba::notificacion()->info('Operación importada exitosamente');
		admin_util::refrescar_editor_item($nuevo_item['componente']);
	}	
}

?>