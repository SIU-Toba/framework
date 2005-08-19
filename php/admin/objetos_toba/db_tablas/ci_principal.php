<?php
require_once('admin/objetos_toba/ci_editores_toba.php'); 
require_once("admin/db/toba_dbt.php");

class ci_principal extends ci_editores_toba
{
	protected $db_tablas;
	protected $seleccion_dependencia;
	protected $seleccion_dependencia_anterior;

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "db_tablas";
		$propiedades[] = "seleccion_dependencia";
		$propiedades[] = "seleccion_dependencia_anterior";
		return $propiedades;
	}

	function get_dbt()
	//Acceso al db_tablas
	{
		if (! isset($this->db_tablas)) {
			$this->db_tablas = toba_dbt::objeto_db_tablas();
			//$this->db_tablas->cargar( array('proyecto'=>'toba', 'objeto'=>'1400') );
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

	//*******************************************************************
	//**  DB registros  *************************************************
	//*******************************************************************
	
	function evt__dbr_form__alta($datos)
	{
		$this->get_dbt()->elemento("dependencias")->agregar_registro($datos);
	}
	
	function evt__dbr_form__baja()
	{
		$this->get_dbt()->elemento("dependencias")->eliminar_registro($this->seleccion_dependencia_anterior);
		$this->evt__dbr_form__cancelar();
	}
	
	function evt__dbr_form__modificacion($datos)
	{
		$this->get_dbt()->elemento("dependencias")->modificar_registro($this->seleccion_dependencia_anterior, $datos);
		$this->evt__dbr_form__cancelar();
	}
	
	function evt__dbr_form__carga()
	{
		if(isset($this->seleccion_dependencia)){
			$this->seleccion_dependencia_anterior = $this->seleccion_dependencia;
			return $this->get_dbt()->elemento("dependencias")->get_registro($this->seleccion_dependencia_anterior);
		}
	}

	function evt__dbr_form__cancelar()
	{
		unset($this->seleccion_dependencia_anterior);
		unset($this->seleccion_dependencia);
		$this->dependencias["dbr_cuadro"]->deseleccionar();
	}

	//-------------------------------------------------------------
	//-- Cuadro
	//-------------------------------------------------------------

	function evt__dbr_cuadro__seleccion($id)
	{
		$this->seleccion_dependencia = $id;
	}

	function evt__dbr_cuadro__carga()
	{
		return $this->get_dbt()->elemento("dependencias")->get_registros();
	}
	//-------------------------------------------------------------

	//*******************************************************************
	//** PROCESAR  ******************************************************
	//*******************************************************************

	function evt__procesar()
	{
		//Seteo los datos asociados al uso de este editor
		$this->get_dbt()->elemento('base')->set_registro_valor(0,"proyecto",toba::get_hilo()->obtener_proyecto() );
		$this->get_dbt()->elemento('base')->set_registro_valor(0,"clase_proyecto", "toba" );
		$this->get_dbt()->elemento('base')->set_registro_valor(0,"clase", "objeto_datos_relacion" );
		//Sincronizo el DBT
		$this->get_dbt()->sincronizar();	
	}
	//-------------------------------------------------------------------
}
?>