<?php
require_once('info_ei.php');

class info_ei_arbol extends info_ei
{
	//---------------------------------------------------------------------	
	//-- EVENTOS
	//---------------------------------------------------------------------

	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();	
		$eventos['cambio_apertura']['parametros'] = array('apertura');
		$eventos['cambio_apertura']['comentarios'] = array("arreglo asociativo 'id_del_nodo' => 0|1 determinando si esta abierto o no");
		$eventos['ver_propiedades']['parametros'] = array('nodo');
		$eventos['ver_propiedades']['comentarios'] = array();
		return $eventos;
	}
}
?>