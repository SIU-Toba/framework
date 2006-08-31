<?php 
//--------------------------------------------------------------------
class form_validaciones_server extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo "{$this->objeto_js}.validar_ef = function() {
				return true;
			}
			{$this->objeto_js}.submit_orig = {$this->objeto_js}.submit;
			{$this->objeto_js}.submit = function() {
				this.submit_orig();	
				this.ef('numero').input().value = 'texto';
				this.ef('porcentaje_mayor').input().value = '110';
				this.ef('porcentaje_neg').input().value = '-10';
				this.ef('fecha_inv').input().value = '31/02/2006';
			}
		";
	}

	function validar_estado()
	{
		$status =	true;
		//Valida	el	estado de los ELEMENTOS	de	FORMULARIO
		echo "<a href='#' onclick='toggle_nodo(this.nextSibling)'>Mensajes:</a><ul  style='display:none'>";
		foreach ($this->lista_ef as $ef) {
			$valido = $this->elemento_formulario[$ef]->validar_estado();
			if ($valido !== true)  {
				echo "<li>$ef: ".$valido."</li>";
			} else {
				throw new toba_excepcion("El ef $ef no debería haber validado.");				
			}
		}
		echo "</ul>";
	}
}

?>