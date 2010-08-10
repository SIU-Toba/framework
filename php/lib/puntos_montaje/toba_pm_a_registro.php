<?php
/**
 * Clase que provee utilidades para convertir toba_punto_montaje a toba_registro.
 * La conversión se realiza en esta clase para alivianar la clase toba_punto_montaje
 */
class toba_pm_a_registro
{
    static function insert(toba_punto_montaje $punto, $db)
	{
		$registro = new toba_registro_insert($db, 'apex_puntos_montaje');
		$registro->add_columna('etiqueta', $punto->get_etiqueta());
		$registro->add_columna('proyecto', $punto->get_proyecto());
		$registro->add_columna('descripcion', $punto->get_descripcion());
		$registro->add_columna('tipo', $punto->get_tipo());
		if ($punto->es_de_proyecto()) {
			$registro->add_columna('proyecto_ref', $punto->get_proyecto_referenciado());
			$registro->add_columna('path_pm', $punto->get_path());
		} else {
			$registro->add_columna('proyecto_ref', '');
			$registro->add_columna('path_pm', '');
		}
		
		return $registro;
	}

	static function update(toba_punto_montaje $punto, $db)
	{
		$registro = new toba_registro_update($db, 'apex_puntos_montaje');
		$registro->add_clave('id', $punto->get_id());
		$registro->add_columna('etiqueta', $punto->get_etiqueta());
		$registro->add_columna('proyecto', $punto->get_proyecto());
		$registro->add_columna('descripcion', $punto->get_descripcion());
		$registro->add_columna('tipo', $punto->get_tipo());
		if ($punto->es_de_proyecto()) {
			$registro->add_columna('proyecto_ref', $punto->get_proyecto_referenciado());
			$registro->add_columna('path_pm', $punto->get_path());
		} else {
			$registro->add_columna('proyecto_ref', '');
			$registro->add_columna('path_pm', '');
		}

		return $registro;
	}

	static function delete(toba_punto_montaje $punto, $db)
	{
		$registro = new toba_registro_delete($db, 'apex_puntos_montaje');
		$registro->add_clave('id', $punto->get_id());

		return $registro;
	}
}
?>
