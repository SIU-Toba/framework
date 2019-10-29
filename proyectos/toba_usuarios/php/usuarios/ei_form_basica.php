<?php
class ei_form_basica extends toba_ei_formulario
{

	function extender_objeto_js()
	{
            $id_js = toba::escaper()->escapeJs($this->objeto_js);
            echo
		"$id_js.evt__usuario__validar = function()
		{
			if (this.ef('usuario').get_estado().indexOf(' ') != -1) {
				this.ef('usuario').set_error('No puede contener espacios.');
				return false;
			}
			return true;
		}

		//---- Procesamiento de EFs --------------------------------

		$id_js.evt__pide_2do_factor__procesar = function(es_inicial)
		{
                    if (this.ef('pide_2do_factor')) {
                        var visible = this.ef('pide_2do_factor').chequeado();
                        this.ef('clave').mostrar(visible);
                    }
		}
		";
	}
}
?>