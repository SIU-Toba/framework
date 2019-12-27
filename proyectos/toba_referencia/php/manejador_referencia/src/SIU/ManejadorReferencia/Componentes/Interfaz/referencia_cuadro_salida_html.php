<?php

//namespace SIU\ManejadorReferencia\Componentes\Interfaz;

use SIU\InterfacesManejadorSalidaToba\Componentes\Interfaz\ICuadroSalidaHtml;

class referencia_cuadro_salida_html implements ICuadroSalidaHtml {
	public function getInicioHtml($info_cuadro) {
		return "<div class='panel panel-default inicio-html' >";
	}
	public function getIncioZonaColapsable($id, $clase, $estilo) {
		return "<div $estilo id='$id' class='zona-colapzable'>"; // -- INICIO zona COLAPSABLE del cuadro completo
	}
	public function getCabeceraHtml($info_cuadro, $objeto_js, $exportacion_excel_plano, $filas_disponibles_selector, $total_columnas) {
		$salida = " <div class='panel-body custom'>";
		$salida .= "<div class='pull-left'>";

		if (isset ( $info_cuadro ) && $info_cuadro ['exportar_pdf'] == 1) {
			$img = \toba_recurso::imagen_toba ( 'extension_pdf.png', true );
			$salida .= "<a href='javascript: $objeto_js.exportar_pdf()' title='Exporta el listado a formato PDF'>$img</a>";
		}
		if (isset ( $info_cuadro ) && $info_cuadro ['exportar_xls'] == 1) {
			// Si hay vista xls entonces se muestra el link comï¿½n y para exportar a plano
			if ($exportacion_excel_plano) {
				$img_plano = \toba_recurso::imagen_toba ( 'exp_xls_plano.gif', true );
				$salida .= "<a href='javascript: $objeto_js.exportar_excel_sin_cortes()' title='Exporta el listado a formato Excel sin cortes (.xlsx)'>$img_plano</a>";
			}
			$img = \toba_recurso::imagen_toba ( 'exp_xls.gif', true );
			$salida .= "<a href='javascript: $objeto_js.exportar_excel()' title='Exporta el listado a formato odioExcel (.xlsx)'>$img</a>";
		}
		if ($info_cuadro ["ordenar"]) {
			$img = \toba_recurso::imagen_toba ( 'ordenar.gif', true );
			$filas = \toba_js::arreglo ( $filas_disponibles_selector );
			$salida .= "<a href=\"javascript: $objeto_js.mostrar_selector($filas);\" title='Permite ordenar por múltiples columnas'>$img</a>";
		}
		if (trim ( $info_cuadro ["subtitulo"] ) != "") {
			$salida .= $info_cuadro ["subtitulo"];
		}
		$salida .= "</div>";
		$salida .= "</div>";
		return $salida;
	}

	public function getInicioContenido($estilo) {
		return "";
	}

	public function getInicioCuadro($existen_cortes,$nivel) {
		$salida = '';
		if(!$existen_cortes || isset($nivel)){
			$class = ($nivel>0)? 'col-md-'.(8-$nivel):'row';
			$salida .= "<div class='table-responsive $class'> \n";
			$salida .= "	<table class='table table-condensed table-hover table-bordered table-striped'> \n";
		}
		return $salida;
	}

	public function getCabeceraCuadro($columnas, $columnas_agrupadas) {
	}

	public function getInicioHeadCuadro($debe_mostrar_titulos_columnas_cc) {
		$salida = '';
		if($debe_mostrar_titulos_columnas_cc)
			$salida .= "<table class='table table-borded'>";
		$salida .= "<thead> \n";
		$salida .= "	<tr> \n";
		return $salida;
	}

