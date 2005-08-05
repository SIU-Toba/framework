<?
require_once("admin/db/dao_editores.php");
require_once("api/elemento_item.php");
require_once("api/elemento_objeto.php");

class exportador
{
	function proyecto_a_sql($proyecto)
	{
		self::items_a_sql($proyecto);
		self::objetos_a_sql($proyecto);			
	}
	
	function items_a_sql($proyecto)
	{
		foreach( dao_editores::get_ids_items_instancia($proyecto) as $item){
			$i = new elemento_item();
			$i->cargar_db($proyecto, $item['item']);
			$i->exportar_sql();
		}
	}
	
	function objetos_a_sql($proyecto)
	{
		foreach( dao_editores::get_ids_objetos_instancia($proyecto) as $ibjeto){
			$i = new elemento_objeto();
			$i->cargar_db($proyecto, $ibjeto['objeto']);
			$i->exportar_sql();
		}
	}

}
?>