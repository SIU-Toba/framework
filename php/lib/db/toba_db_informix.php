<?php
/**
 * Driver de conexi�n con informix
 * @package Fuentes
 * @subpackage Drivers
 */
class toba_db_informix extends toba_db
{
	function __construct($profile, $usuario, $clave, $base, $puerto)
	{
		$this->motor = "informix";
		parent::__construct($profile, $usuario, $clave, $base, $puerto);
	}
	
	/**
	 * Ejecuta un BEGIN WORK en la conexi�n
	 */	
	function abrir_transaccion()
	{
		$sql = 'BEGIN WORK';
		$this->ejecutar($sql);
		toba::logger()->debug("************ ABRIR transaccion ($this->base@$this->profile) ****************", 'toba');
	}
	
	/**
	 * Ejecuta un ROLLBACK WORK en la conexi�n
	 */		
	function abortar_transaccion()
	{
		$sql = 'ROLLBACK WORK';
		$this->ejecutar($sql);		
		toba::logger()->debug("************ ABORTAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
	}

	/**
	 * Ejecuta un COMMIT WORK en la conexi�n
	 */		
	function cerrar_transaccion()
	{
		$sql = "COMMIT WORK";
		$this->ejecutar($sql);		
		toba::logger()->debug("************ CERRAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
	}	
}
?>
