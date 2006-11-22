<?php
php_referencia::instancia()->agregar(__FILE__);

class form_persona_juegos extends toba_ei_formulario_ml
{
	function extender_objeto_js()
	{
		/*
			El lapso de horas tiene que ser valido (inicio < fin)
		echo  "
			{$this->objeto_js}.evt__validar_datos = function () {
				var ok = true;
				var fila;
				var mensaje = \"La 'Hora inicio' tiene que ser menor a la 'Hora fin.'\";
				for (fila in this._filas) {
					var ef_hora_i = this.ef('hora_inicio').ir_a_fila( fila );
					var ef_hora_f = this.ef('hora_fin').ir_a_fila( fila );
					if ( parseFloat( ef_hora_i.valor() ) >= parseFloat( ef_hora_f.valor() ) ) {
						//Deja marcado el ERROR.
						ef_hora_i.set_error(mensaje);
						ef_hora_f.set_error(mensaje);
						ok = false;
					}
				}
				if(!ok){
					notificacion.agregar(mensaje);
				}
				return ok;
			}
		";
			Esto limpiar el ERROR que posiblemente dejo la validacion
		echo "
			{$this->objeto_js}.evt__hora_inicio__procesar = function(es_inicial, fila) {
				if(!es_inicial){
					this.ef('hora_inicio').ir_a_fila( fila ).resetear_error();
					this.ef('hora_fin').ir_a_fila( fila ).resetear_error();
				}
			}
			{$this->objeto_js}.evt__hora_fin__procesar = function(es_inicial, fila) {
				if(!es_inicial){
					this.ef('hora_inicio').ir_a_fila( fila ).resetear_error();
					this.ef('hora_fin').ir_a_fila( fila ).resetear_error();
				}
			}
		";
		*/
	}
}
?>