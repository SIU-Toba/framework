<?php
class toba_selenium_conector_base 
{	
	static function ejecutar_base($sql)
	{
		$conexion = pg_connect(self::get_dsn()) or die ("No se pudo conectar\n");
		pg_query($conexion, $sql);
		pg_close($conexion);		
	}
	
	static function buscar_base($sql, $campo = null)
	{
		$res = null;
		$conexion = pg_connect(self::get_dsn()) or die ("No se pudo conectar\n");
		$result = pg_query($conexion, $sql);
		$rows = pg_fetch_all($result);
		if (! is_null($campo) && $rows !== false) {
			$res = current($rows)[$campo];
		}
		pg_close($conexion);		
		return $res;
	}

	static function get_dsn()
	{
		$host = "localhost"; 	$user = "postgres"; 
		$pass = ""; $db = "toba_trunk"; 		
		return "host=$host dbname=$db user=$user password=$pass";
	}
}
?>
