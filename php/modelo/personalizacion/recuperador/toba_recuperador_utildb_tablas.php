<?php

class toba_recuperador_utildb_tablas
{
	protected $proyecto;
	protected $db;

	function __construct($proyecto)
	{
		$this->proyecto = $proyecto;
		$this->db = $this->proyecto->get_db();
	}

	function corresponde_comparar($tabla)
	{
		$definicion = toba_db_tablas_proyecto::$tabla();

		return 
			isset($definicion['clave_proyecto']) &&
			isset($definicion['clave_elemento'])
		;
	}

	function get_contenido_tabla_datos($tabla, $where_extra=null)
	{
		$definicion = toba_db_tablas_proyecto::$tabla();

		//Genero el SQL
		if( isset($definicion['dump_where']) && ( trim($definicion['dump_where']) != '') ) {
   			$w = stripslashes($definicion['dump_where']);
   			$where = str_replace("%%",$this->proyecto->get_id(), $w);
        } else {
   			$where = " ( proyecto = '".$this->proyecto->get_id()."')";
		}
		if(isset($where_extra)) $where = $where . ' AND ('. $where_extra .')';
		$sql = 'SELECT ' . implode(', ', $definicion['columnas']) .
				" FROM $tabla " .
				" WHERE $where " .
				" ORDER BY {$definicion['dump_order_by']} ;\n";
		$datos = $this->db->consultar($sql);

		$reorganizado = $this->reorganizar_tabla($datos, $definicion['clave_elemento']);
		$regs = count( $datos );
//		if ( $regs > 1 ) {
//			$columnas_orden = array_map('trim', explode(',',$definicion['dump_order_by']) );
//			$datos = rs_ordenar_por_columnas( $datos, $columnas_orden );
//		}
		toba_logger::instancia()->debug("TABLA  $tabla  ($regs reg.)");
		return $reorganizado;
	}

	protected function reorganizar_tabla(&$datos, $claves)
	{	
		$reorganizado = array();
		$claves_arr = explode(',', $claves);

		foreach (array_keys($datos) as $key_registro) {
			$nueva_clave = $this->armar_clave($datos[$key_registro], $claves_arr);
			foreach ($datos[$key_registro] as $columna => $valor) {
				$reorganizado[$nueva_clave][$columna] = $valor;
			}
		}

		return $reorganizado;
	}

	protected function armar_clave(&$registro, &$claves)
	{
		$resultado = array();

		foreach ($claves as $clave) {
			$aux = trim($clave);
			$valor = $registro[$aux];
			$resultado[] = "$aux:$valor";
		}

		return implode(';', $resultado);
	}
}
?>
