<?php

class eiform_ap extends toba_ei_formulario
{
	protected $js_abrir='';
	
	function set_js_abrir($js)
	{
		$this->js_abrir = $js;
	}
		
	function extender_objeto_js() 
	{
		echo "
		{$this->objeto_js}.evt__ap_archivo__procesar = function(inicial) {
			if (!inicial && this.ef('ap_clase').valor() == '') {
				var archivo = this.ef('ap_archivo').valor();
				var basename = archivo.replace( /.*\//, '' );
				var clase = basename.substring(0, basename.lastIndexOf('.'));
				this.ef('ap_clase').cambiar_valor(clase);
			}
		}

		{$this->objeto_js}.evt__ap__procesar = function () {
			var flag;
			flag = this.ef('ap').valor();
			if( flag == 3 ){
				this.ef('ap_clase').mostrar();
				this.ef('ap_archivo').mostrar();
			}else{
				this.ef('ap_clase').ocultar(true);
				this.ef('ap_archivo').ocultar(true);
			}
		}

		{$this->objeto_js}.evt__abrir_php = function () {
			$this->js_abrir;
			return false;
		}
		";
	}
}
?>