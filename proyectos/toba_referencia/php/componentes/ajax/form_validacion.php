<?php 
php_referencia::instancia()->agregar(__FILE__);

class form_validacion extends toba_ei_formulario_ml
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			/**
			 * Acción que se ejecuta cuando cambia una fecha
			 */
			{$id_js}.evt__dia__procesar = function(es_inicial, fila)
			{
				if (! es_inicial)  {
					var dia = this.ef('dia').ir_a_fila(fila).get_estado();
					if (this.ef('dia').validar() && dia != '') {
						//Hago la peticion de datos al server, la respuesta vendra en el método this.validar_dia
						//Como caso especial se le pasa en el ultimo parametro la fila sobre la cual quiero consultar asi la recuerda en la respuesta 
						this.controlador.ajax('validar_dia_habil', dia, this, this.validar_dia, fila)
					}
				}
			}

			/**
			 * Acción cuando vuelve la respuesta desde PHP
			 */
			{$id_js}.validar_dia = function(resultado, fila)
			{
				var ef = this.ef('dia').ir_a_fila(fila);
				var es_valido = resultado['es_valido'];
				if (! es_valido) {
					ef.set_error(resultado['mensaje']);
					this.set_ef_valido(ef, false);
				}
			}
	
			//-- Funcion auxiliar para cargar datos desde la ayuda
			function ejemplo_cambiar_fecha(nueva) {
				{$id_js}.ef('dia').ir_a_fila({$id_js}._filas[0]).set_estado(nueva);
			}

		";
	}
	
	
}

?>