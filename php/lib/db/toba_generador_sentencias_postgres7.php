<?php
class toba_generador_sentencias_postgres7 implements toba_generador_sentencias
{
	function generar_sql_select($columnas, $from , $where = array())
	{
		$sql = "SELECT\n\t" . implode(", \n\t", $columnas);
		$sql .= "\nFROM\n\t" . implode(", ", $from);
		if(! empty($where)) {
			$sql .= "\nWHERE";
			foreach ($where as $clausula) {
				$sql .= "\n\t$clausula AND";
			}
			$sql = substr($sql, 0, -4); 	//Se saca el ultimo AND
		}		
		return $sql;
	}
	
	function generar_sql_insert($tabla, $columnas, $valores)
	{		
		$sql = "INSERT INTO " . $tabla .
					" ( " . implode(", ", $columnas) . " ) ".
					"\n VALUES (" . implode(", ", $valores) . ");";
		return $sql;
	}

	function generar_sql_update($tabla, $columnas, $where) 
	{
		$sql = "UPDATE " . $tabla . "\nSET ".
		implode(",\n\t",$set) .
		"\nWHERE " . implode("\n\tAND ", $where ) .";";
		return $sql;
	}
	
	function generar_sql_delete($tabla, $where) 
	{
		$sql = "DELETE FROM " . $tabla .
			" WHERE " . implode(" AND ",$where ) .";";
		return $sql;
	}
	
	static function generar_clausula_where($columnas, $tabla_alias)
	{		
		$clausula = array();
		foreach($columnas as $columna => $valor) {
			if (isset($valor)) {
				if (is_bool($valor)) {
					$valor = ($valor) ? 'true' : 'false';
				}
				$valor = self::quote($valor);
				$clausula[] = "$tabla_alias" . "$columna = $valor";
			} else {
				$clausula[] = "$tabla_alias" . "$columna IS NULL";
			}
		}
		return $clausula;
	}
}
?>
