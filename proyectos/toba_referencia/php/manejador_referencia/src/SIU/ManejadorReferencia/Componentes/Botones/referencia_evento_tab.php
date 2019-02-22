<?php
//namespace SIU\ManejadorReferencia\Componentes\Botones;

use SIU\InterfacesManejadorSalidaToba\Componentes\Botones\IEventoTab;

class referencia_evento_tab implements IEventoTab{

	public function getImagen($imagen, $imagen_recurso_origen,$tipo_tab){
		
		return \toba::output()->get('EventoTab',true)->getImagen($imagen, $imagen_recurso_origen,$tipo_tab);
	}

	public function getJs($seleccionado, $activado, $id_componente, $evento){
		return \toba::output()->get('EventoTab',true)->getJs($seleccionado, $activado, $id_componente, $evento);
	}
	
	public function getTabHorizontal($id, $acceso, $contenido, $js, $js_extra, $editor, $activado, $oculto, $seleccionado){
		$clase_tab = $seleccionado ?'active':'';
		$habilitar = $seleccionado ? 'data-toggle="tab"' : '';//Analizo si es un tab navegable
		$_oculto = $oculto ? 'display:none;':'';
		$html = "<li class='$clase_tab' style='$_oculto'>$editor";
		$html .= "<a  $habilitar aria-expanded='true' href='#' id='$id' $acceso $js>$contenido</a>";
		$html .= "</li>";
		if( ! $seleccionado ) {// -- Tab ACTUAL --
			$html .= $js_extra;
		} 
		return $html;
	}
	
	public function getTabVertical($id, $acceso, $contenido, $js, $js_extra, $editor, $activado, $oculto, $seleccionado){
		return $this->getTabHorizontal($id, $acceso, $contenido, $js, $js_extra, $editor, $activado, $oculto, $seleccionado);
		
	}
	
}
