<?php 
//--------------------------------------------------------------------
class form_prueba_efs extends objeto_ei_formulario
{
	function extender_objeto_js()
	{
		echo "{$this->objeto_js}.evt__tipo__procesar = function(inicial) {
				if (! inicial) {
					this.set_evento(new evento_ei('modificacion'), true);
				}
			}
		";
	}


}

?>