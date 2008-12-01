<?php
/**
 * @ignore
 */
abstract class toba_codigo_elemento
{
	protected $nombre;
	protected $identacion=0;
	protected $caracteres_tab = 4;
	protected $grupo = null;
	
	
	/**
	 * Permite indicar que un elemento del codigo pertenece a un grupo dado (por ejemplo tal dependencia del ci)
	 */
	function set_grupo($grupo)
	{
		$this->grupo = $grupo;
	}
	
	function get_grupo()
	{
		return $this->grupo;
	}
	
	function get_nombre()
	{
		return $this->nombre;	
	}
	
	function identar($nivel)
	{
		$this->identacion += $nivel;
	}
	
	function identado()
	{
		return str_repeat("\t",$this->identacion);
	}
	
	function get_caracteres_identacion()
	{
		return $this->identacion * $this->caracteres_tab;	
	}
	
	abstract function get_codigo();
}
?>