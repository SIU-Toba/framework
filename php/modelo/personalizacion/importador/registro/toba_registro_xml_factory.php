<?php
/**
 * Esta clase es la encargada de construir registros desde strings xml
 */
class toba_registro_xml_factory
{

	static function construir($db, $nombre_tabla, $registro_xml)
	{
		$estado = self::get_estado($registro_xml);
		
		switch ($estado) {
			case toba_personalizacion::registro_inserted:
				return  self::construir_insert($db, $nombre_tabla, $registro_xml);
			case toba_personalizacion::registro_updated:
				return  self::construir_update($db, $nombre_tabla, $registro_xml);
			case toba_personalizacion::registro_deleted:
				return  self::construir_delete($db, $nombre_tabla, $registro_xml);
			default:
				throw new toba_error('FACTORY REGISTROS: El tipo de registro especificado no es válido');
		}
	}

	static protected function get_estado($registro_xml)
	{
		$tag_estado = toba_pers_xml_atributos::estado;
		if (isset($registro_xml[$tag_estado])) {
			return (string) $registro_xml[$tag_estado];
		}

		return toba_personalizacion::registro_inserted;	// Por defecto se asume registro nuevo
	}

	static protected function construir_insert($db, $nombre_tabla, $registro_xml)
	{
		$registro = new toba_registro_insert($db, $nombre_tabla);
		self::add_columnas($registro, $registro_xml);
		return $registro;
	}

	static protected function construir_update($db, $nombre_tabla, $registro_xml)
	{
		$registro = new toba_registro_update($db, $nombre_tabla);
		self::add_columnas($registro, $registro_xml);
		self::init_valores_originales($registro, $registro_xml);
		self::init_claves($registro, $registro_xml);
		return $registro;
	}

	static protected function construir_delete($db, $nombre_tabla, $registro_xml)
	{
		$registro = new toba_registro_delete($db, $nombre_tabla);
		self::init_claves($registro, $registro_xml);
		return $registro;
	}

	static protected function add_columnas($registro, $registro_xml)
	{
		$tag_nombre	 = toba_pers_xml_atributos::nombre;
		$tag_valor	 = toba_pers_xml_atributos::valor;

		foreach ($registro_xml as $columna) {
			$registro->add_columna((string)$columna[$tag_nombre], (string)$columna[$tag_valor]);
		}
	}

	static protected function init_valores_originales($registro, $registro_xml)
	{
		$tag_nombre	= toba_pers_xml_atributos::nombre;
		$tag_valor_original = toba_pers_xml_atributos::valor_original;

		foreach ($registro_xml as $columna) {
			$registro->set_valor_original(
					(string)$columna[$tag_nombre],
					(string)$columna[$tag_valor_original]
			);
		}
	}

	static protected function init_claves($registro, $registro_xml)
	{
		$claves_xml = $registro_xml[toba_pers_xml_atributos::clave];

		$claves = explode(';', $claves_xml);
		foreach ($claves as $clave) {
			list($col, $val) = explode(':', $clave);
			$registro->add_clave($col, $val);
		}
	}
}
?>
