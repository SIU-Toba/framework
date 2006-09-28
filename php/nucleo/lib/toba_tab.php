<?php
require_once('nucleo/lib/toba_boton.php');

/**
 * Representa a un tab o solapa, ya sea vertical u horizontal
 * @package Componentes
 * @subpackage Eis
 */
class toba_tab extends toba_boton
{
	/**
	 * Retorna el HTML del tab
	 *
	 * @param string $tipo 'V' para vertical o 'H' para horizontal
	 * @param string $id_submit Id. unico del contenedor (utilizado para formar el id del tab)
	 * @param string $id_componente Id. de js del componente contenedor
	 * @param boolean $seleccionado
	 * @param string $editor
	 */
	function get_html($tipo, $id_submit, $id_componente, $seleccionado, $editor='')
	{
		if ( $this->anulado ) return null;
		if( ($tipo != 'V') && ($tipo != 'H') ) {
			throw new toba_error("Los tipos validos de TABS son 'V' y 'H'.");	
		}
		static $id_tab = 1;
		$evento = $this->datos['identificador'];
		$contenido = '';
		$tab_order = manejador_tabs::instancia()->siguiente();		
		$img = $this->get_imagen();
		if(!isset($img) && $tipo == 'H') {
			$img = gif_nulo(1, 16);
		}
		$contenido .= $img . ' ';
		$tip = $this->datos['ayuda'];
		$acceso = tecla_acceso( $this->datos['etiqueta'] );
		$contenido .= $acceso[0];
		$tecla = $acceso[1];
		if (!isset($tecla)&&($id_tab<10)) $tecla = $id_tab;
		$tip = str_replace("'", "\\'",$tip);			
		$acceso = toba_recurso::ayuda($tecla, $tip);
		$id = $id_submit.'_cambiar_tab_'.$evento;
		
		if ( $this->activado ) {
			$js = "onclick=\"{$id_componente}.ir_a_pantalla('$evento');return false;\"";
			$js_extra = '';
		} else {
			// Si el tab esta desactivado, le tengo que dejar en onclick en una propiedad para que el codigo JS la pueda usar.
			$js = '';
			$js_extra = toba_js::abrir();
			$js_extra .= "document.getElementById('$id').onclick_viejo = function(event) { {$id_componente}.ir_a_pantalla('$evento');return false;}";
			$js_extra .= toba_js::cerrar();
		}
		if( $tipo == 'H' ) {	//********************* TABs HORIZONTALES **********************
			if( $seleccionado ) {// -- Tab ACTUAL --
  				$estilo_li = 'background:url("'.toba_recurso::imagen_toba('tabs/left_on.gif').'") no-repeat left top;';
  				$estilo_a = 'background:url("'.toba_recurso::imagen_toba('tabs/right_on.gif').'") no-repeat right top;';
				$html = "<li class='ci-tabs-h-solapa-sel' style='$estilo_li'>$editor";
				$html .= "<a href='#' id='$id' style='$estilo_a' $acceso>$contenido</a>";
				$html .= "</li>";
			} else {
				$oculto = $this->oculto ? '; display: none' : '';
  				$estilo_li = 'background:url("'.toba_recurso::imagen_toba('tabs/left.gif').'") no-repeat left top;';
  				$estilo_a = 'background:url("'.toba_recurso::imagen_toba('tabs/right.gif').'") no-repeat right top;';
				$html = "<li  class='ci-tabs-h-solapa' style='$estilo_li $oculto'>$editor";
				$html .= "<a href='#' id='$id' style='$estilo_a' $acceso $js>$contenido</a>";
				$html .= "</li>";
				$html .= $js_extra;
			}
		} else {				// ********************* TABs VERTICALES ************************
			if( $seleccionado ) {// -- Tab ACTUAL --
				$html = "<div class='ci-tabs-v-solapa-sel'><div class='ci-tabs-v-boton-sel'>$editor ";
				$html .= "<div id='$id'>$contenido</div>";
				$html .= "</div></div>";
			} else {
				$oculto = $this->oculto ? "style='display: none'" : '';
				$html = "<div class='ci-tabs-v-solapa' $oculto >$editor ";
				$html .= "<a href='#' id='$id' $acceso $js>$contenido</a>";
				$html .= "</div>";
				$html .= $js_extra;
			}
		}
		$id_tab++;
		return $html;
	}	
}
?>