<?php 

class eiform_detalle_carga extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			{$id_js}.evt__estatico__procesar = function(inicial) {
				var cheq = this.ef('estatico').chequeado();
				this.ef('include').mostrar(cheq, true);
				this.ef('clase').mostrar(cheq, true);
			}
		";
		echo "						
			{$id_js}.obtener_nombre_clase = function(archivo) {
					var basename = archivo.replace( /.*\//, '' );
					return basename.substring(0, basename.lastIndexOf('.'));
			}
		";
		echo "						
			{$id_js}.evt__include__procesar = function(inicial) {
				if (!inicial && this.ef('clase').valor() == '') {
					var archivo = this.ef('include').valor();
					this.ef('clase').cambiar_valor( this.obtener_nombre_clase(archivo) );
				}
			}
		";
	}	
}

?>