	public function getCuadroCabeceraColumnaEvento($rowspan, $pre_columnas, $eventos_sobre_fila, $cuadro_id, $clase_editor_item) {
		$salida = '';
		// -- Eventos sobre fila
		if (count ( $eventos_sobre_fila ) > 0) {
			$minimo_uno = false;
			foreach ( $eventos_sobre_fila as $id => $evento ) {
				$minimo_uno = $minimo_uno || ! ($pre_columnas xor $evento->tiene_alineacion_pre_columnas ());
			}
			if ($minimo_uno) {
				$salida .= "<th>\n";
			}
			foreach ( $eventos_sobre_fila as $evento ) {
				$etiqueta = '&nbsp;';
				if ($evento->es_seleccion_multiple ()) {
					$etiqueta = $evento->get_etiqueta ();
				}

				/**
				 * Condiciones gobernantes:
				 * Evento con alineacion a Izquierda
				 * Se estan graficando eventos pre-columnas de datos
				 *
				 *
				 * El evento se grafica unicamente cuando se dan ambas condiciones o
				 * cuando no se cumple ninguna de las dos, logicamente eso seria:
				 * ((A || !B) && (!A || B)) lo cual es igual a un XOR negado.
				 */
				if (! ($pre_columnas xor $evento->tiene_alineacion_pre_columnas ())) {
					$salida .= \toba_editor::get_vinculo_evento ( $cuadro_id, $clase_editor_item, $evento->get_id () ) . "\n";
				}
			}
			if ($minimo_uno) {
				$salida .= "</th>\n";
			}
		}
		return $salida;
	}

	public function getCuadroCabeceraColumna($columna, $row_span, $ordenamieno) {
		$titulo = '';
		if (trim($columna["clave"]) != '' || trim($columna["vinculo_indice"])!="") {
			$titulo = $columna["titulo"];
		}
		return "<th $row_span>$titulo $ordenamieno</th>";
	}

	public function getCabeceraColumnasAgrupadas($nombre_grupo, $cantidad_columnas) {
		return "<th class='ei-cuadro-col-tit ei-cuadro-col-tit-grupo' colspan='$cantidad_columnas'>$nombre_grupo</th>";
	}

	public function getParseGrupoColumnas($columnas_agrupadas) {
		return "<tr>$columnas_agrupadas</tr>\n";
	}

	public function getInicioFila($estilo) {
		return "<tr>\n"; // Abro tag para la fila
	}

	public function getEstiloFila($seleccionada) {
		return ($seleccionada) ? 'ei-cuadro-fila-sel' : 'ei-cuadro-fila';
	}

	public function getCeldaCuadro($valor, $clase, $estilo, $js) {
		$salida = "<td $js>\n";
		if (trim ( $valor ) !== '') {
			$salida .= $valor;
		} else {
			$salida .= '&nbsp;';
		}
		$salida .= "</td>\n";
		return $salida;
	}

	public function getPreCeldaEvento($pre_columnas, $eventos_sobre_fila) {
		$minimo_uno = false;
		$clase_evento = "align=" . (! $pre_columnas ? "'right'" : "'center'");
		foreach ( $eventos_sobre_fila as $id => $evento ) {
			$minimo_uno = $minimo_uno || ! ($pre_columnas xor $evento->tiene_alineacion_pre_columnas ());
		}

		if ($minimo_uno) {
			return "<td >\n";
		}
		return '';
	}

	public function getCeldaEvento($id_fila, $clave_fila, $pre_columnas, $evento, $parametros, $id_form, $descripcion_resp_popup, $invocacion_evento_fila) {
		$salida = '';
		$grafico_evento = ! ($pre_columnas xor $evento->tiene_alineacion_pre_columnas ()); // Decido si se debe graficar el boton en este lugar (logica explicada en html_cuadro_cabecera_columna_evento)
		if ($grafico_evento) {
			$clase_alineamiento = ($evento->es_seleccion_multiple ()) ? 'col-cen-s1' : ''; // coloco centrados los checkbox si es multiple
			if ($evento->posee_accion_respuesta_popup ()) {
				$descripcion_popup = \toba_js::sanear_string ( $descripcion_resp_popup );
				$salida .= \toba_form::hidden ( $id_form . $id_fila . '_descripcion', \toba_js::sanear_string ( $descripcion_resp_popup ) ); // Podemos hacer esto porque no vuelve nada!
			}
			$salida .= $invocacion_evento_fila;
		}
		return $salida;
	}

