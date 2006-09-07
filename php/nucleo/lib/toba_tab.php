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
	 * @param boolean $clickeable
	 * @param string $estilo Clase css
	 */
	function get_html($tipo, $id_submit, $id_componente, $clickeable=true, $estilo=null)
	{
		if( ($tipo != 'V') && ($tipo != 'H') ) {
			throw new toba_error("Los tipos validos de TABS son 'V' y 'H'.");	
		}
		static $id_tab = 1;
		$id = $this->datos['identificador'];
		$estilo = isset($estilo) ? "style='$estilo'" : '';
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
		
		if($clickeable) {
			$js = "onclick=\"$id_componente.ir_a_pantalla('$id');return false;\"";
			$id = $id_submit.'_cambiar_tab_'.$id;
			$html = "<a href='#' id='$id' $estilo $acceso $js>$contenido</a>";
		} else {
			if( $tipo == 'H' ) {
				$html = "<a href='#' id='$id' $estilo>$contenido</a>";
			} else {
				$html = $contenido;	
			}
		}

		$id_tab++;
		return $html;
	}	

}
?>