<?
require_once('nucleo/lib/fuente_de_datos.php');

class fuente_editor extends fuente_de_datos
{
	function get_db()
	{
		return editor::get_base_activa();
	}
}
?>