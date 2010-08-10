<?php

class ml extends toba_testing_pers_ei_formulario_ml
{
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__checkbox__procesar = function () {
				var total = 0;	
				var filas = this.filas();
				for (fila in filas)	{
					if (this.ef('checkbox').ir_a_fila(filas[fila]).chequeado()) {
						this.ef('editable_numero').ir_a_fila(filas[fila]).activar();
						valor = this.ef('editable_numero').ir_a_fila(filas[fila]).valor();
						valor = (valor=='' || isNaN(valor)) ? 0 : valor;
						total += valor
					}
					else { //desactivar el campo numero
						this.ef('editable_numero').ir_a_fila(filas[fila]).desactivar();			
				 	}
				}
				total = Math.round(total * 100)/100;
				this.cambiar_total('editable_numero', total);
				return total;
			}

			{$this->objeto_js}.evt__editable_numero__procesar = function () {
				this.evt__checkbox__procesar();
			}			
		";
	}
}



?>