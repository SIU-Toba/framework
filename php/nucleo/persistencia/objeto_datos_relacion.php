<?
require_once("nucleo/browser/clases/objeto.php");

/*
	Las relaciones con los hijos son a travez de un unico ID
		por cada dependencias, tiene que haber un ID para conectarse a un padre
		y otro para conectarse a un hijo... no hay que definir los IDs por operacion.
		Incluso la relacion con dos hijos a travez de dos IDs distintos podrian generar algo extraño

*/

class objeto_datos_relacion extends objeto
{
	protected $relaciones;		//Deberian ser una clase aparte?

	function __construct($id)
	{
		parent::__construct($id);	
		$this->cargar_tablas();
		$this->construir_relaciones();
	}
	
	private function cargar_tablas()
	{
		$this->cargar_info_dependencias();
		foreach( $this->lista_dependencias as $dep){
			$this->cargar_dependencia($dep);
		}
	}
	
	private function construir_relaciones()
	{
		/*
			Falta validar que las relaciones coincidan
		*/
		for($a=0;$a<count($this->info_relaciones);$a++){
			$padre = $this->info_relaciones[$a]['padre_id'];
			$hijo = $this->info_relaciones[$a]['hijo_id'];
			$this->relaciones['padre'][ $padre ]['hijos'][$hijo] = explode(",",$this->info_relaciones[$a]['hijo_clave']);
			$this->relaciones['padre'][ $padre ]['clave'] = explode(",",$this->info_relaciones[$a]['padre_clave']);
			$this->relaciones['hijo'][ $hijo ][] = $padre;
		}
		$this->relaciones['raiz'] = array_diff( 	
										array_keys($this->relaciones['padre']),
										array_keys($this->relaciones['hijo']));
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
					 AND		objeto = '".$this->id[1]."';";
		$sql["info_relaciones"]["tipo"]="x";
		$sql["info_relaciones"]["estricto"]="1";		
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

		$ap = $this->get_persistidor();
		$ap->cargar($clave);
	}

	function sincronizar()
	{
		$ap = $this->get_persistidor();
		return $ap->sincronizar();
	}
	
	function get_relaciones(){
		return $this->relaciones;
	}
	
	//-------------------------------------------------------------------------------
}
?>