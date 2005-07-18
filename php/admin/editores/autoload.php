<?

	function __autoload($clase)
	{
		static $definicion;
		$definicion["dbt_ci"] = "admin/editores/ci/dbt_ci.php";
		$definicion["dbt_ei_cuadro"] = "admin/editores/ei_cuadro/dbt_ei_cuadro.php";
		if(isset($definicion[$clase])){
			//echo "AUTOLOAD: " . $definicion[$clase] ."<br>";
			require_once($definicion[$clase]);
		}	
	}
	
?>