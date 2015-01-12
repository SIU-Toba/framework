<?php

class toba_ei_cuadro_salida_html extends toba_ei_cuadro_salida
{

	//--------------------------------------------------------------------------------------------------------------------------------------------------//
	//														WRAPPERS DE TOBA_EI
	//--------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 * @ignore
	 */
	function get_html_barra_editor()
	{
		//La funcion esta definida en toba_ei
		$this->_cuadro->get_html_barra_editor();
	}

	/**
	 * @ignore
	 */
	function generar_html_barra_sup($titulo=null, $control_titulo_vacio=false, $estilo="")
	{
		//La funcion esta definida en toba_ei
		$this->_cuadro->generar_html_barra_sup($titulo, $control_titulo_vacio, $estilo);
	}

	/**
	 * @ignore
	 */
	function get_invocacion_evento_fila($evento, $fila, $clave_fila, $salida_como_vinculo = false, $param_extra = array())
	{
		return $this->_cuadro->get_invocacion_evento_fila($evento, $fila, $clave_fila, $salida_como_vinculo, $param_extra);
	}

	function generar_botones()
	{
		$this->_cuadro->generar_botones();
	}
	//--------------------------------------------------------------------------------------------------------------------------------------------------//
	//																	METODOS GENERALES
	//--------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 * @ignore
	 */
	protected function html_generar_campos_hidden()
	{
		$nombre_campos = $this->_cuadro->get_nombres_parametros();
		//Campos de comunicación con JS
		echo toba_form::hidden($nombre_campos['submit'], '');
		echo toba_form::hidden($nombre_campos['seleccion'], '');
		echo toba_form::hidden($nombre_campos['extra'], '');
		echo toba_form::hidden($nombre_campos['orden_columna'], '');
		echo toba_form::hidden($nombre_campos['orden_sentido'], '');
		echo toba_form::hidden($nombre_campos['orden_multiple'], '');

		//Genero los hidden para los eventos multiples
		$eventos_multiples = $this->_cuadro->get_nombres_eventos_multiples();
		foreach($eventos_multiples as $evento) {
			if ($evento != $nombre_campos['seleccion']) {	//El de seleccion ya se envia asi que no lo piso
				echo toba_form::hidden($evento, '');
			}
		}
	}

	/**
	 * @ignore
	 */
	function html_inicio()
	{
		$this->_cuadro->resetear_claves_enviadas();
		$id_js = $this->_cuadro->get_id_objeto_js();
		$total_col = $this->_cuadro->get_cantidad_columnas_total();
		$muestra_titulo_cc = $this->_cuadro->debe_mostrar_titulos_columnas_cc();
		$cuadro_colapsa = $this->_cuadro->es_cuadro_colapsado();

		$this->html_generar_campos_hidden();
		//-- Scroll y tabla Base
		$this->generar_tabla_base();
		echo $this->get_html_barra_editor();

		$this->generar_html_barra_sup(null, true,"ei-cuadro-barra-sup");

		//-- INICIO zona COLAPSABLE del cuadro completo
		$colapsado = ($cuadro_colapsa) ? "style='display:none'" : "";
		echo "<TABLE class='ei-cuadro-cuerpo' $colapsado id='cuerpo_$id_js'>";
		//------- Cabecera -----------------
		echo "<tr><td class='ei-cuadro-cabecera' colspan='$total_col'>";
		$this->html_cabecera();
		echo "</td></tr>\n";
		//--- INICIO CONTENIDO  -----
		echo "<tr><td class='ei-cuadro-cc-fondo' colspan='$total_col'>\n";
		// Si el layout es cortes/tabular se genera una sola tabla, que empieza aca
		if( $this->_cuadro->tabla_datos_es_general() ){
			$this->html_cuadro_inicio();
		}
		//-- Se puede por api cambiar a que los titulos de las columnas se muestren antes que los cortes
		if ($muestra_titulo_cc) {
			$this->html_cuadro_cabecera_columnas();
		}
	}

	protected function generar_tabla_base()
	{
		$info_cuadro = $this->_cuadro->get_informacion_basica_cuadro();
		//-- Scroll
		if($info_cuadro["scroll"]){
			$ancho = isset($info_cuadro["ancho"]) ? $info_cuadro["ancho"] : "";
			$alto = isset($info_cuadro["alto"]) ? $info_cuadro["alto"] : "auto";
			echo "<div class='ei-cuadro-scroll' style='height: $alto; width: $ancho; '>\n";
		}else{
			$ancho = isset($info_cuadro["ancho"]) ? $info_cuadro["ancho"] : "";
		}
		//-- Tabla BASE del cuadro
		$ancho = convertir_a_medida_tabla($ancho);
		echo "\n<table class='ei-base ei-cuadro-base' $ancho>\n";
		echo"<tr><td style='padding:0;'>\n";
	}

	/**
	 * @ignore
	 */
	function html_fin()
	{
		$acumulador = $this->_cuadro->get_acumulador_general();
		$info_cuadro = $this->_cuadro->get_informacion_basica_cuadro();
		if (isset($acumulador)) {
			$this->html_cuadro_totales_columnas($acumulador);
		}
		$this->html_acumulador_usuario();
		if( $this->_cuadro->tabla_datos_es_general() ){
			$this->html_cuadro_fin();
		}

		echo "</td></tr>\n";
		//--- FIN CONTENIDO  ---------
		// Pie
		echo"<tr><td class='ei-cuadro-pie'>";
		$this->html_pie();
		echo "</td></tr>\n";
		//Paginacion
		if ($info_cuadro["paginar"]) {
			echo"<tr><td>";
           	$this->html_barra_paginacion();
			echo "</td></tr>\n";
		}

		//Barra que muestra el total de registros disponibles
		$this->html_barra_total_registros();

		//Botonera
		if ($this->_cuadro->hay_botones() && $this->_cuadro->botonera_abajo()) {
			echo"<tr><td>";
			$this->generar_botones();
			echo "</td></tr>\n";
		}
		echo "</TABLE>\n";
		//-- FIN zona COLAPSABLE
		echo"</td></tr>\n";
		echo "</table>\n";
		if($info_cuadro["scroll"]){
			echo "</div>\n";
		}

		//Aca tengo que meter el javascript y el html del cosote para ordenar
		if ($info_cuadro["ordenar"]) {
			$this->html_selector_ordenamiento();
		}
	}

