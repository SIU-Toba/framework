<?php 
class ml_combo_editable extends toba_ei_formulario_ml
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__estado__procesar = function(es_inicial, fila)
		{
			if (! es_inicial) {
				this.ef('pais').ir_a_fila(fila).set_estado(5);
				this.ef('estado').ir_a_fila(fila).chequear(false, false);				
			}
		}
		";
	}
}

?>