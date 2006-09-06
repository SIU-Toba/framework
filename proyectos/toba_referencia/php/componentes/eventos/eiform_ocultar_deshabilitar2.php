<?php 

class eiform_ocultar_deshabilitar2 extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__a__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('a').chequeado()) {
						this.controlador.ocultar_boton('a');
					} else {
						this.controlador.mostrar_boton('a');
					}
				}
			} 

			{$this->objeto_js}.evt__b__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('b').chequeado()) {
						this.controlador.ocultar_boton('b');
					} else {
						this.controlador.mostrar_boton('b');
					}
				}
			} 

			{$this->objeto_js}.evt__c__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('c').chequeado()) {
						this.controlador.deshabilitar_boton('c');
					} else {
						this.controlador.deshabilitar_boton('c');
					}
				}
			} 

			{$this->objeto_js}.evt__d__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('d').chequeado()) {
						this.controlador.deshabilitar_boton('d');
					} else {
						this.controlador.deshabilitar_boton('d');
					}
				}
			} 
		";
	}

}

?>