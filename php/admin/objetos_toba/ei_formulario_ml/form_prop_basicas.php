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
		";
	}
}

?>