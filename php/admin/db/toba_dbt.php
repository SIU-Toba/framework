<?php
require_once("admin/db/autoload.php");

class toba_dbt
{
	function item()
	{
		return new dbt_item("instancia");
	}

	function objeto_ci()
	{
		return new dbt_objeto_ci("instancia");	
	}

	function objeto_ei_cuadro()
	{
		return new dbt_objeto_ei_cuadro("instancia");	
	}

	function objeto_ei_formulario_ml()
	{
		return new dbt_objeto_ei_formulario_ml("instancia");	
	}
	
	function objeto_ei_formulario()
	{
		return new dbt_objeto_ei_formulario("instancia");	
	}

	function objeto_ei_filtro()
	{
		return new dbt_objeto_ei_filtro("instancia");	
	}

	function objeto_db_registros()
	{
		return new dbt_objeto_db_registros("instancia");	
	}

	function objeto_db_tablas()
	{
		return new dbt_objeto_db_tablas("instancia");	
	}

	function objeto_ei_arbol()
	{
		return new dbt_objeto_ei_arbol("instancia");	
	}

	function objeto_ei_archivos()
	{
		return new dbt_objeto_ei_archivos("instancia");	
	}

	function objeto_ei_calendario()
	{
		return new dbt_objeto_ei_calendario("instancia");	
	}
}
?>