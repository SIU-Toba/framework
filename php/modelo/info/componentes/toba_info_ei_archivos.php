<?php
class toba_info_ei_archivos extends toba_info_ei
{
	//------------------------------------------------------------------------
	//------ METACLASE -------------------------------------------------------
	//------------------------------------------------------------------------

	function get_molde_subclase()
	{
		return $this->get_molde_vacio();
	}
	
	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();
		$eventos['seleccionar_archivo']['parametros'] = array('archivo');
		$eventos['seleccionar_archivo']['comentarios'] = array();
		return $eventos;
	}
}
?>