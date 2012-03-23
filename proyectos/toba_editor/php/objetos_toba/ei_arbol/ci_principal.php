<?php
require_once('objetos_toba/ci_editores_toba.php');

class ci_principal extends ci_editores_toba
{
	protected $clase_actual = 'toba_ei_arbol';
	
	function get_dbr_eventos()
	{
		return $this->get_entidad()->tabla('eventos');
	}
	
	function get_eventos_estandar($modelo)
	{
		return info_ei::get_lista_eventos_estandar($modelo);
	}	
	
	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

}
?>