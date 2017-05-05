<?php 
php_referencia::instancia()->agregar(__FILE__);

class eiform_ocultar_desactivar1 extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			{$id_js}.evt__a__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('a').chequeado()) {
						this.ocultar_boton('a');
					} else {
						this.mostrar_boton('a');
					}
				}
			} 

			{$id_js}.evt__b__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('b').chequeado()) {
						this.ocultar_boton('b');
					} else {
						this.mostrar_boton('b');
					}
				}
			} 

			{$id_js}.evt__c__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('c').chequeado()) {
						this.desactivar_boton('c');
					} else {
						this.activar_boton('c');
					}
				}
			} 

			{$id_js}.evt__d__procesar = function(inicial) {
				if(!inicial) { //En el inicial no se afecta para que se perciba el ocultamiento desde el server
					if (this.ef('d').chequeado()) {
						this.desactivar_boton('d');
					} else {
						this.activar_boton('d');
					}
				}
			} 
		";
	}
}
?>
