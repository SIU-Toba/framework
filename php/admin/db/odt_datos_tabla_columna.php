<?
require_once("nucleo/persistencia/objeto_datos_tabla.php");

class odt_datos_tabla_columna extends objeto_datos_tabla
{
	function configuracion()
	{
		$this->set_no_duplicado(array('columna'));
	}
}
?>