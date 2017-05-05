<?php

class eiform_ap extends toba_ei_formulario
{
	protected $js_abrir = '';
	
	function set_js_abrir($js)
	{
		$this->js_abrir = $js;
	}
		
	function extender_objeto_js() 
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
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
			if( flag == 3 ){
				this.ef('ap_clase').mostrar();
				this.ef('ap_archivo').mostrar();
				this.ef('punto_montaje').mostrar();
				if (this.get_boton('extender_ap')) {
					this.mostrar_boton('extender_ap');
				}				
			}else{
				this.ef('ap_clase').ocultar(true);
				this.ef('ap_archivo').ocultar(true);
				this.ef('punto_montaje').ocultar(true);
				if (this.get_boton('extender_ap')) {
					this.ocultar_boton('extender_ap');
				}				
			}
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
		{$id_js}.evt__abrir_php = function () {
			$this->js_abrir;
			return false;
		}
		";
	}
}
?>