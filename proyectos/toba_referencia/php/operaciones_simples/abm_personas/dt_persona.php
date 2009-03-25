<?php
class dt_persona extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = 'SELECT id, nombre FROM ref_persona ORDER BY nombre';
		return consultar_fuente($sql);
	}


	function get_listado()
	{
		$sql = 'SELECT
					rp.id,
					rp.nombre,
					rp.fecha_nac,
					rp.imagen
				FROM
					ref_persona as rp
				ORDER BY nombre
		';
		return toba::db('toba_referencia')->consultar($sql);
	}


}

?>