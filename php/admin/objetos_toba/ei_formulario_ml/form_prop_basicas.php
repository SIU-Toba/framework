<?php
require_once("nucleo/browser/clases/objeto_ei_formulario.php");

class form_prop_basicas extends objeto_ei_formulario
{

	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__filas_ordenar__procesar = function () {
				if (this.ef('filas_ordenar').chequeado())
					this.ef('columna_orden').mostrar();
				else
					this.ef('columna_orden').ocultar();
			}
			
			{$this->objeto_js}.evt__filas_agregar__procesar = function() {
				if (this.ef('filas_agregar').chequeado())
					this.ef('filas_agregar_online').mostrar();
				else
					this.ef('filas_agregar_online').ocultar();				
			}
		";
	}
}

?>