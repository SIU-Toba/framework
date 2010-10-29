<?php

/**
* El tipo de página básico está pensado como clase base para las personalizaciones fuertes de la salida.
* Presenta la estructura básica que de la salida html del framework:
*  - Doctype
*  - Titulo de la pagina
*  - Codificacion
*  - Plantillas css
*  - Includes js básico
* 
* @package SalidaGrafica
*/
class toba_tp_basico extends toba_tipo_pagina
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
		if ( toba_editor::modo_prueba() ) {
			echo "<br>";
			$item = toba::solicitud()->get_datos_item('item');
			$accion = toba::solicitud()->get_datos_item('item_act_accion_script');
			toba_editor::generar_zona_vinculos_item($item, $accion);
		}		
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
		toba_js::cargar_consumos_basicos();
		echo "</HEAD>\n";
	}
	
	protected function titulo_pagina()
	{
		$item = toba::solicitud()->get_datos_item('item_nombre');
		return toba::proyecto()->get_parametro('descripcion_corta') . ' - ' . $item;
	}
	
	protected function encoding()
	{
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"/>\n";
	}

	protected function plantillas_css()
	{
		echo toba_recurso::link_css('toba', 'screen');
		echo toba_recurso::link_css('toba_impr', 'print');
		$ico = toba_recurso::imagen_proyecto('favicon.ico');
		echo '<link rel="icon" href="'.$ico.'" type="image/x-icon" /><link rel="shortcut icon" href="'.$ico.'" type="image/x-icon" />';		
	}
	
	protected function estilos_css()
	{
		echo "
		<link rel='stylesheet' href='".toba_recurso::url_toba()."/js/formalize/stylesheets/formalize.css' /> 		
		<style type='text/css'>
			#overlay, #capa_espera {
				background-image:url(". toba_recurso::imagen_toba('nucleo/overlay.gif'). ");     			
			}
			#barra_superior {
				display:none;
			}
		</style>			
		";
	}

	/**
	 * Crea el <body> y toba_recursos basicos. 
	 * Incluye un <div> que se propaga hasta el fin de la zona parte sup. 
	 */
	protected function comienzo_cuerpo()
	{
		$this->comienzo_cuerpo_basico();
		echo "<div class='{$this->clase_encabezado}'>";
	}
	
	protected function comienzo_cuerpo_basico()
	{
		echo "<body>\n";		
		$cerrar = toba_recurso::imagen_toba('nucleo/cerrar_ventana.gif', false);
		toba_js::cargar_consumos_globales(array('basicos/tipclick'));
		echo "\n<div id='overlay'>";
		echo "<div id='overlay_contenido'></div></div>";		
		$wait = toba_recurso::imagen_toba('wait.gif');
		echo "<div id='div_toba_esperar' class='div-esperar' style='display:none'>";
		echo "<img src='$wait' style='vertical-align: middle;' alt='' /> Procesando...";
		echo "</div>\n";
		
		$logo = toba_recurso::imagen_proyecto('logo.gif', false);
		echo "<div id='capa_espera'>
				<div>
					<img class='overlay-cerrar' title='Cerrar ventana' src='$cerrar' onclick='mostrar_esperar()' />
					<img src='$logo' /><p>Procesando. Por favor aguarde...</p><img src='$wait'>
				</div>
			</div>
		";
	}

	function barra_superior()
	{
		echo "<div id='barra_superior' class='barra-superior'>";		
	}
}
?>
