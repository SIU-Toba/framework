<?php
require_once("nucleo/componentes/persistencia/toba_ap_relacion_db.php");

class ap_relacion_objeto extends toba_ap_relacion_db
{
	/**
	 * 	Guardo el ID del objeto en la asociacion de eventos y puntos de control
	 */
	function evt__pre_sincronizacion()
	{
		if ($this->_objeto_relacion->existe_tabla('puntos_control')) {
			$clave =  $this->_objeto_relacion->tabla('base')->get_clave_valor(0);
			$this->_objeto_relacion->tabla('puntos_control')->set_columna_valor('objeto',$clave['objeto']);
		}
	}

	/**
	 * 	Log de modificacion de un OBJETO TOBA
	 */
	function evt__post_sincronizacion()
	{
		$clave =  $this->_objeto_relacion->tabla('base')->get_clave_valor(0);
		$usuario = toba::usuario()->get_id();
		$sql = "INSERT INTO apex_log_objeto (usuario, objeto_proyecto, objeto, observacion)
				VALUES ('$usuario','{$clave['proyecto']}','{$clave['objeto']}',NULL)";
		ejecutar_fuente( $sql, $this->_objeto_relacion->get_fuente() );
	}
}
?>	