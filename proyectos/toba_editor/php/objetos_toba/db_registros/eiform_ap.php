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
		{$this->objeto_js}.evt__ap__procesar = function () {
			var flag;
			flag = this.ef('ap').valor();
			if( flag == 0 ){
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