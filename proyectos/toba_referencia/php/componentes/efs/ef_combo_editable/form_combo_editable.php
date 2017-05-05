<?php 

class form_combo_editable extends toba_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$id_js}.evt__pais__procesar = function(es_inicial)
		{
			this.ef('numero').set_estado(this.ef('pais').get_estado());
		}
		
		
		{$id_js}.evt__solo_lectura__procesar = function(es_inicial) {
			this.ef('pais').set_solo_lectura(this.ef('solo_lectura').chequeado());
		}
		
		{$id_js}.evt__mostrar__procesar = function(es_inicial) {
			this.ef('pais').mostrar(this.ef('mostrar').chequeado());
		}			
		
		{$id_js}.evt__resetear__procesar = function(es_inicial) {
			if (! es_inicial) {
				this.ef('pais').resetear_estado();
				this.ef('resetear').chequear(false, false);				
			}
		}	

		{$id_js}.evt__seleccionar__procesar = function(es_inicial) {
			if (! es_inicial) {
				this.ef('pais').seleccionar();
				this.ef('seleccionar').chequear(false, false);				
			}
		}
		
		{$id_js}.evt__estado__procesar = function(es_inicial) {
			if (! es_inicial) {
				this.ef('pais').set_estado(5);
				this.ef('estado').chequear(false, false);		
			}		
		}
		";
	}
}

?>