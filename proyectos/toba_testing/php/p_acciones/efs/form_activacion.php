<?php 
//--------------------------------------------------------------------
class form_activacion extends toba_testing_pers_ei_formulario
{
	function extender_objeto_js()
	{
		echo "{$this->objeto_js}.validar_ef = function() {
				return true;
			}
		";
	}
	
	function validar_estado()
	{
		$ok = false;
		try {
			parent::validar_estado();
		} catch (toba_error_validacion $e) {
			$causante = $e->get_causante()->get_id();
			//--- El obligatorio no tiene que dar excepcion
			if ($causante == 'obligatorio') {
				throw new toba_error_validacion("El ef obligatorio no debio tirar excepcion!!<br>".
													$e->getMessage(), $e->get_causante());
			} else {
				$ok = true;	
			}
		}
		//--- El NO obligatorio si tiene que dar		
		if (! $ok) {
			throw new toba_error_validacion("El ef no obligatorio debio tirar excepcion!!",
												 $this->ef('no_obligatorio'));
		}
	}
}

?>