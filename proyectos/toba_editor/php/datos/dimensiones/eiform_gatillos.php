<?php 

class eiform_gatillos extends toba_ei_formulario_ml
{
	protected $tablas;
	protected $contexto_opuesto;

	function set_tablas_utilizadas($tablas, $contexto_opuesto)
	{
		$this->tablas = $tablas;
		$this->contexto_opuesto = $contexto_opuesto;
	}
	
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$escapador = toba::escaper();
		echo $escapador->escapeJs($this->objeto_js)
		.".evt__validar_datos = function()
		{
			var tablas = new Array();
			var repetidas = new Array();
			var filas = this.filas();
			// Control de filas repetidas
			for (var id_fila in filas) {
				var valor = this.ef('tabla_rel_dim').ir_a_fila(filas[id_fila]).get_estado();
				if( in_array(valor, tablas) ) {
					repetidas.push( valor );
				} else {
				    tablas.push( valor );
				}
			}
			
			// Control de valores duplicados con los datos del contexto
			var tablas_utilizadas = ".toba_js::arreglo($this->tablas).";
			var reutilizadas = new Array();
			for ( id_fila in tablas ) {
				if( in_array(tablas[id_fila], tablas_utilizadas) ) {
					reutilizadas.push( tablas[id_fila] );
				}
			}
			
			// Notificacion y validacion
			var ok = true;
			if( repetidas.length > 0 ) {
				notificacion.agregar('No es posible repetir GATILLOS dentro de una dimension. (' + repetidas.join(', ')  + ')' );
				ok = false;
			}
			if( reutilizadas.length > 0 ) {
				notificacion.agregar('Existen gatillos que ya fueron utilizados como GATILLOS ". $escapador->escapeJs($this->contexto_opuesto).". (' + reutilizadas.join(', ') + ')' );
				ok = false;
			}
			return ok;
		}
		";
	}
}
?>