<?php

class toba_registro_insert extends toba_registro
{
	function  __construct($db, $nombre_tabla)
	{
		parent::__construct($db, $nombre_tabla);
		$this->tipo = self::registro_insert;
	}


	function get_tabla()
	{
		return $this->tabla;
	}


	 function to_sql()
	{
		$cols	= array();
		$values = array();
		foreach ($this->columnas as $columna => $valor) {
			$cols[]		= $columna;
			$values[]	= (is_null($valor['valor'])) ? 'NULL' : $this->db->quote($valor['valor']);
		}

		$imp_cols	= implode(',', $cols);
		$imp_values = implode(',', $values);
		$sql = "INSERT INTO $this->tabla ($imp_cols) VALUES ($imp_values)";

		return $sql;
	}
}
?>
