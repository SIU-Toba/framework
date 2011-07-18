<?php
/**
 * 	@package Componentes
 *  @subpackage Persistencia
 * 	@ignore
 */
class toba_ap_tabla_db_mt extends toba_ap_tabla_db
{
	const id_ap_mt = 4;
	protected $_tabla_ext;
	
	// Arreglo de punteros a $this->_columnas ordenado por tabla
	protected $_cols_por_tabla;
	protected $_fks;

	/**
	 * Refactorizar más, cambiar el construct del padre
	 */
	final function  __construct($datos_tabla)
	{
		parent::__construct($datos_tabla);
		$this->_tabla_ext		= $this->objeto_tabla->get_tabla_extendida();
		$this->_cols_por_tabla	= $this->reordenar_columnas();
		$this->limpiar_claves();
		$this->_fks				= $this->objeto_tabla->get_fks();

		$this->inicializar();
		$this->ini();
	}

	/**
	 * Remueve las claves de la tabla extendida de $this->_clave
	 */
	protected function limpiar_claves()
	{
		foreach ($this->_clave as $key => $clave) {
			if ($this->get_tabla($clave) == $this->_tabla_ext) {
				unset($this->_clave[$key]);
			}
		}
	}

	function get_tipo()
	{
		return toba_ap_tabla_db::tipo_multitabla;
	}

	protected function get_sql_campos_default($where)
	{
		$sql = "SELECT\n\t".implode(", \n\t", $this->_insert_campos_default);
		$sql .= "\nFROM\n\t {$this->get_from_default()} ";
		$sql .= "\nWHERE ".implode(' AND ', $where);

		return $sql;
	}

	protected function get_from_default()
	{
		$tabla		= $this->_tabla;
		$alias		= $this->_alias;
		$tabla_ext	= $this->_tabla_ext;

		$condicion	= '';
		foreach ($this->_fks as $fk) {
			$condicion .= implode(' = ', $fk). ' AND ';
		}
		$condicion = substr($condicion, 0, -5);	// Sacamos el último AND

		return "$tabla as $alias LEFT OUTER JOIN $tabla_ext ON $condicion";
	}

	protected function es_seq_tabla_ext($col)
	{
		return $this->_tabla_ext == $this->get_tabla($col);
	}

	protected function ejecutar_sql_insert($id_registro, $solo_retornar=false, $tabla = null)
	{
		if ($solo_retornar) {
			// Se devuelve esto xq si sólo se retorna no hay manera de armar la
			// fk para el próximo insert
			return parent::ejecutar_sql_insert($id_registro, $solo_retornar);
		}

		parent::ejecutar_sql_insert($id_registro, false);

		if ($this->hay_cambios_ext($id_registro)) {
			$this->actualizar_fks_ext($id_registro);
			parent::ejecutar_sql_insert($id_registro, false, $this->_tabla_ext);
		}
	}

	protected function eliminar_registro_db($id_registro)
	{
		if ($this->existe_fila_ext($id_registro)) {
			//si no existe no disparamos el delete para evitar error
			//de sincronización
			$sql_ext = $this->generar_sql_delete_ext($id_registro);
			$this->log("registro: $id_registro - " . $sql_ext);
			$this->ejecutar_sql($sql_ext, $id_registro);
		}

		$sql = $this->generar_sql_delete($id_registro);
		$this->log("registro: $id_registro - " . $sql);
		$this->ejecutar_sql($sql, $id_registro);
		return $sql;
	}

	protected function ejecutar_sql_update($id_registro)
	{
		parent::ejecutar_sql_update($id_registro);

		if ($this->existe_fila_ext($id_registro)) {
			$where = $this->generar_sql_where_registro_ext($id_registro);
			parent::ejecutar_sql_update($id_registro, $this->_tabla_ext, $where);
		} else { // Hay que hacer un insert
			$this->actualizar_fks_ext($id_registro);
			parent::ejecutar_sql_insert($id_registro, false, $this->_tabla_ext);
		}
	}

