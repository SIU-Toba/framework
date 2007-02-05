<?php 
//--------------------------------------------------------------------
class eiform_proyecto_confnucleo extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo "						
			{$this->objeto_js}.obtener_nombre_clase = function(archivo) {
					var basename = archivo.replace( /.*\//, '' );
					return basename.substring(0, basename.lastIndexOf('.'));
			}
			";
		echo "						
			{$this->objeto_js}.evt__sesion_subclase_archivo__procesar = function(inicial) {
				if (!inicial && this.ef('sesion_subclase').valor() == '') {
					var archivo = this.ef('sesion_subclase_archivo').valor();
					this.ef('sesion_subclase').cambiar_valor( this.obtener_nombre_clase(archivo) );
				}
			}
			";
		echo "						
			{$this->objeto_js}.evt__usuario_subclase_archivo__procesar = function(inicial) {
				if (!inicial && this.ef('usuario_subclase').valor() == '') {
					var archivo = this.ef('usuario_subclase_archivo').valor();
					this.ef('usuario_subclase').cambiar_valor( this.obtener_nombre_clase(archivo) );
				}
			}
			";
		echo "						
			{$this->objeto_js}.evt__salida_impr_html_a__procesar = function(inicial) {
				if (!inicial && this.ef('salida_impr_html_c').valor() == '') {
					var archivo = this.ef('salida_impr_html_a').valor();
					this.ef('salida_impr_html_c').cambiar_valor( this.obtener_nombre_clase(archivo) );
				}
			}
			";
		echo "						
			{$this->objeto_js}.evt__ce_subclase_archivo__procesar = function(inicial) {
				if (!inicial && this.ef('ce_subclase').valor() == '') {
					var archivo = this.ef('ce_subclase_archivo').valor();
					this.ef('ce_subclase').cambiar_valor( this.obtener_nombre_clase(archivo) );
				}
			}
			";
	}
}

?>