	public function getPostCeldaEvento($pre_columnas, $eventos_sobre_fila) {
		$minimo_uno = false;
		foreach ( $eventos_sobre_fila as $id => $evento ) {
			$minimo_uno = $minimo_uno || ! ($pre_columnas xor $evento->tiene_alineacion_pre_columnas ());
		}

		if ($minimo_uno) {
			return "</td>\n";
		}
		return '';
	}

	public function getFinFila() {
		return "</tr>\n";
	}

	public function getCuadroPaginacion($objeto_js, $total_registros, $tamanio_pagina, $pagina_actual, $cantidad_paginas, $parametros, $eventos) {
		$salida = "<nav aria-label='pagination'> \n";
		$salida .= "<ul class='pager'> \n";
		if (isset ( $total_registros ) && ! ($tamanio_pagina >= $total_registros)) {
			// Calculo los posibles saltos
			// Primero y Anterior
			if ($pagina_actual == 1) {
				$anterior = '<li class="disabled"><a href="#"><span aria-hidden="true">&larr;</span> Anterior</a></li> ';
			} else {
				$evento_js = \toba_js::evento ( 'cambiar_pagina', $eventos ["cambiar_pagina"], $pagina_actual - 1 );
				$js = "$objeto_js.set_evento($evento_js);";
				$anterior = "<li ><a href='#' onclick=\"$js\"><span aria-hidden='true'>&larr;</span> Anterior</a></li>";
			}

			if ($pagina_actual == $cantidad_paginas) {
				$siguiente = "<li class='disabled'><a href='#'>Siguiente <span aria-hidden='true'>&rarr;</span></a></li> \n";
			} else {
				$evento_js = \toba_js::evento ( 'cambiar_pagina', $eventos ["cambiar_pagina"], $pagina_actual + 1 );
				$js = "$objeto_js.set_evento($evento_js);";
				$siguiente = "<li ><a href='#' onclick=\"$js\"> Siguiente <span aria-hidden='true'>&rarr;</span></a></li>";
			}

			$salida .= "$anterior Página \n";
			$js = "$objeto_js.ir_a_pagina(this.value);";
			$tamanio = ceil ( log10 ( $total_registros ) );

			$salida .= \toba_form::text ( $parametros ['paginado'], $pagina_actual, false, '', $tamanio, 'form-control input-pager', "onchange=\"$js\"" );

			$salida .= "</strong> de <strong>{$cantidad_paginas}</strong> $siguiente \n";
		}
		$salida .= "</ul> \n";
		$salida .= "</nav> \n";
		return $salida;
	}

	public function getSumarizacion($datos, $titulo, $ancho, $css, $profundidad, $html_pre, $html_post) {
		if (! empty($datos)) {
			foreach($datos as $desc => $valor) {
			return '<span>' . $desc. ' : ' . $valor . '</span>';
			}
		}
		return '';
	}

	public function getFinHeadCuadro($debe_mostrar_titulos_columnas_cc) {
		$salida = "	</tr> \n";
		$salida .= "</thead> \n";
		if ($debe_mostrar_titulos_columnas_cc) {
			$salida .= "</table>";
		} else {
			$salida .= "<tbody>\n";
		}
		return $salida;
	}

	public function getFinCuadro($existen_cortes,$nivel) {
		$salida = '';
		if(!$existen_cortes || isset($nivel)){
			$salida .= "		</tbody>\n";
			$salida .= "	</table>\n";
			$salida .= "</div> \n";
		}
		return $salida;
	}

	public function getBarraTotalRegistros($total_registros, $mostrar) {
		if ($mostrar) {
			$plural = $total_registros > 1 ? "s" : "";
			return "<h5 class='text-primary text-center'>Se encontraron <b> $total_registros </b> registro$plural</h5>";
		}
		return '';
	}

	public function getFinContenido() {
		return "";
	}

	public function getPie($info_cuadro) {
		return "";
	}

	public function getInicioBotonera($tiene_datos) {
	}

	public function getClaseBotonera($hay_datos, $superior) {
		return "row divider";
	}

