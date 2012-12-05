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
		echo "</body>\n";
		echo "</html>\n";
	}	
	
	protected function cabecera_html()
	{
		echo "<!DOCTYPE html>\n";
		//-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/
		echo '<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="es"> <![endif]-->';
		echo '<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="es"> <![endif]-->';
		echo '<!--[if IE 8]>    <html class="no-js lt-ie9" lang="es"> <![endif]-->';
		echo '<!--[if gt IE 8]><!--> <html class="no-js" lang="es"> <!--<![endif]-->';
		echo "<head>\n";
		echo "<title>".$this->titulo_pagina()."</title>\n";
		$this->encoding();
		$this->plantillas_css();
		$this->estilos_css();
		toba_js::cargar_consumos_basicos();
		echo "</head>\n";
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
		if (toba::proyecto()->get_parametro('es_css3')) {		
			echo "<link rel='stylesheet' href='".toba_recurso::url_toba()."/js/formalize/stylesheets/formalize.css' />";
		} else {
			echo "
				<style type='text/css'>
	            #overlay, #capa_espera {
	                background-image:url('". toba_recurso::imagen_toba('nucleo/overlay.gif'). "');               
	            }
	            .barra-superior {
	                background: url('". toba_recurso::imagen_skin('barra-sup.gif') ."') repeat-x top;
	            }
	            .ei-cuadro-col-tit, .ei-ml-columna, .ei-filtro-columna {
	                background: url('". toba_recurso::imagen_skin('cuadro-col-titulo.gif') ."') repeat-x top;
	            }
	            .ei-barra-sup, .ci-botonera {
	                background: url('". toba_recurso::imagen_skin('barra-sup.gif') ."') repeat-x top;
	            }
	            .ci-tabs-h-lista {
	            	background: url('".toba_recurso::imagen_skin('tabs/bg.gif')."') repeat-x bottom;
	            }
	            .ci-tabs-h-solapa {
					background:url('".toba_recurso::imagen_skin('tabs/left.gif')."') no-repeat left top;	            
	            }
	            .ci-tabs-h-solapa a {				
	            	background:url('".toba_recurso::imagen_skin('tabs/right.gif')."') no-repeat right top;
	            }	            
	            .ci-tabs-h-solapa-sel {
					background:url('".toba_recurso::imagen_skin('tabs/left_on.gif')."') no-repeat left top;	            
	            }
	            .ci-tabs-h-solapa-sel a {				
	            	background:url('".toba_recurso::imagen_skin('tabs/right_on.gif')."') no-repeat right top;
	            }
				</style>
			";
		}
		echo "
		<style type='text/css'>
			#overlay, #capa_espera {
				background-image:url('". toba_recurso::imagen_toba('nucleo/overlay.gif'). "');     			
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
	
	function mostrar_resize_fuente()
	{
		echo '<div class="zoom-fuente">';
		echo '<a href="#" title="Ampliar fuente" onclick="ampliar_fuente();">';
		echo toba_recurso::imagen_toba('resize-icon-full.png', true, null, null, 'Ampliar fuente');
		echo '</a>';
		echo '<a href="#" title="Reducir fuente" onclick="reducir_fuente();">';
		echo toba_recurso::imagen_toba('resize-icon-small.png', true, null, null, 'Reducir fuente');
		echo '</a>';
		echo '</div>';
	}
	
}
?>
