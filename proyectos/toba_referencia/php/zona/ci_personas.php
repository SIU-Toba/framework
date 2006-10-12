<?php 
require_once('operaciones_simples/consultas.php');

class ci_personas extends toba_ci
{

	function conf()
	{
		if (! toba::zona()->cargada()) {
			$this->pantalla()->eliminar_evento('descargar');
		}	
	}
	
	function evt__descargar()
	{
		toba::zona()->resetear();	
	}
	
	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- cuadro -----------------------------------------------------------------------

	function evt__cuadro__seleccion($seleccion)
	{
		toba::zona()->cargar($seleccion);
	}

	//El formato del retorno debe ser array( array('columna' => valor, ...), ...)
	function conf__cuadro($componente)
	{
		return consultas::get_personas();
	}
}

?>