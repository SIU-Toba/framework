<?php
/**
 * Description of toba_recuperador_componentes
 *
 * @author sp14ab
 */
class toba_recuperador_componentes extends toba_recuperador {
	protected $componentes_db;

	protected function ini()
	{
		$this->componentes_db =  new toba_recuperador_utildb_componentes($this->proyecto);
	}
	
	protected function get_unicos($schema) 
	{
		$data = array();
		$el_otro_schema = $this->get_otro_schema($schema);

		$this->db->set_schema($schema);
		
		foreach (toba_recuperador_utildb_componentes::get_lista_tipos_componentes() as $tipo) {
			$condicion = $this->armar_condicion($tipo, $el_otro_schema, true);
			$componentes = $this->componentes_db->get_componentes($tipo, $condicion, $schema);
			if (!empty($componentes)) {
				$data[] = $this->add_metadata($tipo, $componentes);
			}
		}
		array_borrar_subarrays_vacios($data);

		return $data;
	}
	
	protected function get_diferencia()
	{
		$data = array();
		$schema = $this->schema_a;
		$el_otro_schema = $this->get_otro_schema($schema);
		
		foreach (toba_recuperador_utildb_componentes::get_lista_tipos_componentes() as $tipo) {
			$condicion = $this->armar_condicion($tipo, $el_otro_schema, false);
			$componentes = $this->componentes_db->get_componentes($tipo, $condicion, $schema);
			$data[] = $this->comparar($tipo, $componentes);
		}
		
		array_borrar_subarrays_vacios($data);
		return $data;
	}

	protected function comparar($tipo, &$componentes)
	{
		$diferencia = array();
		
		$this->db->set_schema($this->schema_a);
		foreach ($componentes as $id) {
			$nuevo = toba_cargador::instancia()->get_metadatos_simples_diff($id, $tipo, $this->db);
			$this->db->set_schema($this->schema_b);
			$viejo = toba_cargador::instancia()->get_metadatos_simples_diff($id, $tipo, $this->db);
			$this->db->set_schema($this->schema_a);

			$diff = $this->diff($nuevo, $viejo);

			if (!empty($diff)) {
				$diferencia[] = array(
					'tipo'		=> $tipo,
					'id'		=> $id,
					'metadata'	=> $diff
				);
			}
		}
		return $diferencia;
	}

	/**
	 * Calcula la diferencia entre dos componentes
	 * @param array $viejo
	 * @param array $nuevo
	 * @return array
	 */
	protected function diff(&$nuevo, &$viejo)
	{
		$componente = array();

		$this->diff_borrados($viejo, $nuevo, $componente);

		foreach (array_keys($nuevo) as $key_tabla) {
			foreach (array_keys($nuevo[$key_tabla]) as $key_registro) {
				$comparado = $this->comparar_registro($nuevo[$key_tabla][$key_registro], $viejo[$key_tabla][$key_registro]);

				switch ($comparado['estado']) {
					case 'insertado':
						$comparado['estado'] = toba_personalizacion::registro_inserted;
						$componente[$key_tabla][$key_registro] = $comparado;
						break;
					case 'modificado':
						$comparado['estado'] = toba_personalizacion::registro_updated;
						$comparado['clave'] = $key_registro;
						$componente[$key_tabla][$key_registro] = $comparado;
					default:;
				}
			}
		}

		return $componente;
	}
	
	/**
	 * Agrega los elementos borrados a la diferencia
	 * @param array $viejo
	 * @param array $nuevo
	 * @param array $diferencia
	 */
	protected function diff_borrados(&$viejo, &$nuevo, &$diferencia)
	{
		foreach (array_keys($viejo) as $key_tabla) {
			foreach (array_keys($viejo[$key_tabla]) as $key_registro) {
				if (!isset($nuevo[$key_tabla][$key_registro])) {	// el registro aparece en viejo pero no en nuevo -> Fue borrado
					$diferencia[$key_tabla][$key_registro] = array(
						'estado' => toba_personalizacion::registro_deleted,
						'clave'	 => $key_registro,
						'data'	 => array()		// se agrega por regularidad de los arreglos retornados
					);
				}
			}
		}
	}

	protected function add_metadata($tipo, &$componentes, $borrados = false)
	{
		$res = array();

		foreach ($componentes as $id) {
			$aux = array(
				'id'		=> $id,
				'tipo'		=> $tipo,
			);
			if (!$borrados) {
				$aux['metadata'] = toba_cargador::instancia()->get_metadatos_simples_diff($id, $tipo, $this->db);
			}
			$res[] = $aux;
		}

		return $res;
	}

	/**
	 *
	 * @param string $tipo el tipo de componente para el cuál armar la condición
	 * @param string $schema el schema sobre el cuál se va a armar la condición
	 * @param boolean $unico_schema si la condición va a excluir las componentes del schema $schema o va a incluirlas
	 * @return string sql para inyectar en toba_componentes_db::get_componentes
	 */
	protected function armar_condicion($tipo, $schema, $unico_schema = true)
	{
		$proyecto = $this->db->quote($this->proyecto->get_id());
		$comp_sano = $this->db->quote($tipo);

		$not = ($unico_schema) ? 'NOT' : '';

		if ($tipo == 'toba_item') {
			$condicion = "
				item $not IN
					(SELECT item as componente
					 FROM $schema.apex_item
					 WHERE proyecto = $proyecto)
			";
		} elseif(strpos($tipo,'toba_asistente')!== false) {
				$condicion = "
					o.molde $not IN
						(SELECT o.molde
						 FROM	$schema.apex_molde_operacion o,
							$schema.apex_molde_operacion_tipo t
						 WHERE o.operacion_tipo = t.operacion_tipo
						 AND t.clase = $comp_sano
						 AND proyecto = $proyecto)
				";
		}  else {
				$condicion = "
					objeto $not IN
						(SELECT objeto
						FROM $schema.apex_objeto
						WHERE proyecto = $proyecto
						AND clase = $comp_sano)
				";
		}

		return $condicion;
	}
}
?>
