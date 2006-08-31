<?php 

class eiform_proyecto_confbasica extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
		{$this->objeto_js}.evt__log_archivo__procesar = function () {
			if( this.ef('log_archivo').chequeado() ){
				this.ef('log_archivo_nivel').mostrar();
			}else{
				this.ef('log_archivo_nivel').ocultar();
			}
		}";
	}
}
?>