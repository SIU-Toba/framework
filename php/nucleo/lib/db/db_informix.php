<?
require_once("db.php");

class db_informix extends db
{
	function __construct($profile, $usuario, $clave, $base)
	{
		$this->motor = "informix";
		parent::__construct($profile, $usuario, $clave, $base);
	}
	
	function abrir_transaccion()
	{
		$sql = 'BEGIN WORK';
		$this->ejecutar($sql);
		toba::get_logger()->debug("************ ABRIR transaccion ($this->base@$this->profile) ****************", 'toba');
	}
	
	function abortar_transaccion()
	{
		$sql = 'ROLLBACK WORK';
		$this->ejecutar($sql);		
		toba::get_logger()->debug("************ ABORTAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
	}
	
	function cerrar_transaccion()
	{
		$sql = "COMMIT WORK";
		$this->ejecutar($sql);		
		toba::get_logger()->debug("************ CERRAR transaccion ($this->base@$this->profile) ****************", 'toba'); 
	}	
}
?>