	/**
	 * Genera la cabecera del cuadro, por defecto muestra el titulo, si tiene
	 */
	protected function html_cabecera()
	{
		$info_cuadro = $this->_cuadro->get_informacion_basica_cuadro();
		$objeto_js = $this->_cuadro->get_id_objeto_js();
		
		if (isset($info_cuadro) && $info_cuadro['exportar_pdf'] == 1) {
			$img = toba_recurso::imagen_toba('extension_pdf.png', true);
			echo "<a href='javascript: $objeto_js.exportar_pdf()' title='Exporta el listado a formato PDF'>$img</a>";
		}
		if (isset($info_cuadro) && $info_cuadro['exportar_xls'] == 1) {
			//Si hay vista xls entonces se muestra el link común y para exportar a plano
			if ($this->_cuadro->permite_exportacion_excel_plano()) {
				$img_plano = toba_recurso::imagen_toba('exp_xls_plano.gif', true);
				echo "<a href='javascript: $objeto_js.exportar_excel_sin_cortes()' title='Exporta el listado a formato Excel sin cortes (.xls)'>$img_plano</a>";
			}
			$img = toba_recurso::imagen_toba('exp_xls.gif', true);
			echo "<a href='javascript: $objeto_js.exportar_excel()' title='Exporta el listado a formato Excel (.xls)'>$img</a>";
		}
		if ($info_cuadro["ordenar"]) {
			$img = toba_recurso::imagen_toba('ordenar.gif', true);
			$filas = toba_js::arreglo($this->_cuadro->get_filas_disponibles_selector());
			echo "<a href=\"javascript: $objeto_js.mostrar_selector($filas);\" title='Permite ordenar por múltiples columnas'>$img</a>";
		}
		if(trim($info_cuadro["subtitulo"])<>""){
			echo $info_cuadro["subtitulo"];
		}
	}

	/**
	 * Genera el pie del cuadro
	 */
	protected function html_pie()
	{
	}

	/**
	 * Genera el html que el cuadro muestra cuando no tiene datos cargados
	 * @param string $texto Texto a mostrar en base a la definición del componente
	 */
	function html_mensaje_cuadro_vacio($texto)
	{
		$this->_cuadro->resetear_claves_enviadas();
		$info_cuadro = $this->_cuadro->get_informacion_basica_cuadro();
		$colapsado = $this->_cuadro->es_cuadro_colapsado();
		$objeto_js = $this->_cuadro->get_id_objeto_js();
		
		$this->html_generar_campos_hidden();
		$ancho = isset($info_cuadro["ancho"]) ? $info_cuadro["ancho"] : "";
		//-- Tabla BASE
		$ancho = convertir_a_medida_tabla($ancho);
		echo "\n<table class='ei-base ei-cuadro-base' $ancho>\n";
		echo"<tr><td style='padding:0;'>\n";
		echo $this->get_html_barra_editor();
		$this->generar_html_barra_sup(null, true,"ei-cuadro-barra-sup");
		$ancho = isset($info_cuadro["ancho"]) ? $info_cuadro["ancho"] : "";
		$colapsado = (isset($colapsado) && $colapsado) ? "display:none;" : '';
		echo "<div class='ei-cuadro-scroll ei-cuadro-cuerpo' style='width: $ancho; $colapsado' id='cuerpo_$objeto_js'>\n";
		echo ei_mensaje($texto);
		if ($this->_cuadro->hay_botones() && $this->_cuadro->botonera_abajo()) {
			$this->generar_botones();
		}
		echo '</div></td></tr></table>';
	}

	/**
	 * Genera el HTML que contendra el selector de ordenamiento
	 */
	protected function html_selector_ordenamiento()
	{
		$id = $this->_cuadro->get_id_form();
		//Armo el div con el HTML
		echo "<div id='{$id}_selector_ordenamiento' style='display:none;'>";
		$this->html_botonera_selector();
		echo "<table class='tabla-0 ei-base ei-form-base ei-ml-grilla' width='100%'>";
		$this->html_cabecera_selector();
		$filas = $this->html_cuerpo_selector();
		echo '</table></div>';
	}

	/**
	 *  Envia la botonera del selector
	 */
	private function html_botonera_selector()
	{
		$objeto_js = $this->_cuadro->get_id_objeto_js();
		//Saco la botonera para subir/bajar filas
		echo "<div id='botonera_selector' class='ei-ml-botonera'>";
		echo toba_form::button_html("{$objeto_js}_subir", toba_recurso::imagen_toba('nucleo/orden_subir.gif', true),
								"onclick='{$objeto_js}.subir_fila_selector();'", 0, '<', 'Sube una posición la fila seleccionada');
		echo toba_form::button_html("{$objeto_js}_bajar", toba_recurso::imagen_toba('nucleo/orden_bajar.gif', true),
								"onclick='{$objeto_js}.bajar_fila_selector();' ", 0, '>', 'Baja una posición la fila seleccionada');
		echo '</div>';
	}

	/**
	 * Genera la cabecera con los titulos del selector
	 */
	private function html_cabecera_selector()
	{
		echo "<thead>
						<th class='ei-ml-columna'>Activar</th>
						<th class='ei-ml-columna'>Columna</th>
						<th class='ei-ml-columna' colspan='2'>Sentido</th>
				</thead>";
	}

