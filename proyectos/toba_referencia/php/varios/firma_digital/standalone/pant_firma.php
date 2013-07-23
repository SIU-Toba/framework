<?php
class pant_firma extends toba_ei_pantalla
{
	function generar_layout()
	{
		$firmador = get_firmador();
		$url_firmador = toba_recurso::url_proyecto()."/firmador_pdf/firmador.jar";
		$url_firmador = $firmador->get_url_base_actual().$url_firmador;
		
 		$url_descarga = toba::vinculador()->get_url(null, "30000064", array('accion' => "descargar"), array(), true);
		$url_descarga = $firmador->get_url_base_actual().$url_descarga;
		
		$url_subir = toba::vinculador()->get_url(null, "30000064", array('accion' => "subir"), array(), true);
		$url_subir = $firmador->get_url_base_actual().$url_subir;
		
		$firmador->generar_applet($url_firmador, $url_descarga, $url_subir, "Prueba", 500, 310);
		echo toba_form::hidden("firmador_codigo", $firmador->generar_sesion());
	}
	
	function extender_objeto_js() {
		echo "
			{$this->objeto_js}.desactivar_boton('finalizar');
				
			function firmaOk() {
				{$this->objeto_js}.activar_boton('finalizar');
			}
		";
		
	}

}
?>