<?
require_once("nucleo/browser/clases/objeto.php");
require_once("relacion_entre_tablas.php");

/*
	(Sobre las claves)
		Las relaciones con los hijos son a travez de un unico ID
		por cada dependencias, tiene que haber un ID para conectarse a un padre
		y otro para conectarse a un hijo... no hay que definir los IDs por operacion.
		Incluso la relacion con dos hijos a travez de dos IDs distintos podrian generar algo extra?o

	ATENCION: no hay una nomenclatura consistente (padre/hijo; padre/hija; madre/hija)

*/

class objeto_datos_relacion extends objeto
{
	protected $relaciones;		
	protected $tablas_raiz;

	function __construct($id)
	{
		parent::__construct($id);	
		$this->crear_tablas();
		$this->crear_relaciones();
	}
	
	private function crear_tablas()
	{
		$this->cargar_info_dependencias();
		foreach( $this->lista_dependencias as $dep){
			$this->cargar_dependencia($dep);
		}
	}
	
	private function crear_relaciones()
	{
		if(count($this->info_relaciones)>0){
			for($a=0;$a<count($this->info_relaciones);$a++)
			{
				$this->relaciones[] = new relacion_entre_tablas(	$this->info_relaciones[$a]['identificador'],
																	$this->dependencias[ $this->info_relaciones[$a]['padre_id'] ],
																	explode(",",$this->info_relaciones[$a]['padre_clave']),
																	$this->dependencias[ $this->info_relaciones[$a]['hijo_id'] ],
																	explode(",",$this->info_relaciones[$a]['hijo_clave'])
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

	function get_lista_tablas()
	{
		return array_keys($this->dependencias);	
	}

	public function tabla($tabla)
	//Devuelve una referencia a una tabla para trabajar con ella
	{
		if($this->existe_tabla($tabla)){
			return $this->dependencias[$tabla];
		}else{
			throw new excepcion_toba("db_tablas: El db_registros '$tabla' solicitado no existe.");
		}
	}

	public function existe_tabla($tabla)
	{
		return $this->existe_dependencia($tabla);
	}

	public function registrar_evento($elemento, $evento, $parametros)
	{
		//Ver si se implemento un evento		
	}

	public function resetear()
	{
		foreach($this->dependencias as $dependencia){
			$dependencia->resetear();
		}
	}
	
	protected function evt__validar(){}

	public function disparar_validacion_tablas()
	{
		foreach($this->dependencias as $dependencia){
			$dependencia->validar();
		}
	}

	//-------------------------------------------------------------------------------
	//-- PERSISTENCIA  -------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function get_persistidor()
	//Devuelve el persistidor por defecto
	{
		require_once("ap_relacion_db.php");
		return new ap_relacion_db( $this );
	}

	function cargar($clave)
	{
		//ATENCION: hay que controlar el formato de la clave
		$this->log('********* Inicio CARGAR **********');
		$ap = $this->get_persistidor();
		$ap->cargar($clave);
		$this->log('********* Fin CARGAR **********');
	}

	function sincronizar()
	{
		$this->disparar_validacion_tablas();
		$this->evt__validar();
		$ap = $this->get_persistidor();
		$ap->sincronizar();
	}
	
	function eliminar()
	{
		$ap = $this->get_persistidor();
		$ap->eliminar();
	}
	
	function get_tablas_raiz()
	{
		return $this->tablas_raiz;
	}
	
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