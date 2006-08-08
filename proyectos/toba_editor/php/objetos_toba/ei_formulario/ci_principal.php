<?php
require_once('objetos_toba/ci_editores_toba.php'); 
require_once('modelo/componentes/info_ei_formulario.php');

class ci_principal extends ci_editores_toba
{
	protected $ef_seleccionado;
	protected $clase_actual = 'objeto_ei_formulario';	

	function __construct($id)
	{
		parent::__construct($id);
		$ef = toba::get_hilo()->obtener_parametro('ef');
		//¿Se selecciono un ef desde afuera?
		if (isset($ef)) {
			$this->ef_seleccionado = $ef;
		}
	}
	
	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_entidad()->tabla('efss')->info(true),"efsS");
		//ei_arbol($this->get_estado_sesion(),"Estado sesion");
	}
	
	function get_etapa_actual()
	{
		if (isset($this->ef_seleccionado)) {
			return 2;	//Si se selecciono un ef desde afuera va a la pantalla de edición de ef
		} 
		return parent::get_etapa_actual();
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

	//Antes de cargar los datos de los efs, ver si alguno particular se selecciono desde afuera
	function evt__pre_cargar_datos_dependencias__2()
	{
		if (isset($this->ef_seleccionado)) {
			$this->dependencia('efs')->seleccionar_ef($this->ef_seleccionado);
		}
	}
		
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
		return info_ei_formulario::get_modelos_evento();
	}
	
	function get_eventos_estandar($modelo)
	{
		return info_ei_formulario::get_lista_eventos_estandar($modelo);
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