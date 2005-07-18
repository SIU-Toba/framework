<?
require_once("nucleo/persistencia/db_tablas.php");

class dbt_objeto_toba extends db_tablas
{
	protected $objeto_procesado = null;

	function set_objeto_procesado($id)
	{
		$this->objeto_procesado = $id;	
	}

	function evt__post_sincronizacion()
	/*
		Log de modificacion de un OBJETO TOBA
	*/	
	{
		if(!isset($this->objeto_procesado))
			throw new excepcion_toba("Es necesario indicar el objeto que se edito en el DBT hijo");
		$usuario = toba::get_hilo()->obtener_usuario();
		$proyecto = $this->objeto_procesado['proyecto'];
		$objeto = $this->objeto_procesado['objeto'];
		$sql = "INSERT INTO apex_log_objeto (usuario, objeto_proyecto, objeto, observacion)
				VALUES ('$usuario','$proyecto','$objeto',NULL)";
		ejecutar_sql( $sql, $this->fuente);		
	}
}
?>	