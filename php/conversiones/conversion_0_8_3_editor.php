<?
require_once("conversion_toba.php");

class conversion_0_8_3_fotos extends conversion_toba
{
	function get_version()
	{
		return "0.8.3.editor";	
	}

	/**
		La subclase del ci 'ci_cn' deja de existir. La logica de las mismas pasa al CI padre.
		
		- Hay que pasar a objeto_cn:
			- objeto_cn_t
		- Hay que pasar a objeto_ci
			- objeto_ci_abm
			- ci_abm_dbr
			- ci_abm_dbt
			- ci_abm_nav	
		
	*/
	function cambio_migrar_objetos_clase_ci_cn()
	{
		$sql = "UPDATE apex_objeto SET clase = 'objeto_ci' WHERE clase = 'ci_cn'";
		$this->ejecutar_sql($sql,"instancia");
	}


	/*
		Migrar eventos:
			- Si no hay evento en un form, es una modificacion implicita
			- Poner los grupos de modif, baja y cancelar
	*/
	
	/*
		Establecer el menu en los parametros de los proyectos.
	
	*/

	/*
		Si un cuadro no tiene clave definida, seleccionar la clave del DBR
	*/
}