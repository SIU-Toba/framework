<?
require_once("nucleo/componentes/persistencia/ap_relacion_db.php");

class ap_relacion_item extends ap_relacion_db
{
	/**
	 * 	Log de modificacion de un OBJETO TOBA
	 */
	function evt__post_sincronizacion()
	{
		$clave =  $this->objeto_relacion->tabla('base')->get_clave_valor(0);
		$usuario = toba::get_hilo()->obtener_usuario();
		$sql = "INSERT INTO apex_log_objeto (usuario, objeto_proyecto, item, observacion)
				VALUES ('$usuario','{$clave['proyecto']}','{$clave['item']}',NULL)";
		ejecutar_sql( $sql, $this->objeto_relacion->get_fuente() );
	}
}
?>	