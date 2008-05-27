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
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__tipo__procesar = function(es_inicial, fila)
		{
			var mostrar = (this.ef('tipo').ir_a_fila(fila).get_estado() == 'opciones');
			this.ef('opciones_es_multiple').ir_a_fila(fila).mostrar(mostrar);
			this.ef('opciones_ef').ir_a_fila(fila).mostrar(mostrar);
		}
		";
	}
}

?>