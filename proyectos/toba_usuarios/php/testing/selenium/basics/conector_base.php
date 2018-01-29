<?php
class conector_base 
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
	
	
	/*static function conectar_base($accion, $string, $campo='nada')
	{
		$host = "localhost"; 	$user = "postgres"; 
		$pass = ""; $db = "toba_trunk"; 
		$conexion = pg_connect("host=$host dbname=$db user=$user password=$pass") or die ("No se pudo conectar\n");
		switch ($accion) {
			case 'SELECT':
				$input = pg_query($string);
				$resultado = pg_fetch_all($input);
				$res = $resultado[0][$campo];
				return $res;
				break;
			case 'INSERT':
			case 'DELETE':
				$input = pg_query($string);
				break;
		}
		pg_close($conexion); 
	}*/
}
?>
