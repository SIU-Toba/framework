<?
require_once("conversion_toba.php");

/**
*	----------------------------------------------
*	 MIGRACION y LIMPIEZA DE CLASES NO UTILIZADAS
*	----------------------------------------------
*/
class conversion_0_8_3_2 extends conversion_toba
{
	function get_version()
	{
		return "0.8.3.2";	
	}

	/**
	*	Las clases ci_cn, objeto_ci_abm, ci_abm_dbr, ci_abm_dbt y ci_abm_nav pasan a ser ci comunes.
	*/
	function cambio_subclases_ci()
	{
		$sql = "UPDATE apex_objeto 
				SET clase = 'objeto_ci' 
				WHERE 
					proyecto = '{$this->proyecto}' AND
					clase IN ('ci_cn', 'objeto_ci_abm', 'ci_abm_dbr', 'ci_abm_dbt', 'ci_abm_nav')
					
		";
		$this->ejecutar_sql($sql,"instancia");
	}
	
	
	function cambio_subclases_cn()
	{
		$sql = "UPDATE apex_objeto
				SET clase = 'objeto_cn' 
				WHERE 
					proyecto = '{$this->proyecto}' AND				
					clase IN ('objeto_cn_t')
		";
		$this->ejecutar_sql($sql);		
	}
	
	/**
	*	Todos los items con patrones relacionados con ci pasan a usar el patrón CI, incluso los que tienen popup
	*/
	function cambio_patrones_ci()
	{
		$sql = "
			UPDATE apex_item
			SET
				actividad_patron = 'CI'
			WHERE
				proyecto = '{$this->proyecto}' AND							
				actividad_patron IN ('ci', 'generico_ci_cn', 'ci_cn_popup', 'CI_POPUP')
		";
		$this->ejecutar_sql($sql);

	}
	
}
?>