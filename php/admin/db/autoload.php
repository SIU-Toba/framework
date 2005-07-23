<?

	function __autoload($clase)
	{
		static $definicion;
		$definicion["dbt_ci"] = 		"admin/objetos_toba/ci/dbt_ci.php";
		$definicion["dbt_ei_cuadro"] = 	"admin/objetos_toba/ei_cuadro/dbt_ei_cuadro.php";
		if(isset($definicion[$clase])){
			//echo "AUTOLOAD: " . $definicion[$clase] ."<br>";
			require_once($definicion[$clase]);
		}	
	}
	
?>