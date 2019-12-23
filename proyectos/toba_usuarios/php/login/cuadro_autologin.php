<?php 

class cuadro_autologin extends toba_ei_cuadro
{
	function extender_objeto_js()
	{
		$escapador = toba::escaper();
		$id_form = $escapador->escapeJs($this->controlador->dependencia('datos')->get_id_objeto_js());
		$id_js = $escapador->escapeJs($this->objeto_js);
		echo "
			{$id_js}.set_evento_orig = {$id_js}.set_evento;

			{$id_js}.set_evento = function(evento, hacer_submit) {
				$id_form.ef('usuario').set_estado('autologin');
				$id_form.ef('clave').set_estado('autologin');
				this.set_evento_orig(evento, hacer_submit);
			}
		";
	}
}

?>