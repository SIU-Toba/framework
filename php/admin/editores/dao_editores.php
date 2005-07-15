<?

class dao_editores
{
	public function get_lista_clases_toba()
	{
		$sql = "SELECT 	proyecto || ',' || clase as clase, 
						clase as descripcion
				FROM apex_clase 
				WHERE clase IN (	'objeto_ci',
									'objeto_ei_cuadro',
									'objeto_ei_formulario',
									'objeto_ei_formulario_ml',
									'objeto_ei_filtro'
								)
				AND proyecto = '". toba::get_hilo()->obtener_proyecto() ."'
				ORDER BY 2";
		return consultar_fuente($sql, "instancia");
	}	

	public function get_lista_objetos_toba($clase=null)
	{
		$sql = "SELECT 	proyecto, 
						objeto, 
						objeto || ' - ' || nombre as descripcion
				FROM apex_objeto 
				WHERE clase <> 'objeto'
				AND proyecto = '". toba::get_hilo()->obtener_proyecto() ."'
				ORDER BY 2";
		return consultar_fuente($sql, "instancia");
	}
}
?>