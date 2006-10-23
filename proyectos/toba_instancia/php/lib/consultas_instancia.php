<?

class consultas_instancia
{
	static function get_lista_proyectos()
	{
		$sql = "SELECT proyecto FROM apex_proyecto;";
		return toba::db()->consultar($sql);
	}

	static function get_datos_proyecto($proyecto)
	{
		$sql = "SELECT * FROM apex_proyecto WHERE proyecto = '$proyecto';";
		$rs = toba::db()->consultar($sql);
		return $rs[0];
	}
}

?>