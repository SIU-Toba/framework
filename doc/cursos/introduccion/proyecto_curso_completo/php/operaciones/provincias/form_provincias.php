<?php
class form_provincias extends toba_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Validacion general ----------------------------------
		
		{$this->objeto_js}.evt__validar_datos = function()
		{
			var nombre = this.ef('nombre').get_estado();
			if (nombre.length < 5) {
				notificacion.agregar('El nombre ingresado es demasiado corto.');
				return false;
			}
			return true;
		}
		";
	}
}
?>