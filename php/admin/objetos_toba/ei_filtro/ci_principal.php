<?php
require_once('admin/objetos_toba/ci_editores_toba.php'); 
require_once("admin/db/toba_dbt.php");

class ci_principal extends ci_editores_toba
{
	//efss
	private $id_intermedio_efs;

	function destruir()
	{
		parent::destruir();
		//ei_arbol($this->get_dbt()->elemento('efss')->info(true),"efsS");
		//ei_arbol($this->get_estado_sesion(),"Estado sesion");
	}



	function get_dbt()
	//Acceso al db_tablas
	{
		if (! isset($this->db_tablas)) {
			$this->db_tablas = toba_dbt::objeto_ei_formulario_ml();
			//$this->db_tablas->cargar( array('proyecto'=>'toba', 'objeto'=>'1387') );
		}
		if($this->cambio_objeto){	
			$this->db_tablas->cargar( $this->id_objeto );
		}		
		return $this->db_tablas;
	}

	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function evt__base__carga()
	{
		return $this->get_dbt()->elemento("base")->get();
	}

	function evt__base__modificacion($datos)
	{
		$this->get_dbt()->elemento("base")->set($datos);
	}

	function evt__prop_basicas__carga()
	{
		return $this->get_dbt()->elemento("prop_basicas")->get();
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_dbt()->elemento("prop_basicas")->set($datos);
		
	}

	//*******************************************************************
	//** Dialogo con el CI de EFs  **************************************
	//*******************************************************************
	
	function evt__salida__2()
	{
		$this->dependencias['efs']->limpiar_seleccion();
	}

	function get_dbr_efs()
	{
		return $this->get_dbt()->elemento('efs');
	}


	//*******************************************************************
	//** Dialogo con el CI de EVENTOS  **********************************
	//*******************************************************************

	function get_eventos_estandar()
	{
		require_once('api/elemento_objeto_ei_filtro.php');
		return elemento_objeto_ei_filtro::get_lista_eventos_estandar();
	}

	function evt__salida__3()
	{
		$this->dependencias['eventos']->limpiar_seleccion();
	}

	function get_dbr_eventos()
	{
		return $this->get_dbt()->elemento('eventos');
	}

	//*******************************************************************
	//*******************  PROCESAMIENTO  *******************************
	//*******************************************************************

	function evt__procesar()
	{
		//Seteo los datos asociados al uso de este editor
		$this->get_dbt()->elemento('base')->set_registro_valor(0,"proyecto",toba::get_hilo()->obtener_proyecto() );
		$this->get_dbt()->elemento('base')->set_registro_valor(0,"clase_proyecto", "toba" );
		$this->get_dbt()->elemento('base')->set_registro_valor(0,"clase", "objeto_ei_filtro" );
		//Sincronizo el DBT
		$this->get_dbt()->sincronizar();		}
	//-------------------------------------------------------------------
}
?>