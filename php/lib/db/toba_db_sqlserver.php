<?php
/**
 * Driver de conexión con SQLServer 2005 o posterior.
 * @package Fuentes
 * @subpackage Drivers
 */
class toba_db_sqlserver extends toba_db
{

	function __construct($profile, $usuario, $clave, $base, $puerto)
	{
		$this->motor = "sqlserver";
		parent::__construct($profile, $usuario, $clave, $base, $puerto);
	}
	
	/**
	 * Retorna el string de conexion para el motor
	 * @return string
	 */
	function get_dsn()
	{
		$profile = $this->profile; 
		if ($this->puerto != '') {
			$profile = "{$this->profile}, {$this->puerto}";
		}
		
		$ssl = $certs = '';
		if ($this->sslmode != '') {
			$ssl =  "Encrypt={$this->sslmode}";
			$certs = "TrustServerCertificate=0";							//No confia en certs autofirmados
		}	
		
		return "sqlserver:host=$profile;dbname=$this->base;$ssl;$certs";
	}

	/**
	 * Retorna una ER para quitar comentarios de la SQL
	 */
	function get_separador_comentarios()
	{
		return "/\/\*([^'])*?\*\/|(?:-{2,}[^']*?\R)/im";
	}
}
?>