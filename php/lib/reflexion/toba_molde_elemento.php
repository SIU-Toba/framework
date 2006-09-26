<?php

abstract class toba_molde_elemento
{
	protected $identacion=0;
	
	function identar($nivel)
	{
		$this->identacion += $nivel;
	}
	
	function identado()
	{
		return str_repeat("\t",$this->identacion);
	}
	
	abstract function get_codigo();
}
?>