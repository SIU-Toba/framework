<?php 

class eiform_ocultar_deshabilitar1 extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__a__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('a').chequeado()) {
						this.ocultar_boton('a');
					} else {
						this.mostrar_boton('a');
					}
				}
			} 

			{$this->objeto_js}.evt__b__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('b').chequeado()) {
						this.ocultar_boton('b');
					} else {
						this.mostrar_boton('b');
					}
				}
			} 

			{$this->objeto_js}.evt__c__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('c').chequeado()) {
						this.deshabilitar_boton('c');
					} else {
						this.deshabilitar_boton('c');
					}
				}
			} 

			{$this->objeto_js}.evt__d__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('d').chequeado()) {
						this.deshabilitar_boton('d');
					} else {
						this.deshabilitar_boton('d');
					}
				}
			} 
		";
	}
}
?>