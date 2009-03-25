<?php
require_once('formateo_referencia.php');
php_referencia::instancia()->agregar(__FILE__);

class extension_ci extends toba_ci
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
			$datos[] = array('fecha' => "2006-03-$i", 'importe' => 100 - $i);
		}
		if (isset($this->orden)) {
			$ordenamiento = array();
			foreach ($datos as $fila) {
	            $ordenamiento[] = $fila[$this->orden['columna']]; 
			}
	        $sentido = ($this->orden['sentido'] == 'asc') ? SORT_ASC : SORT_DESC;
			array_multisort($ordenamiento, $sentido, $datos); 
		}
		return $datos;
	}
	
	function conf__cuadro_auto($cuadro)
	{
		$cuadro->set_formateo_columna('importe', 'pesos_sin_coma', 'formateo_referencia');		
		return $this->get_datos();	
	}

	
	function conf__cuadro($cuadro)
	{
		$datos = $this->get_datos();
		$cuadro->set_total_registros(count($datos));
		$tamanio_pagina = $cuadro->get_tamanio_pagina();
		$offset = ($cuadro->get_pagina_actual() - 1) * $tamanio_pagina;
		$cuadro->set_datos(array_slice($datos, $offset, $tamanio_pagina));
	}
	
	function evt__cuadro__ordenar($orden)
	{
		$this->orden = $orden;
	}

}
?>