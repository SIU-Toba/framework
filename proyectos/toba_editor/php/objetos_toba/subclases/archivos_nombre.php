<?php
class archivos_nombre extends toba_ei_archivos
{
	
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
			.".seleccionar_archivo = function(nombre) {
				var basename = nombre.replace( /.*\//, '' );
				var clase = basename.substring(0, basename.lastIndexOf('.'));
				this.controlador.dep('form_nombre').ef('nombre').set_estado(clase);
				this.controlador.dep('form_nombre').evt__nombre__procesar(true);
				this.controlador.dep('form_nombre').validar();
			}
		";
	}
}

?>