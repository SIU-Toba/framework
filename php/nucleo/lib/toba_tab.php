<?php
require_once('nucleo/lib/toba_boton.php');

class toba_tab extends toba_boton
{
	function get_html($id_submit, $id_componente, $clickeable=true, $estilo=null)
	{
		static $id_tab = 0;
		$id = $this->datos['identificador'];
		$estilo = isset($estilo) ? "style='$estilo'" : '';
		$contenido = '';
		$tab_order = manejador_tabs::instancia()->siguiente();		
		$img = $this->get_imagen();
		if(!isset($img)) {
			$img = gif_nulo(1, 16);
		}
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
			$html = $contenido;
		}

		$id_tab++;
		return $html;
	}	
}
?>