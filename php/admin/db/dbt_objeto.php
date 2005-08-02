<?
require_once("nucleo/persistencia/db_tablas.php");

class dbt_objeto extends db_tablas
{
	function evt__post_sincronizacion()
	/*
		Log de modificacion de un OBJETO TOBA
	*/	
	{
		$clave = $this->elemento['base']->get_clave_valor(0);
		$usuario = toba::get_hilo()->obtener_usuario();
		$sql = "INSERT INTO apex_log_objeto (usuario, objeto_proyecto, objeto, observacion)
				VALUES ('$usuario','{$clave['proyecto']}','{$clave['objeto']}',NULL)";
		ejecutar_sql( $sql, $this->fuente);		
	}
}
?>	