<?php 
require_once('objetos_toba/eiform_abm_detalle.php');

class ml_cols extends eiform_abm_detalle 
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		parent::extender_objeto_js();
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$id_js}.evt__tipo__procesar = function(es_inicial, fila)
		{
			var mostrar = (this.ef('tipo').ir_a_fila(fila).get_estado() == 'opciones');
			this.ef('opciones_es_multiple').ir_a_fila(fila).mostrar(mostrar);
			this.ef('opciones_ef').ir_a_fila(fila).mostrar(mostrar);
		}
		
		{$id_js}.evt__nombre__procesar = function(es_inicial, fila)
		{
			if (! es_inicial) {
				var ef_expresion = this.ef('expresion').ir_a_fila(fila);
				var ef_nombre = this.ef('nombre').ir_a_fila(fila);
				if (ef_nombre.tiene_estado() && !ef_expresion.tiene_estado()) {
					ef_expresion.set_estado(ef_nombre.get_estado());
				}
			}
		}		
		
		";
	}
}

?>