<?php
require_once('objetos_toba/ci_editores_toba.php');
require_once('modelo/componentes/info_ei_formulario_ml.php');

class ci_principal extends ci_editores_toba
{
	protected $clase_actual = 'objeto_ei_formulario_ml';	
	
	function ini()
	{
		parent::ini();
		$ef = toba::hilo()->obtener_parametro('ef');
		//¿Se selecciono un ef desde afuera?
		if (isset($ef)) {
			$this->set_pantalla(2);
			$this->dependencia('efs')->seleccionar_ef($ef);
		}
	}

	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function conf__prop_basicas()
	{
		return $this->get_entidad()->tabla("prop_basicas")->get();
	}
	
	function evt__base__modificacion($datos)
	{
		$this->get_entidad()->tabla("base")->set($datos);
	}

	function evt__prop_basicas__modificacion($datos)
	{
		if (! $datos['filas_ordenar']) {
			$datos['columna_orden'] = '';
		}
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
		return info_ei_formulario_ml::get_modelos_evento();
	}
	
	function get_eventos_estandar($modelo)
	{
		return info_ei_formulario_ml::get_lista_eventos_estandar($modelo);
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