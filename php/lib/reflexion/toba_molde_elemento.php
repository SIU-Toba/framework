<?php

abstract class toba_molde_elemento
{
	protected $nombre;
	protected $identacion=0;
	protected $caracteres_tab = 4;
	
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