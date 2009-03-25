<?php 
php_referencia::instancia()->agregar(__FILE__);

class control_runtime extends toba_ci
{
	protected $modificar = false;

	function ini()
	{
		$this->datos[0]['id'] = 'A';	
		$this->datos[0]['desc'] = 'Desc. A';	
		$this->datos[1]['id'] = 'B';	
		$this->datos[1]['desc'] = 'Desc. B';	
	}

	function evt__modificar()
	{
		$this->modificar = true;
		$this->pantalla()->evento('a')->set_etiqueta('Nueva etiqueta de A');
		$this->pantalla()->evento('b')->set_etiqueta('B (ahora con confirmacion)');
		$this->pantalla()->evento('b')->set_msg_confirmacion('¿Seguro?');
		$this->dep('cuadro')->evento('a')->set_etiqueta('Hola A');
		$this->dep('formulario')->evento('a')->set_etiqueta('Hola A');
		$this->dep('formulario')->evento('b')->set_imagen('borrar.gif');
	}

	function conf__cuadro()
	{
		return $this->datos;
	}

	function conf_evt__cuadro__a($evento, $fila)
	{
		if ( $this->modificar ) {
			if ( $fila === 1 ) {
				$evento->set_imagen('nucleo/agregar.gif');
				$evento->set_etiqueta($this->datos[$fila]['desc']);
			} else {
				$evento->anular();
			}
		}
	}
	
	function conf__multilinea()
	{
		return $this->datos;
	}

	function conf_evt__multilinea__a($evento, $fila)
	{
		if ( $this->modificar ) {
			if ( $fila === 1 ) {
				$evento->set_imagen('nucleo/agregar.gif');
			}
			$evento->set_etiqueta($this->datos[$fila]['desc']);
		}
	}
}
?>