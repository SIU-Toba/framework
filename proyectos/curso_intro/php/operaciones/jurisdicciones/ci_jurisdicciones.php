<?php 

class ci_jurisdicciones extends toba_ci
{
	function ini__operacion()
	{
		$this->dep('tabla')->cargar();
	}

	function evt__procesar()
	{
		$this->dep('tabla')->sincronizar();
	}

	//--------- FORMULARIO ------------------------------------------------

	function evt__form__modificacion($datos)
	{
		$this->dep('tabla')->procesar_filas($datos);
	}

	function conf__form($componente)
	{
		$componente->set_datos($this->dep('tabla')->get_filas());
	}
}
?>