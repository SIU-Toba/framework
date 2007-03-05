<?php
require_once('nucleo/componentes/interface/toba_ei_formulario.php'); 
//----------------------------------------------------------------
class form_prop_basicas extends toba_ei_formulario
{

	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__menu__procesar = function() {
				if (this.ef('menu').chequeado())
					this.ef('orden').mostrar();
				else
					this.ef('orden').ocultar();
			}
			
			{$this->objeto_js}.evt__zona__procesar = function() {
				if (this.ef('zona').valor() != apex_ef_no_seteado) {
					this.ef('zona_listar').mostrar();
				} else {
					this.ef('zona_listar').ocultar();
				}
				this.evt__zona_listar__procesar();
			}
			
			{$this->objeto_js}.evt__zona_listar__procesar = function() {
				if (this.ef('zona_listar').chequeado()) {
					this.ef('zona_orden').mostrar();
				} else {
					this.ef('zona_orden').ocultar();				
				}
			}
			{$this->objeto_js}.evt__imagen_recurso_origen__procesar = function(inicial) {
				if (! inicial) {
					this.evt__imagen__procesar(inicial);
				}
			}
			{$this->objeto_js}.evt__imagen__procesar = function(inicial) {
				if (inicial) {
					this.ef('imagen').input().onkeyup = this.ef('imagen').input().onblur;
				}
				var prefijo = '';
				if (this.ef('imagen_recurso_origen').get_estado() == 'apex') {
					prefijo = toba_alias + '/';
				} else {
					prefijo = toba_proyecto_alias + '/';
				}
				var imagen_src = prefijo + 'img/' + this.ef('imagen').get_estado();
				$('editor_imagen_src').src= imagen_src;
			}
			
			
			{$this->objeto_js}.elegir_imagen = function(respuesta) {
				var callback =
				{
				  success: this.respuesta_listado ,
				  failure: toba.error_comunicacion,
				  scope: this
				}
				var parametros = {'imagen': this.ef('imagen').get_estado(),
								  'imagen_recurso_origen': this.ef('imagen_recurso_origen').get_estado()  };
				var vinculo = vinculador.crear_autovinculo('ejecutar', parametros);
				conexion.asyncRequest('GET', vinculo, callback, null);
				return true;
			}
			
			{$this->objeto_js}.respuesta_listado = function(resp) {
				notificacion.mostrar_ventana_modal('Seleccione la imagen',
								 resp.responseText, 'Cerrar','400px');
				
			}
			
			function seleccionar_imagen(path) {
				overlay();			
				{$this->objeto_js}.ef('imagen').set_estado(path);
				{$this->objeto_js}.evt__imagen__procesar(false);
			}
		";
	}
	
	function generar_input_ef($ef)
	{
		if ($ef == 'imagen') {
			$this->generar_input_ef('imagen_recurso_origen');	
		}		
		parent::generar_input_ef($ef);
		if ($ef == 'imagen') {
			$predeterminada = toba_recurso::imagen_toba('image-missing.png', false);
			echo " <button id='editor_imagen_preview'
						title='Elegir la imagen desde un listado' onclick='{$this->objeto_js}.elegir_imagen()'>
					<img id='editor_imagen_src' src='$predeterminada' onError='this.src=\"$predeterminada\"'/></button>";
		} 
	}
	
	function generar_html_ef($ef)
	{
		if ($ef != 'imagen_recurso_origen') {
			parent::generar_html_ef($ef);
		}	
	}
}

?>