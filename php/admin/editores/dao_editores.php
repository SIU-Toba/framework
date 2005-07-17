<?

class dao_editores
{
	static function get_clases_validas()
	{
		return array(	'objeto_ci',
						'objeto_ei_cuadro',
						'objeto_ei_formulario',
						'objeto_ei_formulario_ml',
						'objeto_ei_filtro' );
	}

	static function get_lista_clases_toba()
	{
		$sql = "SELECT 	proyecto || ',' || clase as clase, 
						clase as descripcion
				FROM apex_clase 
				WHERE clase IN ('". implode("','",self::get_clases_validas() ) ."')
				AND proyecto = '". toba::get_hilo()->obtener_proyecto() ."'
				ORDER BY 2";
		return consultar_fuente($sql, "instancia");
	}	

	static function get_lista_objetos_toba($clase=null)
	{
		$sql = "SELECT 	proyecto, 
						objeto, 
						objeto || ' - ' || nombre as descripcion
				FROM apex_objeto 
				WHERE clase IN ('". implode("','",self::get_clases_validas() ) ."')
				AND proyecto = '". toba::get_hilo()->obtener_proyecto() ."'
				ORDER BY 2";
		return consultar_fuente($sql, "instancia");
	}
}
?>