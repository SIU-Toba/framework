<?php 
class form_eliminar_operaciones extends toba_ei_formulario_ml
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
		.".evt__eliminar__procesar = function(incicial, fila)
		{
			var tiene_subclase = this.ef('posee_subclase').ir_a_fila(fila).get_estado() != '';
			if (!tiene_subclase || ! this.ef('eliminar').ir_a_fila(fila).chequeado()) {
				this.ef('eliminar_archivo').ir_a_fila(fila).chequear(false);
				this.ef('eliminar_archivo').ir_a_fila(fila).desactivar();
			} else {
				this.ef('eliminar_archivo').ir_a_fila(fila).activar();
			}
		}
		";
	}
}

?>