<?
require_once("ap.php");
/*
	Administrador de persistencia a DB
		Armo los WHERE para cargar y guio la sincronizacion de valores provenientes de las clases

	PENDIENTE

		- Hay que cambiar el editor de relaciones para que las claves se emparejen posicionalmente	
			(Hay que poner un ML por cada relacion)
*/
class ap_relacion_db extends ap
{
	protected $objeto_relacion;
	protected $relaciones;
	protected $utilizar_transaccion;			// La sincronizacion con la DB se ejecuta dentro de una transaccion
	
	function __construct($objeto_relacion)
	{
		$this->objeto_relacion = $objeto_relacion;
	}

	function get_estado_relacion()
	{
		$this->relaciones = $this->objeto_relacion->get_relaciones();
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
	{
		asercion::es_array($clave,"OBJETO DATOS RELACION: La clave debe ser un array");
		$this->get_estado_relacion();
		foreach($this->relaciones['raiz'] as $tabla_raiz ){
			$this->cargar_tabla($tabla_raiz, $clave);
		}
		$this->cargado = true;
	}

	function cargar_tabla($tabla, $clave)
	/*
		ATENCION, por ahora esto funciona solo para el caso mas simple...
	*/
	{	
		//Cargo la tabla
		$ap = $this->objeto_relacion->tabla($tabla)->get_persistidor();
		$where = $ap->generar_clausula_where_lineal($clave);
		$ap->cargar_con_clausulas_sql($where);
		//Cargo los hijos
		if(isset($this->relaciones['padre'][$tabla])){
			$clave_padre = $this->relaciones['padre'][$tabla]['clave'];
			foreach( $this->relaciones['padre'][$tabla]['hijos'] as $hijo => $clave_hijo)
			{
				/*
					Carga recursiva real:
						- Recupero todos los registros clave de la tabla recien cargada
						- Armo claves de hijos con un emparejamiento posicional que falta
						- Compacto los compacto y los uso para hacer varias una clausulas WHERE
						- Relaciono todas la clausulas WHERE con un OR
						- cargo los hijos
				*/
				/*
					De aca para abajo es cualquiera
				*/
				for($a=0;$a<count($clave_hijo);$a++){
					$clave_convertida[$clave_hijo[$a]] = $clave[$clave_padre[$a]];
				}
				$this->cargar_tabla($hijo, $clave_convertida);
			}
		}
	}

	public function resetear()
	{
		foreach($this->dependencias as $dependencia){
			$dependencia->resetear();
		}
		$this->cargado = false;
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
		try{
			abrir_transaccion();
			$this->evt__pre_sincronizacion();
			$this->sincronizar_plan();
			$this->evt__post_sincronizacion();
			cerrar_transaccion();			
		}catch(excepcion_toba $e){
			abortar_transaccion();
			toba::get_logger()->debug($e);
			throw new excepcion_toba($e->getMessage());
		}					
	}

	public function sincronizar_plan()
	//Por defecto supone una relacion MAESTRO - DETALLE
	{
		$this->elemento[$this->cabecera]->sincronizar();
		//Se obtiene el id de la cabecera
		$valores = $this->elemento[$this->cabecera]->get_clave_valor(0);
		//Se asigna cada valor a los registros del detalle que tienen que sincronizarse con la DB
		foreach( $this->detalles as $id => $columna_clave ){
			if($registros_a_sincronizar = $this->elemento[$id]->get_id_registros_a_sincronizar()){
				foreach($registros_a_sincronizar as $registro){
					$i = 0;
					foreach ($valores as $valor){
						$this->elemento[$id]->set_registro_valor( $registro, $columna_clave[$i] , $valor);
						$i++;
					}
				}
				$this->elemento[$id]->sincronizar();
			}
		}
	}

	//-------------------------------------------------------------------------------
	//--  EVENTOS de SINCRONIZACION con la DB   -------------------------------------
	//-------------------------------------------------------------------------------
	/*
		Este es el lugar para meter validaciones (disparar una excepcion) o disparar procesos.
	*/
	protected function evt__pre_sincronizacion(){}
	protected function evt__post_sincronizacion(){}
	protected function evt__pre_eliminacion(){}
	protected function evt__post_eliminacion(){}

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
		if(count($this->detalles)>0){
			$detalles = array_reverse(array_keys($this->detalles));
			foreach( $detalles as $detalle ) {
				$this->elemento[$detalle]->eliminar_registros();
				$this->elemento[$detalle]->sincronizar(false);
			}
		}
		$this->elemento[$this->cabecera]->eliminar_registros();
		$this->elemento[$this->cabecera]->sincronizar(false);		
	}
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