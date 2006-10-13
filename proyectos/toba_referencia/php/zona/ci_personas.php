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
	
	function evt__cuadro__cargar($seleccion)
	{
		toba::zona()->cargar($seleccion);
	}

	function conf__cuadro($componente)
	{
		return consultas::get_personas();
	}
	
	/**
	 * Fuerza a que el vinculo de la fila del cuadro cargue la zona con el id de la fila
	 */
	function conf_evt__cuadro__cargar_url(toba_evento_usuario $evento, $fila)
	{
		$evento->vinculo()->set_editable_zona(array('id' => $evento->get_parametros()));
	}

	
}

?>