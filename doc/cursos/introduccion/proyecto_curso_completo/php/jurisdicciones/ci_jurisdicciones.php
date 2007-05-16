<?php 

class ci_jurisdicciones extends toba_ci
{
	function ini__operacion()
	{
		$this->dep('datos')->cargar();
	}

	function evt__procesar()
	{
		$this->dep('datos')->sincronizar();
	}

	//--------- FORMULARIO ------------------------------------------------

	function evt__form__modificacion($datos)
	{
		$this->dep('datos')->procesar_filas($datos);
	}

	function conf__form($componente)
	{
		$componente->set_datos($this->dep('datos')->get_filas());
	}
}
?>