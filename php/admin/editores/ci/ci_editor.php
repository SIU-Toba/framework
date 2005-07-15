<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once("admin/editores/autoload.php");

class ci_editor extends objeto_ci
{
	protected $db_tablas;

/*
	function __construct($id)
	{
		parent::__construct($id);	
		$this->db_tablas = $this->get_dbt();
	}
*/
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "db_tablas";
		return $propiedades;
	}

	function get_dbt()
	//Acceso al db_tablas
	{
		if (! isset($this->db_tablas)) {
			$this->db_tablas = new dbt_ci($this->info['fuente']);
		}
		return $this->db_tablas;
	}

	//-------------------------------------------------------------------
	//--- Eventos
	//-------------------------------------------------------------------

	//--- Pantalla 1 ---

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

	//--- Pantalla 2 ---

	function llenar_combo_dependencias()
	{
		return null;		
	}

/*
	function evt__procesar()
	{
	}

	function evt__cancelar()
	{
	}

	function evt__inicializar()
	{
	}

	function evt__limpieza_memoria()
	{
	}

	function evt__post_recuperar_interaccion()
	{
	}

	function evt__validar_datos()
	{
	}

	function evt__error_proceso_hijo()
	{
	}

	function evt__pre_cargar_datos_dependencias()
	{
	}

	function evt__post_cargar_datos_dependencias()
	{
	}

	//----------------------------- base -----------------------------

	function evt__dependencias__carga()
	{
		//if isset($this->datos_dependencias)
		//	return $this->datos_dependencias;
	}

	//----------------------------- pantallas -----------------------------
	function evt__pantallas__carga()
	{
		//if isset($this->datos_pantallas)
		//	return $this->datos_pantallas;
	}

	function evt__pantallas__baja()
	{
	}

	function evt__pantallas__cancelar()
	{
	}

	//----------------------------- pantallas_ei -----------------------------
	function evt__pantallas_ei__carga()
	{
		//if isset($this->datos_pantallas_ei)
		//	return $this->datos_pantallas_ei;
	}

	function evt__pantallas_ei__modificacion($registros)
	{
		//$this->datos_pantallas_ei = $registros;	
	}

	//----------------------------- pantallas_lista -----------------------------
	function evt__pantallas_lista__carga()
	{
		//if isset($this->datos_pantallas_lista)
		//	return $this->datos_pantallas_lista;
	}

	function evt__pantallas_lista__modificacion($registros)
	{
		//$this->datos_pantallas_lista = $registros;	
	}

	//----------------------------- prop_basicas -----------------------------
*/
}
?>