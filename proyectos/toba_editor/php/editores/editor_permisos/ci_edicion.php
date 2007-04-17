<?php

class ci_edicion extends toba_ci
{
	protected $esta_cargado = false;
	
	/**
	 * @return toba_datos_relacion
	 */
	function get_relacion()	
	{
		return $this->controlador->get_relacion();
	}
		
	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	function conf__basicas()
	{
		$props = $this->get_relacion()->tabla('permiso')->get();
		$props['grupos'] = $this->get_relacion()->tabla('grupos')->get_grupos();		
		return $props;
	}

	function evt__basicas__modificacion($datos)
	{
		$grupos = $datos['grupos'];
		unset($datos['grupos']);
		$this->get_relacion()->tabla('permiso')->set($datos);
		$this->get_relacion()->tabla('grupos')->set_grupos($grupos);
	}

}

?>