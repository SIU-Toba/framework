<?php
class dt_apex_proyecto extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = 'SELECT proyecto, descripcion_corta FROM apex_proyecto ORDER BY descripcion_corta';
		return toba::db('instancia')->consultar($sql);
	}
	
	function get_proyecto_actual()
	{
		return toba::proyecto()->get_id();
	}


}
?>