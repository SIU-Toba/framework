<?php 
php_referencia::instancia()->agregar(__FILE__);

class filtrado_directo_php extends toba_ci
{
	function evt__filtrar()
	{
		$this->pantalla()->eliminar_evento('a');
		$this->pantalla()->eliminar_evento('b');
		$this->dep('formulario')->eliminar_evento('a');
		$this->dep('formulario')->eliminar_evento('b');
		$this->dep('cuadro')->eliminar_evento('a');
		$this->dep('cuadro')->eliminar_evento('b');
	}

	function evt__asociar()
	{
		$this->pantalla()->agregar_evento('c');
		$this->pantalla()->agregar_evento('d');
	}

	function conf__cuadro()
	{
		$datos[0]['id'] = 'A';	
		$datos[0]['desc'] = 'Desc. A';	
		$datos[1]['id'] = 'B';	
		$datos[1]['desc'] = 'Desc. B';	
		return $datos;
	}
}
?>