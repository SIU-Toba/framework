<?php
/**
 * Factory para construir puntos de montaje en runtime
 * @package Centrales
 * @subpackage Punto Montaje
 * @author sp14ab
 */
class toba_punto_montaje_factory
{
	/**
	 * Construye un punto de montaje a partir de un registro en la tabla puntos de montaje
	 * @param array $registro
	 */
	static function construir($registro)
	{
		$tipo = $registro['tipo'];

		switch($tipo) {
			case toba_punto_montaje::tipo_indefinido:
				return self::construir_indefinido($registro);
			case toba_punto_montaje::tipo_proyecto:
				return self::construir_proyecto($registro);
			case toba_punto_montaje::tipo_pers:
				return self::construir_pers($registro);	
			default:
				throw new toba_error("PUNTOS DE MONTAJE: El tipo $tipo es inválido");
		}
	}

	/**
	 * Inicializa los valores comunes entre los distintos tipos de punto
	 * @param toba_punto_montaje $punto
	 * @param array $registro
	 */
	static protected function init_punto_generico(toba_punto_montaje $punto, $registro)
	{
		$punto->set_id($registro['id']);
		$punto->set_etiqueta($registro['etiqueta']);
		$punto->set_proyecto($registro['proyecto']);
		$punto->set_path($registro['path_pm']);
		$punto->set_descripcion($registro['descripcion']);
		if (isset($registro['etiqueta_anterior'])) {
			$punto->set_etiqueta_anterior($registro['etiqueta_anterior']);
		}
	}

	/**
	 * Construye un PM generico
	 * @param array $registro
	 * @return \toba_punto_montaje
	 */
	static protected function construir_indefinido($registro)
	{
		$punto = new toba_punto_montaje();
		self::init_punto_generico($punto, $registro);
		return $punto;
	}

	/**
	 * Construye un PM de proyecto
	 * @param array $registro
	 * @return \toba_punto_montaje_proyecto
	 */
	static protected function construir_proyecto($registro)
	{
		$punto = new toba_punto_montaje_proyecto();
		self::init_punto_generico($punto, $registro);
		$punto->set_proyecto_referenciado($registro['proyecto_ref']);
		return $punto;
	}

	/**
	 * Construye un PM de personalizacion
	 * @param array $registro
	 * @return \toba_punto_montaje_pers
	 */
	static protected function construir_pers($registro)
	{
		$punto = new toba_punto_montaje_pers();
		self::init_punto_generico($punto, $registro);
		$punto->set_proyecto_referenciado($registro['proyecto_ref']);
		return $punto;
	}
}
?>
