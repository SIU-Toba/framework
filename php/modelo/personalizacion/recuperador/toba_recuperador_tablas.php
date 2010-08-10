<?php

/**
 * Description of toba_recuperador_tablas
 *
 * @author sp14ab
 */
class toba_recuperador_tablas extends toba_recuperador
{
	protected $tablas_db;

	protected function ini()
	{
		$this->tablas_db = new toba_recuperador_utildb_tablas($this->proyecto);
	}

	protected function get_diferencia()
	{
		$tablas = array();
		foreach ( toba_db_tablas_proyecto::get_lista() as $tabla ) {
			if ($this->tablas_db->corresponde_comparar($tabla)) {
				$tablas[$tabla] = $this->comparar($tabla);
			}

		}

		array_borrar_subarrays_vacios($tablas);

		return $tablas;
	}
	
	protected function get_unicos($schema)
	{
		return array();		// Asumo que no se agregan ni se borran tablas
	}

	/**
	 * Compara la tabla $tabla en los dos schemas
	 * @param string $tabla 
	 */
	protected function comparar($tabla)
	{
		$this->db->set_schema($this->schema_a);
		$tabla_a = $this->tablas_db->get_contenido_tabla_datos($tabla);
		$this->db->set_schema($this->schema_b);
		$tabla_b = $this->tablas_db->get_contenido_tabla_datos($tabla);
		return array_merge_recursive($this->diff_borrados($tabla_a, $tabla_b), $this->diff($tabla_a, $tabla_b));
	}

	protected function diff($tabla_a, $tabla_b)
	{
		$tabla = array();

		foreach(array_keys($tabla_a) as $key_registro)
		{
			$comparado = $this->comparar_registro($tabla_a[$key_registro], $tabla_b[$key_registro]);
			switch ($comparado['estado']) {
				case 'insertado':
					$comparado['estado'] = toba_personalizacion::registro_inserted;
					$tabla[$key_registro] = $comparado;
					break;
				case 'modificado':
					$comparado['estado'] = toba_personalizacion::registro_updated;
					$comparado['clave'] = $key_registro;
					$tabla[$key_registro] = $comparado;
				default:;
			}
		}

		return $tabla;
	}

	/**
	 * Devuelve los registros que aparecen en $tabla_b pero no en $tabla_a
	 * @param array $tabla_a
	 * @param array $tabla_b
	 * @return array
	 */
	protected function diff_borrados($tabla_a, $tabla_b)
	{
		$tabla = array();

		foreach(array_keys($tabla_b) as $key_registro) {
			$comparado = $this->comparar_registro($tabla_b[$key_registro], $tabla_a[$key_registro]);
			switch ($comparado['estado']) {
				case 'insertado':
					$comparado['estado'] = toba_personalizacion::registro_deleted;
					$comparado['clave'] = $key_registro;
					$tabla[$key_registro] = $comparado;
					break;
			}
		}

		return $tabla;
	}

}

?>
