<?

class representacion_item extends representacion
{
	function construct()
	{
		$this->tabla = "apex_item";
		$this->columnas[] = "nombre";
		$this->columnas[] = "descripcion";
		$this->columnas[] = "menu";
		$this->claves[] = "proyecto";
		$this->claves[] = "item";
		$this->restricciones['menu']['where']= "AND menu = 1";
		$this->asociaciones['objetos']['tabla'] = "apex_item_objeto";
		$this->asociaciones['objetos']['claves'][] = "item";
		$this->asociaciones['objetos']['claves'][] = "proyecto";
		
	}	
	
}
?>