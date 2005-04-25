<?
require_once('nucleo/lib/buffer_db.php');
/*
Una entidad posee un conjunto de buffers y conoce la logica de persistencia entre los mismos
Definir un nuevo tipo de entidad deberia implicar:

	- decir que buffers posee
	- explicar la relacion entre los mismos
	- reescribir las primitivas (sincro, upd) si hace algo muy especifico	

*/
class entidad
{
	protected $log;
	protected $elemento;
	protected $cargado;
	
	function __construct()
	{
		//Llevar el plan a una estructura de control concreta?
		$this->cargado = false;
		$this->log = toba::get_logger();
	}
	
	function info()
	{
		foreach(array_keys($this->elemento) as $elemento)
		{
			$temp[$elemento] = $this->elemento[$elemento]['buffer']->info(true);
		}
		return $temp;
	}
	
	//-------------------------------------------------------
	//------ Interface de EXTERNA
	//-------------------------------------------------------

	public function acc_elemento($elemento, $accion, $parametros)
	//Entrada a la modificacion de los buffers
	{
		//ei_arbol($parametros, "ELEMENTO: " . $elemento . " - ACCION: " . $accion);
		//--[ 1 ]-- Controlo que el elemento exista
		if( ! ($this->existe_elemento($elemento)) ){
			throw new excepcion_toba("El elemento '$elemento' no forma parte de la entidad");	
		}
		//--[ 2 ]-- Controlo que la accion solicitada exista.
		if(! method_exists($this, $accion) ){
			throw new excepcion_toba("La accion solicitada sobre el elemento '$elemento' no esta definida");	
		}
		//Disparo la accion
		return $this->$accion($elemento, $parametros);
	}
	//-------------------------------------------------------
	
	public function existe_elemento($elemento)
	{
		return ($this->elemento[$elemento]['buffer'] instanceof buffer_db);
	}

	//-------------------------------------------------------
	//------ Interface interna con los BUFFERS
	//-------------------------------------------------------	

	protected function get($elemento)
	{
		if($this->elemento[$elemento]['registros']=="1"){
			return $this->elemento[$elemento]['buffer']->obtener_registro(0);
		}else{
			return $this->elemento[$elemento]['buffer']->obtener_registros();
		}
	}
	//-------------------------------------------------------

	protected function get_x($elemento, $id)
	//parametros = 
	{
		if($this->elemento[$elemento]['registros']=="n"){
			return $this->elemento[$elemento]['buffer']->obtener_registro($id);
		}else{
			throw new excepcion_toba("Error en la definicion de la ENTIDAD.
				El buffer no maneja multiples registros");	
		}
	}
	//-------------------------------------------------------

	protected function set($elemento, $registro)
	{
		if($this->elemento[$elemento]['registros']=="1"){
			if( $this->elemento[$elemento]['buffer']->cantidad_registros() > 0 ){
				$this->elemento[$elemento]['buffer']->modificar_registro($registro, 0);
			}else{
				$this->elemento[$elemento]['buffer']->agregar_registro($registro);
			}
		}else{
			throw new excepcion_toba("Error en la definicion de la ENTIDAD. 
			El metodo SET es para BUFFERS que manejan un solo registro");	
		}
	}
	//-------------------------------------------------------

	protected function ins($elemento, $registro)
	{
		if($this->elemento[$elemento]['registros']=="n"){
			//echo "Estoy aca!";
			$this->elemento[$elemento]['buffer']->agregar_registro($registro);
		}else{
			throw new excepcion_toba("Error en la definicion de la ENTIDAD. El buffer posee N registros.");	
		}
	}
	//-------------------------------------------------------

	protected function upd($elemento, $parametros)
	{
		$registro = $parametros['registro'];
		$id = $parametros['id'];
		if($this->elemento[$elemento]['registros']=="n"){
			$this->elemento[$elemento]['buffer']->modificar_registro($registro, $id);
		}else{
			throw new excepcion_toba("Error en la definicion de la ENTIDAD. El buffer posee N registros");	
		}
	}
	//-------------------------------------------------------

	protected function del($elemento, $id)
	{
		if($this->elemento[$elemento]['registros']=="n"){
			$this->elemento[$elemento]['buffer']->eliminar_registro($id);
		}else{
			throw new excepcion_toba("Error en la definicion de la ENTIDAD. El buffer posee N registros");	
		}
	}

	//-------------------------------------------------------
	//------ Interface de sincronizacion
	//-------------------------------------------------------

	public function descargar()
	{
		foreach(array_keys($this->elemento) as $elemento)
		{
			$this->elemento[$elemento]['buffer']->resetear();
		}
	}
	//-------------------------------------------------------

	public function cargar($id)
	//Carga un instanciacion de la entidad
	{
		//Armo los WHERE y cargo
		//a los BUFFERS
	}
	//-------------------------------------------------------

	public function sincronizar_db()
	//Sincroniza la entidad contra la base de datos
	//Esto deberia leer un plan y ejecutarlo. Si la entidad tiene una regla de grabacion
	//muy complicada, deberia redefinir esta funcion
	{
	}
	//-------------------------------------------------------

	public function eliminar()
	//Elimina el contenido de los BUFFERs y los sincroniza
	{
	}
	//-------------------------------------------------------
}
?>