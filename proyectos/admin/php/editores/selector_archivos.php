<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once('nucleo/browser/clases/objeto_ei_archivos.php'); 
//----------------------------------------------------------------
class ci_selector_archivos extends objeto_ci
{
	protected $archivo;
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		return $propiedades;
	}
	
	function evt__listado__carga()
	{
		$inicial = toba::get_hilo()->obtener_parametro('ef_popup_valor');
		$relativo = toba::get_hilo()->obtener_proyecto_path()."/php/";
		$this->dependencia('listado')->set_path_relativo_inicial($relativo);
		if ($inicial != null)
			$this->dependencia('listado')->set_path(dirname($inicial));
	}

	
}
//----------------------------------------------------------------
class ei_selector_archivos extends objeto_ei_archivos
{

	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__seleccionar_archivo = function()
			{
				if (this._path_relativo != '')
					var path = this._path_relativo + '/' + this._evento.parametros;
				else
					path = this._evento.parametros;
				seleccionar(path, path);	//Comunicacion con la ventana padre
			}
		";
	}
}
?>