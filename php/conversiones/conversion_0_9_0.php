<?
require_once("conversion_toba.php");

class conversion_0_9_0 extends conversion_toba
{
	function get_version()
	{
		return "0.9.0";	
	}

	/**
	*	Las claves pasan a encriptarse con md5 (los passwords planos siguen funcionando)
	*/
	function cambio_claves_encriptadas()
	{
		$sql = "UPDATE apex_usuario SET clave=md5(clave), autentificacion='md5' 
				WHERE autentificacion IS NULL OR autentificacion='plano'";
		$this->ejecutar_sql($sql,"instancia");	
	}
}