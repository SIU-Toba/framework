<?php
require_once('3ros/simpletest/unit_tester.php');

class test_toba extends UnitTestCase
{
	function __construct()
	{

	}
	
	function tearDown()
	{
		$this->restaurar_estado($this->sentencias_restauracion());	
	}

	protected function sentencias_restauracion()
	{
		return array();
	}
	
	protected function restaurar_estado($sentencias)
	{
		foreach ($sentencias as $sql) {
			$rs = toba::get_db('instancia')->Execute($sql);
			if (!$rs)
			    $this->Fail("Error restaurando estado:\n$sql\n".toba::get_db('instancia')->ErrorMsg());
		}	
	}

}


?>