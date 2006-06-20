<?php
require_once("nucleo/negocio/objeto_cn_t.php");

/*
	Manejo de Buffers de un registro,
	sincronizados en el mismo request que se modifican
*/

class objeto_cn_buffer extends objeto_cn_t
{
	protected $buffer;

	function __construct($id, $resetear=false)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{
		parent::__construct($id, $resetear);
	}

	function info_buffer()
	{
		//Informacion
		ei_arbol( $this->buffer->info(true));
	}

	//-------------------------------------------------------------------------------
	//----- ACCESO al BUFFER
	//-------------------------------------------------------------------------------

	public function ins($datos)
	{
		try{
			$this->buffer->agregar_registro($datos);			
			$this->procesar();
		}catch(excepcion_toba $e){
			$this->informar_msg($e->getMessage(),"info");
		}
	}
	//-------------------------------------------------------------------------------
	
	public function del()
	{
		try{
			$this->buffer->eliminar_registro(0);
			$this->procesar();
		}catch(excepcion_toba $e){
			$this->informar_msg($e->getMessage(),"info");
		}
	}
	//-------------------------------------------------------------------------------
	
	public function upd($datos)
	{
		try{
			$this->buffer->modificar_registro($datos, 0);
			$this->procesar();
		}catch(excepcion_toba $e){
			$this->informar_msg($e->getMessage(),"info");
		}
	}
	//-------------------------------------------------------------------------------
	
	public function get()
	{
		if($this->buffer->get_cantidad_registros() > 0){
			return $this->buffer->get_registro(0);			
		}		
	}
	//-------------------------------------------------------------------------------
	
	public function seleccionar($id_registro)
	//Esto carga un elemento en el BUFFER
	{
		$this->cargar($id_registro);		
	}
	//-------------------------------------------------------------------------------

	public function limpiar($parametros)
	//Mantiene estado de pantalla de seleccion de registros de la pantalla
	{
		$this->buffer->resetear();
	}

	//-------------------------------------------------------------------------------
	//----- Carga y descarga de entidades
	//-------------------------------------------------------------------------------
	
	function procesar_especifico()
	{
		$this->sincronizar();
		$this->buffer->resetear();
	}
	
	//-------------------------------------------------------------------------------
	//----- Carga y descarga de entidades
	//-------------------------------------------------------------------------------

	function cargar($id)
	{
		$this->log->info("Atencion el metodo que carga el BUFFER debe ser redefinido en el hijo");
	}
	//-------------------------------------------------------------------------------
}
?>
