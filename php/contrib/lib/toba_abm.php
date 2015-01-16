<?php

class toba_abm
{
	static protected function get_valor_sql($valor) 
	{
		if ($valor === null) {
			return 'NULL';
		} elseif (is_bool($valor)) {
			return $valor ? 'true' : 'false';
		} else {
			return quote($valor);
		}
	}
	
	static function get_sql_alta($tabla, $datos)
	{
		$campos = implode(', ', array_keys($datos));
		$valores = array();
		foreach ($datos as $clave => $valor) {
			$valores[$clave] = self::get_valor_sql($valor);
		}
		$valores = implode(", ", $valores);
		$sql = "INSERT INTO $tabla ($campos) VALUES ($valores)";
		return $sql;
	}

	static function get_sql_baja($tabla, $clave)
	{		
		$where = self::get_where_de_clave($clave);
		$sql = "DELETE FROM $tabla WHERE $where";
		return $sql;
	}

	static function get_sql_modificacion($tabla, $datos, $clave)
	{
		$where = self::get_where_de_clave($clave);
		$valores = array();
		foreach ($datos as $campo => $valor) {
			$valores[] = $campo . ' = '. self::get_valor_sql($valor);
		}
		$valores = implode(', ', $valores);

		$sql = "UPDATE $tabla SET $valores WHERE $where";
		return $sql;
	}

	/**
	 * Determina si una tabla contiene registro/s con una clave dada
	 * Una pavada, pero para ahorrar 3 lineas de codigo sirve!
	 */
	static function get_sql_cant_registros($tabla, $clave, $clave_excluir=null)
	{
		$where = self::get_where_de_clave($clave);
		if (isset($clave_excluir)) {
			$where .= ' AND NOT ('.self::get_where_de_clave($clave_excluir).')';
		}
		$sql = "SELECT COUNT(*) as cant FROM $tabla WHERE $where";
		return $sql;
	}


	static function get_where_de_clave($clave)
	{
		$where = 'TRUE';
		if (! empty($clave)) {
			$where = array();
			foreach ($clave as $campo => $valor) {
				$where[] = $campo . ' = ' . quote($valor);
			}
			$where = implode(' AND ', $where);
		}
		return $where;
	}
	
}
?>
