<?php
class pant_edicion_serv_ofrecidos extends toba_ei_pantalla
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$escapador = toba::escaper();
		$id_form_par = $escapador->escapeJs($this->controlador()->dep('form_parametros')->get_id_objeto_js());
		$id_form = $escapador->escapeJs($this->controlador()->dep('form_basico')->get_id_objeto_js());
		
		echo $escapador->escapeJs($this->controlador()->objeto_js) . 
		".evt__validar_datos = function()
		{
			var hay_archivo = $id_form.ef('cert_file').tiene_estado();				
			var parametros = $id_form_par.get_datos().length;			
			var hay_parametros = (isset(parametros) && parametros != 0);
			if (hay_parametros && ! hay_archivo) {
				notificacion.agregar('Se debe especificar un certificado para los parametros');
				return false;
			} else if (hay_archivo && ! hay_parametros) {
				notificacion.agregar('Se debe especificar una forma de identificar el cliente');
				return false;
			}
			return true;
		}
		";
	}


}

?>