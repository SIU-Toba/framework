<?php 

class cuadro_autologin extends toba_testing_pers_ei_cuadro
{
	function extender_objeto_js()
	{
		$id_form = $this->controlador->dependencia('datos')->get_id_objeto_js();
		echo "
			{$this->objeto_js}.set_evento_orig = {$this->objeto_js}.set_evento;

			{$this->objeto_js}.set_evento = function(evento, hacer_submit) {
				$id_form.ef('usuario').set_estado('autologin');
				$id_form.ef('clave').set_estado('autologin');
				this.set_evento_orig(evento, hacer_submit);
			}
		";
	}
}

?>