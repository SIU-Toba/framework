<?
require_once("nucleo/browser/clases/objeto.php");
require_once("relacion_entre_tablas.php");

/**
 * (Sobre las claves)
 * Las relaciones con los hijos son a travez de un unico ID
 * por cada dependencias, tiene que haber un ID para conectarse a un padre
 * y otro para conectarse a un hijo... no hay que definir los IDs por operacion.
 * Incluso la relacion con dos hijos a travez de dos IDs distintos podrian generar algo extra?o
 * 	@todo	ATENCION: no hay una nomenclatura consistente (padre/hijo; padre/hija; madre/hija)
 * 	@package Objetos
 *  @subpackage Persistencia
 */
class objeto_datos_relacion extends objeto
{
	protected $relaciones;		
	protected $tablas_raiz;
	protected $persistidor;

	function __construct($id)
	{
		parent::__construct($id);	
		$this->crear_tablas();
		$this->crear_relaciones();
	}
	
	/**
	 * Carga los datos_tabla y les pone los topes mínimos y máximos
	 */
	private function crear_tablas()
	{
		$this->cargar_info_dependencias();
		foreach( $this->lista_dependencias as $dep){
			$this->cargar_dependencia($dep);
			//La cantidad minima y maxima se pasan a traves de dos parametros genericos del objeto
			$posicion = $this->indice_dependencias[$dep];
			$cant_min = $this->info_dependencias[$posicion]['parametros_a'];
			$cant_max = $this->info_dependencias[$posicion]['parametros_b'];
			$this->dependencias[$dep]->set_tope_min_filas($cant_min);
			$this->dependencias[$dep]->set_tope_max_filas($cant_max);
		}
	}

	/**
	 * Para cada relación definida crea una relacion_entre_tablas
	 * Determina cual es la tabla raiz
	 */
	private function crear_relaciones()
	{
		if(count($this->info_relaciones)>0){
			for($a=0;$a<count($this->info_relaciones);$a++)
			{
				$this->relaciones[] = new relacion_entre_tablas(	$this->info_relaciones[$a]['identificador'],
																	$this->dependencias[ $this->info_relaciones[$a]['padre_id'] ],
																	explode(",",$this->info_relaciones[$a]['padre_clave']),
																	$this->info_relaciones[$a]['padre_id'],
																	$this->dependencias[ $this->info_relaciones[$a]['hijo_id'] ],
																	explode(",",$this->info_relaciones[$a]['hijo_clave']),
																	$this->info_relaciones[$a]['hijo_id']
																);
				$padres[] = $this->info_relaciones[$a]['padre_id'];
				$hijos[] = $this->info_relaciones[$a]['hijo_id'];
			}
			//Padres sin hijos
			$this->tablas_raiz = array_diff( array_unique($padres), array_unique($hijos) );
		}else{
			//No hay relaciones
			$this->relaciones = null;
			$this->tablas_raiz = array_keys($this->dependencias);
		}
	}

	public function obtener_definicion_db()
	{
		$sql = parent::obtener_definicion_db();
		//------------- Info base de la estructura ----------------
		$sql["info_estructura"]["sql"] = "SELECT	proyecto 	,	
													objeto      ,	
													clave		,	
													ap			,	
													ap_clase	,	
													ap_archivo		
					 FROM		apex_objeto_datos_rel
					 WHERE		proyecto='".$this->id[0]."'	
					 AND		objeto='".$this->id[1]."';";
		$sql["info_estructura"]["estricto"]="1";
		$sql["info_estructura"]["tipo"]="1";
		//------------ Columnas ----------------
		$sql["info_relaciones"]["sql"] = "SELECT	proyecto 		,
												objeto 		    ,
												asoc_id			,
												identificador   ,
												padre_proyecto	,
												padre_objeto	,
												padre_id		,
												padre_clave		,
												hijo_proyecto	,
												hijo_objeto		,
												hijo_id			,
												hijo_clave		,
												cascada			,
												orden			
					 FROM		apex_objeto_datos_rel_asoc 
					 WHERE		proyecto = '".$this->id[0]."'
					 AND		objeto = '".$this->id[1]."'
					 ORDER BY 	orden;";
		$sql["info_relaciones"]["tipo"]="x";
		$sql["info_relaciones"]["estricto"]="0";
		return $sql;
	}

	function elemento_toba()
	{
		require_once('api/elemento_objeto_datos_relacion.php');
		return new elemento_objeto_datos_relacion();
	}

	//-------------------------------------------------------------------------------
	//-- Servicios basicos
	//-------------------------------------------------------------------------------

	/**
	 *	Retorna los identificadores de los datos_tabla incluídos
	 * @return array
	 */
	function get_lista_tablas()
	{
		return array_keys($this->dependencias);	
	}

