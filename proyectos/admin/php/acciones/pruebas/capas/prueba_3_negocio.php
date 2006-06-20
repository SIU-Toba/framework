<?php
require_once("nucleo/negocio/objeto_cn_t.php");	//Ancestro de todos los OE

//-------------------------------------------------------------------------------
//----  Controlador de NEGOCIO
//-------------------------------------------------------------------------------

class objeto_cn_t_p3 extends objeto_cn_t
{
	var $cosas;

	function __construct($id)
	{
		parent::__construct($id);
	}

	function mantener_estado_sesion()
	//Propiedades que necesitan persistirse en la sesion
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "cosas";
		return $propiedades;
	}

	function get_cosas()
	{
		if(isset($this->cosas)){
			return $this->cosas;
		}else{
			$cosas[0]['nombre']="Mesa";
			$cosas[0]['largo']="1";
			$cosas[0]['alto']="2";
			$cosas[1]['nombre']="Mesa 2";
			$cosas[1]['largo']="31";
			$cosas[1]['alto']="24";
			$cosas[2]['nombre']="Mesa 3";
			$cosas[2]['largo']="1";
			$cosas[2]['alto']="233";
			return $cosas;
		}
	}

	function set_cosas($datos)
	{
		$this->cosas = $datos;	
	}

	function procesar()
	{
		ei_arbol($this->cosas,"Cosas para procesar");

/*
		$this->inciar_transaccion();
		$this->ejecutar_sql($array_sql);
		$this->finalizar_transaccion]();
		$this->abortar_transaccion();
*/
	}

}
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
?>