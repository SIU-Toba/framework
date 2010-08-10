<?php

class form_solo_lectura extends toba_testing_pers_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
			var solo_lectura_activo = false;
			{$this->objeto_js}.evt__readonly_cliente = function() {
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
		";

	}

}

?>