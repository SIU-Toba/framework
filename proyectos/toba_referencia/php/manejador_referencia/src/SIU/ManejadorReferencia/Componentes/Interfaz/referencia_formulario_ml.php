<?php
//namespace SIU\ManejadorReferencia\Componentes\Interfaz;

use SIU\InterfacesManejadorSalidaToba\Componentes\Interfaz\IFormularioMl;

class referencia_formulario_ml implements IFormularioMl{

	public function getConsumosJs(){
		return [
				'../siu/manejador_referencia/js/bt_formulario',
				'../siu/manejador_referencia/js/bt_formulario_ml'
		];
	}

	public function getPreLayout($id,$ancho,$alto,$scroll, $colapsado){
		$style = '';
		$colapsado = (isset($colapsado) && $colapsado) ? "display:none;" : "";
		if($colapsado){
			if ($alto != 'auto') {
				$style .= "overflow: auto; height: $alto;";
			}
		}
		return "<div class='form-horizontal' style='$colapsado $style' id='$id'>"; //Comienza el formulario
	}

	public function getBotoneraExportacion($exportar_pdf, $exportar_xls, $pdf_js, $xls_js){
		$salida = '';
		if (($exportar_pdf || $exportar_xls)){
			$salida .= "<div class='btn-group' role='group'>";
			if ($exportar_pdf == 1) {
				$img = \toba_recurso::imagen_toba('extension_pdf.png', true);
				$salida .= "<a class='btn btn-default' href='javascript: $pdf_js' title='Exporta el listado a formato PDF'>$img</a>";
			}
			if ($exportar_xls== 1) {
				$img = \toba_recurso::imagen_toba('exp_xls.gif', true);
				$salida .= "<a class='btn btn-default' href='javascript: $xls_js' title='Exporta el listado a formato Excel (.xlsx)'>$img</a>";
			}
			$salida .= "</div>\n";
		}
		return $salida;
	}

	public function getBotoneraManejoFila($objeto_js,$tab, $agregar, $mostrar_agregar, $modo_agregar,$js_agregar, $borrar_enlinea, $js_borrar, $filas_agregar, $js_deshacer, $ordenar, $ordenar_linea, $js_subir, $js_bajar){
		$salida = "<div class='btn-group col-md-12' role='group'>"; // Inicio de botonera
		if ($agregar) {
			if ($mostrar_agregar) {
				if (! $modo_agregar[0]) {
					$img= '<span class="glyphicon glyphicon-plus"></span>';
					if ($modo_agregar[1] != '') {
						$img .= $modo_agregar[1];
					}
					$salida .= \toba_form::button_html("{$objeto_js}_agregar", $img,"onclick='$js_agregar'", $tab, '+', 'Crea una nueva fila');
				}
			}
			if (! $borrar_enlinea) {
				$img= '<span class="glyphicon glyphicon-minus"></span>';
				$salida .= \toba_form::button_html("{$objeto_js}_eliminar", $img,"onclick='$js_borrar' disabled", $tab, '-', 'Elimina la fila seleccionada');
			}
		}

		if ($filas_agregar ) {		//Si se pueden agregar o quitar filas, el deshacer debe estar
			$html = "<span class='glyphicon glyphicon-refresh' id='{$objeto_js}_deshacer_cant'></span>";
			$salida .= \toba_form::button_html("{$objeto_js}_deshacer", $html,"onclick='$js_deshacer' disabled", $tab, 'z', 'Deshace la Última eliminación');
		}

		if ($ordenar && !$ordenar_linea) {
			$arriba = '<span class="glyphicon glyphicon-arrow-up"></span>';;
			$abajo = '<span class="glyphicon glyphicon-arrow-down"></span>';;
			$salida .= \toba_form::button_html("{$objeto_js}_subir", $arriba,"onclick='$js_subir' disabled ", $tab, '<', 'Sube una posición la fila seleccionada');
			$salida .= \toba_form::button_html("{$objeto_js}_bajar", $abajo,"onclick='$js_bajar' disabled ", $tab, '>', 'Baja una posición la fila seleccionada');
		}
		$salida .= "</div>\n"; // Fin de botonera
		return $salida;
	}

	public function getInicioLayout($ancho){
		$salida = "<div class='table-resposive'>";
		$salida .= "	<table class='table table-condensed'>";
		return $salida;
	}

	public function getInicioBody(){
		return "<tbody>";
	}

	/******Inicio Metodos cabecera*****/
	public function getInicioCabecera($id, $enumerar_filas){
		$salida = "<thead id='$id' >\n";
		$salida .= "<tr>\n";
		if ($enumerar_filas) {
			$salida .= "<th>#</th>\n";
		}
		return $salida;
	}

	public function getInicioColumnaCabecera($id, $class, $extra, $es_evento,$post_tag){
		return "<th id='$id'>\n";
	}

