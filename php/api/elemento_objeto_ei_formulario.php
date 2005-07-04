<?php
require_once('api/elemento_objeto.php');

class elemento_objeto_ei_formulario extends elemento_objeto
{

	function eventos_predefinidos()
	{
		$eventos = array();
		if ($this->datos['apex_objeto_ut_formulario'][0]['ev_agregar']) {
			$eventos[] = 'alta';
		}
		if ($this->datos['apex_objeto_ut_formulario'][0]['ev_mod_eliminar']) {
			$eventos[] = 'baja';
		}		
		if ($this->datos['apex_objeto_ut_formulario'][0]['ev_mod_modificar']) {
			$eventos[] = 'modificacion';
		}
		if ($this->datos['apex_objeto_ut_formulario'][0]['ev_mod_limpiar']) {
			$eventos[] = 'cancelar';
		}
		return $eventos;
	}
	
	

}


?>