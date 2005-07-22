<?
require_once("elemento.php");
require_once("elemento_objeto.php");
require_once("nucleo/lib/item.php");

class elemento_item extends elemento implements recorrible_como_arbol
{
	protected $item;
	
	function __construct()
	{
		$this->tipo = "item";	
		parent::__construct();
	}
	
	function cargar_db_subelementos()
	{
		//Cableado de apis de items
		$this->item = new item($this->datos['apex_item'][0]);
	
		//Si hay objetos asociados...
		if(isset($this->datos['apex_item_objeto']))
		{
			for($a=0;$a<count($this->datos['apex_item_objeto']);$a++)
			{
				$proyecto = $this->datos['apex_item_objeto'][$a]['proyecto'];
				$objeto = $this->datos['apex_item_objeto'][$a]['objeto'];
				$this->subelementos[$a] = $this->construir_objeto($proyecto, $objeto);
			}
		}
	}
	
	function obtener_docbook()
	{
		$docbook = "";
		if(isset($this->datos['apex_item_info'][0]['descripcion_larga'])){
			$docbook .= $this->datos['apex_item_info'][0]['descripcion_larga'];
		}else{
			$docbook .= "<para></para>";
		}
		/*
		for($a=0;$a<count($this->subelementos);$a++)
		{
			$docbook .= $this->subelementos[$a]->obtener_docbook();
		}
		*/
		return $docbook;
	}
	
	function obtener_php()
	{
		//Devuelve el PHP asociado al ITEM	
	}
	
	///------------ Recorrible como arbol
	function hijos()
	{
		return $this->subelementos;
	}
	
	function es_hoja()
	{
		return (count($this->subelementos) == 0);
	}

	function tiene_propiedades()
	{
		return false;
	}	
	
	function nombre_corto()
	{
		return $this->datos['apex_item'][0]['nombre'];
	}
	
	function nombre_largo()
	{
		return $this->nombre_corto();
	}
	
	function id()
	{
		return $this->datos['apex_item'][0]['item_id'];	
	}
	
	function iconos()
	{
		return $this->item->iconos();
	}
	
	function utilerias()
	{
		return $this->item->utilerias();	
	}
}
?>