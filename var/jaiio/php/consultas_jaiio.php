<?php
class consultas_jaiio
{
	function get_nacionalidades()
	{
		$sql = "SELECT nacionalidad, descripcion FROM nacionalidades";
		return consultar_fuente($sql);
	}

}
?>