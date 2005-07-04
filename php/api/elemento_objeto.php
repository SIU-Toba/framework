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
		if(isset($this->datos['apex_objeto_dependencias']))	{
			for($a=0;$a<count($this->datos['apex_objeto_dependencias']);$a++) {
				$this->subelementos[$a]= $this->construir_subelemento($this->datos['apex_objeto_dependencias'][$a]);
			}
		}
	}
	
	function construir_subelemento($datos)
	{
		//ATENCION: la clase del objeto se debería conocer antes para poder crear la clase asociada a la clase (!)
		$sql = "
			SELECT 
				c.clase,
				c.archivo
			FROM apex_clase c,
				apex_objeto o
			WHERE (o.clase_proyecto = c.proyecto)
				AND (o.clase = c.clase)
				AND (o.objeto = '{$datos['objeto_proveedor']}')
				AND (o.proyecto = '{$datos['proyecto']}')";
		$rs = consultar_fuente($sql,"instancia",null,true);
		require_once($rs[0]['archivo']);
		$elemento = call_user_func(array($rs[0]['clase'], 'elemento_toba'));
		$elemento->cargar_db($datos['proyecto'], $datos['objeto_proveedor']);
		$elemento->set_consumidor($this, $datos);
		return $elemento;
	}

	
	//---- MANEJO DE EVENTOS
	function es_evento($metodo)
	{
		return (ereg("^evt(.*)", $metodo)); //evt seguido de cualquier cosa	
	}	
	
	function eventos_predefinidos()
	{
		return array();
	}	
	
	function es_evento_predefinido($metodo)
	{
		if (! $this->es_evento_valido($metodo))
			return false;
	
		if (isset($this->rol_en_consumidor['identificador'])) {
			//Debe buscar cosas de tipo 'evt__id__evento'?
			$id = $this->rol_en_consumidor['identificador'];
			ereg("^evt__".$id."__(.*)", $metodo, $detalle);
			if (count($detalle) == 2 && in_array($detalle[1], $this->eventos_predefinidos()))
				return true;
		} else {
			//Debe buscar cosas de tipo 'evt__evento'		
			ereg("^evt__(.*)", $metodo, $detalle);
			if (count($detalle) == 2 && in_array($detalle[1], $this->eventos_predefinidos()))
				return true;		
		}
		//Los hijos lo tienen?
		foreach ($this->subelementos as $elemento) {
			if ($elemento->es_evento_predefinido($metodo))
				return true;
		}
		return false;
	}

	function es_evento_valido($metodo)
	{
		if (ereg("^evt__(.*)", $metodo))
			return true; //evt__ seguido de cualquier cosa
		foreach ($this->subelementos as $elemento) {
			if ($elemento->es_evento_valido($metodo))
				return true;
		}	
		return false;
	}

	function es_evento_sospechoso($metodo)
	{
		//Busca cosas como evt___
		if (ereg("^evt___(.*)", $metodo)) {	
			return true;
		}	
		if (isset($this->rol_en_consumidor['identificador'])) {
			$id = $this->rol_en_consumidor['identificador'];

			//Busca cosas como evt__id_evento
			ereg("^evt__".$id."_([^_].*)", $metodo, $detalle);
			if (count($detalle) == 2 && in_array($detalle[1], $this->eventos_predefinidos()))
				return true;
				
			//Busca cosas como evt__id___evento
			ereg("^evt__".$id."__[_*](.*)", $metodo, $detalle);
			if (count($detalle) == 2)
				return true;			
		}
		foreach ($this->subelementos as $elemento) {
			if ($elemento->es_evento_sospechoso($metodo))
				return true;
		}			
		return false;		
	}	

}
?>