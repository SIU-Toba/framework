<?
require_once("ap.php");

/**
 * 	Administrador de persistencia de un relaci�n a una DB relacion. Puede cargar y sincronizar un grupo de tablas
 * 	@todo Hay que cambiar el editor de relaciones para que las claves se emparejen posicionalmente	(Hay que poner un ML por cada relacion)
 *  @todo Cada TABLA tiene una clave de carga (relacion con el ancestro) y una para relacionarse con los hijos. En las tablas raiz, las don son la misma
 * 	@package Objetos
 *  @subpackage Persistencia
 */
class ap_relacion_db extends ap
{
	protected $objeto_relacion; 				//objeto_datos_relacion que persiste
	protected $utilizar_transaccion;			//Determina si la sincronizacion con la DB se ejecuta dentro de una transaccion
	protected $retrazar_constraints=false;		//Intenta retrazar el chequeo de claves foraneas hasta el fin de la transacci�n
	
	/**
	 * @param objeto_datos_relacion $objeto_relacion Relaci�n que persiste
	 */
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
	
	/**
	 * Intenta retrazar el chequeo de constraints hasta el final de la transacci�n
	 */
	public function retrasar_constraints()
	{
		$this->retrazar_constraints = true;	
	}

	//-------------------------------------------------------------------------------
	//------  CARGA  ----------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Se cargan las tablas RAIZ y de ah� en m�s se cargan las dem�s a travez de las RELACIONES
	 * El formato de la clave del DR ($clave) tiene que ser consitente con las claves de las tablas raiz
	 * Hay que hacer una correspondencia posicional de la "clave" del DR con las claves hacia hijos de las tablas raiz 
	 * (porque en ellas se cumple que la clave del link y la propia son iguales)
	 * @param array $clave
	 * @return boolean Falso si no se cargo una tabla raiz
	 */
	public function cargar($clave)
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

	/**
	 * Sincroniza los cambios con la base de datos
	 * En caso de error se aborta la transacci�n (si tiene) y se lanza una excepci�n
	 */
	public function sincronizar()
	{
		$fuente = $this->objeto_relacion->get_fuente();
		try{
			if($this->utilizar_transaccion) {
				abrir_transaccion($fuente);
				if ($this->retrazar_constraints) {
					toba::get_db($fuente)->retrazar_constraints();
				}
			}
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

	/**
	 * Sincronizo las tabla de la raiz, de ahi en mas sigue el proceso a travez de las relaciones
	 */
	protected function proceso_sincronizacion()
	{
		$tablas_raiz = $this->objeto_relacion->get_tablas_raiz();
		if(is_array($tablas_raiz)){
			foreach( $tablas_raiz as $tabla ){
				$this->objeto_relacion->tabla($tabla)->sincronizar();
			}
		}
	}
	
	/**
	 * Este es el lugar para inclu�r validaciones (disparar una excepcion) o disparar procesos previo a sincronizar
	 * La transacci�n con la bd ya fue iniciada (si es que hay)
	 */
	protected function evt__pre_sincronizacion(){}
	
	/**
	 * Este es el lugar para inclu�r validaciones (disparar una excepcion) o disparar procesos posteriores a la sincronizaci�n
	 * La transacci�n con la bd a�n no fue terminada (si es que hay)
	 */	
	protected function evt__post_sincronizacion(){}

	//-------------------------------------------------------------------------------
	//------  ELIMINACION  -------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Elimina cada elemento de las tabla de la relaci�n y luego sincroniza con la base
	 * Todo el proceso se ejecuta dentro de una transacci�n, si se definio as�
	 */
	public function eliminar()
	//
	{
		$fuente = $this->objeto_relacion->get_fuente();		
		try{
			if ($this->utilizar_transaccion) {
				abrir_transaccion($fuente);
			}
			$this->evt__pre_eliminacion();
			$this->eliminar_plan();
			$this->evt__post_eliminacion();
			if ($this->utilizar_transaccion) {
				cerrar_transaccion($fuente);
			}
		}catch(excepcion_toba $e){
			if($this->utilizar_transaccion) {
				abortar_transaccion($fuente);
			}
			toba::get_logger()->debug($e);
			throw new excepcion_toba($e->getMessage());
		}		
	}

	/**
	 * Por defecto supone una relacion MAESTRO-DETALLE
	 */
	protected function eliminar_plan()
	{
		$tablas_raiz = $this->objeto_relacion->get_tablas_raiz();
		if(is_array($tablas_raiz)){
			foreach( $tablas_raiz as $tabla ){
				$this->objeto_relacion->tabla($tabla)->eliminar();
			}
		}
	}

	/**
	 * Este es el lugar para inclu�r validaciones (disparar una excepcion) o disparar procesos previo a la eliminaci�n
	 * La transacci�n con la bd ya fue iniciada (si es que hay)
	 */
	protected function evt__pre_eliminacion(){}
	
	/**
	 * Este es el lugar para inclu�r validaciones (disparar una excepcion) o disparar procesos posteriores a la eliminaci�n
	 * La transacci�n con la bd ya fue iniciada (si es que hay)
	 */	
	protected function evt__post_eliminacion(){}

}
?>