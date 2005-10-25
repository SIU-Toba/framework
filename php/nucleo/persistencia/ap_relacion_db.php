<?
require_once("ap.php");
/*
	Administrador de persistencia a DB
		Puede cargar y sincronizar un grupo de tablas

	PENDIENTE

		- Hay que cambiar el editor de relaciones para que las claves se emparejen posicionalmente	
			(Hay que poner un ML por cada relacion)
			
		- Cada TABLA tiene una clave de carga (relacion con el ancestro) y una para relacionarse con los hijos
			En las tablas raiz, las don son la misma
*/
class ap_relacion_db extends ap
{
	protected $objeto_relacion;
	protected $relaciones;
	protected $utilizar_transaccion;			// La sincronizacion con la DB se ejecuta dentro de una transaccion
	
	function __construct($objeto_relacion)
	{
		$this->objeto_relacion = $objeto_relacion;
		$this->activar_transaccion();
	}

	public function info()
	{
		return get_object_vars($this);
	}

	//-------------------------------------------------------------------------------
	//------  Configuracion  ----------------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function activar_transaccion()		
	{
		$this->utilizar_transaccion = true;
	}

	public function desactivar_transaccion()		
	{
		$this->utilizar_transaccion = false;
	}

	//-------------------------------------------------------------------------------
	//------  CARGA  ----------------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function cargar($clave)
	/*
		El formato de la clave del DR ($clave) tiene que ser consitente con las claves
		de las tablas raiz. Hay que hacer una correspondencia posicional de la "clave"
		del DR con las claves hacia hijos de las tablas raiz (porque en ellas se cumple
		que la clave del link y la propia son iguales)

		La idea es que cargo la tablas RAIZ y de ahi en mas se cargan las
		demas a travez de las RELACIONES
	*/
	{
		asercion::es_array($clave,"AP objeto_datos_relacion -  ERROR: La clave debe ser un array");
		$tablas_raiz = $this->objeto_relacion->get_tablas_raiz();
		if(is_array($tablas_raiz)){
			foreach( $tablas_raiz as $tabla ){
				if( $this->objeto_relacion->tabla($tabla)->cargar( $clave ) !== true ){
					//No se cargo una tabla raiz, cancelo el proceso
					return false;
				}
			}
			$this->cargado = true;
		}
		return true;
	}

	public function esta_cargado()
	{
		return $this->cargado;	
	}

	//-------------------------------------------------------------------------------
	//------  SINCRONIZACION  -------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function sincronizar()
	{
		$fuente = $this->objeto_relacion->get_fuente();
		try{
			if($this->utilizar_transaccion) abrir_transaccion($fuente);
			$this->evt__pre_sincronizacion();
			$this->proceso_sincronizacion();
			$this->evt__post_sincronizacion();
			if($this->utilizar_transaccion) cerrar_transaccion($fuente);			
		}catch(excepcion_toba $e){
			if($this->utilizar_transaccion) abortar_transaccion($fuente);
			toba::get_logger()->debug($e);
			throw new excepcion_toba($e->getMessage());
		}					
	}

	public function proceso_sincronizacion()
	/*
		Sincronizo las tabla de la raiz, de ahi en mas sigue el proceso
		a travez de las relaciones
	*/
	{
		$tablas_raiz = $this->objeto_relacion->get_tablas_raiz();
		if(is_array($tablas_raiz)){
			foreach( $tablas_raiz as $tabla ){
				$this->objeto_relacion->tabla($tabla)->sincronizar();
			}
		}
	}
	/*
		--- EVENTOS de SINCRONIZACION ---
		Este es el lugar para meter validaciones (disparar una excepcion) o disparar procesos.
	*/
	protected function evt__pre_sincronizacion(){}
	protected function evt__post_sincronizacion(){}

	//-------------------------------------------------------------------------------
	//------  ELIMINACION  -------------------------------------------------------
	//-------------------------------------------------------------------------------

	public function eliminar()
	//Elimina el contenido de los DB_REGISTROS y los sincroniza
	{
		try{
			abrir_transaccion();
			$this->evt__pre_eliminacion();
			$this->eliminar_plan();
			$this->evt__post_eliminacion();
			cerrar_transaccion();			
		}catch(excepcion_toba $e){
			abortar_transaccion();
			toba::get_logger()->debug($e);
			throw new excepcion_toba($e->getMessage());
		}		
	}

	public function eliminar_plan()
	//Por defecto supone una relacion MAESTRO-DETALLE
	{
		$tablas_raiz = $this->objeto_relacion->get_tablas_raiz();
		if(is_array($tablas_raiz)){
			foreach( $tablas_raiz as $tabla ){
				$this->objeto_relacion->tabla($tabla)->eliminar();
			}
		}
	}

	/*
		--- EVENTOS de ELIMINACION ---
		Este es el lugar para meter validaciones (disparar una excepcion) o disparar procesos.
	*/
	protected function evt__pre_eliminacion(){}
	protected function evt__post_eliminacion(){}

	//-------------------------------------------------------------------------------
	//------ Servicios de generacion de SQL   ---------------------------------------
	//-------------------------------------------------------------------------------

	function get_sql_inserts()
	{
		$sql = array();
		foreach($this->elemento as $elemento ) {
			$sql = array_merge($sql, $elemento->get_sql_inserts());
		}
		return $sql;
	}
	//-------------------------------------------------------
}
?>