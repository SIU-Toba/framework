<?php
require_once('nucleo/browser/clases/objeto_ei_formulario_ml.php'); 


class eiform_abm_detalle extends objeto_ei_formulario_ml
{
	private $fila_protegida;

	function set_fila_protegida($fila)
	{
		$this->fila_protegida = $fila;	
	}

		/*
	function extender_objeto_js()
	{
		if(isset($this->fila_protegida)){
			echo "	{$this->objeto_js}.evt__baja = function (fila) {
					if( fila == {$this->fila_protegida}	){
						alert('No es posible eliminar la columna que se esta editando');
						return false;
					}else{
						return true;
					}
				}
";
		}
	}
		*/
}
?>