<?
require_once("nucleo/componentes/persistencia/ap_relacion_db.php");

class ap_relacion_objeto extends ap_relacion_db
{
	/**
	 * 	Log de modificacion de un OBJETO TOBA
	 */
	function evt__post_sincronizacion()
	{
		$clave =  $this->objeto_relacion->tabla('base')->get_clave_valor(0);
		$usuario = toba::get_hilo()->obtener_usuario();
		$sql = "INSERT INTO apex_log_objeto (usuario, objeto_proyecto, objeto, observacion)
				VALUES ('$usuario','{$clave['proyecto']}','{$clave['objeto']}',NULL)";
		ejecutar_fuente( $sql, $this->objeto_relacion->get_fuente() );
	}
}
?>	