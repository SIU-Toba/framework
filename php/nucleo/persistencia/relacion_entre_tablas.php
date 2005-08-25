<?
/*
	Esta clase representa la relacion entre dos tablas

	- Las relaciones se arman macheando posicionalmente columnas

	- El comportamiento de esta clase varia segun la cantidad de registros 
		que maneja el padre... con N registros se suma el problema de
		recuperacion y seteo discrecional de HIJOS
*/

class relacion_entre_tablas
{
	protected $nombre;
	protected $tabla_padre;
	protected $tabla_padre_claves;
	protected $tabla_hijo;
	protected $tabla_hijo_claves;
	protected $mapeo_claves;
	protected $mapeo_filas;

	function __construct($nombre, $tabla_padre, $tabla_padre_clave, $tabla_hijo, $tabla_hijo_clave)
	{
		asercion::arrays_igual_largo($tabla_padre_clave, $tabla_hijo_clave);
		$this->nombre = $nombre;
		$this->tabla_padre = $tabla_padre;
		$this->tabla_padre_claves = $tabla_padre_clave;
		$this->tabla_hijo = $tabla_hijo;	
		$this->tabla_hijo_claves = $tabla_hijo_clave;
		//Notifico la existencia de la relacion a las tablas
		$this->tabla_padre->agregar_relacion_con_hijo( $this );
		$this->tabla_hijo->agregar_relacion_con_padre( $this );
	}

	/**
		Macheo de claves del PADRE y del HIJO
	*/
	function mapear_claves()
	{
		for($a=0;$a<count($this->tabla_padre_claves);$a++){
			$this->mapeo_claves[ $this->tabla_padre_claves[$a] ] = $this->tabla_hijo_claves[$a];
		}
		//ei_arbol($this->mapeo_claves);
	}

	/**
		Este evento de dispara cuando el padre es CARGADO
	*/
	function evt__carga_padre()
	{
		$this->mapear_claves();
		if( $this->tabla_padre->get_cantidad_filas() == 1)
		{
			$fila = $this->tabla_padre->get_fila(0);
			//Armo la clave del HIJO
			foreach($this->mapeo_claves as $padre => $hijo){
				$clave[$hijo] = $fila[$padre];
			}
			//Lo cargo
			$this->tabla_hijo->cargar($clave);
			//Tengo que armar el mapeo de filas
		}else{
			echo "TODAVIA no SOPORTADO";
		}
	}	
}
?>