<?

	function __autoload($clase)
	{
		static $definicion;
		$definicion["dbt_objeto_ci"] = 			"admin/db/dbt_objeto_ci.php";
		$definicion["dbt_objeto_ei_cuadro"] = 	"admin/db/dbt_objeto_ei_cuadro.php";
		if(isset($definicion[$clase])){
			//echo "AUTOLOAD: " . $definicion[$clase] ."<br>";
			require_once($definicion[$clase]);
		}	
	}
	
?>