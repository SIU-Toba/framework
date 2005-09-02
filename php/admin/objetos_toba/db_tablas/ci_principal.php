<?php
require_once('admin/objetos_toba/ci_editores_toba.php'); 
/*
	Cosas faltantes:

		- Control de que existan dependencias
		- Borrado de dependencias
		- Validacion de relaciones
*/

class ci_principal extends ci_editores_toba
{
	protected $seleccion_relacion;
	protected $seleccion_relacion_anterior;

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "seleccion_relacion";
		$propiedades[] = "seleccion_relacion_anterior";
		return $propiedades;
	}

	//*******************************************************************
	//*****************  PROPIEDADES BASICAS  ***************************
	//*******************************************************************

	function evt__base__carga()
	{
		return $this->get_entidad()->tabla("base")->get();
	}

	function evt__base__modificacion($datos)
	{
		$this->get_entidad()->tabla("base")->set($datos);
	}

	function evt__prop_basicas__carga()
	{
		return $this->get_entidad()->tabla("prop_basicas")->get();
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_entidad()->tabla("prop_basicas")->set($datos);
	}

	//*******************************************************************
	//**  DEPENDENCIAS  *************************************************
	//*******************************************************************

	function evt__dependencias__carga()
	{
		return $this->get_entidad()->tabla('dependencias')->get_filas(null,true);	
	}

	function evt__dependencias__modificacion($datos)
	{
		/*
			ATENCION! si se borran dependencias hay que borrar tambien
			sus relaciones
		*/
		$this->get_entidad()->tabla('dependencias')->procesar_filas($datos);
	}

	//*******************************************************************
	//**  RELACIONEs  *************************************************
	//*******************************************************************

	function get_tabla_relaciones()
	//Abastecimiento del CI de relaciones
	{
		return $this->get_entidad()->tabla("relaciones");	
	}

	//*******************************************************************
	//** PROCESAR  ******************************************************
	//*******************************************************************

	function evt__procesar()
	{
		//Seteo los datos asociados al uso de este editor
		$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"proyecto",toba::get_hilo()->obtener_proyecto() );
		//$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"proyecto","toba_testing" );
		$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"clase_proyecto", "toba" );
		$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"clase", "objeto_datos_relacion" );
		//Sincronizo el DBT
		$this->get_entidad()->sincronizar();	
	}
	//-------------------------------------------------------------------
}
?>