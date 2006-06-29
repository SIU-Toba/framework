<?php
require_once('nucleo/componentes/interface/objeto_ei_formulario_ml.php'); 


class eiform_abm_detalle extends objeto_ei_formulario_ml
{
	private $fila_protegida;

	function set_fila_protegida($fila)
	{
		$this->fila_protegida = $fila;	
	}


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
		
		echo "
			{$this->objeto_js}.evt__implicito__procesar = function(inicial, fila_actual) {
				if (this.ef('implicito').ir_a_fila(fila_actual).chequeado()) {
					//Si se selecciona uno implicito, deseleccionar el resto
					for (var id_fila in this._filas) {
						if (this._filas[id_fila] != fila_actual) {
							this.ef('implicito').ir_a_fila(this._filas[id_fila]).chequear(false);
						}
					}
				}
			}
		";
	}
	
	

}
?>