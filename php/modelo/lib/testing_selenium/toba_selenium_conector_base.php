<?php
class toba_selenium_conector_base 
{	
	static $id_campo_usuario_base = 'postgres';
	static $id_campo_pwd_base = 'postgres';
	static $id_base = 'toba_trunk';
	static $host_motor = 'localhost';
	
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
		$host = self::get_host_motor(); 	
		$user = self::get_id_usuario_base(); 
		$pass = self::get_id_password_base(); 
		$db = self::get_id_basae(); 		
		//var_dump("host=$host dbname=$db user=$user password=$pass");
		return "host=$host dbname=$db user=$user password=$pass";
	}
		
	static function get_id_usuario_base()
	{		
		return (defined('TEST_ID_USUARIO_BASE')) ? TEST_ID_USUARIO_BASE: self::$id_campo_usuario_base;
	}
	
	static function get_id_password_base()
	{
		return (defined('TEST_ID_PWD_BASE')) ? TEST_ID_PWD_BASE: self::$id_campo_pwd_base;
	}
	
	static function get_id_base()
	{
		return (defined('TEST_ID_BASE')) ? TEST_ID_BASE: self::$id_base;
	}

	static function get_host_motor()
	{
		return (defined('TEST_HOST_MOTOR')) ? TEST_HOST_MOTOR: self::$host_motor;
	}	
}
?>
