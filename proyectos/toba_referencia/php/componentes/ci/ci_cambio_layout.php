<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_cambio_layout extends toba_ci
{

	function conf__cuadro1($componente)
	{
		$datos = array();
		$inicio = 1;
		$fin = 31;
		for ($i = $inicio ; $i <= $fin; $i++) {
			$datos[] = array('fecha' => "$i-03-2006", 'importe' => 100 - $i);
		}
		if (isset($this->orden)) {
			$ordenamiento = array();
			foreach ($datos as $fila) {
	            $ordenamiento[] = $fila[$this->orden['columna']]; 
			}
	        $sentido = ($this->orden['sentido'] == 'asc') ? SORT_ASC : SORT_DESC;
			array_multisort($ordenamiento, $sentido, $datos); 
		}
		$componente->set_datos($datos);
	}
	
	function conf__form2($componente)
	{
		$componente->set_datos(array(
			array('fecha' => '2006-10-24', 'importe' => 212.25),
			array('fecha' => '2006-10-29', 'importe' => 42),
		));
	}
	
	function conf__esquema($esquema)
	{
		$esquema->set_datos('
			digraph G {	
				rankdir=LR;	
				a -> b;
				b -> c;
				c -> a;
			}
		');
	}

}



?>