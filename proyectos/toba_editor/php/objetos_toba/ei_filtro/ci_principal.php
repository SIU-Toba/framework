<?php
require_once('objetos_toba/ci_editores_toba.php'); 
require_once('modelo/componentes/info_ei_filtro.php');

class ci_principal extends ci_editores_toba
{
	protected $clase_actual = 'objeto_ei_filtro';	
	
	function ini()
	{
		parent::ini();
		$ef = toba::memoria()->get_parametro('ef');
		//¿Se selecciono un ef desde afuera?
		if (isset($ef)) {
			$this->dependencia('efs')->seleccionar_ef($ef);
			$this->set_pantalla(2);
		}
	}
		
	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function conf__prop_basicas()
	{
		return $this->get_entidad()->tabla("prop_basicas")->get();
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_entidad()->tabla("prop_basicas")->set($datos);
		
	}

	//*******************************************************************
	//** Dialogo con el CI de EFs  **************************************
	//*******************************************************************
	
	function evt__2__salida()
	{
		$this->dependencia('efs')->limpiar_seleccion();
	}

	function get_dbr_efs()
	{
		return $this->get_entidad()->tabla('efs');
	}


	//*******************************************************************
	//** Dialogo con el CI de EVENTOS  **********************************
	//*******************************************************************

	function get_modelos_evento()
	{
		return info_ei_filtro::get_modelos_evento();
	}
	
	function get_eventos_estandar($modelo)
	{
		return info_ei_filtro::get_lista_eventos_estandar($modelo);
	}

	function evt__3__salida()
	{
		$this->dependencia('eventos')->limpiar_seleccion();
	}

	function get_dbr_eventos()
	{
		return $this->get_entidad()->tabla('eventos');
	}

}
?>