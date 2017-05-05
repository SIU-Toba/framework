<?php
class eiform_fuente_datos_esquemas extends toba_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
		//---- Procesamiento de EFs --------------------------------
		var estado_original = [];
		
		{$id_js}.evt__esquemas_manejados__procesar = function(es_inicial)
		{ 
			if (! es_inicial) {			
				this.actualizar_opciones_seleccionables(this.ef('esquemas_manejados'));
			}
		}
		
		{$id_js}.actualizar_opciones_seleccionables = function (padre)
		{
			var nuevas_opciones = estado_original;
			var seleccion_padre = padre.get_estado();
			if (seleccion_padre.length != '0') {
				var id = '';		
				nuevas_opciones = [];
				nuevas_opciones[apex_ef_no_seteado] = '';
				for (i in seleccion_padre) {
					id = seleccion_padre[i];	
					nuevas_opciones[id] = seleccion_padre[i];		
				}
			}
			
			this.ef('schema').borrar_opciones();
			this.ef('schema').set_opciones(nuevas_opciones);
		}
		//---- Procesamiento de EFs --------------------------------
		
		{$id_js}.evt__schema__procesar = function(es_inicial)
		{
			if (es_inicial) {
				var elem = this.ef('schema').input();
				var ident;  var i;	
				for ( i = 0; i < elem.length; i++) {
					ident = elem.options[i].value;
					estado_original[ident] = elem.options[i].text;
				}						
			}	
		}
		";
	}
}
?>