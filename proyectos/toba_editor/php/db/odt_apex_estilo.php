<?php



class dt_apex_estilo extends toba_datos_tabla
{
	
	function get_listado($proyecto=null)
	{
		if (! isset($proyecto)) {
			if (toba_editor::acceso_recursivo()) {
				$proyecto = 'toba';
			} else {
				$proyecto = toba_editor::get_proyecto_cargado();
			}
		}
		$sql = 'SELECT
			ae.estilo,
			ae.descripcion,
			ap.descripcion_corta as proyecto_nombre,
			ae.es_css3
		FROM
			apex_estilo as ae,
			apex_proyecto as ap
		WHERE
				ae.proyecto = ap.proyecto
			AND ap.proyecto = '.quote($proyecto).'
		ORDER BY descripcion';
		return toba::db('instancia')->consultar($sql);
	}

	function get_descripciones($proyecto=null)
	{
		if (! isset($proyecto)) {
			if (toba_editor::acceso_recursivo()) {
				$proyecto = 'toba';
			} else {
				$proyecto = toba_editor::get_proyecto_cargado();
			}
		}		
		$sql = 'SELECT proyecto, estilo, descripcion FROM apex_estilo WHERE proyecto='.quote($proyecto)." OR proyecto='toba' ORDER BY descripcion";
		return toba::db('instancia')->consultar($sql);
	}

}




?>