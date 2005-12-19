<?php

require_once("version.php");

class proyecto
{
	private $identificador;			// 	Id del proyecto
	private $instancia;				//	Referencia a la instancia en la que se esta trabajando

	public function __contruct($proyecto, $instancia)
	{
		$this->identificador = $proyecto;
		$this->instancia = $instancia;
	}

	//----------------------------------------------------------------------------
	// Exportacion a SQL
	//----------------------------------------------------------------------------

	public function exportar_a_sql()
	{
		//Exportacion de ITEMS
		foreach( $this->get_ids_items() as $item){
			$i = new elemento_item();
			$i->cargar_db($this->identificador, $item['item']);
			$sql = $i->get_sql();
		}
		//Exportacion de OBJETOS
		foreach( $this->get_ids_objetos() as $objeto){
			$i = new elemento_objeto();
			$i->cargar_db($this->identificador, $objeto['objeto']);
			$i->exportar_sql();
		}
	}

	private function get_ids_items()
	{
		$sql = "SELECT	item 
				FROM 	apex_item
				WHERE proyecto = '$this->identificador'
				ORDER BY 1";
		return consultar_fuente($sql, "instancia");
	}

	private function get_ids_objetos()
	{
		$sql = "SELECT	objeto 
				FROM	apex_objeto 
				WHERE clase IN ('". implode("','",version::get_clases_validas() ) ."')
				AND proyecto = '$this->identificador'
				ORDER BY 1";
		return consultar_fuente($sql, "instancia");
	}
	//----------------------------------------------------------------------------
}
?>