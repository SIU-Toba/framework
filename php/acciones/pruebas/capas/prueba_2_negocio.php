<?php
require_once("nucleo/negocio/objeto_cn_t.php");

class objeto_cn_t_p2 extends objeto_cn_t
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
			$valores[0]['descripcion'] = "Valor 1";
			$valores[0]['valor'] = "3005.50";
			$valores[0]['seleccion'] = "0";
			$valores[1]['descripcion'] = "Valor 2";
			$valores[1]['valor'] = "57.54";
			$valores[1]['seleccion'] = "0";
			$valores[2]['descripcion'] = "Valor 3";
			$valores[2]['valor'] = "123.35";
			$valores[2]['seleccion'] = "0";
			return $valores;
		}
	}

	function set_asignaciones($datos)
	{
		$this->valores = $datos;
	}

	//---------------------------------------------------------------------
	// Planificacion
	//---------------------------------------------------------------------

	function get_planificacion()
	//Planifica en base a los valores asignados
	{
		if(isset($this->planificacion)){
			$datos = $this->planificacion;
		}else{
			$datos['deuda_condona'] = 0;
			$datos['interes_condona'] = 0;
		}
		$total = 0;
		foreach(array_keys($this->valores) as $valor)	
		{
			if($this->valores[$valor]['seleccion']=="1"){
				$total += $this->valores[$valor]['valor'];
			}
		}
		$datos['deuda'] = $total;
		$datos['interes'] = $total / 10;
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

	//---------------------------------------------------------------------
	// Varios
	//---------------------------------------------------------------------

	function controlar_asignacion()
	{
		//Tiene que existir al menos un elemento asignado
		$resultado = false;
		foreach(array_keys($this->valores) as $valor)	
		{
			if($this->valores[$valor]['seleccion']=="1"){
				$resultado = true;
			}
		}
		return $resultado;
	}

	function debug()
	{
		$temp['planificacion'] = $this->planificacion;
		$temp['valores'] = $this->valores;
		ei_arbol($temp,"Estado interdno del Controlador de negocio");
	}
}

?>