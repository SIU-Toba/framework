<?php
php_referencia::instancia()->agregar(__FILE__);

class form_persona_deportes extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		//El lapso de horas tiene que ser valido (inicio < fin)
		echo toba::escaper()->escapeJs($this->objeto_js)
			.".evt__validar_datos = function() {
				var hora_i = this.ef('hora_inicio').hora();
				var hora_f = this.ef('hora_fin').hora();
				if ( hora_i >= hora_f ) {
						var mensaje = \"La 'Hora inicio' tiene que ser menor a la 'Hora fin.'.\";
						notificacion.agregar(mensaje);
						this.ef('hora_fin').set_error(mensaje);
						return false;
				}
				return true;
			}
		";		
	}
}
?>