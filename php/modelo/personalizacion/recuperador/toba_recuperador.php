<?php
/**
 * Clase que maneja la recuperación de datos entre dos schemas. De esta manera
 * es más fácil su comparación.
 *
 * @author sp14ab
 */
abstract class toba_recuperador {
	protected $db;
	protected $schema_a;
	protected $schema_b;
	/**
	 * Constructor de toba_recuperador_bi_schema. El orden de los schemas en la
	 * lista de parámetros SI importa
	 * @param toba_modelo_proyecto $proyecto
	 * @param string $schema_a este schema representa el schema actual
	 * @param string $schema_b este representa el schema original
	 */
	function __construct($proyecto, $schema_a, $schema_b)
	{
		$this->proyecto = $proyecto;
		$this->db = $this->proyecto->get_db();
		$this->schema_a = $schema_a;
		$this->schema_b = $schema_b;
		$this->ini();
	}

	/**
	 * Devuelve un objeto con los datos de la comparacion de los schemas
	 * @return toba_bi_schema_data
	 */
    function get_data()
	{
		$data =  new toba_recuperador_data($this->schema_a, $this->schema_b);
		$un_a = $this->get_unicos($this->schema_a);
		$un_b = $this->get_unicos($this->schema_b);
		$diff = $this->get_diferencia();
		
		$data->set_unicos($this->schema_a, $un_a);
		$data->set_unicos($this->schema_b, $un_b);
		$data->set_diferentes($diff);
		return $data;
	}

	/**
	 * Devuelve el otro schema
	 * @param string $schema
	 * @return string
	 */
	protected function get_otro_schema($schema)
	{
		return ($schema == $this->schema_a) ? $this->schema_b : $this->schema_a;
	}

	abstract protected function ini();
	/**
	 * Obtiene todos los elementos que aparecen en el $schema y que no aparecen en el otro schema
	 * Ej:
	 *		$r =  new toba_recuperador_bi_schema($proyecto, 'personalizacion', 'desarrollo')
	 *		$r->get_unicos('personalizacion') // devuelve los elementos que aparecen en personalizacion y no en desarrollo
	 * @param string $schema
	 */
	abstract protected function get_unicos($schema);

	/**
	 * Devuelve la diferencia entre los elementos del schema_a contra los del schema_b.
	 */
	abstract protected function get_diferencia();

	/**
	 * Compara dos registros de la base de datos y agrega metadata para saber
	 * que cambió
	 * @param array $registro_a
	 * @param array $registro_b
	 * @return array
	 */
	protected function comparar_registro(&$registro_a, &$registro_b = null)
	{
		$resultado = array();
		if (is_null($registro_b)) {
			$resultado['estado'] = 'insertado';
			$resultado['data'] = $registro_a;
			return $resultado;
		}
		$modificado = false;
		
		foreach($registro_a as $key => $valor) {			
			$valor_viejo = $registro_b[$key];
			if ($valor !== $valor_viejo) {
				if (is_null($valor_viejo)) {$valor_viejo = toba_personalizacion::nulo;}
				if (is_null($valor)) { $valor = toba_personalizacion::nulo;}
				$resultado['data'][$key] = array(
					'actual' => $valor,
					'original' => $valor_viejo
				);
				$modificado = true;
			}
		}
		$resultado['estado'] = ($modificado) ? 'modificado' : 'igual';
		return $resultado;
	}
}
?>
