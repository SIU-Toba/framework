<?

	function __autoload($clase)
	{
		static $definicion;
		$definicion["dbt_objeto_ci"] = 					"admin/db/dbt_objeto_ci.php";
		$definicion["dbt_objeto_ei_cuadro"] = 			"admin/db/dbt_objeto_ei_cuadro.php";
		$definicion["dbt_objeto_ei_formulario_ml"] = 	"admin/db/dbt_objeto_ei_formulario_ml.php";
		$definicion["dbt_objeto_ei_formulario"] =	 	"admin/db/dbt_objeto_ei_formulario.php";
		$definicion["dbt_objeto_ei_filtro"] =		 	"admin/db/dbt_objeto_ei_filtro.php";
		$definicion["dbt_objeto_db_registros"] =	 	"admin/db/dbt_objeto_db_registros.php";
		$definicion["dbt_objeto_db_tablas"] =		 	"admin/db/dbt_objeto_db_tablas.php";
		if(isset($definicion[$clase])){
			//echo "AUTOLOAD: " . $definicion[$clase] ."<br>";
			require_once($definicion[$clase]);
		}	
	}
?>