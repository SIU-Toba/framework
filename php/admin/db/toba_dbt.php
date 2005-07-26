<?php
require_once("admin/db/autoload.php");

class toba_dbt
{

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
}
?>