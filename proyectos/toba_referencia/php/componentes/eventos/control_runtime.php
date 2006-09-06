<?php 

class control_runtime extends toba_ci
{

	function conf()
	{
		
	}

	function conf__cuadro()
	{
		$datos[0]['id'] = 'A';	
		$datos[0]['desc'] = 'Desc. A';	
		$datos[1]['id'] = 'B';	
		$datos[1]['desc'] = 'Desc. B';	
		return $datos;
	}

}
?>