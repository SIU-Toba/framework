<?php
include_once('nucleo/browser/clases/objeto_ei_formulario.php');

class formulario extends objeto_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.procesar_combo = function (es_inicial) {
				if (! es_inicial && this.ef('combo').valor() != apex_ef_no_seteado) {
					this.set_evento('M', '{$this->submit_modificar}');
					this.submit();
				}
			}
		";
	}



}




?>