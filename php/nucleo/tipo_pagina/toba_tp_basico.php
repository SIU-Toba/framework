<?php

class tp_basico extends tipo_pagina
{
	protected $clase_encabezado = '';
	
	function encabezado()
	{
		$this->cabecera_html();
		$this->comienzo_cuerpo();
		$this->barra_superior();
		//--- No se cierra el div de encabezado para dar lugar a la zona...
	}
	
	function pre_contenido(){}
	
	function post_contenido(){}

	function pie()
	{
		echo "</BODY>\n";
		echo "</HTML>\n";
	}	
	
	protected function cabecera_html()
	{
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
		echo "<HTML>\n";
		echo "<HEAD>\n";
		echo "<title>".$this->titulo_pagina()."</title>\n";
		$this->encoding();
		$this->plantillas_css();
		$this->estilos_css();
		js::cargar_consumos_basicos();
		echo "</HEAD>\n";
	}
	
	protected function titulo_pagina()
	{
		$item = toba::get_solicitud()->get_datos_item('item_nombre');
		return toba::get_hilo()->obtener_proyecto_descripcion() . ' - ' . $item;
	}
	
	protected function encoding()
	{
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"/>\n";
	}

	protected function plantillas_css()
	{
		$estilo = toba_proyecto::instancia()->get_parametro('estilo');
		echo toba_recurso::link_css($estilo, "screen");
		echo toba_recurso::link_css($estilo."_impr", "print");		
	}
	
	protected function estilos_css()
	{
		?>
		<style type="text/css">
			#overlay {
				background-image:url(<?=toba_recurso::imagen_apl('overlay.gif', false);?>);     			
			}
		</style>			
		<?php
	}

	/**
	 * Crea el <body> y toba_recursos basicos. 
	 * Incluye un <div> que se propaga hasta el fin de la zona parte sup. 
	 */
	protected function comienzo_cuerpo()
	{
		js::cargar_consumos_globales(array('basicos/tipclick'));
		echo "<body>\n";
		if ( editor::modo_prueba() ) {
			$item = toba::get_solicitud()->get_datos_item('item');
			editor::generar_zona_vinculos_item($item);
		}		
		echo "\n<div id='overlay'><div id='overlay_contenido'></div></div>";		
		$img = toba_recurso::imagen_apl('wait.gif');
		echo "<div id='div_toba_esperar' class='div-esperar' style='display:none'>";
		echo "<img src='$img' style='vertical-align: middle;'> Procesando...";
		echo "</div>";
		echo "<div class='{$this->clase_encabezado}'>";
	}

	protected function barra_superior()
	{
	}
}
?>