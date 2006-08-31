<?

class fuente_editor extends fuente_de_datos
{
	function get_db()
	{
		return editor::get_base_activa();
	}
}
?>