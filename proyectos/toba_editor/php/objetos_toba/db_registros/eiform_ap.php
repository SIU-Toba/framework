<?php
class eiform_ap extends toba_ei_formulario
{
	protected $js_abrir = ''; 
	protected $texto_recarga = null;
	
	function set_js_abrir($js)
	{
		$this->js_abrir = $js;
	}
	
	function set_modo_recarga($texto)
	{
		$this->texto_recarga = $texto;
	}
	
	function extender_objeto_js()
	{
		$escapador = toba::escaper();
		$id_js = $escapador->escapeJs($this->objeto_js);
		echo "
		{$id_js}.evt__ap_archivo__procesar = function(inicial) {
			if (!inicial && this.ef('ap_clase').valor() == '') {
				var archivo = this.ef('ap_archivo').valor();
				var basename = archivo.replace( /.*\//, '' );
				var clase = basename.substring(0, basename.lastIndexOf('.'));
				this.ef('ap_clase').cambiar_valor(clase);
			}
		}
		
		{$id_js}.evt__ap__procesar = function () {
			var flag;
			flag = this.ef('ap').valor();
			switch (flag) {
				case '0':
					this.ef('ap_clase').mostrar();
					this.ef('ap_archivo').mostrar();
					this.ef('modificar_claves').mostrar();
					this.ef('punto_montaje').mostrar();
					this.ef('tabla_ext').ocultar(true);
					this.ef('esquema_ext').ocultar(true);
					if (this.hay_cambios()) {
						this.set_evento(new evento_ei('cargar_tablas',true,''));
					}
					break;
				case '1':
					this.ef('ap_clase').ocultar(true);
					this.ef('ap_archivo').ocultar(true);
					this.ef('tabla_ext').ocultar(true);
					this.ef('esquema_ext').ocultar(true);
					this.ef('punto_montaje').ocultar(true);
					this.ef('modificar_claves').mostrar();
					if (this.hay_cambios()) {
						this.set_evento(new evento_ei('cargar_tablas',true,''));
					}
					break;
				case '4':
					this.ef('esquema_ext').mostrar();
					this.ef('tabla_ext').mostrar();					
					this.ef('modificar_claves').ocultar(true);
					this.ef('ap_clase').ocultar(true);
					this.ef('ap_archivo').ocultar(true);
					this.ef('punto_montaje').ocultar(true);
			}
		
			if (this.get_boton('extender_ap')) {
				if (flag == 0) {
					this.mostrar_boton('extender_ap');
				} else {
					this.ocultar_boton('extender_ap');
				}
			}			
		}
		
		{$id_js}.evt__abrir_php = function () {
			$this->js_abrir;
			return false;
		}
		
		{$id_js}.modificar_vinculo__ef_ap_archivo = function(id_vinculo)
		 {
			var estado = this.ef('punto_montaje').get_estado();
			vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
		}
		
		{$id_js}.modificar_vinculo__extender_ap = function(id_vinculo)
		{
			var estado = this.ef('punto_montaje').get_estado();
			vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
		}
		
		{$id_js}.evt__punto_montaje__procesar = function(inicial) {
			  if (!inicial) {
				  this.ef('ap_archivo').cambiar_valor('');
				  this.ef('ap_clase').cambiar_valor('');
			  }
		  }
		
		{$id_js}.evt__esquema__procesar = function(es_inicial)
		{
			if (! es_inicial) {
				this.set_evento(new evento_ei('modificacion', true, ''));		//Disparo el submit de manera adrede
			}
		}		
		
		{$id_js}.evt__esquema_ext__procesar = function(es_inicial)
		{
			if (! es_inicial) {
				this.set_evento(new evento_ei('modificacion', true, ''));		//Disparo el submit de manera adrede
			}
		}		

		";
		
		if ( isset($this->texto_recarga) ) {
			$usar_confirm = ($this->texto_recarga == '') ? 'true' : 'false';
			echo "
				{$id_js}.evt__tabla__procesar = function(es_inicial)
				{
					if (! es_inicial && this.ef('tabla').get_estado() != apex_ef_no_seteado) {
						if( $usar_confirm || confirm('". $escapador->escapeJs($this->texto_recarga)."')) {
							this.set_evento(new evento_ei('cargar_tablas',true,''));
						}
					}
				}
						
				{$id_js}.evt__tabla_ext__procesar = function(es_inicial)
				{
					if (! es_inicial && this.ef('tabla_ext').get_estado() != apex_ef_no_seteado) {
						if( $usar_confirm || confirm('". $escapador->escapeJs($this->texto_recarga)."')) {
							this.set_evento(new evento_ei('cargar_tablas',true,''));
						}
					}
				}
		";
		}
	}
}
?>