	/**
	 *  Genera el cuerpo del selector
	 */
	private function html_cuerpo_selector()
	{
		$columnas = $this->_cuadro->get_columnas();
		$objeto_js = $this->_cuadro->get_id_objeto_js();
		
		$cuerpo = '';
		foreach($columnas as $col) {
			if ($col['no_ordenar'] != 1) {
				//Saco el contenedor de la fila y un checkbox para seleccionar.
				$cuerpo .= "<tr id='fila_{$col['clave']}'  onclick=\"$objeto_js.seleccionar_fila_selector('{$col['clave']}');\" class='ei-ml-fila'><td>";
				$cuerpo .= 	toba_form::checkbox('check_'.$col['clave'], null, '0','ef-checkbox', "onclick=\"$objeto_js.activar_fila_selector('{$col['clave']}');\" ");
				$cuerpo .= "</td><td> {$col['titulo']}</td><td>";

				//Saco el radiobutton para el sentido ascendente
				$id = $col['clave'].'0';
				$cuerpo .=  "<label class='ef-radio' for='$id'><input type='radio' id='$id' name='radio_{$col['clave']}' value='asc'  disabled/>Ascendente</label>";
				$cuerpo .= '</td><td>' ;

				//Saco el radiobutton para el sentido descendente
				$id = $col['clave'].'1';
				$cuerpo .= "<label class='ef-radio' for='$id'><input type='radio' id='$id' name='radio_{$col['clave']}' value='des'  disabled/>Descendente</label>";
				$cuerpo .= '</td></tr>';
			}
		}
		$cuerpo .= "<tr class='ei-botonera'><td colspan='4'>". toba_form::button('botonazo', 'Aplicar' ,  "onclick=\"$objeto_js.aplicar_criterio_ordenamiento();\"").'</td></tr>';
		echo $cuerpo;
	}

	//-------------------------------------------------------------------------------
	//-- Generacion de los CORTES de CONTROL
	//-------------------------------------------------------------------------------
	/**
	 * @ignore
	 */
	function html_cc_inicio_nivel()
	{
		if($this->_cuadro->get_cortes_modo() == apex_cuadro_cc_anidado){
			echo "<ul>\n";
		}
	}

	/**
	 * @ignore
	 */
	function html_cc_fin_nivel()
	{
		if($this->_cuadro->get_cortes_modo() == apex_cuadro_cc_anidado){
			echo "</ul>\n";
		}
	}

	function html_inicio_zona_colapsable($id_unico, $estilo)
	{
			echo "<table class='tabla-0' id='$id_unico' width='100%' $estilo><tr><td>\n";
	}

	function html_fin_zona_colapsable()
	{
			echo "</td></tr></table>\n";
	}

		 /**
	 *  Verifica que el nivel de profundidad no sea mayor a 2
	 *@param integer $profundidad
	 * @ignore
	 */
	protected function get_nivel_css($profundidad)
	{
		return ($profundidad > 2) ? 2 : $profundidad;
	}

	/**
		Genera la CABECERA del corte de control
	*/
	function html_cabecera_corte_control(&$nodo, $id_unico = null)
	{
		//Dedusco el metodo que tengo que utilizar para generar el contenido
		$metodo = 'html_cabecera_cc_contenido';
		$metodo_redeclarado = $metodo . '__' . $nodo['corte'];
		if(method_exists($this, $metodo_redeclarado)){
			$metodo = $metodo_redeclarado;
		}
		$nivel_css = $this->get_nivel_css($nodo['profundidad']);
		$class = "ei-cuadro-cc-tit-nivel-$nivel_css";
		if($this->_cuadro->get_cortes_modo() == apex_cuadro_cc_tabular) {
				$objeto_js = $this->_cuadro->get_id_objeto_js();
				$total_columnas = $this->_cuadro->get_cantidad_columnas_total();

				$js = "onclick=\"$objeto_js.colapsar_corte('$id_unico');\"";
				if ($this->_cuadro->debe_colapsar_cortes()) {
					echo "<table width='100%' class='tabla-0' border='0'><tr><td width='100%' $js class='$class ei-cuadro-cc-colapsable'>";
				} else {
					echo "<tr><td  colspan='$total_columnas' class='$class'>\n";
				}
				$this->$metodo($nodo);
				if ($this->_cuadro->debe_colapsar_cortes()) {
					$img = toba_recurso::imagen_toba('colapsado.gif', true, null, null, null, null, $js);
					echo "</td><td class='$class ei-cuadro-cc-colapsable impresion-ocultable'>$img</td></tr></table>";
				}else {
					echo "</td></tr>\n";
				}
		}else{
				echo "<li class='$class'>\n";
				$this->$metodo($nodo);
		}
	}

	/**
		Genera el CONTENIDO de la cabecera del corte de control
			Muestra las columnas seleccionadas como descripcion del corte separadas por comas
	*/
	protected function html_cabecera_cc_contenido(&$nodo)
	{
		$indice = $this->_cuadro->get_indice_cortes();
		$descripcion = $indice[$nodo['corte']]['descripcion'];
		$valor = implode(", ",$nodo['descripcion']);
		if (trim($descripcion) != '') {
			echo $descripcion . ': <strong>' . $valor . '</strong>';
		} else {
			echo '<strong>' . $valor . '</strong>';
		}
	}

