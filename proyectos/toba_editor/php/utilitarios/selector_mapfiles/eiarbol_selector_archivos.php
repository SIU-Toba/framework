<?php

class ei_selector_archivos extends toba_ei_archivos
{
	protected $_extensiones = array('map');			//Solo selecciona archivos .map

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