<?php

class eiform_eventos extends objeto_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__accion__procesar = function() {
				if (this.ef('accion').valor() == 'H') {
					this.ef('accion_imphtml_debug').mostrar();
				} else {
					this.ef('accion_imphtml_debug').ocultar();
				}
		
			}
		";
	}
}

?>