	/**
	 * Genera el PIE del corte de control
	 * Estaria bueno que esto consuma primitivas para:
	 * 	- no pisarse con el contenido anidado.
	 * 	- reutilizar en la regeneracion completa.
	 * @ignore
	 */
	function html_pie_corte_control(&$nodo, $es_ultimo)
	{
		if($this->_cuadro->get_cortes_modo() == apex_cuadro_cc_tabular){				//MODO TABULAR
			$indice = $this->_cuadro->get_indice_cortes();
			$total_columnas = $this->_cuadro->get_cantidad_columnas_total();
			

			if( ! $this->_cuadro->tabla_datos_es_general() ) {
				echo "<table class='tabla-0 ei-cuadro-cc-resumen' width='100%'>";
			}

			//-----  Cabecera del PIE --------
			$this->html_cabecera_pie($indice, $nodo, $total_columnas);
			$nivel_css = $this->get_nivel_css($nodo['profundidad']);
			$css_pie = 'ei-cuadro-cc-pie-nivel-' . $nivel_css;
			//----- Totales de columna -------
			if (isset($nodo['acumulador'])) {
				$titulos = false;
				if($indice[$nodo['corte']]['pie_mostrar_titulos']) {
					$titulos = true;
				}
				$this->html_cuadro_totales_columnas($nodo['acumulador'],
													'ei-cuadro-cc-sum-nivel-'.$nivel_css,
													$titulos,
													$css_pie);
			}
			//------ Sumarizacion AD-HOC del usuario --------
			$this->html_sumarizacion_usuario($nodo, $total_columnas);
			//----- Contar Filas
			if($indice[$nodo['corte']]['pie_contar_filas']) {
				echo "<tr><td  class='$css_pie' colspan='$total_columnas'>\n";
				echo $this->etiqueta_cantidad_filas($nodo['profundidad']) . count($nodo['filas']);
				echo "</td></tr>\n";
			}
			//----- Contenido del usuario al final del PIE
			$this->html_pie_pie($nodo, $total_columnas, $es_ultimo);
			if( ! $this->_cuadro->tabla_datos_es_general() ) {
				echo "</table>";
			}
		}else{																//MODO ANIDADO
			echo "</li>\n";
		}
	}

	/**
	 * @ignore
	 * @param <type> $nodo
	 * @param <type> $total_columnas
	 */
	function html_cabecera_pie($indice, $nodo, $total_columnas)
	{
		$nivel_css = $this->get_nivel_css($nodo['profundidad']);
		$css_pie = 'ei-cuadro-cc-pie-nivel-' . $nivel_css;
		$css_pie_cab = 'ei-cuadro-cc-pie-cab-nivel-'.$nivel_css;
		if($indice[$nodo['corte']]['pie_mostrar_titular']) {
				$metodo_redeclarado = 'html_pie_cc_cabecera__' . $nodo['corte'];
				if(method_exists($this, $metodo_redeclarado)){
					$descripcion = $this->$metodo_redeclarado($nodo);
				}else{
				 	$descripcion = $this->html_cabecera_pie_cc_contenido($nodo);
				}
				echo "<tr><td class='$css_pie' colspan='$total_columnas'>\n";
				echo "<div class='$css_pie_cab'>$descripcion<div>";
				echo "</td></tr>\n";
		}
	}

	/**
	 * @ignore
	 * @param <type> $nodo
	 * @param <type> $total_columnas
	 */
	function html_pie_pie($nodo, $total_columnas, $es_ultimo)
	{
		$nivel_css = $this->get_nivel_css($nodo['profundidad']);
		$css_pie = 'ei-cuadro-cc-pie-nivel-' . $nivel_css;
		$metodo = 'html_pie_cc_contenido__' . $nodo['corte'];
		if(method_exists($this, $metodo)){
			echo "<tr><td  class='$css_pie' colspan='$total_columnas'>\n";
			$this->$metodo($nodo, $es_ultimo);
			echo "</td></tr>\n";
		}
	}

	/**
	 * @ignore
	 * @param <type> $nodo
	 */
	function html_sumarizacion_usuario($nodo, $total_columnas)
	{
		if(isset($nodo['sum_usuario'])) {
			$datos = array();
			$acumulador_usuario = $this->_cuadro->get_acumulador_usuario();
			$nivel_css = $this->get_nivel_css($nodo['profundidad']);
			$css = 'ei-cuadro-cc-sum-nivel-'.$nivel_css;
			$css_pie = 'ei-cuadro-cc-pie-nivel-' . $nivel_css;
			foreach($nodo['sum_usuario'] as $id => $valor) {
				$desc = $acumulador_usuario[$id]['descripcion'];
				$datos[$desc] = $valor;
			}
			echo "<tr><td  class='$css_pie' colspan='$total_columnas'>\n";
			$this->html_cuadro_sumarizacion($datos,null,300,$css);
			echo "</td></tr>\n";
		}
	}
	
	/**
	 * Retorna el CONTENIDO de la cabecera del PIE del corte de control
	 * Muestra las columnas seleccionadas como descripcion del corte separadas por comas
	 * @return string
	 * @ignore
	 */
	protected function html_cabecera_pie_cc_contenido(&$nodo)
	{
		$indice = $this->_cuadro->get_indice_cortes();
		$descripcion = $indice[$nodo['corte']]['descripcion'];
		$valor = implode(", ",$nodo['descripcion']);
		if (trim($descripcion) != '') {
			return 'Resumen ' . $descripcion . ': <strong>' . $valor . '</strong>';
		} else {
			return 'Resumen <strong>' . $valor . '</strong>';
		}
	}

	//-------------------------------------------------------------------------------
	//-- Generacion del CUADRO
	//-------------------------------------------------------------------------------

