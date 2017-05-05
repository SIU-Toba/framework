<?php
php_referencia::instancia()->agregar(__FILE__);

class form_solo_lectura extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			var solo_lectura_activo = false;
			{$id_js}.evt__readonly_cliente = function() {
				solo_lectura_activo = !solo_lectura_activo;
				for (id_ef in this._efs) {
					this.ef(id_ef).set_solo_lectura(solo_lectura_activo);
				}
				/*
				if (solo_lectura_activo) {
					alert('Modo solo_lectura activado');
				} else {
					alert('Modo solo_lectura desactivado');				
				}
				*/
				return false;
			}
			
			{$id_js}.evt__checkbox__procesar = function(inicial) {
				if (this.ef('checkbox').input().type == 'hidden') {
					//Caso particular del solo-lectura en server
					var chequeado = this.ef('checkbox').input().value;
				} else {
					var chequeado = this.ef('checkbox').chequeado();
				}
			}
		";

	}

}

?>