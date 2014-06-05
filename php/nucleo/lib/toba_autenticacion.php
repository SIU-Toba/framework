<?php
class toba_autenticacion
{
	static $marca_login_basico = 'uso_login_basico';
	static $marca_login_central = 'uso_login_central';

	
	function usar_login_basico()
	{
		$this->setear_marca_login(self::$marca_login_basico);
	}
	
	function uso_login_basico()
	{
		return $this->verificar_marca_login(self::$marca_login_basico);
	}
	
	function usar_login_centralizado()
	{
		$this->setear_marca_login(self::$marca_login_central);
	}
	
	function uso_login_centralizado()
	{
		return $this->verificar_marca_login(self::$marca_login_central);
	}
	
	function eliminar_login_basico()
	{
		$this->eliminar_marca_login(self::$marca_login_basico);
	}
		
	// --- Funciones que trabajan sobre la session de PHP, debido a que la memoria de Toba no alcanza a guardarse en este tipo de autenticacion.
	protected function eliminar_marca_login($marca)
	{
		if (isset($_SESSION[$marca])) {
			unset($_SESSION[$marca]);
		}
	}
	
	protected function setear_marca_login($marca)
	{
		$_SESSION[$marca] = 'true';
	}
	
	protected function verificar_marca_login($marca)
	{
		return (isset($_SESSION[$marca]) && $_SESSION[$marca]);
	}
}
?>