	/**
	 * Genera el html correspondiente a las filas del cuadro
	 */
	function html_cuadro(&$filas)
	{
		//Si existen cortes de control y el layout es tabular, el encabezado de la tabla ya se genero
		if( ! $this->_cuadro->tabla_datos_es_general() ){
			$this->html_cuadro_inicio();
		}
		//-- Se puede por api cambiar a que los titulos de las columnas se muestren antes que los cortes, en ese caso se evita hacerlo aqui
		if (! $this->_cuadro->debe_mostrar_titulos_columnas_cc()) {
			$this->html_cuadro_cabecera_columnas();
		}
		$par = false;
		$formateo = $this->_cuadro->get_instancia_clase_formateo('html');
		$layout_cant_columnas = $this->_cuadro->get_layout_cant_columnas();
		$i = 0;
		if (!is_null($layout_cant_columnas)) {
			echo "<tr>";
		}

		$columnas = $this->_cuadro->get_columnas();
		$datos = $this->_cuadro->get_datos();
		$objeto_js = $this->_cuadro->get_id_objeto_js();
		$evt_multiples = $this->_cuadro->get_eventos_multiples();

		foreach($filas as $f)
		{
			if (!is_null($layout_cant_columnas) && ($i % $layout_cant_columnas == 0)) {
				$ancho = floor(100 / (count($filas) / $layout_cant_columnas));
				echo "<td><table class='ei-cuadro-agrupador-filas' width='$ancho%' >";
			}
			$estilo_fila = $par ? 'ei-cuadro-celda-par' : 'ei-cuadro-celda-impar';
			$clave_fila = $this->_cuadro->get_clave_fila($f);

			//Genero el html de la fila, junto con sus eventos y vinculos
			$this->generar_layout_fila($columnas, $datos, $f, $clave_fila, $evt_multiples, $objeto_js, $estilo_fila, $formateo);
			$par = !$par;
			if (isset($layout_cant_columnas) && $i % $layout_cant_columnas == $layout_cant_columnas-1) {
				echo "</table></td>";
			}
			$i++;
		}
		
		if (isset($layout_cant_columnas)) {
			echo "</tr>";
		}
		if( ! $this->_cuadro->tabla_datos_es_general() ){
			$this->html_acumulador_usuario();
			$this->html_cuadro_fin();
		}
	}

	function get_estilo_seleccion($clave_fila)
	{
		$esta_seleccionada = $this->_cuadro->es_clave_fila_seleccionada($clave_fila);
		return ($esta_seleccionada) ? 'ei-cuadro-fila-sel' : 'ei-cuadro-fila';
	}

	function generar_layout_fila($columnas, $datos, $id_fila,  $clave_fila, $evt_multiples, $objeto_js, $estilo_fila, $formateo)
	{
		$estilo_seleccion = $this->get_estilo_seleccion($clave_fila);

		  //Javascript de seleccion multiple
		$js = $this->get_invocacion_js_eventos_multiples($evt_multiples, $id_fila, $objeto_js);

		 //---> Creo las CELDAS de una FILA <----
		echo "<tr class='$estilo_fila' >\n";

		//---> Creo los EVENTOS de la FILA  previos a las columnas<---
		$this->html_cuadro_celda_evento($id_fila, $clave_fila, true);
		foreach (array_keys($columnas) as $a) {
			//*** 1) Recupero el VALOR
			$valor = "";
			if(isset($columnas[$a]["clave"])) {
				if(isset($datos[$id_fila][$columnas[$a]["clave"]])) {
					$valor_real = $datos[$id_fila][$columnas[$a]["clave"]];
					//-- Hace el saneamiento para evitar inyección XSS
					if (!isset($columnas[$a]['permitir_html']) || $columnas[$a]['permitir_html'] == 0) {
						  $valor_real = texto_plano($valor_real);
					}
				}else{
					$valor_real = null;
					//ATENCION!! hay una columna que no esta disponible!
				}
				//Hay que formatear?
				if(isset($columnas[$a]["formateo"])) {
					$funcion = "formato_" . $columnas[$a]["formateo"];
					//Formateo el valor
					$valor = $formateo->$funcion($valor_real);
				} else {
					$valor = $valor_real;
				}
			}

			//*** 2) La celda posee un vinculo??
			if ($columnas[$a]['usar_vinculo'] )  {
					$valor = $this->get_html_cuadro_celda_vinculo($columnas, $a, $id_fila, $clave_fila, $valor);
			}

			//*** 3) Genero el HTML
			$ancho = "";
			if(isset($columnas[$a]["ancho"])) {
				$ancho = " width='". $columnas[$a]["ancho"] . "'";
			}

		  //Emito el valor de la celda
			echo "<td class='$estilo_seleccion ".$columnas[$a]["estilo"]."' $ancho $js>\n";
			if (trim($valor) !== '') {
				echo $valor;
			} else {
				echo '&nbsp;';
			}
			echo "</td>\n";
			//Termino la CELDA
		}
		//---> Creo los EVENTOS de la FILA <---
		$this->html_cuadro_celda_evento($id_fila, $clave_fila, false);
		echo "</tr>\n";
	}

	protected function get_invocacion_js_eventos_multiples($evt_multiples, $id_fila, $objeto_js)
	{
		$js = '';
		if ($this->_cuadro->hay_eventos_multiples()) {
			$lista_eventos_js = toba_js::arreglo($evt_multiples);
			$js = "onclick=\"$objeto_js.seleccionar('$id_fila', $lista_eventos_js);\" ";
		} 
		return $js;
	}
	
	protected function get_html_cuadro_celda_vinculo($columnas, $id_columna, $id_fila, $clave_fila, $valor_real)
	{
		// Armo el vinculo.
		$clave_columna = isset($columnas[$id_columna]['vinculo_indice']) ? $columnas[$id_columna]['vinculo_indice'] : $columnas[$id_columna]['clave'];
		$id_evt_asoc = $columnas[$id_columna]['evento_asociado'];		//Busco el evento asociado al vinculo
		$evento = $this->_cuadro->evento($id_evt_asoc);
		$parametros = $this->get_parametros_interaccion($id_fila, $clave_fila);
		$parametros[$clave_columna] = $valor_real;	//Esto es backward compatible
		$js =  $this->get_invocacion_evento_fila($evento, $id_fila, $clave_fila, true, $parametros);
		$valor = "<a href='#' onclick=\"$js\">$valor_real</a>";
		return $valor;
	}
	
