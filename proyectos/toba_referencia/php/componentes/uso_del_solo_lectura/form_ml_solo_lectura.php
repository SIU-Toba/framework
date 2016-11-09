<?php
php_referencia::instancia()->agregar(__FILE__);

class form_ml_solo_lectura extends toba_ei_formulario_ml
{
	function extender_objeto_js()
	{
		echo "
			var solo_lectura_activo = false;".
			toba::escaper()->escapeJs($this->objeto_js).".evt__readonly_cliente = function() {
				solo_lectura_activo = !solo_lectura_activo;
				for (id_fila in this._filas) {
					for (id_ef in this._efs) {
						if (id_ef != 'editable')
								this.ef(id_ef).ir_a_fila(this._filas[id_fila]).set_solo_lectura(solo_lectura_activo);
					}
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
		";

	}

}

?>