	/**
	 * Siempre retorna false. En un ap multitabla no se pueden modificar las
	 * claves
	 * @return boolean
	 */
	protected function get_flag_mod_clave()
	{
		return false;
	}


	protected function get_select_col($col)
	{
		if ($this->get_tabla($col) == $this->_tabla) {
			return $this->_alias  . "." . $col;
		} else {
			return $this->_tabla_ext  . "." . $col;
		}
	}
	
	protected function existe_fila_ext($id_registro)
	{
		$sql = "SELECT 1 FROM $this->_tabla_ext WHERE ".
		  implode(' AND ', $this->generar_sql_where_registro_ext($id_registro));

		$rs = toba::db($this->_fuente)->consultar_fila($sql);
		return !empty($rs);
	}

	protected function generar_sql_delete_ext($id_registro)
	{
		return "DELETE FROM " . $this->_tabla_ext .
				" WHERE " . implode(" AND ",$this->generar_sql_where_registro_ext($id_registro) ) .";";
	}

	protected function generar_sql_where_registro_ext($id_registro)
	{
		$registro = $this->datos[$id_registro];
		$where = array();
		foreach ($this->_fks as $fk) {
			$col = $this->_cols_por_tabla[$this->_tabla][$fk['columna']];
			$where[$fk['columna_ext']] = $registro[$col['columna']];
		}

		return $this->generar_clausula_where_lineal($where, false);
	}

	//-------------------------------------------------------------------------------
	//------Métodos que ayudan al manejo multitabla----------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Actualiza los datos de foreign keys en el registro pasado por parámeto
	 * a partir de los datos en el registro
	 * @param integer $id_registro
	 */
	protected function actualizar_fks_ext($id_registro)
	{
		$fks = $this->armar_fk($id_registro);
		foreach ($fks as $col => $valor) {
			// Actualizamos los valores de las fks para la inserción
			$this->datos[$id_registro][$col] = $valor;
		}
	}

	/**
	 * Arma la fk a partir del último registro de la tabla padre impactado en la base
	 */
	protected function armar_fk($id_registro)
	{
		$registro = $this->datos[$id_registro];
		$rs	= array();
		foreach ($this->_fks as $fk) {
			$col = $this->_cols_por_tabla[$this->_tabla][$fk['columna']];
			if ($col['secuencia'] != "" && !isset($registro[$col['columna']])) {
				// es secuencia y no tiene el valor seteado
				$valor = recuperar_secuencia($col['secuencia'], $this->_fuente);
			} else {
				$valor = $registro[$col['columna']];
			}
			$rs[$fk['columna_ext']] = $valor;
		}
		return $rs;
	}

	protected function reordenar_columnas()
	{
		$rs = array();
		foreach ($this->_columnas as $key => $col) {
			$rs[$col['tabla']][$col['columna']] = &$this->_columnas[$key];
		}

		return $rs;
	}

	protected function reordenar_pks()
	{
		$rs = array();
		foreach ($this->_columnas as $key => $col) {
			if ($col['pk'] == 1) {
				$rs[$col['tabla']][$col['columna']] = &$this->_columnas[$key];
			}
		}

		return $rs;
	}

	/**
	 * Devuelve la tabla de una determinada columna. Si no la encuentra tira una
	 * excepción. Se asume que las columnas no están repetidas!!
	 * @param string $col
	 */
	protected function get_tabla($col)
	{
		if (isset($this->_cols_por_tabla[$this->_tabla][$col])) {
			return $this->_tabla;
		} elseif (isset($this->_cols_por_tabla[$this->_tabla_ext][$col])) {
			return $this->_tabla_ext;
		} else {
			throw new toba_error("TOBA AP MT: No existe la columna $col");
		}
	}


	protected function hay_cambios_ext($id_registro)
	{
		$registro = $this->datos[$id_registro];
		foreach($this->_columnas as $col) {
			if ($this->get_tabla($col['columna']) == $this->_tabla_ext) {
				if (!empty($registro[$col['columna']])) {
					return true;
				}
			}
		}
		return false;
	}
}
?>