<?php
class dt_ref_deportes extends toba_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			rd.id,
			rd.nombre,
			rd.descripcion,
			rd.fecha_inicio
		FROM
			ref_deportes as rd
		ORDER BY nombre";
		return toba::db('toba_referencia')->consultar($sql);
	}



}

?>