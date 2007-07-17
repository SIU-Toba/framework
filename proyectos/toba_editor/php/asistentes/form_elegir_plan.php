<?php 

class form_elegir_plan extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__crear_nuevo__procesar = function(es_inicial) {
			if (this.ef('crear_nuevo').get_estado() == 1) {
				this.controlador.dep('cuadro_planes').mostrar();
			} else {
				this.controlador.dep('cuadro_planes').ocultar();
			}
		}
		";
	}
}

?>