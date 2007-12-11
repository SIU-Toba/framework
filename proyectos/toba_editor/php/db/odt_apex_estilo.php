<?php

class dt_apex_estilo extends toba_datos_tabla
{
	
	function get_listado($proyecto)
	{
		$sql = "SELECT
	ae.estilo,
	ae.descripcion,
	ap.descripcion_corta as proyecto_nombre
FROM
	apex_estilo as ae,
	apex_proyecto as ap
WHERE
		ae.proyecto = ap.proyecto
	AND ap.proyecto = '$proyecto'
ORDER BY descripcion";
		return toba::db('instancia')->consultar($sql);
	}

}


?>