	public function getEtiquetaColumna($estilo, $obligatorio, $editor,$etiqueta, $descripcion){

		$marca = $obligatorio?'(*)':'';
		if ($estilo == '') {
			if ($obligatorio) {
				$estilo = 'ei-ml-etiq-oblig';
			} else {
				$estilo = 'ei-ml-etiq';
			}
		}
		$desc = $descripcion;
		if ($desc !=""){
			$desc = \toba_recurso::imagen_toba("descripcion.gif",true,null,null,$desc);
		}
		return "<span class='$estilo'>$etiqueta $marca $editor $desc</span>\n";
	}

	public function getInputToggle($id, $js_toggle){
		return "<input id='$id' type='checkbox' class='ef-checkbox' onclick='$js_toggle' />";
	}

	public function getFinColumnaCabecera(){
		return "</th>";
	}

	public function getFinCabecera(){
		$salida = "</tr>\n";
		$salida .= "</thead>\n";
		return $salida;
	}

	/******Fin Metodos cabecera*****/



	/*****Inicio metodos filas*****/
	public function getInicioFila($id, $js_seleccion,$class, $style_fila,$numerar, $numero_fila, $id_numerar ){
		$salida = "<tr $style_fila id='$id' onclick='$js_seleccion'>";
		if ($numerar) {
			$salida .= "<td><span id='$id_numerar'>$numero_fila</span></td>\n";
		}
		return $salida;
	}

	public function getFormateoCelda($clase, $es_evento, $contenido){
		$clase .= $es_evento ? ' celda-evento': '';

		$salida = "<td class='$clase'>";
		$salida .= $contenido;
		$salida .= "</td>";
		return $salida;
	}

	public function getFormateoEventos($estilo_celda,$html_eventos){
		$salida = "<td class='celda-evento'>";
		foreach ($html_eventos as $evento){
			$salida .= $evento;
		}
		$salida .= "</td>";
		return $salida;
	}

	public function getBotoneraOrdenarLinea($id, $estilo_actual, $js_subir, $js_bajar,$fila){
		$arriba = '<span class="glyphicon glyphicon-arrow-up"></span>';;
		$abajo = '<span class="glyphicon glyphicon-arrow-down"></span>';;
		$salida = "<td class='$estilo_actual ei-ml-fila-ordenar celda-evento'>\n";
		$salida .= "<a href='javascript: $js_subir' id='{$id}_subir$fila' style='visibility:hidden' title='Subir la fila'>$arriba</a>";
		$salida .= "<a href='javascript: $js_bajar' id='{$id}_bajar$fila' style='visibility:hidden' title='Bajar la fila'>$abajo</a>";
		$salida .= "</td>\n";
		return $salida;
	}

	public function getFinFila(){
		return "</tr>\n";
	}

	public function getInicioLayoutEf($id_form, $class){
		$salida = "<td id='cont_$id_form'>\n";
		$salida .= "<div id='nodo_$id_form'>\n";
		return $salida;
	}

	public function getFinLayoutEf(){
		$salida = "</div>";
		$salida .= "</td>\n";
		return $salida;
	}



	/*****FIN metodos Filas********/
	public function getFinBody(){
		return  "</tbody>";
	}

	public function getPieFormulario($id_foot, $lista_ids, $cantidad_totales, $enumerar_filas, $cant_eventos_sobre_fila, $_colspan){
		$salida = "<tfoot id='$id_foot'>\n";
		//Defino la cantidad de columnas
		$colspan = count($lista_ids);
		$colspan += $_colspan;
		//------ Totales y Eventos------
		if($cantidad_totales>0){
			$salida .= "\n<tr  class='warning'>\n";
			if ($enumerar_filas) {
				$salida .= "<td>&nbsp;</td>\n";
			}
			foreach ($lista_ids as $id_form_total){
				$salida .= "<td id='$id_form_total'>&nbsp;\n";
				$salida .= "</td>\n";
			}
			//-- Eventos sobre fila
			$cant_sobre_fila = $cant_eventos_sobre_fila;
			if($cant_sobre_fila > 0){
				$salida .= "<td colspan='$cant_sobre_fila'>\n";
				$salida .= "</td>\n";
			}
			$salida .= "</tr>\n";
		}
		$salida .= "</tfoot>\n";
		return $salida;
	}

	public function getInicioBotoneraMl($class){
		return "<div class='col-md-12 divider  $class'>";
	}

	public function getBotonAgregarInferior($id, $js_agregar, $tab, $modo_agregar){
		$texto = "<span class='glyphicon glyphicon-plus'></span>";
		if ($modo_agregar != '') {
			$texto .= ' '.$modo_agregar[1];
		}
		return \toba_form::button_html("$id", $texto, "onclick='$js_agregar'", $tab , '+', 'Crea una nueva fila');
	}

	public function getFinBotoneraMl(){
		return "</div>";
	}

	public function getFinLayout(){
		$salida = "	</table>";
		$salida .= "\n</div>";
		return $salida;
	}

	public function getFinPreLayout(){
		return "</div>\n"; // Fin de formulario
	}

}
