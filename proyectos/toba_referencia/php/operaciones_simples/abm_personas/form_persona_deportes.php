<?php
require_once('nucleo/componentes/interface/toba_ei_formulario.php'); 
//----------------------------------------------------------------
class form_persona_deportes extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		//El lapso de horas tiene que ser valido (inicio < fin)
		echo "
			{$this->objeto_js}.evt__validar_datos = function() {
				var hora_i = parseFloat( this.ef('hora_inicio').valor() );
				var hora_f = parseFloat( this.ef('hora_fin').valor() );
				if ( hora_i >= hora_f ) {
						var mensaje = \"La 'Hora inicio' tiene que ser menor a la 'Hora fin.'.\";
						cola_mensajes.agregar(mensaje);
						this.ef('hora_fin').set_error(mensaje);
						return false;
				}
				return true;
			}
		";		
	}
}
?>