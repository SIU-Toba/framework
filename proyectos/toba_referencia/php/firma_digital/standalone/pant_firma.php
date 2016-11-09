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
		
 		$url_pdf = toba::vinculador()->get_url(null, "30000064", array('accion' => "descargar"), array(), false); //No necesita url_encode
		$url_pdf = $firmador->get_url_base_actual().$url_pdf;		
		
		$firmador->generar_applet($url_firmador, $url_descarga, $url_subir, "Prueba");
		$firmador->generar_visor_pdf(dirname($url_firmador).'/pdfobject.min.js', $url_pdf, "800px", "400px");
		
		echo toba_form::hidden("firmador_codigo", $firmador->generar_sesion());
	}
	
	function extender_objeto_js() {
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			{$id_js}.desactivar_boton('finalizar');
				
			function firmaOk() {
				{$id_js}.activar_boton('finalizar');
			}
		";
		
	}

}
?>