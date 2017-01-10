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

	function generar_botones($clase='')
	{
		$this->_cuadro->generar_botones($clase);
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
		echo toba::output()->get('CuadroSalidaHtml')->getIncioZonaColapsable("cuerpo_$id_js","ei-cuadro-cuerpo", $colapsado );
		
		//------- Cabecera -----------------
		$this->html_cabecera();
		
		//--- INICIO CONTENIDO  -----
		echo toba::output()->get('CuadroSalidaHtml')->getInicioContenido("colspan='$total_col'");
		
		// Si el layout es cortes/tabular se genera una sola tabla, que empieza aca
		if( $this->_cuadro->tabla_datos_es_general() ){
			$this->html_cuadro_inicio(null);
		}
		//-- Se puede por api cambiar a que los titulos de las columnas se muestren antes que los cortes
		if ($muestra_titulo_cc) {
			$this->html_cuadro_cabecera_columnas();
		}
	}

	protected function generar_tabla_base()
	{
		$info_cuadro = $this->_cuadro->get_informacion_basica_cuadro();
		
		echo toba::output()->get('CuadroSalidaHtml')->getInicioHtml($info_cuadro);
	}
		
	/**
	 * Genera la cabecera del cuadro, por defecto muestra el titulo, si tiene
	 */
	protected function html_cabecera()
	{
		$info_cuadro = $this->_cuadro->get_informacion_basica_cuadro();
		$objeto_js = $this->_cuadro->get_id_objeto_js();
		$exportacion_excel_plano = $this->_cuadro->permite_exportacion_excel_plano();
		$filas_disponibles_selector = $this->_cuadro->get_filas_disponibles_selector();
		$total_col = $this->_cuadro->get_cantidad_columnas_total();

		echo toba::output()->get('CuadroSalidaHtml')->getCabeceraHtml($info_cuadro,$objeto_js, $exportacion_excel_plano,$filas_disponibles_selector,$total_col);
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
			$this->html_cuadro_fin(null);
		}

		echo toba::output()->get('CuadroSalidaHtml')->getFinContenido();//--- FIN CONTENIDO  ---------
		
		// Pie
		echo toba::output()->get('CuadroSalidaHtml')->getPie($info_cuadro);
		
		//Paginacion
		if ($info_cuadro["paginar"]) {
           	$this->html_barra_paginacion();
		}

		//Barra que muestra el total de registros disponibles
		$this->html_barra_total_registros();

		//Botonera
		if ($this->_cuadro->hay_botones() && $this->_cuadro->botonera_abajo()) {
			echo toba::output()->get('CuadroSalidaHtml')->getInicioBotonera($this->_cuadro->datos_cargados());
			$clase = toba::output()->get('CuadroSalidaHtml')->getClaseBotonera($this->_cuadro->datos_cargados(),!$this->_cuadro->botonera_abajo());
			$this->generar_botones($clase);
			echo toba::output()->get('CuadroSalidaHtml')->getFinBotonera($this->_cuadro->datos_cargados());
		}
		
		//-- FIN zona COLAPSABLE
		echo toba::output()->get('CuadroSalidaHtml')->getFinZonaColapsable();
		
		echo toba::output()->get('CuadroSalidaHtml')->getFinHtml($info_cuadro);

		//Aca tengo que meter el javascript y el html del cosote para ordenar
		if ($info_cuadro["ordenar"]) {
			$this->html_selector_ordenamiento();
		}
	}


	/**
	 * Genera el pie del cuadro
	 * @deprecated
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
		$editor =  $this->get_html_barra_editor();		
		echo toba::output()->get('CuadroSalidaHtml')->getInicioCuadroVacio($ancho, $editor);
		
		$this->generar_html_barra_sup(null, true,"ei-cuadro-barra-sup");
		$ancho = isset($info_cuadro["ancho"]) ? $info_cuadro["ancho"] : "";
		$colapsado = (isset($colapsado) && $colapsado) ? "display:none;" : '';	
		echo toba::output()->get('CuadroSalidaHtml')->getMensajeCuadroVacio("cuerpo_$objeto_js",$ancho, $colapsado, $texto);		
		if ($this->_cuadro->hay_botones() && $this->_cuadro->botonera_abajo()) {
			echo toba::output()->get('CuadroSalidaHtml')->getInicioBotonera($this->_cuadro->datos_cargados());
			$clase = toba::output()->get('CuadroSalidaHtml')->getClaseBotonera($this->_cuadro->datos_cargados(),$this->_cuadro->botonera_abajo());			
			$this->generar_botones($clase);
			echo toba::output()->get('CuadroSalidaHtml')->getFinBotonera($this->_cuadro->datos_cargados());
		}
		echo toba::output()->get('CuadroSalidaHtml')->getFinCuadroVacio();;
	}

	/**
	 * Genera el HTML que contendra el selector de ordenamiento
	 */
	protected function html_selector_ordenamiento()
	{
		$id = $this->_cuadro->get_id_form();
		$objeto_js = $this->_cuadro->get_id_objeto_js();
		$columnas = $this->_cuadro->get_columnas();
		echo toba::output()->get('CuadroSalidaHtml')->getSelectorOrdenamiento($id, $objeto_js, $columnas);
	}

	/**
	 *  Envia la botonera del selector
	 *  @deprecated
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
	 * @deprecated
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
	 *  @deprecated
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
		echo toba::output()->get('CuadroSalidaHtml')->getInicioNivel($this->_cuadro->get_cortes_modo());
	}
	
	/**
	 * @ignore
	 */
	function html_cc_fin_nivel()
	{
		echo toba::output()->get('CuadroSalidaHtml')->getFinNivel($this->_cuadro->get_cortes_modo());
	}

	function html_inicio_zona_colapsable($id_unico, $estilo)
	{
		
		echo toba::output()->get('CuadroSalidaHtml')->getInicioZonaColapsableCC($id_unico, $estilo);
	}

	function html_fin_zona_colapsable()
	{
		echo toba::output()->get('CuadroSalidaHtml')->getFinZonaColapsableCC();
		
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
		if($this->_cuadro->get_cortes_modo() == apex_cuadro_cc_tabular) {				
				$objeto_js = $this->_cuadro->get_id_objeto_js();
				$total_columnas = $this->_cuadro->get_cantidad_columnas_total();
				$js = "onclick=\"$objeto_js.colapsar_corte('$id_unico');\"";

				echo toba::output()->get('CuadroSalidaHtml')->getInicioCabeceraCC($this->_cuadro->debe_colapsar_cortes(), $nivel_css,$total_columnas, $js);
				$this->$metodo($nodo);
				echo toba::output()->get('CuadroSalidaHtml')->getFinCabeceraCC($this->_cuadro->debe_colapsar_cortes(), $nivel_css,$total_columnas, $js);
		}else{
			
				echo "<li class='$class'>\n";
				//$this->$metodo($nodo);
		}
	}

	/**
		Genera el CONTENIDO de la cabecera del corte de control
			Muestra las columnas seleccionadas como descripcion del corte separadas por comas
	*/
	protected function html_cabecera_cc_contenido(&$nodo)
	{
		
		echo toba::output()->get('CuadroSalidaHtml')->getContenidoCabeceraCC($this->_cuadro->get_indice_cortes(),$nodo);
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
			
			
			echo toba::output()->get('CuadroSalidaHtml')->getInicioPieCC($this->_cuadro->tabla_datos_es_general());


			$this->html_cabecera_pie($indice, $nodo, $total_columnas);	//-----  Cabecera del PIE --------
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
				$etiqueta = $this->etiqueta_cantidad_filas($nodo['profundidad']) . count($nodo['filas']);
				echo toba::output()->get('CuadroSalidaHtml')->getContadorFilaCC($nivel_css, $total_columnas, $etiqueta);
				
			}
			//----- Contenido del usuario al final del PIE
			$this->html_pie_pie($nodo, $total_columnas, $es_ultimo);
			
			echo toba::output()->get('CuadroSalidaHtml')->getFinPieCC($this->_cuadro->tabla_datos_es_general());
			
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
		
		if($indice[$nodo['corte']]['pie_mostrar_titular']) {
				$metodo_redeclarado = 'html_pie_cc_cabecera__' . $nodo['corte'];
				if(method_exists($this, $metodo_redeclarado)){
					$descripcion = $this->$metodo_redeclarado($nodo);
				}else{
				 	$descripcion = $this->html_cabecera_pie_cc_contenido($nodo);
				}
				echo toba::output()->get('CuadroSalidaHtml')->getCabeceraPieCC($descripcion,$nivel_css,$total_columnas);
		}
	}

	/**
	 * @ignore
	 * @param <type> $nodo
	 * @param <type> $total_columnas
	 * @todo Por el momento no se estaria usando y no se como se usa
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
			echo toba::output()->get('CuadroSalidaHtml')->getSumarizacion($datos,null,300,$css,$nivel_css,  "<tr><td  class='$css_pie' colspan='$total_columnas'>\n","</td></tr>\n");
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
		return toba::output()->get('CuadroSalidaHtml')->getContenidoCabeceraPieCC($indice, $nodo);
	}

	//-------------------------------------------------------------------------------
	//-- Generacion del CUADRO
	//-------------------------------------------------------------------------------

	/**
	 * Genera el html correspondiente a las filas del cuadro
	 */
	function html_cuadro(&$filas,$totales=0, $nodo=null)
	{
		//Si existen cortes de control y el layout es tabular, el encabezado de la tabla ya se genero
		if( ! $this->_cuadro->tabla_datos_es_general() ){
			$this->html_cuadro_inicio($nodo);
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
			echo toba::output()->get('CuadroSalidaHtml')->getInicioFila(null);
		}

		$columnas = $this->_cuadro->get_columnas();
		$datos = $this->_cuadro->get_datos();
		$objeto_js = $this->_cuadro->get_id_objeto_js();
		$evt_multiples = $this->_cuadro->get_eventos_multiples();

		foreach($filas as $f)
		{
			if (!is_null($layout_cant_columnas) && ($i % $layout_cant_columnas == 0)) {
				$ancho = floor(100 / (count($filas) / $layout_cant_columnas));
				echo "<td><table class='ei-cuadro-agrupador-filas' width='$ancho%' >";/**@todo no encontre donde se utiliza*/
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
			echo toba::output()->get('CuadroSalidaHtml')->getFinFila();
		}
		if( ! $this->_cuadro->tabla_datos_es_general() ){
			$this->html_acumulador_usuario();
			$this->html_cuadro_fin($nodo);
		}
	}

	function get_estilo_seleccion($clave_fila)
	{
		$esta_seleccionada = $this->_cuadro->es_clave_fila_seleccionada($clave_fila);		
		return toba::output()->get('CuadroSalidaHtml')->getEstiloFila($esta_seleccionada);
	}

	function generar_layout_fila($columnas, $datos, $id_fila,  $clave_fila, $evt_multiples, $objeto_js, $estilo_fila, $formateo)
	{
		$estilo_seleccion = $this->get_estilo_seleccion($clave_fila);

		  //Javascript de seleccion multiple
		$js = $this->get_invocacion_js_eventos_multiples($evt_multiples, $id_fila, $objeto_js);

		 //---> Creo las CELDAS de una FILA <----
		echo toba::output()->get('CuadroSalidaHtml')->getInicioFila($estilo_fila);

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
			$estilo_columna = $columnas[$a]["estilo"];
			echo toba::output()->get('CuadroSalidaHtml')->getCeldaCuadro($valor," $estilo_seleccion $estilo_columna", $ancho, $js);			
			//Termino la CELDA
		}
		//---> Creo los EVENTOS de la FILA <---
		$this->html_cuadro_celda_evento($id_fila, $clave_fila, false);
		echo toba::output()->get('CuadroSalidaHtml')->getFinFila();
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
		$id_form = $this->_cuadro->get_id_form();
		$descripcion_resp_popup = $this->_cuadro->get_descripcion_resp_popup($id_fila);
		
		echo toba::output()->get('CuadroSalidaHtml')->getPreCeldaEvento($pre_columnas,$this->_cuadro->get_eventos_sobre_fila());
		foreach ($this->_cuadro->get_eventos_sobre_fila() as $id => $evento) {
			$parametros = $this->get_parametros_interaccion($id_fila, $clave_fila);
			$invocacion_evento_fila = $this->get_invocacion_evento_fila($evento, $id_fila, $clave_fila, false, $parametros);
			
			echo toba::output()->get('CuadroSalidaHtml')->getCeldaEvento($id_fila, $clave_fila, $pre_columnas,$evento,$parametros,$id_form,$descripcion_resp_popup,$invocacion_evento_fila);
		}
		
		//Se agrega la clave a la lista de enviadas
		$this->_cuadro->agregar_clave_enviada($clave_fila);
		echo toba::output()->get('CuadroSalidaHtml')->getPostCeldaEvento($pre_columnas,$this->_cuadro->get_eventos_sobre_fila());
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
	protected function html_cuadro_inicio($nodo=null)
	{
		$cortes = $this->_cuadro->existen_cortes_control();
		echo toba::output()->get('CuadroSalidaHtml')->getInicioCuadro($cortes,$this->get_nivel_css($nodo['profundidad']));
	}

	/**
	 *@ignore
	 */
	protected function html_cuadro_fin($nodo=null)
	{
		$cortes = $this->_cuadro->existen_cortes_control();
		echo toba::output()->get('CuadroSalidaHtml')->getFinCuadro($cortes,$this->get_nivel_css($nodo['profundidad']));
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
			
			echo toba::output()->get('CuadroSalidaHtml')->getInicioHeadCuadro($this->_cuadro->debe_mostrar_titulos_columnas_cc());
			
			$this->html_cuadro_cabecera_columna_evento($rowspan, true);
			foreach (array_keys($columnas) as $a) {
				$html_columna = '';
				//El alto de la columna, si esta agrupada es uno sino es el general
				$rowspan_col = isset($columnas[$a]['grupo']) ? "" : $rowspan;

				
				
				
				$ordenamiento_editor = $this->html_cuadro_cabecera_columna(    $columnas[$a]["titulo"], $columnas[$a]["clave"], $a );
				
				
				$html_columna = toba::output()->get('CuadroSalidaHtml')->getCuadroCabeceraColumna($columnas[$a], $rowspan_col, $ordenamiento_editor);

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
						
						echo toba::output()->get('CuadroSalidaHtml')->getCabeceraColumnasAgrupadas($grupo_actual, $cant_col);
						
					}
				}
			}
			//-- Eventos sobre fila
			$this->html_cuadro_cabecera_columna_evento($rowspan, false);
			
			
			
			//-- Columnas Agrupadas
			if ($html_columnas_agrupadas != '') {
				echo toba::output()->get('CuadroSalidaHtml')->getParseGrupoColumnas($html_columnas_agrupadas);
			}
			echo toba::output()->get('CuadroSalidaHtml')->getFinHeadCuadro($this->_cuadro->debe_mostrar_titulos_columnas_cc());

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
		
		/**
		 * @todo por ahora las imagenes del ornamientos queda fijado en Toba ( Despues veremos )
		 */
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
		//---Editor de la columna
		if ( toba_editor::modo_prueba()) {
			$item_editor = "1000253";
			$param_editor = array( apex_hilo_qs_zona => implode(apex_qs_separador,$this->_cuadro->get_id()),
					'columna' => $columna );
			$salida .= toba_editor::get_vinculo_subcomponente($item_editor, $param_editor);
		}
		return $salida;
	}
	

	protected function html_cuadro_cabecera_columna_evento($rowspan, $pre_columnas)
	{
		$editor_item = null;
		$eventos_sobre_fila = $this->_cuadro->get_eventos_sobre_fila();
		if (toba_editor::modo_prueba()) {
			$info_comp = $this->_cuadro->get_informacion_basica_componente();
			$editor_item = $info_comp['clase_editor_item'];
		}
		
		echo toba::output()->get('CuadroSalidaHtml')->getCuadroCabeceraColumnaEvento($rowspan, $pre_columnas,$eventos_sobre_fila, $this->_cuadro->get_id(), $editor_item);
	}
	
	 /**
	 * @ignore
	 */
	function html_acumulador_usuario()
	{
		$sumarizacion = $this->_cuadro->calcular_totales_sumarizacion_usuario();
		$total_columnas = $this->_cuadro->get_cantidad_columnas_total();
		if (! empty($sumarizacion)) {
			echo toba::output()->get('CuadroSalidaHtml')->getSumarizacion($sumarizacion,null,300,$css, "<tr><td colspan='$total_columnas'>\n","</td></tr>\n");
		}
	}

	protected function html_cuadro_columnas_relleno($cantidad_columnas)
	{
		echo toba::output()->get('CuadroSalidaHtml')->getColumnasRelleno($cantidad_columnas);
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
			echo toba::output()->get('CuadroSalidaHtml')->getInicioFila('');
			$this->html_cuadro_columnas_relleno($cant_evt_pre_columnas);
			foreach (array_keys($columnas) as $clave) {
			    if(isset($totales[$clave])){
					$valor = $columnas[$clave]["titulo"];
					echo toba::output()->get('CuadroSalidaHtml')->getCeldaAcumulador($valor,$columnas[$clave]["estilo_titulo"],null);
				}else{
					echo toba::output()->get('CuadroSalidaHtml')->getCeldaAcumulador("&nbsp;",null,$clase_linea);
				}
			}
			//-- Eventos sobre fila
			$this->html_cuadro_columnas_relleno($cant_evt_restantes);
			echo toba::output()->get('CuadroSalidaHtml')->getFinFila();
		}
		if ($totales !== null){
			echo toba::output()->get('CuadroSalidaHtml')->getInicioFila('ei-cuadro-totales');
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
					echo toba::output()->get('CuadroSalidaHtml')->getCeldaAcumulador($valor,"ei-cuadro-total $estilo",null);
				}else{
					echo toba::output()->get('CuadroSalidaHtml')->getCeldaAcumulador("&nbsp;",null,$clase_linea);
				}
			}
			//-- Eventos sobre fila
			$this->html_cuadro_columnas_relleno($cant_evt_restantes);
			echo toba::output()->get('CuadroSalidaHtml')->getFinFila();
		}//if totales
	}

	//-------------------------------------------------------------------------------
	//-- Elementos visuales independientes
	//-------------------------------------------------------------------------------
    /**
     *  Genera el HTML correspondiente a la sumarizacion de los datos
     *  @deprecated
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
        
		echo toba::output()->get('CuadroSalidaHtml')->getCuadroPaginacion($objeto_js,$total_registros,$tamanio_pagina,$pagina_actual,$cantidad_paginas,$parametros,$eventos);
	}

	/**
	 * @ignore
	 */
	protected function html_barra_total_registros()
	{
		$total_registros = $this->_cuadro->get_total_registros();
		$mostrar = $this->_cuadro->debe_mostrar_total_registros();
		
		echo toba::output()->get('CuadroSalidaHtml')->getBarraTotalRegistros($total_registros, $mostrar);
	}
}
?>
