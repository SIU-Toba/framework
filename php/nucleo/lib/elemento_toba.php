<?
/*
Mecanismo de exportacion de elementos del toba.
Orientado al intercambio de componentes entre instancias.

Este esquema tambien tiene que usarse para hacer dump a PHP de las
definiciones del toba. Dumpeo a array o serializacion directa de
los objetos?

En realidad esto deberia convertirse en toda la logica de negocio del toba

*/

class elemento_toba
{
	protected $tipo;			
	protected $tablas;
	protected $indice_tablas;
	protected $datos;
	protected $subelementos;

	function __construct()
	{
		//Cargar la definicion del elemento TOBA (tablas por donde esta distribuido)
		$sql = " SELECT elemento_infra			as	elemento_infra,	
					tabla					as	tabla					,	
					columna_clave_proyecto	as	columna_clave_proyecto	,	
					columna_clave			as	columna_clave			,	
					orden					as	orden					,	
					descripcion				as	descripcion				,	
					dependiente				as	dependiente				,	
					proc_borrar				as	proc_borrar				,	
					proc_exportar			as	proc_exportar			,	
					proc_clonar				as	proc_clonar	,
					obligatoria				as	obligatoria
				FROM apex_elemento_infra_tabla
				WHERE elemento_infra = '{$this->tipo}'
				AND dependiente <> 1
				ORDER BY orden";
		$this->tablas = consultar_fuente($sql, "instancia");
		//Completo la definicion de las tablas a guardar
		for($a=0;$a<count($this->tablas);$a++){
			$this->indice_tablas[$this->tablas[$a]['tabla']] = $a;
		}
	}
	//--------------------------------------------------------------
	
	function info($definicion=false)
	{
		//ei_arbol($this->indice_tablas, "Indice de tablas");
		if($definicion){
			ei_arbol($this->tablas,"TABLAS que conforman al componente");
		}
		ei_arbol($this->datos,"ELEMENO CARGADO");
		//Cargo los datos de los subelementos
		for($a=0;$a<count($this->subelementos);$a++)
		{
			ei_separador("SUBelemento: $a");
			$this->subelementos[$a]->info();
		}
	}

	//--------------------------------------------------------------
	//-----  Cargar el ELEMENTO  -----------------------------------
	//--------------------------------------------------------------

	function cargar_db($proyecto, $elemento)
	//Prepara las sentencias para cargar un ITEM
	{
		//Cargo las tablas en las que esta definido el elemento
		for($a=0;$a<count($this->tablas);$a++)
		{
			$tabla = $this->tablas[$a]['tabla'];
			//Busco las columnas
			$sql = " SELECT columna				as	columna
					FROM apex_mod_datos_tabla_columna
					WHERE tabla_proyecto = 'toba' AND tabla = '$tabla'
					ORDER BY orden";
			$temp = consultar_fuente($sql, "instancia");
			//Formateo columnas
			$columnas = array();
			for($b=0;$b<count($temp);$b++){
				$columnas[$b] = $temp[$b]['columna'];
			}
			$sql = "SELECT " . implode(", ",$columnas).
					" FROM " . $tabla .
					" WHERE  ( {$this->tablas[$a]['columna_clave_proyecto']} = '$proyecto' ) 
					AND ({$this->tablas[$a]['columna_clave']} = '$elemento' ) ;";
			//echo $sql . enter();
			//Cargo los datos
			if(!($temp = consultar_fuente($sql, "instancia"))){
				if($this->tablas[$a]['obligatoria']==1){
					//No se cargaron datos y la tabla es obligatoria
					throw new excepcion_toba("No se cargo una tabla obligatoria ($tabla)");
				}
			}else{
				$this->datos[$tabla]=$temp;
			}
		}
		//Cargo la definicion de los SUBELEMENTOS
		$this->cargar_db_subelementos();
	}

	function cargar_db_subelementos()
	{ 
		$this->subelementos = array();
	}

	function elemento_cargado()
	//Condicion para controlar si hay un elemento cargado
	{
		return (count($this->datos)>0);
	}

	//--------------------------------------------------------------
	//-----  Procesar el ELEMENTO  ---------------------------------
	//--------------------------------------------------------------

	function cambiar_proyecto($proyecto)
	//Cambia el proyecto de los objetos
	{
		
	}	

	function cambiar_id($id)
	//Permite cambiar el ID del elemento
	{
		
	}
	
	function modificar_propiedad($tabla, $propiedad)
	{
		
	}

	//--------------------------------------------------------------
	//-----  Exportar el ELEMENTO  ---------------------------------
	//--------------------------------------------------------------

	function generar_sql_insert($tabla)
	{
		$sql = array();
		for($a=0;$a<count($this->datos[$tabla]);$a++){
			$sql[]= sql_array_a_insert($tabla, $this->datos[$tabla][$a]);
		}
		return $sql;
	}

	function generar_sql_update()
	{
		
	}

	//--------------------------------------------------------------
	//-----  I/O  --------------------------------------------------
	//--------------------------------------------------------------
	
	function exportar_sql_insert()
	//Devuelve un ARRAY de sentencias SQL
	{
		//Genero el SQL del los subcomponentes		
		$sql = array();
		for($a=0;$a<count($this->subelementos);$a++)
		{
			$sql = array_merge($sql, $this->subelementos[$a]->exportar_sql_insert());
		}
		//Genero el SQL del elemento
		foreach( array_keys($this->datos) as $tabla ){
			$sql = array_merge($sql, $this->generar_sql_insert($tabla));
		}
		return $sql;
	}

	function exportar_php()
	//Para la ejecucion del toba sin fuente
	{
		$php = "";
		//PHP de los SUBCOMPONENTES
		for($a=0;$a<count($this->subelementos);$a++){
			$php .= $this->subelementos[$a]->exportar_php();
		}
		foreach( array_keys($this->datos) as $tabla ){
			$php .= dump_array_php($this->datos[$tabla], "\$". $tabla );
		}
		return $php;
	}
}
//######################################################################
//######################################################################

class elemento_toba_item extends elemento_toba
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
				$this->subelementos[$a]= new elemento_toba_objeto();
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
//######################################################################
//######################################################################

class elemento_toba_objeto extends elemento_toba
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
				$this->subelementos[$a]= new elemento_toba_objeto();
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
//######################################################################
//######################################################################

class elemento_toba_clase extends elemento_toba
{
	function __construct()
	{
		$this->tipo = "clase";	
		parent::__construct();
	}
}
//######################################################################
//######################################################################

class elemento_toba_patron extends elemento_toba
{
	function __construct()
	{
		$this->tipo = "patron";	
		parent::__construct();
	}
}
//######################################################################
//######################################################################
?>