	protected function html_cuadro_celda_evento($id_fila, $clave_fila, $pre_columnas)
	{
		foreach ($this->_cuadro->get_eventos_sobre_fila() as $id => $evento) {
			$grafico_evento = !($pre_columnas xor $evento->tiene_alineacion_pre_columnas());		//Decido si se debe graficar el boton en este lugar (logica explicada en html_cuadro_cabecera_columna_evento)
			if ($grafico_evento) {
					$parametros = $this->get_parametros_interaccion($id_fila, $clave_fila);
					$clase_alineamiento = ($evento->es_seleccion_multiple())?  'col-cen-s1' : '';	//coloco centrados los checkbox si es multiple
					echo "<td class='ei-cuadro-fila-evt $clase_alineamiento' width='1%'>\n";
					if ($evento->posee_accion_respuesta_popup()) {
						$descripcion_popup = toba_js::sanear_string($this->_cuadro->get_descripcion_resp_popup($id_fila));
						echo  toba_form::hidden($this->_cuadro->get_id_form(). $id_fila .'_descripcion', toba_js::sanear_string($this->_cuadro->get_descripcion_resp_popup($id_fila)));	//Podemos hacer esto porque no vuelve nada!
					}
					echo $this->get_invocacion_evento_fila($evento, $id_fila, $clave_fila, false, $parametros);	//ESto hay que ver como lo modifico para que de bien
					echo "</td>\n";
			}
		}
		//Se agrega la clave a la lista de enviadas
		$this->_cuadro->agregar_clave_enviada($clave_fila);
	}

	protected function get_parametros_interaccion($id_fila, $clave_fila)
	{
		if ($this->_cuadro->usa_modo_seguro()) {
			$parametros = array('fila_safe' => $clave_fila);
		} else {
			$parametros =  $this->_cuadro->get_clave_fila_array($id_fila);
		}
		return $parametros;
	}
		
	/**
	 *@ignore
	 */
	protected function html_cuadro_inicio()
	{
		echo "<TABLE width='100%' class='tabla-0' border='0'>\n";
	}

	/**
	 *@ignore
	 */
	protected function html_cuadro_fin()
	{
		echo "</TABLE>\n";
	}

		/**
	 * Genera la cabecera de las columnas del cuadro, colocando los titulos de las mismas
	 *@ignore
	 */
	protected function html_cuadro_cabecera_columnas()
	{
		//¿Alguna columna tiene título?
		$alguna_tiene_titulo = false;
		$columnas = $this->_cuadro->get_columnas();
		foreach(array_keys($columnas) as $clave) {
			if (trim($columnas[$clave]["titulo"]) != '') {
				$alguna_tiene_titulo = true;
				break;
			}
		}
		if ($alguna_tiene_titulo) {
			/*
			 * Verifico si el grupo tiene columnas visibles, sino no lo muestro,
			 * al mismo tiempo intersecto las columnas del grupo con las visibles para que no se expanda de mas el colspan.
			 */
			$hay_grupo_visible = false;
			$columnas_act_id = array_keys($columnas);
			$columnas_agrupadas = $this->_cuadro->get_columnas_agrupadas();
			foreach($columnas_agrupadas as $klave =>  $grupo) {
				foreach ($columnas_act_id as $a) {
					$hay_grupo_visible = ($hay_grupo_visible || in_array($a, $grupo));
				}
				$columnas_agrupadas[$klave] = array_intersect($grupo, $columnas_act_id);
			}

			$rowspan = ! $hay_grupo_visible ? '' : "rowspan='2'";
			$html_columnas_agrupadas = '';
			$grupo_actual = null;
			echo "<tr>\n";
			$this->html_cuadro_cabecera_columna_evento($rowspan, true);
			foreach (array_keys($columnas) as $a) {
				$html_columna = '';
				//El alto de la columna, si esta agrupada es uno sino es el general
				$rowspan_col = isset($columnas[$a]['grupo']) ? "" : $rowspan;

				if(isset($columnas[$a]["ancho"])){
					$ancho = " width='". $columnas[$a]["ancho"] . "'";
				}else{
					$ancho = "";
				}
				$estilo_columna = $columnas[$a]["estilo_titulo"];
				if(!$estilo_columna){
					$estilo_columna = 'ei-cuadro-col-tit';
				}
				$html_columna .= "<td $rowspan_col class='$estilo_columna' $ancho>\n";
				$html_columna .= $this->html_cuadro_cabecera_columna(    $columnas[$a]["titulo"],
											$columnas[$a]["clave"],
											$a );
				$html_columna .= "</td>\n";

				if (! isset($columnas[$a]['grupo']) || $columnas[$a]['grupo'] == '') {
					//Si no es una columna agrupada,saca directamente su html
					echo $html_columna;
					$grupo_actual = null;
				} else {
					//Guarda el html de la columna para sacarlo una fila mas abajo
					$html_columnas_agrupadas .= $html_columna;
					//Si es la primera columna de la agrupación saca un unico <td> del ancho de la agrupacion
					if (! isset($grupo_actual) || $grupo_actual != $columnas[$a]['grupo']) {
						$grupo_actual = $columnas[$a]['grupo'];
						$cant_col = count(array_unique($columnas_agrupadas[$grupo_actual]));		//Cuando se fija manualmente el grupo y se re procesa la definicion trae la misma columna + de una vez
						echo "<td class='ei-cuadro-col-tit ei-cuadro-col-tit-grupo' colspan='$cant_col'>$grupo_actual</td>";
					}
				}
			}
			//-- Eventos sobre fila
			$this->html_cuadro_cabecera_columna_evento($rowspan, false);
			echo "</tr>\n";
			//-- Columnas Agrupadas
			if ($html_columnas_agrupadas != '') {
				echo "<tr>\n";
				echo $html_columnas_agrupadas;
				echo "</tr>\n";
			}
		}
	}

