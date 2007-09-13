<?php 
class dt_persona extends toba_datos_tabla
{
	
	function get_listados()
	{
		$sql = "
			SELECT 
				id,
				nombre
			FROM
				ref_persona
		";
		return toba::db()->consultar($sql);	
	}
	
}

?>