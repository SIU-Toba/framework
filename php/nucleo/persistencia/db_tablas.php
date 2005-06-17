<?
require_once('nucleo/persistencia/db_registros.php');
/*

	PROBLEMAS NO RESUELTOS
	----------------------

		- Servicio que pueda leer un plan y construir los planes de sincro, carga y eliminacion	

*/
class db_tablas
{
	protected $log;
	protected $elemento;
	protected $cargado;
	protected $fuente;
	
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
	/*
			Recibe el conjunto de valores que se consideren la clave del db_tablas, luego
	 		por cada db_registros se construye el WHERE sql que carga al mismo con esa clave (si usa
	 		cargar_datos) o la clave que le corresponde (si usa cargar_datos_clave). 
	*/
	{
		//Si se desea preguntar si la carga fue exitosa, hay que setear esta variable
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
			$this->sincronizar_plan();
			cerrar_transaccion();			
		}catch(excepcion_toba $e){
			abortar_transaccion();
			toba::get_logger()->debug($e);
			throw new excepcion_toba($e->getMessage());
		}					
	}

	protected function sincronizar_plan()
	{
		$this->log("No existe un plan de SINCRONIZACION!");
	}
	//-------------------------------------------------------

	public function eliminar()
	//Elimina el contenido de los DB_REGISTROS y los sincroniza
	{
		try{
			abrir_transaccion();
			$this->eliminar_plan();
			cerrar_transaccion();			
		}catch(excepcion_toba $e){
			abortar_transaccion();
			toba::get_logger()->debug($e);
			throw new excepcion_toba($e->getMessage());
		}		
	}

	protected function eliminar_plan()
	{
		$this->log("No existe un plan de ELIMINACION!");
	}
	//-------------------------------------------------------
}
?>