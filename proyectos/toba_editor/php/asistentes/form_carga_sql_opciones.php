<?php 
class form_carga_sql_opciones extends toba_ei_formulario
{
		
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$id_js}.evt__carga_origen__procesar = function(es_inicial)
		{
			if (this.ef('carga_origen').get_estado() == 'consulta_php') {
				this.ef('carga_php').mostrar();
				this.ef('carga_php_metodo').mostrar();
				if (! es_inicial) {
					this.evt__carga_php__procesar(false);
				}
			} else {
				this.ef('carga_php').ocultar();
				this.ef('carga_php_metodo').ocultar();
				this.ef('carga_php_metodo_nuevo').ocultar();
			}
		}
	
		{$id_js}.evt__carga_php__procesar = function(es_inicial)
		{
			if (! es_inicial) {
				this.evt__carga_php_metodo__procesar(false);
			}
		}
	
		{$id_js}.evt__carga_php_metodo__procesar = function(es_inicial)
		{
			if (this.ef('carga_origen').get_estado() == 'consulta_php') {
				if (this.ef('carga_php_metodo').get_estado() == apex_ef_no_seteado) {
					this.ef('carga_php_metodo_nuevo').mostrar();
				} else {
					this.ef('carga_php_metodo_nuevo').ocultar();
				}
			}
		}
		";
	}	
}

?>