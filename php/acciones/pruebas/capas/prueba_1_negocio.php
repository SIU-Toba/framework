<?php
require_once("nucleo/negocio/objeto_cn_t.php");	//Ancestro de todos los OE

//-------------------------------------------------------------------------------
//----  Controlador de NEGOCIO
//-------------------------------------------------------------------------------

class objeto_cn_t_p1 extends objeto_cn_t
{
	var $valores;
	var $planificacion;

	function __construct($id)
	{
		parent::__construct($id);
	}

	function mantener_estado_sesion()
	//Propiedades que necesitan persistirse en la sesion
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "valores";
		$propiedades[] = "planificacion";
		return $propiedades;
	}

	//---------------------------------------------------------------------
	// Asignaciones
	//---------------------------------------------------------------------

	function get_valores_a_asignar()
	{
		if(isset($this->valores)){
			return $this->valores;
		}else{
			$valores[0]['descripcion'] = "tope 1";
			$valores[0]['tope'] = "3005.50";
			$valores[0]['valor'] = "0";
			$valores[1]['descripcion'] = "tope 2";
			$valores[1]['tope'] = "57.54";
			$valores[1]['valor'] = "0";
			$valores[2]['descripcion'] = "tope 3";
			$valores[2]['tope'] = "123.35";
			$valores[2]['valor'] = "0";
			return $valores;
		}
	}

	function set_asignaciones($datos)
	{
		if(count($datos)>0){
			$this->valores = $datos;
		}
	}

	//---------------------------------------------------------------------
	// Planificacion
	//---------------------------------------------------------------------

	function get_planificacion()
	//Planifica en base a los valores asignados
	{
		return $datos;
	}
	
	function set_planificacion($datos)
	{
		$this->planificacion = $datos;
	}

	//---------------------------------------------------------------------
	// Transaccion
	//---------------------------------------------------------------------

	function procesar()
	{
		echo ei_mensaje("Ejecutar TRANSACCION");
	}
}
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
?>