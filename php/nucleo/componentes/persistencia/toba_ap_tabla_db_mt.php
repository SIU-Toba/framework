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
	protected $_schema_ext;
	
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
		$this->_schema_ext	= $this->objeto_tabla->get_schema_ext();
		$this->_cols_por_tabla	= $this->reordenar_columnas();
		$this->limpiar_claves();
		$this->_fks			= $this->objeto_tabla->get_fks();

		$this->inicializar();
		$this->ini();
	}
	
	function get_tipo()
	{
		return toba_ap_tabla_db::tipo_multitabla;
	}
	
	//--------------------------------------------------------------------------------------------------------------------//
	//					METODOS AUXILIARES
	//--------------------------------------------------------------------------------------------------------------------//	
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

	protected function es_seq_tabla_ext($col)
	{
		return ($this->_tabla_ext == $this->get_tabla($col));
	}
	
	protected function get_from_default()
	{
		$tabla		= $this->agregar_schema($this->_tabla);
		$alias		= $this->_alias;
		$tabla_ext	= $this->agregar_schema($this->_tabla_ext, true);

		$condiciones	= array();
		foreach ($this->_fks as $fk) {		//Genero las igualdades para c/fk
			$condiciones[] = implode(' = ', array("$alias.{$fk['columna']}", "$tabla_ext.{$fk['columna_ext']}"));
		}
		
		$condicion = implode(' AND ', $condiciones);			//Junto las condiciones
		return "$tabla as $alias LEFT OUTER JOIN $tabla_ext ON $condicion";
	}
	
	protected function get_sql_campos_default($where)
	{
		$sql = "SELECT\n\t".implode(", \n\t", $this->_insert_campos_default);
		$sql .= "\nFROM\n\t {$this->get_from_default()} ";
		$sql .= "\nWHERE ".implode(' AND ', $where);

		return $sql;
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

	/**
	 * Ventana para manejar la pérdida de sincronización con la tabla en la base de datos
	 * El escenario es que ejecuto un update/delete usando los valores de las columnas originales y no arrojo resultados, con lo que se asume que alguien más modifico el registro en el medio
	 * La transacción con la bd aún no se terminó (si es que esta definida)
	 * 
	 * @param integer $id_fila Id. de fila de la tabla en la cual se encontró el problema
	 * @param string $sql_origen Sentencia que se intento ejecutar
	 * @ventana
	 */
	function evt__perdida_sincronizacion($id_fila, $sql_origen)
	{
		$mensaje_usuario = "Error de concurrencia en la edición de los datos.<br><br>".
							"Mientras Ud. editaba esta información, la misma fue modificada por alguien más. ".
							"Para garantizar consistencia sólo podrá guardar cambios luego de reiniciar la edición.<br>";

		//--Hace una consulta SQL contra la tabla para averiguar puntualmente cuál fue el cambio que llevo a esta situación		
		$id = array();
		foreach($this->_clave as $clave){
			$id[$clave] = $this->_cambios[$id_fila]['clave'][$clave];
		}

		//Primero se hace para la tabla ppal
		$columnas = array();
		foreach ($this->_cols_por_tabla[$this->_tabla] as $col) {
			if(!$col['externa'] && $col['tipo'] != 'B') {
				$columnas[] = $col['columna'];
			}
		}
		
		$where = $this->generar_clausula_where_lineal($id, false);
		$nombre_tabla = $this->agregar_schema($this->_tabla);
		$sql = "SELECT\n\t" . implode(", \n\t", $columnas);
		$sql .= "\nFROM\n\t  $nombre_tabla";
		$sql .= "\nWHERE ".implode(' AND ', $where);
		$fila_base = toba::db($this->_fuente)->consultar_fila($sql);		
		$msg_tabla_original = $this->determinar_cambios_fila($this->_tabla, $fila_base, $id_fila, $columnas, $sql);
		
		//Luego para la tabla extendida
		$columnas = array();
		foreach ($this->_cols_por_tabla[$this->_tabla_ext] as $col) {
			if(!$col['externa'] && $col['tipo'] != 'B') {
				$columnas[] = $col['columna'];
			}
		}
		
		$where = $this->generar_sql_where_registro_ext($id_fila);
		$nombre_tabla = $this->agregar_schema($this->_tabla_ext, true);
		$sql = "SELECT\n\t" . implode(", \n\t", $columnas);
		$sql .= "\nFROM\n\t  $nombre_tabla";
		$sql .= "\nWHERE ".implode(' AND ', $where);
		$fila_base_ext = toba::db($this->_fuente)->consultar_fila($sql);
		$msg_tabla_ext = $this->determinar_cambios_fila($this->_tabla_ext, $fila_base_ext, $id_fila, $columnas, $sql);
	
		$mensaje_debug = $msg_tabla_original ."\n Tabla Extendida: " . $msg_tabla_ext;		
		throw new toba_error_usuario($mensaje_usuario, $mensaje_debug);
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
		$nombre_tabla = $this->agregar_schema($this->_tabla_ext, true);
		$sql = "SELECT 1 FROM $nombre_tabla WHERE ".
		  implode(' AND ', $this->generar_sql_where_registro_ext($id_registro));

		$rs = toba::db($this->_fuente)->consultar_fila($sql);
		return !empty($rs);
	}

	//------------------------------------------------------------------------------------------------------------------------//
	//				SINCRONIZACION CON LA BASE
	//------------------------------------------------------------------------------------------------------------------------//
	protected function ejecutar_sql_insert($id_registro, $solo_retornar=false, $tabla = null, $cols_tabla = array(), $tabla_ext = false)
	{
		if ($solo_retornar) {
			// Se devuelve esto xq si sólo se retorna no hay manera de armar la
			// fk para el próximo insert
			return parent::ejecutar_sql_insert($id_registro, $solo_retornar, null, $this->_cols_por_tabla[$this->_tabla]);
		}

		parent::ejecutar_sql_insert($id_registro, false, null, $this->_cols_por_tabla[$this->_tabla]);
		
		if ($this->hay_cambios_ext($id_registro)) {
			$this->actualizar_fks_ext($id_registro);
			//$nombre_tabla = $this->agregar_schema($this->_tabla_ext);
			parent::ejecutar_sql_insert($id_registro, false, $this->_tabla_ext, $this->_cols_por_tabla[$this->_tabla_ext], true);
		}
	}
	
	protected function ejecutar_sql_update($id_registro, $tabla = null, $where = null, $cols_tabla = array(), $tabla_ext = false)	
	{
		parent::ejecutar_sql_update($id_registro, null, null, $this->_cols_por_tabla[$this->_tabla]);

		//$nombre_tabla = $this->agregar_schema($this->_tabla_ext);
		if ($this->existe_fila_ext($id_registro)) {
			$where = $this->generar_sql_where_registro_ext($id_registro);
			parent::ejecutar_sql_update($id_registro, $this->_tabla_ext, $where, $this->_cols_por_tabla[$this->_tabla_ext], true);
		} else { // Hay que hacer un insert
			$this->actualizar_fks_ext($id_registro);
			parent::ejecutar_sql_insert($id_registro, false, $this->_tabla_ext, $this->_cols_por_tabla[$this->_tabla_ext], true);
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
	
	protected function generar_sql_delete_ext($id_registro)
	{
		$nombre_tabla = $this->agregar_schema($this->_tabla_ext, true);
		return "DELETE FROM " . $nombre_tabla .
				" WHERE " . implode(" AND ",$this->generar_sql_where_registro_ext($id_registro) ) .";";
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
				$nombre_secuencia = $this->agregar_schema($col['secuencia']);
				$valor = recuperar_secuencia($nombre_secuencia, $this->_fuente);
			} else {
				$valor = $registro[$col['columna']];
			}
			$rs[$fk['columna_ext']] = $valor;
		}
		return $rs;
	}

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
	
	//--------------------------------------------------------------------------------------------------//
	//		METODOS QUE ARMAN ESTRUCTURA INTERNA
	//--------------------------------------------------------------------------------------------------//
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

	protected function agregar_schema($elemento, $es_externa = false)
	{
		$schema_actual = ($es_externa) ? $this->_schema_ext: $this->_schema;
		$resultado = (is_null($schema_actual)) ? $elemento : $schema_actual . '.' . $elemento;
		return $resultado;
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
	
	protected function determinar_cambios_fila($tabla, $fila_base, $id_fila, $columnas, $sql_origen)
	{
		if ($fila_base === false) {
			$diff = "La fila '$id_fila' no existe en la base, fue borrada";
		} else {
			$fila_original = $this->_cambios[$id_fila]['original'];
			$diff = "<ul>";
			foreach ($columnas as $col) {
				if (! isset($fila_base[$col])) {
					$fila_base[$col] = null;
				}
				if (! isset($fila_original[$col])) {
					$fila_original[$col] = null;
				}
				$modificado = (string) $fila_base[$col] !== (string) $fila_original[$col];
				if ($modificado) {
					$anterior = isset($fila_original[$col]) ? "'".$fila_original[$col]."'" : 'NULL';
					$actual = isset($fila_base[$col]) ? "'".$fila_base[$col]."'" : 'NULL';
					$diff .= "<li>$col: tenía el valor $anterior y ahora tiene $actual </li>";
				}
			}
			$diff .= '</ul>';
		}
		
		$mensaje_debug = '';
		$mensaje_debug .= "<p><b>Tabla:</b>$tabla</p>";
		$mensaje_debug .= "<p><b>Diff de datos:</b> Cambios en fila $id_fila ".$diff."</p>";
		$mensaje_debug .= "<p><b>SQL:</b> $sql_origen</p>";
		
		return ($mensaje_debug);
	}
}
?>