	protected function html_cuadro_cabecera_columna_evento($rowspan, $pre_columnas)
	{
		 //-- Eventos sobre fila
		if($this->_cuadro->cant_eventos_sobre_fila() > 0) {
			foreach ($this->_cuadro->get_eventos_sobre_fila() as $evento) {
				$etiqueta = '&nbsp;';
				if ($evento->es_seleccion_multiple()) {
					$etiqueta = $evento->get_etiqueta();
				}

				/**
				 * Condiciones gobernantes:
				 *  Evento con alineacion a Izquierda
				 *  Se estan graficando eventos pre-columnas de datos
				 *
				 *
				 * El evento se grafica unicamente cuando se dan ambas condiciones o
				 * cuando no se cumple ninguna de las dos, logicamente  eso seria:
				 * ((A || !B) && (!A || B)) lo cual es igual a un XOR negado.
				 */
				if ( !($pre_columnas xor $evento->tiene_alineacion_pre_columnas())) {
					echo "<td $rowspan class='ei-cuadro-col-tit'>$etiqueta";
					if (toba_editor::modo_prueba()) {
						$info_comp = $this->_cuadro->get_informacion_basica_componente();
						echo toba_editor::get_vinculo_evento($this->_cuadro->get_id(), $info_comp['clase_editor_item'], $evento->get_id())."\n";
					}
					echo "</td>\n";
				}
			}
		}
	}
	
	/**
	 * Genera la cabecera de una columna
	 * @ignore
	 */
	protected function html_cuadro_cabecera_columna($titulo,$columna,$indice)
	{
		$salida = '';
		$eventos = $this->_cuadro->get_eventos();
		$columnas = $this->_cuadro->get_columnas();
		$objeto_js = $this->_cuadro->get_id_objeto_js();

		//--- ¿Es ordenable?
		if (	isset($eventos['ordenar'])
				&& $columnas[$indice]["no_ordenar"] != 1
				/*&& $this->_tipo_salida == 'html' */) {
			$sentido = array();
			$sentido[] = array('asc', 'Ordenar ascendente');
			$sentido[] = array('des', 'Ordenar descendente');
			$salida .= "<span class='ei-cuadro-orden'>";
			foreach($sentido as $sen){
				$sel="";
				if ($this->_cuadro->es_sentido_ordenamiento_seleccionado($columna, $sen[0])) {
					$sel = "_sel";//orden ACTIVO
				}

				//Comunicación del evento
				$parametros = array('orden_sentido'=>$sen[0], 'orden_columna'=>$columna);
				$evento_js = toba_js::evento('ordenar', $eventos['ordenar'], $parametros);
				$js = "$objeto_js.set_evento($evento_js);";
				$src = toba_recurso::imagen_toba("nucleo/sentido_". $sen[0] . $sel . ".gif");
				$salida .= toba_recurso::imagen($src, null, null, $sen[1], '', "onclick=\"$js\"", 'cursor: pointer; cursor:hand;');
			}
			$salida .= "</span>";
		}
		//--- Nombre de la columna
		if (trim($columna) != '' || trim($columnas[$indice]["vinculo_indice"])!="") {
			$salida .= $titulo;
		}
		//---Editor de la columna
		if ( toba_editor::modo_prueba()) {
			$item_editor = "1000253";
			$param_editor = array( apex_hilo_qs_zona => implode(apex_qs_separador,$this->_cuadro->get_id()),
									'columna' => $columna );
			$salida .= toba_editor::get_vinculo_subcomponente($item_editor, $param_editor);
		}
		return $salida;
	}

	 /**
	 * @ignore
	 */
	function html_acumulador_usuario()
	{
		$sumarizacion = $this->_cuadro->calcular_totales_sumarizacion_usuario();
		$total_columnas = $this->_cuadro->get_cantidad_columnas_total();
		
		if (! empty($sumarizacion)) {
			$css = 'cuadro-cc-sum-nivel-1';
			echo "<tr><td colspan='$total_columnas'>\n";
			$this->html_cuadro_sumarizacion($sumarizacion,null,300,$css);
			echo "</td></tr>\n";
		}
	}

	protected function html_cuadro_columnas_relleno($cantidad_columnas)
	{
		if($cantidad_columnas > 0) {
			echo "<td colspan='$cantidad_columnas'>&nbsp;</td>\n";
		}
	}
		
	  /**
	 * @ignore
	 */
	protected function html_cuadro_totales_columnas($totales,$estilo=null,$agregar_titulos=false, $estilo_linea=null)
	{
		$formateo = $this->_cuadro->get_instancia_clase_formateo('html');
		$columnas = $this->_cuadro->get_columnas();
		$cant_evt_fila = $this->_cuadro->cant_eventos_sobre_fila();

		//Calculo la cantidad de eventos pre y post columnas del cuadro
		$cant_evt_pre_columnas = 0;		
		if ($cant_evt_fila > 0) {
			foreach ($this->_cuadro->get_eventos_sobre_fila() as $evento) {
				if ( $evento->tiene_alineacion_pre_columnas()) {
					$cant_evt_pre_columnas++;
				}
			}
		}
		$cant_evt_restantes = $cant_evt_fila - $cant_evt_pre_columnas;
		
		//Agrego las cabeceras si es necesario
		$clase_linea = isset($estilo_linea) ? "class='$estilo_linea'" : "";
		if($agregar_titulos || (! $this->_cuadro->tabla_datos_es_general())) { 
			echo "<tr>\n";
			$this->html_cuadro_columnas_relleno($cant_evt_pre_columnas);
			foreach (array_keys($columnas) as $clave) {
			    if(isset($totales[$clave])){
					$valor = $columnas[$clave]["titulo"];
					echo "<td class='".$columnas[$clave]["estilo_titulo"]."'><strong>$valor</strong></td>\n";
				}else{
					echo "<td $clase_linea>&nbsp;</td>\n";
				}
			}
			//-- Eventos sobre fila
			$this->html_cuadro_columnas_relleno($cant_evt_restantes);
			echo "</tr>\n";
		}
		if ($totales !== null){
			echo "<tr class='ei-cuadro-totales'>\n";
			$this->html_cuadro_columnas_relleno($cant_evt_pre_columnas);
			foreach (array_keys($columnas) as $clave) {
				//Defino el valor de la columna
			    if(isset($totales[$clave])){
					$valor = $totales[$clave];
					if(!isset($estilo)){
						$estilo = $columnas[$clave]["estilo"];
					}
					//La columna lleva un formateo?
					if(isset($columnas[$clave]["formateo"])){
						$metodo = "formato_" . $columnas[$clave]["formateo"];
						$valor = $formateo->$metodo($valor);
					}
					echo "<td class='ei-cuadro-total $estilo'><strong>$valor</strong></td>\n";
				}else{
					echo "<td $clase_linea>&nbsp;</td>\n";
				}
			}
			//-- Eventos sobre fila
			$this->html_cuadro_columnas_relleno($cant_evt_restantes);
			echo "</tr>\n";
		}//if totales
	}

