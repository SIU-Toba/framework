<?php
//namespace SIU\ManejadorReferencia\Componentes\Interfaz;

use SIU\InterfacesManejadorSalidaToba\Componentes\Interfaz\IElemento;

class referencia_ei implements IElemento{
	
	public function getHtmlDescripcion($mensaje, $tipo=null){
		
		if (! isset($tipo) || $tipo == 'info') {
			$imagen = '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>';
			$clase = 'alert-info';
		}
		if ($tipo== 'warning') {
			$imagen = '<span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>';
			$clase = 'alert-warning';
		}
		if ($tipo == 'error') {
			$imagen = '<span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span>';
			$clase = 'alert-danger';
		}
		$descripcion = \toba_parser_ayuda::parsear($mensaje);
		return "<div class='alert $clase' role='alert'>$imagen $descripcion</div>";
	}
	
	public function getInicioBarraSuperior($tiene_titulo, $botonera_sup, $class, $style){
		//if ( ! ($style == "" && $tiene_titulo )){
			return "<div class='panel-heading'>\n";
		//}
	}
	
	public function getContenidoBarraSuperior($titulo, $descripcion, $modo_tooltip, $colapsable, $colapsado_coherente, $objeto_js,$js_colapsado){
		$salida = $titulo;
		//---Barra de colapsado
		if ($colapsable && isset($objeto_js) && $colapsado_coherente) {
			$img_min = \toba_recurso::imagen_toba('nucleo/sentido_asc_sel.gif', false);
			$salida .= "<img class='ei-barra-colapsar' id='colapsar_boton_$objeto_js' src='$img_min' onclick='{$objeto_js}.cambiar_colapsado()'>";
		}
		return $salida;
	}
	
	public function getFinBarraSuperior(){
		return "</div>";
	}
	
	public function getInicioBotonera($class, $extra){
		return "<div class='$class'>";
	}
	
	public function getFinBotonera(){
		return "</div>";
		
	}
}
