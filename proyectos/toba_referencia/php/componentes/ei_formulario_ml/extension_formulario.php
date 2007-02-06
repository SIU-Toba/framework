<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_formulario_ml extends toba_ei_formulario_ml
{

	function extender_objeto_js()
	{
		/**
		* 	Valida que la fecha sea un lunes
		*/
		echo  "
			/*{$this->objeto_js}.crear_fila_orig = {$this->objeto_js}.crear_fila;
			{$this->objeto_js}.crear_fila = function() {
				var hoy = new Date();
				var hoy_texto = hoy.getDate() + '/' + hoy.getMonth() + '/' + hoy.getFullYear();
				var nueva_fila = this._proximo_id;
				this.crear_fila_orig();
				this.ef('fecha').ir_a_fila(nueva_fila).set_estado(hoy_texto)
			};*/
			
			{$this->objeto_js}.evt__fecha__validar = function (fila) {
				var lunes = 1;
				var ef_fecha = this.ef('fecha').ir_a_fila(fila);
				var fecha = ef_fecha.fecha();
				if (fecha != null && fecha.getDay() != lunes) {
					ef_fecha.set_error('Sólo está permitido ingresar días lunes');
					return false;
				}
				return true;
			}
		";
		
		/**
		* 	Se escuchan los procesamientos
		*/
		echo "
			//Cuando se modifica el valor del importe, recalcula el total
			{$this->objeto_js}.evt__importe__procesar = function(es_inicial, fila) {
				{$this->objeto_js}.refrescar_importes(es_inicial, fila);
			}
			
			//Cuando se modifica el valor del descuento, recalcula el total			
			{$this->objeto_js}.evt__descuento__procesar = function(es_inicial, fila) {
				{$this->objeto_js}.refrescar_importes(es_inicial, fila);
			}

			//Se escucha el checkbox 'con_descuento', cuando esta chequeado habilita el campo descuento
			//Y recalcula los totales
			{$this->objeto_js}.evt__con_descuento__procesar = function(es_inicial, fila) {
				//¿Puede tener descuento?
				var ef_descuento = this.ef('descuento').ir_a_fila(fila);
				if (this.ef('con_descuento').ir_a_fila(fila).chequeado()) {
					ef_descuento.activar();
				}
				else { 
					ef_descuento.desactivar();
					ef_descuento.cambiar_valor(0);
			 	}
			}
		";
		
		/**
		* 	Funciones de lógica particular
		*/		
		echo "			
			{$this->objeto_js}.refrescar_importes = function(es_inicial, fila) {
				var importe = this.ef('importe').ir_a_fila(fila).valor();
				var descuento = this.ef('descuento').ir_a_fila(fila).valor();
				this.ef('neto').ir_a_fila(fila).cambiar_valor(importe - descuento);
			}
		";
	}	
	
	

}

?>