	/**
	 *	Retorna un datos_tabla
	 * @param string $tabla Id. de la tabla en la relación
	 * @return objeto_datos_tabla
	 */
	public function tabla($tabla)
	//Devuelve una referencia a una tabla para trabajar con ella
	{
		if($this->existe_tabla($tabla)){
			return $this->dependencias[$tabla];
		}else{
			throw new excepcion_toba("db_tablas: El db_registros '$tabla' solicitado no existe.");
		}
	}

	/**
	 * Determina si una tabla es parte de la relación
	 * @param string $tabla Id. de la tabla en la relación
	 * @return boolean
	 */
	public function existe_tabla($tabla)
	{
		return $this->existe_dependencia($tabla);
	}

	public function registrar_evento($elemento, $evento, $parametros)
	{
		//Ver si se implemento un evento		
	}

	/**
	 *	Retorna al estado inicial a todas las tablas incluídas
	 */
	public function resetear()
	{
		foreach($this->dependencias as $dependencia){
			$dependencia->resetear();
		}
	}
	
	/**
	 * Lugar para validaciones específicas, se ejecuta justo antes de la sincronización
	 */
	protected function evt__validar(){}

	/**
	 *	Valida cada una de las tablas incluídas en la relación
	 */
	public function disparar_validacion_tablas()
	{
		foreach($this->dependencias as $dependencia){
			$dependencia->validar();
		}
	}

	function get_conjunto_datos_interno()
	{
		foreach($this->dependencias as $id => $dependencia){
			$datos[$id] = $dependencia->get_conjunto_datos_interno();
		}
		return $datos;		
	}

	/**
	 * Muestra un dump de los datos y los cambios realizados a los mismos desde la carga
	 */
	function dump_contenido()
	{
		foreach($this->dependencias as $id => $dependencia){
			$info[$id]['cambios'] = $dependencia->get_cambios();
			$info[$id]['datos'] = $dependencia->get_conjunto_datos_interno();
		}
		ei_arbol( $info, 'DATOS_RELACION: ' . $this->info['nombre'], null, true);
	}

	//-------------------------------------------------------------------------------
	//-- PERSISTENCIA  -------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 *  Retorna una referenca al Adm.Persistencia de la relación
	 * @return ap_relacion
	 */
	function get_persistidor()
	{
		if (!isset($this->persistidor)) {		
			//Se incluye el archivo
			$archivo = "ap_relacion_db.php";
			if (isset($this->info_estructura['ap_archivo'])) {
				$archivo = $this->info_estructura['ap_archivo'];
			}
			require_once($archivo);

			//Se crea la clase		
			$clase = "ap_relacion_db";
			if (isset($this->info_estructura['ap_clase'])) {
				$clase = $this->info_estructura['ap_clase'];
			}
			$this->persistidor = new $clase( $this );
		}
		return $this->persistidor;
	}
	
	/**
	 * Utiliza la carga por clave del administrador de persistencia
	 * Carga la tabla raiz de la relación y a partir de allí ramifica la carga a sus relaciones
	 * @param array $clave Arreglo asociativo campo-valor
	 * @return boolean Falso, si no se encontraron registros
	 */
	function cargar($clave)
	{
		//ATENCION: hay que controlar el formato de la clave
		$this->log('***   Inicio CARGAR ****************************');
		$ap = $this->get_persistidor();
		if($ap->cargar_por_clave($clave) === true){
			$this->log("***   Fin CARGAR (OK) *************************");
			return true;
		}else{
			$this->log("***   Fin CARGAR (No se cargaron datos) ***************");
			return false;
		}
	}

	/**
	*	Fuerza a que los datos_tabla contenidos marquen todos sus filas como nuevas
	*/
	function forzar_insercion()
	{
		foreach($this->dependencias as $id => $dependencia){
			$dependencia->forzar_insercion();
		}
	}
	
	/**
	 * Sincroniza los cambios con el medio de persistencia
	 */
	function sincronizar()
	{
		//$this->dump_contenido();
		$this->disparar_validacion_tablas();
		$this->evt__validar();
		$this->get_persistidor()->sincronizar();
		//$this->dump_contenido();
	}
	
	/**
	 * Elimina del medio de persistencia todos los datos cargados en la relación
	 */
	function eliminar()
	{
		$this->get_persistidor()->eliminar();
		$this->resetear();
	}
	
	/**
	 *	Retorna el id de las tablas que no tienen padres en la relación
	 * @return array
	 */
	function get_tablas_raiz()
	{
		return $this->tablas_raiz;
	}
	
	/**
	 * Fuente de datos que utiliza el objeto y sus dependencias
	 * @return string
	 */
	public function get_fuente()
	{
		return $this->info["fuente"];
	}
	
	protected function log($txt)
	/*
		El objeto deberia tener directamente algo asi
	*/
	{
		toba::get_logger()->debug($this->get_txt() . get_class($this). "' " . $txt);
	}
	//-------------------------------------------------------------------------------
}
?>