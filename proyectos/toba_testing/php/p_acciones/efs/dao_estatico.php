<?php

class dao_estatico
{
	function get_combo_dao1($pal1, $pal2)
	{
		return array(
				array('clave' => $pal1."_".$pal2, 'desc' => "$pal1 - $pal2"),
				array('clave' => 'clave', 'desc' => "Valor")			
			);
	}	
	
	
	function get_combo_dao_comp1($pal1, $pal2)
	{
		return array(
				array('clave1' => $pal1, 'clave2' => $pal2, 'desc' => "$pal1 - $pal2"),
				array('clave1' => 'clave', 'clave2' => 'clave2', 'desc' => "Valor")			
			);
	}	
		
}

?>