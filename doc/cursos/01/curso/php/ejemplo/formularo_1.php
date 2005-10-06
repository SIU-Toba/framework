<?php
require_once('nucleo/browser/clases/objeto_ei_formulario.php'); 
//----------------------------------------------------------------
class formularo_1 extends objeto_ei_formulario
{

	function extender_objeto_js()
	{
	    echo "

			//Validacion en linea
			
	        {$this->objeto_js}.evt__ddi__validar = function() {			
	        	if(this.ef('ddi').valor() > 10){
	     			this.ef('ddi').set_error('Todo mal');
	     			return false;
	        	}
	        	return true;
	        }

			//Validacion GENERAL

	        {$this->objeto_js}.evt__validar_datos = function() {
				if(! (this.ef('nombre').valor()=='aaa') ){
		        	cola_mensajes.agregar('No!!!');
		        	return false;
				}
				return true;
	        }


			//Procesamiento de un EF
						
	        {$this->objeto_js}.evt__esuniversidad__procesar = function(es_inicial) {
         		if( this.ef('esuniversidad').chequeado() ){
					this.ef('ddi').ocultar();
         		}else{
					this.ef('ddi').mostrar();
         		}
	        }

	    "; 		
	}
}

?>