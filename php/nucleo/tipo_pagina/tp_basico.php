<?php

class tp_basico extends tipo_pagina
{
	function encabezado()
	{
		$this->cabecera_html();
		$this->comienzo_cuerpo();
		$this->barra_superior();
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
		$estilo = info_proyecto::instancia()->get_parametro('estilo');
		echo recurso::link_css($estilo, "screen");
		echo recurso::link_css($estilo."_impr", "print");		
	}
	
	protected function estilos_css()
	{
		?>
		<style type="text/css">
			#dhtmltooltip{
				position: absolute;
				width: 150px;
				border: 1px solid black;
				padding: 2px;
				background-color: lightyellow;
				visibility: hidden;
				z-index: 100;
			}
			#dhtml_tooltip_div{
				position:absolute;
				width: 200px;
				visibility:hidden;
				background-color: lightyellow;			 
				padding: 2px;
				border: 1px solid black;
				line-height:18px;
				z-index:100;
			}			
			#div_calendario {
				visibility:hidden;
				position:absolute;
				background-color: white;
			}
			#overlay {
				background-image:url(<?=recurso::imagen_apl('overlay.gif', false);?>);     			
			}
		</style>			
		<?php
	}

	protected function comienzo_cuerpo()
	{
		echo "<body>\n";
		echo "\n<div id='overlay'><div id='overlay_contenido'></div></div>";		
		$img = recurso::imagen_apl('wait.gif');
		echo "<div id='div_toba_esperar' class='div-esperar' style='display:none'>";
		echo "<img src='$img' style='vertical-align: middle;'> Procesando...";
		echo "</div>";
		js::cargar_consumos_globales(array('basicos/dhtml_tooltip'));
		if ( editor::modo_prueba() ) {
			$item = toba::get_solicitud()->get_datos_item('item');
			editor::generar_zona_vinculos_item($item);
		}
	}

	protected function barra_superior()
	{
	}
}
?>