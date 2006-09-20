<?php
require_once('info_ei.php');

class info_ei_archivos extends info_ei
{
	//---------------------------------------------------------------------	
	//-- EVENTOS
	//---------------------------------------------------------------------

	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();
		$eventos['seleccionar_archivo']['parametros'] = array('archivo');
		$eventos['seleccionar_archivo']['comentarios'] = array();
		return $eventos;
	}
}
?>