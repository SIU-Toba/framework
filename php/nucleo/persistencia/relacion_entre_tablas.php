<?
/*
	Esta clase representa la relacion entre dos tablas

	- Las relaciones se arman macheando posicionalmente columnas

	- El comportamiento de esta clase varia segun la cantidad de registros 
		que maneja el padre... con N registros se suma el problema de
		recuperacion y seteo discrecional de HIJOS
	- FALTA la actulizacion dinamica del MAPEO de filas
	- Cuando sea necesario el mapeo de filas, esta clase va tener que mantener su estado en la sesion
*/

class relacion_entre_tablas
{
	protected $nombre;
	protected $tabla_padre;					// Referencia al objeto_datos_tabla PADRE
	protected $tabla_padre_claves;
	protected $tabla_padre_id;
	protected $tabla_hijo;					// Referencia al objeto_datos_tabla HIJO
	protected $tabla_hijo_claves;
	protected $tabla_hijo_id;
	protected $mapeo_claves;
	protected $mapeo_filas;

	function __construct($nombre, $tabla_padre, $tabla_padre_clave, $tabla_padre_id, 
							$tabla_hijo, $tabla_hijo_clave, $tabla_hijo_id)
	{
		asercion::arrays_igual_largo($tabla_padre_clave, $tabla_hijo_clave);
		$this->nombre = $nombre;
		$this->tabla_padre = $tabla_padre;
		$this->tabla_padre_claves = $tabla_padre_clave;
		$this->tabla_padre_id = $tabla_padre_id;
		$this->tabla_hijo = $tabla_hijo;	
		$this->tabla_hijo_claves = $tabla_hijo_clave;
		$this->tabla_hijo_id = $tabla_hijo_id;
		//Notifico la existencia de la relacion a las tablas
		$this->tabla_padre->agregar_relacion_con_hijo( $this, $this->tabla_padre_id );
		$this->tabla_hijo->agregar_relacion_con_padre( $this, $this->tabla_hijo_id );
	}
	
	function mapear_claves()
	//Macheo claves entre PADRE y HIJO
	{
		for($a=0;$a<count($this->tabla_padre_claves);$a++){
			$this->mapeo_claves[ $this->tabla_padre_claves[$a] ] = $this->tabla_hijo_claves[$a];
		}
		//ei_arbol($this->mapeo_claves);
	}

	function evt__carga_padre()
	//El elemento PADRE de la relacion notifica que se CARGO: Se dispara la carga del HIJO
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
			//Armo el MAPEO de filas
			
		}else{
			$error = "FILAS padre: ". $this->tabla_padre->get_cantidad_filas() .". Esta relacion no esta soportada\n";
			throw new excepcion_toba( $this->get_txt_error_base($texto) );
		}
	}	

	function evt__sincronizacion_padre()
	//El elemento PADRE de la relacion notifica que se SINCRONIZO: Se dispara la sincronizacion del hijo
	{
		$this->mapear_claves();
		if( $this->tabla_padre->get_cantidad_filas() == 1)
		{
			$fila = $this->tabla_padre->get_fila(0);
			//Establezco el valor del padre en el HIJO
			foreach($this->mapeo_claves as $columna_padre => $columna_hijo){
				$id_filas = $this->tabla_hijo->get_id_filas_a_sincronizar( array("u","i") );
				if(isset( $id_filas )){
					foreach( $id_filas as $id ){
						$this->tabla_hijo->set_fila_columna_valor($id, $columna_hijo, $fila[$columna_padre]);
					}
				}
			}
			$this->tabla_hijo->sincronizar();
		}else{
			$error = "FILAS padre: ". $this->tabla_padre->get_cantidad_filas() .". Esta relacion no esta soportada\n";
			throw new excepcion_toba( $this->get_txt_error_base($texto) );
		}
	}

	function evt__eliminacion_padre()
	//El elemento PADRE de la relacion notifica que se SINCRONIZO: Se dispara la sincronizacion del hijo
	{
		$this->tabla_hijo->eliminar();
	}

	function get_txt_error_base($error="Ha ocurrido un error")
	{
		$txt = "RELACION: $this->nombre\n";
		$txt .= "TABLA padre: " . $this->tabla_padre->get_txt() 
				. " -- ". $this->tabla_padre_id . " -- [". $this->tabla_padre->get_nombre()  . "]\n";
		$txt .= "TABLA hijo: " . $this->tabla_hijo->get_txt() 
				. " -- ". $this->tabla_hijo_id . " -- [". $this->tabla_hijo->get_nombre() . "]\n";
		$txt .= $error;
		return $txt;
	}
}
?>