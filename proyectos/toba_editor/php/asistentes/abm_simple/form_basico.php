<?php 
class form_basico extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$id_ml = $this->controlador()->dep('form_filas')->get_id_objeto_js();
		echo "
			//---- Procesamiento de EFs --------------------------------
		
			{$this->objeto_js}.evt__tabla__procesar = function(es_inicial)
			{
				if (! es_inicial) {
					this.submit();
				}
			}
			
			{$this->objeto_js}.evt__gen_usa_filtro__procesar = function(es_inicial)
			{
				if (! es_inicial) {
					if (this.ef('gen_usa_filtro').chequeado()) {
						$id_ml.mostrar_columna('en_filtro', true);		
					} else {
						$id_ml.mostrar_columna('en_filtro', false);
					}
				}
			}			
		";
	}
}

?>