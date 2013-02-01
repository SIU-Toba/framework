<?php

class ap_relacion_item extends toba_ap_relacion_db
{
	/**
	 * 	Log de modificacion de un OBJETO TOBA
	 */
	function evt__post_sincronizacion()
	{
		$schema_logs = toba::db()->get_schema() . '_logs';
		$clave = $this->objeto_relacion->tabla('base')->get_clave_valor(0);
		$usuario = toba::usuario()->get_id();
		$sql = "INSERT INTO $schema_logs.apex_log_objeto (usuario, objeto_proyecto, item, observacion)
				VALUES ('$usuario','{$clave['proyecto']}','{$clave['item']}',NULL)";
		ejecutar_fuente($sql, $this->objeto_relacion->get_fuente());
	}
}
?>	