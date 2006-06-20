<?
require_once("nucleo/persistencia/objeto_datos_tabla.php");

class odt_eventos extends objeto_datos_tabla
{
	function configuracion()
	{
		$this->set_no_duplicado(array('identificador'));
	}
}
?>
