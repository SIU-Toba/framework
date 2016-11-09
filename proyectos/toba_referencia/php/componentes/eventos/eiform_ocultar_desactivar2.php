<?php 
php_referencia::instancia()->agregar(__FILE__);

class eiform_ocultar_desactivar2 extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			{$id_js}.evt__a__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('a').chequeado()) {
						this.controlador.ocultar_boton('a');
					} else {
						this.controlador.mostrar_boton('a');
					}
				}
			} 

			{$id_js}.evt__b__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('b').chequeado()) {
						this.controlador.ocultar_boton('b');
					} else {
						this.controlador.mostrar_boton('b');
					}
				}
			} 

			{$id_js}.evt__c__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('c').chequeado()) {
						this.controlador.desactivar_boton('c');
					} else {
						this.controlador.activar_boton('c');
					}
				}
			} 

			{$id_js}.evt__d__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('d').chequeado()) {
						this.controlador.desactivar_boton('d');
					} else {
						this.controlador.activar_boton('d');
					}
				}
			} 
		";
	}

}

?>