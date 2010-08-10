<?php

//--------------------------------------------------------------------
class ml_instancias_server extends toba_testing_pers_ei_formulario_ml
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
	
	function validar_estado()
	{
		$status =	true;
		//Valida	el	estado de los ELEMENTOS	de	FORMULARIO
		echo "<a href='#' onclick='toggle_nodo(this.nextSibling)'>Mensajes:</a><ul  style='display:none'>";
/*		foreach ($this->get_efs_activos() as $ef) {
			$valido = $this->ef($ef)->validar_estado();
			if ($valido !== true)  {
				echo "<li>$ef: ".$valido."</li>";
			} else {
				throw new toba_error("El ef $ef no debería haber validado.");				
			}
		}*/
		echo "</ul>";
	}	


}

?>