<?php
//namespace SIU\ManejadorReferencia\Componentes\Interfaz;

use SIU\InterfacesManejadorSalidaToba\Componentes\Interfaz\IFormulario;

class referencia_formulario implements IFormulario{
	
	function getConsumosJs(){
		return ["../siu/manejador_referencia/js/bt_formulario"];
	}

	function getInicioHtml($class, $style){
		return "<div class='panel panel-default'>";
	}
	
	function getInicioFormulario($id, $style, $colapsado){
		return "<div class='form-horizontal panel-body' style='$colapsado' id='$id'>"; //Comienza el formulario
	}
	
	function getColapsadoTrigger($id,$js){
		$img = \toba_recurso::imagen_skin('expandir_vert.gif', false);
		$colapsado = "style='cursor: pointer; cursor: hand;' onclick=\"$js\" title='Mostrar / Ocultar'";
		$salida = "<div class='ei-form-fila ei-form-expansion col-md-12'>";
		$salida .= "<img class='center-block' id='$id' src='$img' $colapsado>";
		$salida .= "</div>";
		return $salida;
	}
	
	function getInicioEf($id, $esta_expandido, $resaltar, $seleccionado,$es_fieldset){
		
		$estilo_nodo =! $esta_expandido ? $estilo_nodo = "display:none" : "";
		$salida = '';
		if (! $es_fieldset) {							//Si es fieldset no puedo sacar el <div> porque el navegador cierra visualmente inmediatamente el ef.
			$salida .= "<div class='form-group col-md-12' style='$estilo_nodo' id='$id'>\n";
		}
		return $salida;
	}
	
	function getParseEtiqueta($etiqueta,$id,  $clase, $obligatorio,$ancho, $expandir_descripcion, $descripcion, $editor){
		$estilo = 'control-label ';
		$custom = $clase;
		$estilo .= ($custom == '')?'col-sm-2':$custom;
		$marca ='';
		
		if ($obligatorio) {
			$marca .= '(*)';
		} else {
			$estilo .= ' opcional';
		}
		
		$desc='';
		if (!isset($expandir_descripcion) || ! $expandir_descripcion) {
			$desc = $descripcion;
			if ($desc !=""){
				$desc = \toba_parser_ayuda::parsear($desc);
				$desc = "<span class='glyphicon glyphicon-pushpin' data-toggle='tooltip' data-placement='top' title='$desc'></span>";
			}
		}

		if(trim($etiqueta) == '')
			return "";
			
		return "<label class='$estilo' for='$id' >$editor $desc $etiqueta $marca </label>\n";
	}
	
	function getParseEf($ef_html, $id_ef, $ancho, $tiene_etiqueta, $con_etiqueta, $expandir_descripcion, $descripcion, $class){
		$salida = '';
		if ($tiene_etiqueta&& $con_etiqueta) {
			$clase = ($class == "toba_ef_html")?"col-md-10":"col-md-5";
			$salida .= "<div id='$id_ef' class='$clase'>\n";
			$salida .= $ef_html;
			$salida .= "</div>";
			if (isset($expandir_descripcion) && $expandir_descripcion) {
				$salida .= "<span class='ei-form-fila-desc'>$descripcion</span>";
			}
			return $salida;
		} else {
			return $ef_html;
		}
	}
	
	function getFinEf($es_fieldset){
		if (! $es_fieldset) {
			return "</div>\n";
		}
		return '';
	}
	
	function getFinFormulario(){
		return "</div>";
	}
	
	function getFinHtml(){
		return "</div>";
	}
}
