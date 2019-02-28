<?php
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
		$componente = toba::output()->get("EventoTab");
		
		if ( $this->anulado ) return null;
		if( ($tipo != 'V') && ($tipo != 'H') ) {
			throw new toba_error_def("Los tipos validos de TABS son 'V' y 'H'.");	
		}
		static $id_tab = 1;
		$evento = $this->datos['identificador'];
		$contenido = '';
		$tab_order = toba_manejador_tabs::instancia()->siguiente();		
		
		$image_resource = isset($this->datos['imagen_recurso_origen']) ? $this->datos['imagen_recurso_origen'] : null;
		$image_file = isset($this->datos['imagen']) ? $this->datos['imagen'] : null;
		$contenido .= $componente->getImagen($image_file, $image_resource, $tipo) . ' ';
		
		$tip = $this->datos['ayuda'];
		$acceso = tecla_acceso( $this->datos['etiqueta'] );
		$contenido .= $acceso[0];
		$tecla = $acceso[1];
		if (!isset($tecla)&&($id_tab<10)) {
			$tecla = $id_tab;
		}
		$tip = str_replace("'", "\\'",$tip);			
		$acceso = toba_recurso::ayuda($tecla, $tip);
		$id = $id_submit .'_cambiar_tab_'. $evento;
		//$js = "onclick=\"{$id_componente}.ir_a_pantalla('$evento');return false;\"";
		$js = $componente->getJs('','', $id_componente, $evento);
		$js_extra = '';				
		$oculto = $this->oculto ? '; display: none' : '';
		
		if( $tipo == 'H' ) {//********************* TABs HORIZONTALES **********************
			$html = $componente->getTabHorizontal($id, $acceso, $contenido, $js, $js_extra, $editor, $this->activado, $oculto, $seleccionado);
		} else {		// ********************* TABs VERTICALES ************************
			$html = $componente->getTabVertical($id, $acceso, $contenido, $js, $js_extra, $editor, $this->activado, $oculto, $seleccionado);
		}
		$id_tab++;
		return $html;
	}	
	
	
}
?>