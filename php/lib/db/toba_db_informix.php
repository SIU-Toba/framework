<?php
/**
 * Driver de conexión con informix
 * @package Fuentes
 * @subpackage Drivers
 */
class toba_db_informix extends toba_db
{
	protected $id_instancia_server;
	
	function __construct($profile, $usuario, $clave, $base, $puerto, $server)
	{
		$this->motor = "informix";
		$this->id_instancia_server = $server;
		parent::__construct($profile, $usuario, $clave, $base, $puerto);
	}

	function get_dsn()
	{
		$puerto = ($this->puerto != '') ? $this->puerto : '1526';
		$str_conexion ="informix:host=$this->profile;service=$puerto;database=$this->base;server={$this->id_instancia_server}; protocol=olsoctcp;EnableScrollableCursors=1";
		return $str_conexion;
	}
	
	/**
	 * Ejecuta un BEGIN WORK en la conexión
	 */	
	function abrir_transaccion()
	{
		$sql = 'BEGIN WORK';
		$this->ejecutar($sql);
		toba::logger()->debug("************ ABRIR transaccion ($this->base@$this->profile) ****************", 'toba');
	}
	
	/**
	 * Ejecuta un ROLLBACK WORK en la conexión
	 */		
	function abortar_transaccion()
	{
		$sql = 'ROLLBACK WORK';
		$this->ejecutar($sql);		
		toba::logger()->debug("************ ABORTAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
	}

	/**
	 * Ejecuta un COMMIT WORK en la conexión
	 */		
	function cerrar_transaccion()
	{
		$sql = "COMMIT WORK";
		$this->ejecutar($sql);		
		toba::logger()->debug("************ CERRAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
	}	
}
?>
