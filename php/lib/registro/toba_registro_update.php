<?php

class toba_registro_update extends toba_registro_con_clave
{
	function  __construct($db, $nombre_tabla)
	{
		parent::__construct($db, $nombre_tabla);
		$this->tipo  = self::registro_update;
	}

	function set_valor_original($columna, $valor)
	{
		if ($valor == toba_personalizacion::nulo) {
			$valor = null;
		}
		$this->columnas[$columna]['valor_original'] = $valor;
	}

	function get_valor_original($columna)
	{
		if (!isset($this->columnas[$columna])) {
			throw  new toba_error("REGISTRO: La columna a la que se quiere acceder no existe en el registro");
		}
		return $this->columnas[$columna]['valor_original'];
	}

	function analizar_conflictos()
	{
		parent::analizar_conflictos();
		if (count($this->conflictos) > 0) {
			return;	// Si el padre devolvió algún conflicto este impide que se chequeen el resto de los toba_registro_conflictos
		}

		$reg_original = $this->get_registro_a_actualizar();

		foreach ($this->columnas as $columna => $valor) {
			$valor_actual = $reg_original[0][$columna];
			
			if ($valor_actual != $valor['valor_original']) {	// el valor que se cambió no es el mismo
				$this->conflictos[] =  new toba_registro_conflicto_valor_original($this, $columna, $valor_actual);
			}
		}
	}
	
    function to_sql()
	{
		$values = $this->armar_values();
		$where = $this->armar_where();
		
		$sql = "UPDATE $this->tabla SET $values WHERE $where";

		return $sql;
	}

	protected function armar_values()
	{
		$values = array();
		foreach ($this->columnas as $columna => $valor) {
			$safevalue = (is_null($valor['valor'])) ? 'NULL' : $this->db->quote($valor['valor']);
			$values[] = $columna.' = '. $safevalue;
		}

		return implode(', ', $values);
	}
}
?>
