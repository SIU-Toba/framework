<?php
require_once('nucleo/componentes/interface/toba_ei_cuadro.php'); 
//--------------------------------------------------------------------
class cuadro_popup extends toba_ei_cuadro
{
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__seleccion = function(id) {
				var seleccion = id.split('||');
				seleccionar(seleccion[0], seleccion[1]);
				return false;
			}
		";
	}


}

?>