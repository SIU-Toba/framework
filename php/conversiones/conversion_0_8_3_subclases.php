<?
require_once("conversion_toba.php");

class conversion_0_8_3_subclases extends conversion_toba
{
	function get_version()
	{
		return "0.8.3.subclases";	
	}

	function cambio_subclases()
	{
	/**
		$sql = "UPDATE apex_objeto SET clase = 'objeto_ci' WHERE clase = 'ci_cn'";
		$this->ejecutar_sql($sql,"instancia");

		MIGRAR SUBCLASES que dejan de existir

		La subclase del ci 'ci_cn' deja de existir. La logica de las mismas pasa al CI padre.
		
		- Hay que pasar a objeto_cn:
			- objeto_cn_t
		- Hay que pasar a objeto_ci
			- objeto_ci_abm
			- ci_abm_dbr
			- ci_abm_dbt
			- ci_abm_nav	
	*/
	}
}
?>