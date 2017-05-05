<?php 
//--------------------------------------------------------------------
class eiform_proyecto_confnucleo extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "						
			{$id_js}.obtener_nombre_clase = function(archivo) {
					var basename = archivo.replace( /.*\//, '' );
					return basename.substring(0, basename.lastIndexOf('.'));
			}
			";
		echo "
			{$id_js}.evt__pm_sesion__procesar = function(inicial) {
				if (!inicial) {
					this.ef('sesion_subclase_archivo').cambiar_valor('');
					this.ef('sesion_subclase').cambiar_valor('');
				}
			}

			{$id_js}.modificar_vinculo__ef_sesion_subclase_archivo = function(id_vinculo)
            {
				var estado = this.ef('pm_sesion').get_estado();
				vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
            }

			{$id_js}.evt__sesion_subclase_archivo__procesar = function(inicial) {
				if (!inicial && this.ef('sesion_subclase').valor() == '') {
					var archivo = this.ef('sesion_subclase_archivo').valor();
					this.ef('sesion_subclase').cambiar_valor( this.obtener_nombre_clase(archivo) );
				}
			}
			";
		echo "
			{$id_js}.evt__pm_usuario__procesar = function(inicial) {
				if (!inicial) {
					this.ef('usuario_subclase_archivo').cambiar_valor('');
					this.ef('usuario_subclase').cambiar_valor('');
				}
			}

			{$id_js}.modificar_vinculo__ef_usuario_subclase_archivo = function(id_vinculo)
            {
				var estado = this.ef('pm_usuario').get_estado();
				vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
            }
			
			{$id_js}.evt__usuario_subclase_archivo__procesar = function(inicial) {
				if (!inicial && this.ef('usuario_subclase').valor() == '') {
					var archivo = this.ef('usuario_subclase_archivo').valor();
					this.ef('usuario_subclase').cambiar_valor( this.obtener_nombre_clase(archivo) );
				}
			}
			";
		echo "
			{$id_js}.evt__pm_impresion__procesar = function(inicial) {
				if (!inicial) {
					this.ef('salida_impr_html_a').cambiar_valor('');
					this.ef('salida_impr_html_c').cambiar_valor('');
				}
			}

			{$id_js}.modificar_vinculo__ef_salida_impr_html_a = function(id_vinculo)
            {
				var estado = this.ef('pm_impresion').get_estado();
				vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
            }

			{$id_js}.evt__salida_impr_html_a__procesar = function(inicial) {
				if (!inicial && this.ef('salida_impr_html_c').valor() == '') {
					var archivo = this.ef('salida_impr_html_a').valor();
					this.ef('salida_impr_html_c').cambiar_valor( this.obtener_nombre_clase(archivo) );
				}
			}
			";
		echo "
			{$id_js}.evt__pm_contexto__procesar = function(inicial) {
				if (!inicial) {
					this.ef('ce_subclase_archivo').cambiar_valor('');
					this.ef('ce_subclase').cambiar_valor('');
				}
			}

			{$id_js}.modificar_vinculo__ef_ce_subclase_archivo = function(id_vinculo)
            {
				var estado = this.ef('pm_contexto').get_estado();
				vinculador.agregar_parametros(id_vinculo, {'punto_montaje': estado});
            }

			{$id_js}.evt__ce_subclase_archivo__procesar = function(inicial) {
				if (!inicial && this.ef('ce_subclase').valor() == '') {
					var archivo = this.ef('ce_subclase_archivo').valor();
					this.ef('ce_subclase').cambiar_valor( this.obtener_nombre_clase(archivo) );
				}
			}
			";
	}
}

?>