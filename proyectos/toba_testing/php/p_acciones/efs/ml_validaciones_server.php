<?php

//--------------------------------------------------------------------
class ml_instancias_server extends toba_ei_formulario_ml
{
	function extender_objeto_js()
	{
		echo "{$this->objeto_js}.validar_fila = function() {
				return true;
			}
			{$this->objeto_js}.submit_orig = {$this->objeto_js}.submit;
			{$this->objeto_js}.submit = function() {
				this.submit_orig();	
				this.ef('combo_oblig').ir_a_fila(this._filas[0]).cambiar_valor('b');
			}			
			
		";		
	}


}

?>