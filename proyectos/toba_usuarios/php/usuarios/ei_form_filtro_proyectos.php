<?php 
class ei_form_filtro_proyectos extends toba_ei_filtro
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Validacion de EFs -----------------------------------
		
		{$this->objeto_js}.evt__proyecto__procesar = function(es_inicial)
		{
			if (!es_inicial) {
				if (this.ef('proyecto').get_estado() == apex_ef_no_seteado) {
					js_form_2196_filtro_proyectos.set_evento(new evento_ei('cancelar', false, '' ));
				}else{
					js_form_2196_filtro_proyectos.set_evento(new evento_ei('filtrar', true, '' ));
				}
				js_form_2196_filtro_proyectos.submit();	
			}
		}
		";
	}
	
}

?>