<?

class fuente_editor extends toba_fuente_datos
{
	function get_db()
	{
		return toba_editor::get_base_activa();
	}
}
?>