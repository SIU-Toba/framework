<?
require_once("elemento.php");

class elemento_objeto extends elemento
{
	function __construct()
	{
		$this->tipo = "objeto";	
		parent::__construct();
	}
		
	function cargar_db($proyecto, $elemento)
	//Amplio la definicion de las tablas
	{
		//busco las tablas que dependen del tipo de clase
		$sql = "SELECT c.plan_dump_objeto as plan
				FROM apex_clase c,
				apex_objeto o
				WHERE (o.clase_proyecto = c.proyecto)
				AND (o.clase = c.clase)
				AND (o.objeto = '$elemento')
				AND (o.proyecto = '$proyecto')";
		$temp = consultar_fuente($sql,"instancia",null,true);
		$plan_dumpeo = parsear_propiedades( $temp[0]['plan'] );
		//Las agrego en la lista de tablas a dumpear
		$indice = count($this->tablas);
		foreach($plan_dumpeo as $tabla => $clave)
		{
			$temp['tabla'] = $tabla;
			$temp['columna_clave_proyecto'] = $clave . "_proyecto";
			$temp['columna_clave'] = $clave;
			$temp['obligatoria'] = "1";
			$this->tablas[$indice]=$temp;
			$indice++;
		}
		//Llamo al padre para que cargue los datos
		parent::cargar_db($proyecto, $elemento);
	}

	function cargar_db_subelementos()
	{
		//Si hay objetos asociados...
		if(isset($this->datos['apex_objeto_dependencias']))
		{
			for($a=0;$a<count($this->datos['apex_objeto_dependencias']);$a++)
			{
				//Los cargo en el array de subcomponentes
				$this->subelementos[$a]= new elemento_objeto();
				$proyecto = $this->datos['apex_objeto_dependencias'][$a]['proyecto'];
				$objeto = $this->datos['apex_objeto_dependencias'][$a]['objeto_proveedor'];
				$this->subelementos[$a]->cargar_db($proyecto, $objeto);
			}
			
		}
	}

	function obtener_docbook()
	{
		
	}
}
?>