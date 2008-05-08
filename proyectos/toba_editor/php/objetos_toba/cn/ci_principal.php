<?php
require_once('objetos_toba/ci_editores_toba.php');

class ci_principal extends ci_editores_toba
{
	protected $clase_actual = 'toba_cn';

	// *******************************************************************
	// *******************  tab DEPENDENCIAS  ****************************
	// *******************************************************************
	/*
		Metodos necesarios para que el CI de eventos funcione
	*/
	function evt__1__salida()
	{
		$this->dep('dependencias')->limpiar_seleccion();
	}

	function get_dbr_dependencias()
	{
		return $this->get_entidad()->tabla('dependencias');
	}	
	
}