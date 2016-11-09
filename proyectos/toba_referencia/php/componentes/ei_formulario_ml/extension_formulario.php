<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_formulario extends toba_ei_formulario_ml
{

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo  "
			/**
			 *	Redefine la creación de una fila para agregarle valores por defecto
			 */
			{$id_js}.crear_fila_orig = {$id_js}.crear_fila;
			{$id_js}.crear_fila = function() {
				//Invoca a la creacion original
				var nueva_fila = this.crear_fila_orig();
				
				//Coloca los valores por defecto
				var hoy = new Date();
				var hoy_texto = hoy.getDate() + '/' + (hoy.getMonth() +1)  + '/' + hoy.getFullYear();
				this.ef('fecha').ir_a_fila(nueva_fila).set_estado(hoy_texto)
				
				return nueva_fila;
			};

			
			/**
			* 	Valida que la fecha no sea un lunes
			*/			
			{$id_js}.evt__fecha__validar = function (fila) {
				var lunes = 1;
				var ef_fecha = this.ef('fecha').ir_a_fila(fila);
				var fecha = ef_fecha.fecha();
				if (fecha != null && fecha.getDay() == lunes) {
					ef_fecha.set_error('No está permitido ingresar días lunes');
					return false;
				}
				return true;
			}

			/**
			 * Cuando se modifica el valor del importe, recalcula el total
			 */
			{$id_js}.evt__importe__procesar = function(es_inicial, fila) {
				{$id_js}.refrescar_importes(es_inicial, fila);
			}
			
			/**
			 * Cuando se modifica el valor del descuento, recalcula el total
			 */			
			{$id_js}.evt__descuento__procesar = function(es_inicial, fila) {
				{$id_js}.refrescar_importes(es_inicial, fila);
			}

			/**
			 * Se escucha el checkbox 'con_descuento', cuando esta chequeado habilita el campo descuento
			 * Y recalcula los totales
			 */
			{$id_js}.evt__con_descuento__procesar = function(es_inicial, fila) {
				//¿Puede tener descuento?
				var ef_descuento = this.ef('descuento').ir_a_fila(fila);
				if (this.ef('con_descuento').ir_a_fila(fila).chequeado()) {
					ef_descuento.activar();
				} else { 
					ef_descuento.desactivar();
					ef_descuento.cambiar_valor(0);
			 	}
			}
		
			/**
			* 	Refresca el importe neto de una fila
			*/		
			{$id_js}.refrescar_importes = function(es_inicial, fila) {
				var importe = this.ef('importe').ir_a_fila(fila).valor();
				var descuento = this.ef('descuento').ir_a_fila(fila).valor();
				this.ef('neto').ir_a_fila(fila).cambiar_valor(importe - descuento);
			}


			{$id_js}.evt__seleccionar_fila = function(fila) {
					//alert('Voy a seleccionar la fila ' + fila);
			}
		";
	}	
	
	

}

?>