	public function getFinBotonera($tiene_datos) {
	}

	public function getFinZonaColapsable() {
		return "</div>\n";
	}

	public function getFinHtml($info_cuadro) {
		return "</div>";
	}


	public function getInicioCuadroVacio($estilo, $editor) {
		return "<div class='panel panel-default' >\n";
	}

	public function getMensajeCuadroVacio($id, $ancho, $colapsado, $texto) {
		$salida ="<div class='ei-cuadro-scroll ei-cuadro-cuerpo' $colapsado id='$id'>\n";
		$salida .= "	<div class='panel-body text-danger text-center'>$texto</div>\n";
		$salida .= '</div>';
		return $salida;
	}

	public function getFinCuadroVacio() {
		return '</div>';
	}

	/**
	 * ****************************************************************************************************
	 */
	/**
	 * GENERACIÓN DE CORTES DE CONTROL
	 */
	/**
	 * ****************************************************************************************************
	 */
	public function getSelectorOrdenamiento($id_form, $objeto_js, $columnas)
	{
		$salida =  "<div id='{$id_form}_selector_ordenamiento' class='panel' style='display:none;'>";
		$salida .= $this->html_botonera_selector($objeto_js);
		$salida .= "<table  class='table table-condensed'>";
		$salida .= $this->html_cabecera_selector();
		$salida .= $this->html_cuerpo_selector($objeto_js, $columnas);
		$salida .=  '</table></div>';
		return $salida;
	}
	/**
	 *  Envia la botonera del selector
	 */
	protected function html_botonera_selector($objeto_js)
	{
		$js_subir = "{$objeto_js}.subir_fila_selector();";
		$js_bajar= "{$objeto_js}.bajar_fila_selector();";
		$arriba = '<span class="glyphicon glyphicon-arrow-up"></span>';
		$abajo = '<span class="glyphicon glyphicon-arrow-down"></span>';

		//Saco la botonera para subir/bajar filas
		$salida = "<div id='botonera_selector' class='celda-evento'>\n";
		$salida .= "<a href='javascript: $js_subir' id='{$objeto_js}_subir'  title='Sube una posición la fila seleccionada'>$arriba</a>";
		$salida .= "<a href='javascript: $js_bajar' id='{$objeto_js}_bajar'  title='Baja una posición la fila seleccionada'>$abajo</a>";
		$salida .= "</div>\n";
		return $salida;
	}

	/**
	 * Genera la cabecera con los titulos del selector
	 */
	protected function html_cabecera_selector()
	{
		return "<thead>
				<th>Activar</th>
				<th>Columna</th>
				<th  colspan='2'>Sentido</th>
			</thead>";
	}

	/**
	 *  Genera el cuerpo del selector
	 */
	protected function html_cuerpo_selector( $objeto_js, $columnas)
	{
		$cuerpo = '';
		foreach($columnas as $col) {
			if ($col['no_ordenar'] != 1) {
				//Saco el contenedor de la fila y un checkbox para seleccionar.
				$cuerpo .= "<tr id='fila_{$col['clave']}'  onclick=\"$objeto_js.seleccionar_fila_selector('{$col['clave']}');\" ><td>";
				$cuerpo .= 	\toba_form::checkbox('check_'.$col['clave'], null, '0','ef-checkbox', "onclick=\"$objeto_js.activar_fila_selector('{$col['clave']}');\" ");
				$cuerpo .= "</td><td> {$col['titulo']}</td><td>";

				//Saco el radiobutton para el sentido ascendente
				$id = $col['clave'].'0';
				$cuerpo .=  "<label  for='$id'><input type='radio' id='$id' name='radio_{$col['clave']}' value='asc'  disabled/>Ascendente</label>";
				$cuerpo .= '</td><td>' ;

				//Saco el radiobutton para el sentido descendente
				$id = $col['clave'].'1';
				$cuerpo .= "<label for='$id'><input type='radio' id='$id' name='radio_{$col['clave']}' value='des'  disabled/>Descendente</label>";
				$cuerpo .= '</td></tr>';
			}
		}
		$cuerpo .= "<tr><td colspan='4'>". \toba_form::button('botonazo', 'Aplicar' ,  "onclick=\"$objeto_js.aplicar_criterio_ordenamiento();\"", 'btn btn-default pull-right').'</td></tr>';
		return $cuerpo;
	}

