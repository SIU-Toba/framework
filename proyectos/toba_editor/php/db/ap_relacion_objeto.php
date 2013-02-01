<?php

class ap_relacion_objeto extends toba_ap_relacion_db
{
	/**
	 * 	Guardo el ID del objeto en la asociacion de eventos y puntos de control
	 */
	function evt__pre_sincronizacion()
	{
		if ($this->objeto_relacion->existe_tabla('puntos_control')) {
			$clave = $this->objeto_relacion->tabla('base')->get_clave_valor(0);
			$this->objeto_relacion->tabla('puntos_control')->set_columna_valor('objeto', $clave['objeto']);
		}

		//Aca controlo que las relaciones entre tablas tengan sus columnas
		if ($this->objeto_relacion->existe_tabla('columnas_relacion')) {
			$rel = $this->objeto_relacion->tabla('relaciones')->get_id_filas_a_sincronizar(array('i','u'));								//Pregunto si hay relaciones nuevas.
			if (! empty($rel)) {
				foreach ($rel as $id_rel) {
					$this->verificar_relaciones_columnas($id_rel);
				}
			}
		}
	}

	/**
	 * Verifica que la relacion indicada tenga columnas asociadas
	 * @param integer $id_rel
	 */
	function verificar_relaciones_columnas($id_rel)
	{
		$busqueda = $this->objeto_relacion->tabla('columnas_relacion')->nueva_busqueda();
		$busqueda->set_padre('relaciones', $id_rel);
		$ids = $busqueda->buscar_ids();

		if (empty($ids)) {
			$tablas = $this->objeto_relacion->tabla('relaciones')->get_fila($id_rel);
			$mensaje = 'Debe especificar al menos una asociación de columnas en la '.
						"relación entre {$tablas['padre_id']} y {$tablas['hijo_id']}";
			throw new toba_error($mensaje);
		}
	}

	/**
	 * 	Log de modificacion de un OBJETO TOBA
	 */
	function evt__post_sincronizacion()
	{
		$clave = $this->objeto_relacion->tabla('base')->get_clave_valor(0);
		$schema_logs = toba::db()->get_schema() . '_logs';
		$usuario = toba::usuario()->get_id();
		$sql = "INSERT INTO $schema_logs.apex_log_objeto (usuario, objeto_proyecto, objeto, observacion)
				VALUES ('$usuario','{$clave['proyecto']}','{$clave['objeto']}',NULL)";
		ejecutar_fuente($sql, $this->objeto_relacion->get_fuente());
	}
}
?>