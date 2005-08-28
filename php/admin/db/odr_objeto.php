<?
require_once("nucleo/persistencia/objeto_datos_relacion.php");

class odr_objeto extends objeto_datos_relacion
{
	/*
	function evt__post_sincronizacion()
	//Log de modificacion de un OBJETO TOBA
	{
		$clave = $this->elemento['base']->get_clave_valor(0);
		$usuario = toba::get_hilo()->obtener_usuario();
		$sql = "INSERT INTO apex_log_objeto (usuario, objeto_proyecto, objeto, observacion)
				VALUES ('$usuario','{$clave['proyecto']}','{$clave['objeto']}',NULL)";
		ejecutar_sql( $sql, $this->fuente);		
	}
	*/
}
?>	