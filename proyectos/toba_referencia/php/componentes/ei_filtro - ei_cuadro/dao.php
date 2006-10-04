<?php

class dao_importes
{
	static function get_importes()
	{
		return array(
			array( 'fecha' => '2005-05-20', 'importe' => 2500), 
			array( 'fecha' => '2005-05-21', 'importe' => 2200), 
			array( 'fecha' => '2005-05-22', 'importe' => 500), 		
			array( 'fecha' => '2005-05-23', 'importe' => 1500),
			array( 'fecha' => '2005-05-24', 'importe' => 2500)	
		);
	}
	
	static function get_cantidad_importes()
	{
		return 5;
	}
	
}

?>