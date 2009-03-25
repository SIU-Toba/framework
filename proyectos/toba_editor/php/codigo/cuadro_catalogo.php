<?php
class cuadro_catalogo extends toba_ei_cuadro
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$editor = toba::vinculador()->get_url(null, '3463');
		echo "
		//---- Eventos ---------------------------------------------
		
		{$this->objeto_js}.evt__abrir = function(archivo)
		{
			alert(archivo);
			return false;
		}
		
		{$this->objeto_js}.evt__editar = function()
		{
			return false;
		}
		";
	}

}

?>