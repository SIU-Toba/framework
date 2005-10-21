<?
require_once("elemento.php");
require_once("elemento_objeto.php");
require_once("nucleo/lib/item_toba.php");

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
		$this->item = new item_toba($this->datos['apex_item'][0]);
	
		//Si hay objetos asociados...
		if(isset($this->datos['apex_item_objeto']))
		{
			for($a=0;$a<count($this->datos['apex_item_objeto']);$a++)
			{
				$proyecto = $this->datos['apex_item_objeto'][$a]['proyecto'];
				$objeto = $this->datos['apex_item_objeto'][$a]['objeto'];
				$this->subelementos[$a] = elemento_objeto::get_elemento_objeto($proyecto, $objeto);
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
	
	//-------------------------------------------
	
	function exportar_sql()
	{
		$cabecera = "-- Exportacion: ". date("d/M/Y") . "\n";
		$car_invalidos = array("*", "?", "/", ">", "<", "\"", "'");
		$nombre_item = str_replace($car_invalidos, "-", $this->id);
		$dbt = toba_dbt::item();
		$dbt->cargar(array( "proyecto"=>$this->proyecto, "item"=>$this->id));
		$sql = $dbt->get_sql_inserts();
		$data = $cabecera . implode("\n",$sql);
		//ATENCION: El path se debe diferenciar por proyecto		
		$path = toba::get_hilo()->obtener_proyecto_path() . "/sql/exportacion/items/$nombre_item.sql";
		manejador_archivos::crear_archivo_con_datos($path, $data);
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
		return $this->datos['apex_item'][0]['item'];	
	}
	
	function iconos()
	{
		return $this->item->iconos();
	}
	
	function vinculo_editor()
	{
		return $this->item->vinculo_editor();	
	}
	
	function utilerias()
	{
		$utilerias = $this->item->utilerias();
		$utilerias[] = array(
			'imagen' => recurso::imagen_apl("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear un objeto asociado al item",
			'vinculo' => toba::get_vinculador()->generar_solicitud("toba","/admin/objetos_toba/crear",
								array('destino_tipo' =>'item', 
										'destino_proyecto' => $this->proyecto,
										'destino_id' => $this->id ),
										false, false, null, true, "central")
		);
		return $utilerias;
	}
}
?>