	/**
	 * ****************************************************************************************************
	 */
	/**
	 * GENERACIÓN DE CORTES DE CONTROL
	 */
	/**
	 * ****************************************************************************************************
	 */
	public function getInicioNivel($modo) {
		return "<div class='row inicio-nivel'>\n";
	}

	public function getInicioCabeceraCC($colapsar, $profundidad, $total_columnas, $js_colapsar) {
		$class = " col-md-offset-" . ($profundidad - 1) . " col-md-" . (6 - $profundidad);
		$salida = "<div class='$class ei-cuadro-cc-colapsable' $js_colapsar>\n";
		$salida .= "	<div class='alert corte-$profundidad'>\n";
		return $salida;
	}

	public function getContenidoCabeceraCC($indice, $nodo) {
		$descripcion = $indice [$nodo ['corte']] ['descripcion'];
		$valor = implode ( ", ", $nodo ['descripcion'] );
		if (trim ( $descripcion ) != '') {
			$salida = $descripcion . ': <strong>' . $valor . '</strong>';
		} else {
			$salida = '<strong>' . $valor . '</strong>';
		}
		return $salida;
	}

	public function getFinCabeceraCC($colapsar, $profundidad, $total_columnas, $js_colapsar) {
		$salida = '';
		if ($colapsar)
			$salida .= "<span class='pull-right glyphicon glyphicon-sort'  $js_colapsar ></span>\n";
		$salida .= "	</div>\n";
		$salida .= "</div>\n";
		return $salida;
	}

	public function getInicioPieCC($tabla_datos_es_general) {
		if( $tabla_datos_es_general) {
			return "<div class='row'><div class='col-md-12'><table class='table table-condensed table-hover table-bordered'>";
		}
		return '';
	}

	public function getContenidoCabeceraPieCC($indice, $nodo) {
		$descripcion = $indice[$nodo['corte']]['descripcion'];
		$valor = implode(", ",$nodo['descripcion']);
		if (trim($descripcion) != '') {
			return 'Resumen ' . $descripcion . ': <strong>' . $valor . '</strong>';
		} else {
			return 'Resumen <strong>' . $valor . '</strong>';
		}
	}

	public function getCabeceraPieCC($contenido, $produndidad, $total_columnas) {
		$salida = '';
		$css_pie = 'ei-cuadro-cc-pie-nivel-' . $produndidad;
		$css_pie_cab = 'ei-cuadro-cc-pie-cab-nivel-'.$produndidad;
		if($contenido != ''){
			$salida .= "<tr><td class='$css_pie' colspan='$total_columnas'>\n";
			$salida .= "<div class='$css_pie_cab'>$contenido</div>";
			$salida .= "</td></tr>\n";
		}
		return $salida;
	}

	public function getContadorFilaCC($profundidad, $total_columnas, $etiqueta) {
		$class = ($profundidad>0)? 'col-md-'.(13-$profundidad):'row';
		return "<tr class='bg-warning'><td  class='text-center' colspan='$total_columnas'>$etiqueta</td></tr>";
	}

	public function getFinPieCC($tabla_datos_es_general) {
		if( $tabla_datos_es_general ) {
			return "</table></div></div>";
		}
		return '';
	}
	public function getCeldaAcumulador($valor, $clase, $estilo) {
		return "<td class='$clase' $estilo><strong>$valor</strong></td>\n";
	}


	public function getFinNivel($modo) {
		return "</div>\n";
	}
	public function getInicioZonaColapsableCC($id_unico, $estilo) {
		return "<div id='$id_unico' class='row'>";
	}
	public function getFinZonaColapsableCC() {
		return "</div>";
	}
	public function getColumnasRelleno($cantidad) {
		if ($cantidad > 0)
			return "<td></td>\n";
		return '';
	}
}
