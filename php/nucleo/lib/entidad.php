<?
require_once('nucleo/lib/buffer.php');
/*
Una entidad posee un conjunto de buffers y conoce la logica de persistencia entre los mismos
Definir un nuevo tipo de entidad deberia implicar:

	- decir que buffers posee
	- explicar la relacion entre los mismos
	- reescribir las primitivas (sincro, upd) si hace algo muy especifico	

*/
class entidad
{
	protected $elemento;
	
	function __construct()
	{
		//Llevar el plan a una estructura de control concreta?
	}
	
	//-------------------------------------------------------
	//------ Interface de EDICION
	//-------------------------------------------------------
/*
	Como generalizo la entrada de parametros?
*/

	public function editar($elemento, $accion, $parametros)
	//Entrada a la modificacion de los buffers
	{
		//--[ 1 ]-- Controlo que el elemento exista
		if( ! ($this->elemento[$elemento]['buffer'] instanceof buffer ) ){
			throw new excepcion_toba("El elemento '$elemento' no forma parte de la entidad");	
		}
		//--[ 2 ]-- Controlo que la accion solicitada exista.
		if(! method_exists($this, $accion) ){
			throw new excepcion_toba("La accion solicitada sobre el elemento '$elemento' no esta definida");	
		}
		//Disparo la accion
		return $this->$accion($elemento, $parametro);
	}

	//--------- Para BUFFERS de 1 registro
	
	protected function get($elemento, $parametros)
	{
		//Es un buffer de un registro?
		if($this->elemento[$elemento]['registros']=="1"){
			return $this->elemento[$elemento]['buffer']->obtener_registro(0);
		}else{
			throw new excepcion_toba("Error en la definicion de la ENTIDAD");	
		}
	}

	protected function set($elemento, $parametros)
	{
		//Es un buffer de 1 registro?
		if($this->elemento[$elemento]['registros']=="1"){
			if( $this->elemento[$elemento]['buffer']->cantidad_registros() > 0 ){
				return $this->elemento[$elemento]['buffer']->modificar_registro($registro, 0);
			}else{
				return $this->elemento[$elemento]['buffer']->insertar_registro($registro);
			}
		}else{
			throw new excepcion_toba("Error en la definicion de la ENTIDAD");	
		}
	}

	//-------- Para buffers de N registros

	protected function get_conjunto($elemento, $parametros)
	{
		if($this->elemento[$elemento]['registros']=="n"){

		}else{
			throw new excepcion_toba("Error en la definicion de la ENTIDAD");	
		}
	}

	protected function get_registro($elemento, $parametros)
	{
		if($this->elemento[$elemento]['registros']=="n"){

		}else{
			throw new excepcion_toba("Error en la definicion de la ENTIDAD");	
		}
	}

	protected function ins($elemento, $parametros)
	{
		if($this->elemento[$elemento]['registros']=="n"){

		}else{
			throw new excepcion_toba("Error en la definicion de la ENTIDAD");	
		}
	}

	protected function upd($elemento, $parametros)
	{
		if($this->elemento[$elemento]['registros']=="n"){

		}else{
			throw new excepcion_toba("Error en la definicion de la ENTIDAD");	
		}
	}

	protected function del($elemento, $parametros)
	{
		if($this->elemento[$elemento]['registros']=="n"){

		}else{
			throw new excepcion_toba("Error en la definicion de la ENTIDAD");	
		}
	}

	//-------------------------------------------------------
	//------ Interface de sincronizacion
	//-------------------------------------------------------
	

	public function cargar($id)
	//Carga un instanciacion de la entidad
	{
		//Armo los WHERE y cargo
		//a los BUFFERS
	}

	public function sincronizar()
	//Sincroniza la entidad contra la base de datos
	//Esto lee un plan y lo ejecuta. Si la entidad tiene una regla de grabacion
	//muy complicada, deberia redefinir esta funcion
	{
		
	}
}
?>