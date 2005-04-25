<?
require_once("elemento.php");
require_once("elemento_objeto.php");

class elemento_item extends elemento
{
	
	function __construct()
	{
		$this->tipo = "item";	
		parent::__construct();
	}
	
	function cargar_db_subelementos()
	{
		//Si hay objetos asociados...
		if(isset($this->datos['apex_item_objeto']))
		{
			for($a=0;$a<count($this->datos['apex_item_objeto']);$a++)
			{
				//Los cargo en el array de subcomponentes
				$this->subelementos[$a]= new elemento_objeto();
				$proyecto = $this->datos['apex_item_objeto'][$a]['proyecto'];
				$objeto = $this->datos['apex_item_objeto'][$a]['objeto'];
				$this->subelementos[$a]->cargar_db($proyecto, $objeto);
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
}
?>