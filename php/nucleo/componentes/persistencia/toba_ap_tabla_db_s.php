<?php
/**
 * Clase que se mantiene por compatibildad hacia atrás
 * @package Componentes
 * @subpackage Persistencia
 */
class toba_ap_tabla_db_s extends toba_ap_tabla_db
{
	final function  __construct($datos_tabla)
	{
		parent::__construct($datos_tabla);

		$this->inicializar();
		$this->ini();
	}

	function get_tipo()
	{
		return toba_ap_tabla_db::tipo_tabla_unica;
	}

	protected function es_seq_tabla_ext($col)
	{
		return false;
	}

	protected function get_sql_campos_default($where)
	{
		$sql = "SELECT\n\t" . implode(", \n\t", $this->_insert_campos_default);
		$sql .= "\nFROM\n\t " . $this->agregar_schema($this->_tabla);
		$sql .= "\nWHERE ".implode(' AND ', $where);

		return $sql;
	}

	protected function get_flag_mod_clave()
	{
		return $this->_flag_modificacion_clave;
	}

	protected function get_select_col($col)
	{
		return $this->_alias  . "." . $col;
	}

	protected function get_from_default()
	{
		return $this->agregar_schema($this->_tabla)  . ' as '. $this->_alias;
	}
}
?>