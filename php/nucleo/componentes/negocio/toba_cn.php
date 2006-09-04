<?php
require_once("nucleo/componentes/toba_componente.php");

/**
 * Padre de todas las clases que definen componentes
 * @package Componentes
 * @subpackage Negocio
 */
class toba_cn extends toba_componente
{
	protected $transaccion_abierta;			// privado | boolean | Indica si la transaccion se encuentra en proceso

	function __construct($id)
	{
		parent::__construct($id);
		$this->transaccion_abierta = false;
		$this->ini();
	}

	function ini()
	{
		//Esto hay que redeclararlo en los HIJOS	
	}	

	function evt__limpieza_memoria($no_borrar=null)
	{
		$this->log->debug( $this->get_txt() . "[ evt__limpieza_memoria ]", 'toba');
		//$this->borrar_memoria();
		$this->eliminar_estado_sesion($no_borrar);
		$this->ini();
	}


/*
	function __call($metodo, $argumentos)
	{
		ei_arbol($argumentos, "Llamada al metodo no implementado: " . $metodo);
	}
*/

	//-------------------------------------------------------------------------------
	//------------------  PROCESAMIENTO  --------------------------------------------
	//-------------------------------------------------------------------------------

	function cancelar()
	{
		$this->log->debug( $this->get_txt() . "[ cancelar ]", 'toba');
		$this->evt__limpieza_memoria();
	}
	//-------------------------------------------------------------------------------

	function procesar($parametros=null)
	//ATENCION: ignore_user_abort() //Esto puede ser importante!!!!
	{
		$resultado = null;
		$this->log->debug( $this->get_txt() . "[ procesar ]", 'toba');
		try {
			//ignore_user_abort();				//------> ?????
			$this->iniciar_transaccion();
			$this->evt__validar_datos();
			$resultado = $this->evt__procesar_especifico($parametros);
			$this->finalizar_transaccion();
			$this->evt__limpieza_memoria();
			return $resultado;
		}
		catch(toba_error $e){
			$this->abortar_transaccion();
			$this->log->debug($e, 'toba');	
			throw new toba_error( $e->getMessage() );
		}
	}
	//-------------------------------------------------------------------------------

	function evt__validar_datos()
	{
		//Esto hay que redeclararlo en los HIJOS	
	}
	//-------------------------------------------------------------------------------

	function evt__procesar_especifico()
	{
		//Esto hay que redeclararlo en los HIJOS	
	}

	//-------------------------------------------------------------------------------
	//------------------  Manejo de TRANSACCIONES  ----------------------------------
	//-------------------------------------------------------------------------------

	function iniciar_transaccion()
	{
		$this->transaccion_abierta = true;
		return toba::db($this->info['fuente'])->abrir_transaccion();
	}
	//-------------------------------------------------------------------------------
	
	function finalizar_transaccion($mensaje=null)
	{
		$this->transaccion_abierta = false;
		return toba::db($this->info['fuente'])->cerrar_transaccion();
	}
	//-------------------------------------------------------------------------------
	
	function abortar_transaccion($mensaje=null)
	{
		$this->transaccion_abierta = false;
		return toba::db($this->info['fuente'])->abortar_transaccion($sql);
	}
	//-------------------------------------------------------------------------------

	function ejecutar_sql($sentencias_sql)
	{
		if($this->transaccion_abierta){
			if(!is_array($sentencias_sql)){
				$sentencias_sql = array($sentencias_sql);
			}
			foreach($sentencias_sql as $sql){
				toba::db($this->info['fuente'])->ejecutar($sql);
			}
		}else{
			throw new toba_error('La transaccion no se encuentra abierta');
		}
	}
}
?>