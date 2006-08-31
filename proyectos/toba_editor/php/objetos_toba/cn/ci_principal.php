<?php
require_once('objetos_toba/ci_editores_toba.php');

class ci_principal extends ci_editores_toba
{
	protected $clase_actual = 'toba_cn';

	function get_dbr_dependencias()
	{
		return $this->dependencia('datos')->tabla('dependencias');	
	}
	
}