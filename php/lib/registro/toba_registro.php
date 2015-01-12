<?php

abstract class toba_registro {
	const registro_insert = 'registro_insert';
	const registro_update = 'registro_update';
	const registro_delete = 'registro_delete';

	protected $tipo;

	// La tabla a la que pertenece el registro
	protected $tabla;

	// Las columnas del registro
	protected $columnas = array();

	protected $conversion_auto_latin1 = true;
	/**
	 * La bd donde se tiene que guardar el registro
	 * @var toba_db_postgres7
	 */
	protected $db;
	
	protected $conflictos;

	function  __construct($db, $nombre_tabla)
	{
		$this->db = $db;
		$this->set_tabla($nombre_tabla);
		$this->conversion_auto_latin1 = function_exists('mb_detect_encoding');
	}

	/**
	 * Si es verdadero todos los strings que se pasen como parametro $valor a
	 * add_columna van a ser convertidos automáticamente a latin1 si están en
	 * utf8
	 * @param <type> $param
	 */
	function set_conversion_auto_latin1($param)
	{
		$this->conversion_auto_latin1 = $param;
	}

	/**
	 * Graba el registro en la base
	 */
	function grabar()
	{
		$sql = $this->to_sql();
		$this->db->ejecutar($sql);
	}

	function add_columna($columna, $valor)
	{
		if (empty($columna)) {
			throw  new toba_error('REGISTRO: No se puede agregar una columna cuyo nombre es vacío');
		}
		if ($this->conversion_auto_latin1) {
			$valor = utf8_d_seguro($valor);
		}
		
		if ($valor == toba_personalizacion::nulo) {
			$valor = null;
		}
		
		$this->columnas[$columna]['valor'] = $valor;
	}

	function set_tabla($tabla)
	{
		if (empty($tabla)) {
			throw  new toba_error('REGISTRO: La tabla especificada no es válida');
		}
		$this->tabla = $tabla;
	}

	function get_tabla()
	{
		return $this->tabla;
	}

	function existe_columna($columna)
	{
		return !isset($this->columnas[$columna]);
	}

	/**
	 * Devuelve el valor de la columna $columna
	 * @param string $columna
	 * @return mixed
	 */
	function get_valor($columna)
	{
		if (!isset($this->columnas[$columna])) {
			throw  new toba_error("REGISTRO: La columna no existe en el registro");
		}
		return $this->columnas[$columna]['valor'];
	}

	function get_tipo()
	{
		return $this->tipo;
	}

	function get_db()
	{
		return $this->db;
	}

	function analizar_conflictos()
	{
		$this->conflictos = array();
		$schema = $this->db->get_schema();
		if (!$this->db->existe_tabla($schema, $this->tabla)) {
			$this->conflictos[] =  new toba_registro_conflicto_tabla_inexistente($this);
		}

		$error = $this->check_constraints();
		if ($error !== false) {
			$this->conflictos[] =  new toba_registro_conflicto_constraints($this, $error);
		}	
	}
		
	/**
	 * @return array arreglo con los toba_registro_conflictos del registro
	 */
	function get_conflictos()
	{
		if (! isset($this->conflictos)) {
			$this->analizar_conflictos();
		}
		return $this->conflictos;
	}

	protected function check_constraints()
	{
		$this->db->agregar_savepoint('chequeo_conflicto');
		$sql = $this->to_sql();
		$fallo = false;

		try {
			$this->db->ejecutar($sql);
			$this->db->liberar_savepoint('chequeo_conflicto');
		} catch (toba_error_db $e) {
			$fallo = $e;
			$this->db->abortar_savepoint('chequeo_conflicto');	
		}
		return $fallo;
	}

	function tiene_conflictos()
	{
		return count($this->get_conflictos()) > 0;
	}
	
	function get_conflicto_irresoluble()
	{
		$conflictos = $this->get_conflictos();
		foreach ($conflictos as $conflicto) {
			if ($conflicto->get_tipo() == toba_registro_conflicto::fatal) {
				return $conflicto;
			}
		}
		return null;
	}

	function get_conflictos_solubles()
	{
		$conflictos = $this->get_conflictos();
		$acumulador = array();
		foreach ($conflictos as $conflicto) {
			if ($conflicto->get_tipo() == toba_registro_conflicto::warning) {
				$acumulador[] = $conflicto;
			}
		}
		return $acumulador;
	}


	/**
	 * Obtiene el sql asociado al registro
	 */
	abstract function to_sql();

}
?>
