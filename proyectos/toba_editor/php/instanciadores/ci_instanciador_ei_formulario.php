<?php
require_once('ci_instanciadores.php'); 
//----------------------------------------------------------------
class ci_instanciador_ei_formulario extends ci_instanciadores
{
	protected $datos;

	function mantener_estado_sesion()
	{
		$props = parent::mantener_estado_sesion();
		$props[] = 'datos';
		return $props;
	}
	
	function evt__objeto__alta($parametros)
	{
		$this->datos = $parametros;
	}

	function evt__objeto__modificacion($parametros)
	{
		$this->datos = $parametros;
	}	
	
	function conf__objeto()
	{
		if (isset($this->datos)) {
			return $this->datos;
		}
	}
	
	function evt__objeto__filtrar($parametros)
	{
		$this->datos = $parametros;
	}
	
	function evt__objeto__cancelar()
	{
		unset($this->datos);
	}
	
	function evt__objeto__baja()
	{
		unset($this->datos);
	}
	
	function obtener_html_contenido__simulacion()
	{
		if (isset($this->datos)) {
			ei_arbol($this->datos, "Datos actuales");	
		}
		$this->obtener_html_dependencias();
	}
	

}

?>