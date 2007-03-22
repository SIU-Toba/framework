<?php 
require_once('configuracion/ci_abm_basico.php');

class ci_mensajes_componentes extends ci_abm_basico
{
	
	function ini()
	{
		if (! toba::zona()->cargada()) {
			throw new toba_error('Necesita seleccionar un componente.');
		}	
	}
	
	function get_id_objeto()
	{
		$editable = toba::zona()->get_editable();		
		return $editable[1];
	}
	
	function get_datos_listado()
	{
		return dao_editores::get_mensajes_objeto($this->get_id_objeto());
	}
	
	function evt__formulario__alta($datos)
	{
		$datos['objeto'] = $this->get_id_objeto();
		$datos['objeto_proyecto'] = toba_editor::get_proyecto_cargado();
		parent::evt__formulario__alta($datos);
	}	
}

?>