	//-------------------------------------------------------------------------------
	//-- Elementos visuales independientes
	//-------------------------------------------------------------------------------
    /**
     *  Genera el HTML correspondiente a la sumarizacion de los datos
     */
	protected function html_cuadro_sumarizacion($datos, $titulo=null , $ancho=null, $css='col-num-p1')
	{
		if(isset($ancho)) $ancho = "width='$ancho'";
		echo "<table $ancho class='ei-cuadro-cc-tabla-sum'>";
		//Titulo
		if(isset($titulo)){
			echo "<tr>\n";
			echo "<td class='ei-cuadro-col-tit' colspan='2'>$titulo</td>\n";
			echo "</tr>\n";
		}
		//Datos
		foreach($datos as $desc => $valor){
			echo "<tr>\n";
			echo "<td class='ei-cuadro-col-tit'>$desc</td>\n";
			echo "<td class='$css'>$valor</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
	}

	/**
     * Genera el HTML correspondiente a la barra de paginacion
     */
	protected function html_barra_paginacion()
	{
		$objeto_js = $this->_cuadro->get_id_objeto_js();
		$total_registros = $this->_cuadro->get_total_registros();
		$tamanio_pagina = $this->_cuadro->get_tamanio_pagina();
		$pagina_actual = $this->_cuadro->get_pagina_actual();
		$cantidad_paginas = $this->_cuadro->get_cantidad_paginas();
		$parametros = $this->_cuadro->get_nombres_parametros();
		$eventos = $this->_cuadro->get_eventos();

		echo "<div class='ei-cuadro-pag'>";
		if( isset($total_registros) && !($tamanio_pagina >= $total_registros) ) {
			//Calculo los posibles saltos
			//Primero y Anterior
			if($pagina_actual == 1) {
				$anterior = toba_recurso::imagen_toba("nucleo/paginacion/anterior_deshabilitado.gif",true);
				$primero = toba_recurso::imagen_toba("nucleo/paginacion/primero_deshabilitado.gif",true);
			} else {
				$evento_js = toba_js::evento('cambiar_pagina', $eventos["cambiar_pagina"], $pagina_actual - 1);
				$js = "$objeto_js.set_evento($evento_js);";
				$img = toba_recurso::imagen_toba("nucleo/paginacion/anterior.gif");
				$anterior = toba_recurso::imagen($img, null, null, 'Página Anterior', '', "onclick=\"$js\"", 'cursor: pointer;cursor:hand;');

				$evento_js = toba_js::evento('cambiar_pagina', $eventos["cambiar_pagina"], 1);
				$js = "$objeto_js.set_evento($evento_js);";
				$img = toba_recurso::imagen_toba("nucleo/paginacion/primero.gif");
				$primero = toba_recurso::imagen($img, null, null, 'Página Inicial', '', "onclick=\"$js\"", 'cursor: pointer;cursor:hand;');
			}
			//Ultimo y Siguiente
			if( $pagina_actual == $cantidad_paginas ) {
				$siguiente = toba_recurso::imagen_toba("nucleo/paginacion/siguiente_deshabilitado.gif",true);
				$ultimo = toba_recurso::imagen_toba("nucleo/paginacion/ultimo_deshabilitado.gif",true);
			} else {
				$evento_js = toba_js::evento('cambiar_pagina', $eventos["cambiar_pagina"], $pagina_actual + 1);
				$js = "$objeto_js.set_evento($evento_js);";
				$img = toba_recurso::imagen_toba("nucleo/paginacion/siguiente.gif");
				$siguiente = toba_recurso::imagen($img, null, null, 'Página Siguiente', '', "onclick=\"$js\"", 'cursor: pointer;cursor:hand;');

				$evento_js = toba_js::evento('cambiar_pagina', $eventos["cambiar_pagina"], $cantidad_paginas);
				$js = "$objeto_js.set_evento($evento_js);";
				$img = toba_recurso::imagen_toba("nucleo/paginacion/ultimo.gif");
				$ultimo = toba_recurso::imagen($img, null, null, 'Página Final', '', "onclick=\"$js\"", 'cursor: pointer;cursor:hand;');
			}

			echo "$primero $anterior Página <strong>";
			$js = "$objeto_js.ir_a_pagina(this.value);";
			$tamanio = ceil(log10($total_registros));
			echo toba_form::text($parametros['paginado'], $pagina_actual, false, '', $tamanio, 'ef-numero', "onchange=\"$js\"");
			echo "</strong> de <strong>{$cantidad_paginas}</strong> $siguiente $ultimo";
		}
		echo "</div>";
	}

	/**
	 * @ignore
	 */
	protected function html_barra_total_registros()
	{
		$total_registros = $this->_cuadro->get_total_registros();
		echo"<tr><td>";
		$plural = ($total_registros == 1) ? '' : 's';
		if ($this->_cuadro->debe_mostrar_total_registros()) {
			echo "<div class='ei-cuadro-pag ei-cuadro-pag-total'>Encontrado$plural {$total_registros} registro$plural</div>";
		}
		echo "</td></tr>\n";
	}

	
}
?>