<?php
require_once('objetos_toba/ci_editores_toba.php'); 

class ci_principal extends ci_editores_toba
{
	protected $clase_actual = 'toba_ei_filtro';	
		
	function ini()
	{
		parent::ini();
		$col = toba::memoria()->get_parametro('col');
		//¿Se selecciono una columna desde afuera?
		if (isset($ef)) {
			$this->dependencia('cols')->seleccionar_ef($ef);
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
	//** Dialogo con el CI de COLUMNAS  **************************************
	//*******************************************************************
	
	function evt__2__salida()
	{
		$this->dependencia('cols')->limpiar_seleccion();
	}
	
	function get_dbr_efs()
	{
		return $this->get_entidad()->tabla('cols');
	}


	//*******************************************************************
	//** Dialogo con el CI de EVENTOS  **********************************
	//*******************************************************************


	function get_eventos_estandar($modelo)
	{
		return toba_ei_filtro_info::get_lista_eventos_estandar($modelo);
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