<?php 
class form_nombre extends toba_ei_formulario
{

	/*function generar_input_ef($ef)
	{
		if ($ef == 'archivo') {
			$path = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()).'/php/';
			echo $path;
		}
		parent::generar_input_ef($ef);
	}*/

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$path = $this->controlador->get_path_relativo();
		$prefijo = $this->controlador->get_prefijo_clase();
		echo "
		{$this->objeto_js}.evt__nombre__validar = function()
		{
			var nombre = this.ef('nombre').get_estado();
            if (nombre.indexOf(' ') != -1) {
				this.ef('nombre').set_error('No puede contener espacios');
				return false;
            }
	        if (nombre.toLowerCase() .indexOf('.php') != -1) {
				this.ef('nombre').set_error('La clase no debe contener extension .php');
				return false;
            }         
            if (nombre == '$prefijo') {
				this.ef('nombre').set_error('Ingrese un nombre de clase válido');
				return false;            
			}   
            return true;
            		
		}
		
		//---- Procesamiento de EFs --------------------------------
		var path_base = '$path';
		{$this->objeto_js}.evt__nombre__procesar = function(es_inicial)
		{
			if (es_inicial) {
				var funcion = function() {
					var estado = {$this->objeto_js}.ef('nombre').get_estado();
					if (estado != '') {
						{$this->objeto_js}.ef('archivo').mostrar();
						var path_relativo = (path_base == '') ? 'php' : 'php/' + path_base;
						{$this->objeto_js}.ef('archivo').set_estado(path_relativo + '/' + estado + '.php');
					} else {
						{$this->objeto_js}.ef('archivo').ocultar();
					} 				
				};
				this.ef('nombre').input().onkeyup = funcion;
				funcion();
			}
		}
		";
	}
}

?>