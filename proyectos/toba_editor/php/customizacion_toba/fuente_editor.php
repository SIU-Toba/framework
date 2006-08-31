<?

class fuente_editor extends toba_fuente_datos
{
	function get_db()
	{
		return editor::get_base_activa();
	}
}
?>