<?php 
class form_basico extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			//---- Procesamiento de EFs --------------------------------
		
			{$id_js}.evt__tabla__procesar = function(es_inicial)
			{
				if (! es_inicial && this.ef('tabla').get_estado() != apex_ef_no_seteado) {
					this.submit();
				}
			}		
		";
		if ( $this->controlador()->pantalla()->existe_dependencia('form_filas') ) {
			$id_ml = toba::escaper()->escapeJs($this->controlador()->dep('form_filas')->get_id_objeto_js());
			echo "
				{$id_js}.evt__gen_usa_filtro__procesar = function(es_inicial) 
				{
					if (! es_inicial) {
						if (this.ef('gen_usa_filtro').chequeado()) {
							$id_ml.mostrar_columna('en_filtro', true);		
							this.controlador.mostrar_tab('pant_filtro');							
						} else {
							$id_ml.mostrar_columna('en_filtro', false);
							this.controlador.ocultar_tab('pant_filtro');							
						}
					}
				}
			";
		}
	}
	
}

?>