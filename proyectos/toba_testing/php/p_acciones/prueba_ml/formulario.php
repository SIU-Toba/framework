<?php
include_once('nucleo/browser/clases/toba_ei_formulario.php');

class formulario extends toba_testing_pers_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__combo__procesar = function (es_inicial) {
				if (! es_inicial && this.ef('combo').valor() != apex_ef_no_seteado) {
					this.set_evento(new evento_ei('modificacion', true, '¿Esta seguro que desea enviar esta información?'));
				}
			}\n";
	}



}




?>