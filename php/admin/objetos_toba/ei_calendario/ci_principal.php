<?php
require_once('admin/objetos_toba/ci_editores_toba.php');

class ci_principal extends ci_editores_toba
{

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

	//*******************************************************************
	//*******************  PROCESAMIENTO  *******************************
	//*******************************************************************

	function evt__procesar()
	{
		//Seteo los datos asociados al uso de este editor
		$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"proyecto",toba::get_hilo()->obtener_proyecto() );
		$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"clase_proyecto", "toba" );
		$this->get_entidad()->tabla('base')->set_fila_columna_valor(0,"clase", "objeto_ei_calendario" );
		//Sincronizo el DBT
		$this->get_entidad()->sincronizar();		}
	//-------------------------------------------------------------------
}