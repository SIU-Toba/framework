<?php
require_once('objetos_toba/eiform_abm_detalle.php');
class ei_form_lista_eventos extends eiform_abm_detalle
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		parent::extender_objeto_js();
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__es_seleccion_multiple__procesar = function(es_inicial, fila)
		{
			if (! es_inicial) {
				//Reseteo todos los checkbox ya que solo 1 puede estar activo
				var filas = this.filas();
				var tildado = false;
				 for (id_fila in filas) {
					 tildado = this.ef('es_seleccion_multiple').ir_a_fila(filas[id_fila]).chequeado();
					 if ((filas[id_fila] != fila) && (tildado)) {
						this.ef('es_seleccion_multiple').ir_a_fila(filas[id_fila]).toggle(false);
					}
				 }
			}
		}	
		";
	}

}

?>