<?
require_once('nucleo/persistencia/db_registros.php');

class db_tablas
{
	protected $log;
	protected $elemento;
	protected $cargado;
	protected $fuente;
	//Manejo de relaciones cabecera-detalle
	protected $cabecera;
	protected $detalles;
	
		
	function __construct($fuente=null)
	{
		//Llevar el plan a una estructura de control concreta?
		$this->cargado = false;
		$this->log = toba::get_logger();
		$this->fuente = $fuente;
	}

	function info()
	{
		foreach(array_keys($this->elemento) as $elemento)
		{
			$temp[$elemento] = $this->elemento[$elemento]->info(true);
		}
		return $temp;
	}

	public function elemento($elemento)
	//Devuelve una referencia a un db_registros
	{
		if($this->existe_elemento($elemento)) return $this->elemento[$elemento];
	}

	public function existe_elemento($elemento)
	{
		return ($this->elemento[$elemento] instanceof db_registros);
	}

	public function agregar_elemento($id, $db_registros)
	{
		if(!isset($this->elemento[$id])){
			$this->elemento[$id] = $db_registros;
		}else{
			throw new excepcion_toba("db_tablas: ya existe un elemento con el ID '$id'.");
		}
	}

	public function registrar_evento($elemento, $evento, $parametros)
	{
		//Ver si se implemento un evento		
	}

	protected function log($txt)
	{
		$this->log->debug("db_tablas  '" . get_class($this). "' - [{$this->identificador}] - " . $txt);
	}

	public function check_carga()
	{
		return $this->cargado;	
	}

	//-------------------------------------------------------
	//------ Interface de con la DB
	//-------------------------------------------------------

	public function cargar($id)
	{
		$this->elemento[$this->cabecera]->cargar_datos_clave($id);
		foreach( array_keys($this->detalles) as $detalle ) {
			$this->elemento[$detalle]->cargar_datos_clave($id);
		}
		$this->cargado = true;
	}
	//-------------------------------------------------------

	public function resetear()
	{
		foreach(array_keys($this->elemento) as $elemento){
			$this->elemento[$elemento]->resetear();
		}
		$this->cargado = false;
	}
	//-------------------------------------------------------

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
	{
		$this->elemento[$this->cabecera]->sincronizar();
		//Se obtiene el id de la cabecera
		$valores = $this->elemento[$this->cabecera]->get_clave_valor(0);
		//Se asigna cada valor al detalle
		foreach( $this->detalles as $id => $columna_clave ){
			$i = 0;
			foreach ($valores as $valor){
				$this->elemento[$id]->establecer_valor_columna( $columna_clave[$i] , $valor);
				$i++;
			}
			$this->elemento[$id]->sincronizar();
		}
	}
	//-------------------------------------------------------

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
	{
		$detalles = array_reverse(array_keys($this->detalles));
		foreach( $detalles as $detalle ) {
			$this->elemento[$detalle]->eliminar_registros();
			$this->elemento[$detalle]->sincronizar();
		}
		$this->elemento[$this->cabecera]->eliminar_registros();
		$this->elemento[$this->cabecera]->sincronizar();		
	}
	//-------------------------------------------------------

	protected function evt__pre_sincronizacion()
	{
	}
	
	protected function evt__post_sincronizacion()
	{
	}

	protected function evt__pre_eliminacion()
	{
	}
	
	protected function evt__post_eliminacion()
	{
	}
	//-------------------------------------------------------
}
?>