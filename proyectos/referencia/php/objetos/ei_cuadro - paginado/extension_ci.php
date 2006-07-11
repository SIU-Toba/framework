<?php
require_once('objetos/ei_filtro - ei_cuadro/dao.php');
  
class extension_ci extends objeto_ci
{
	protected $orden;
	
	function mantener_estado_sesion()
	{
		$par = parent::mantener_estado_sesion();
		$par[] = 'orden';
		return $par;
	}
	
	function get_datos()
	{
		$datos = array();
		$inicio = 1;
		$fin = 31;
		for ($i = $inicio ; $i <= $fin; $i++) {
			$datos[] = array('fecha' => "$i-03-2006", 'importe' => 100-$i);
		}
		if (isset($this->orden)) {
			$ordenamiento = array();
	        foreach ($datos as $fila) { 
	            $ordenamiento[] = $fila[$this->orden['columna']]; 
	        }			
	        $sentido = ($this->orden['sentido'] == "asc") ? SORT_ASC : SORT_DESC;
			array_multisort($ordenamiento, $sentido, $datos); 
		}
		return $datos;
	}
	
	function evt__cuadro_auto__carga()
	{
		return $this->get_datos();	
	}
	
	function evt__cuadro__cant_reg()
	{
		return count($this->get_datos());	
	}
	
	function evt__cuadro__carga()
	{
		$datos = $this->get_datos();
		$tamanio_pagina = $this->dependencia('cuadro')->get_tamanio_pagina();
		$pagina_actual = $this->dependencia('cuadro')->get_pagina_actual();
		$offset = ($pagina_actual - 1) * $tamanio_pagina;
		return array_slice($datos, $offset, $tamanio_pagina);		
	}
	
	function evt__cuadro__ordenar($orden)
	{
		$this->orden = $orden;
	}

}
?>