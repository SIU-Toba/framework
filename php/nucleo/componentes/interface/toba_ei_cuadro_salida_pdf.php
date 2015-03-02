<?php
class toba_ei_cuadro_salida_pdf extends toba_ei_cuadro_salida
{
	//Salida PDF
	protected $_pdf_total_generado = false;
	protected $_pdf_letra_tabla = 8;
	protected $_pdf_sep_titulo = 5;
	protected $_pdf_sep_tabla = 5;
	protected $_pdf_tabla_ancho = '100%';
	protected $_pdf_tabla_opciones = array();
	protected $_pdf_sep_cc = 4;
	protected $_pdf_cabecera_cc_0_opciones = array('justification'=>'center');		//Opciones de la cabecera de nivel cero
	protected $_pdf_cabecera_cc_1_opciones = array('justification'=>'left');		//Opciones de la cabecera de nivel mayor que cero
	protected $_pdf_cabecera_cc_0_letra = 12;
	protected $_pdf_cabecera_cc_1_letra = 10;
	protected $_pdf_totales_cc_0_opciones = array('xPos' => 'center', 'xOrientation' => 'center');
	protected $_pdf_totales_cc_1_opciones = array('xPos' => 'right', 'xOrientation' => 'left');
	protected $_pdf_cabecera_pie_cc_0_op = array('justification' => 'center');
	protected $_pdf_cabecera_pie_cc_1_op = array('justification' => 'right');
	protected $_pdf_contar_filas_op = array('justification' => 'right');
	protected $_pdf_cortar_hoja_cc_0 = false;										//Corta la hoja a la finalizacion de un corte de nivel 0
	protected $_pdf_cortar_hoja_cc_1 = false;										//Corta la hoja a la finalizacion de un corte de nivel 1

	//--------------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------  SALIDA PDF  ------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------------------

	function get_resultado_generacion()
	{
		return $this->_objeto_toba_salida;
	}

	/**
	 * @ignore
	 */
	function pdf_inicio()
	{
		$this->_objeto_toba_salida->separacion($this->_pdf_sep_titulo);
	}

	/**
	 * @ignore
	 */
	function pdf_fin()
	{
		if( $this->_cuadro->tabla_datos_es_general() ){
			$acumulador = $this->_cuadro->get_acumulador_general();			
			if (isset($acumulador) && ! $this->_pdf_total_generado) {
				$this->_objeto_toba_salida->separacion($this->_pdf_sep_titulo);
				$this->pdf_cuadro_totales_columnas($acumulador, 0, true);
			}
			$this->pdf_acumulador_usuario();
		}
	}

		/**
	 * @ignore
	 * $nodo se pasa para poder mostrar los totales aqui mismo en caso de cortes con nivel > 0
	 */
	function pdf_cuadro(&$filas, &$totales, &$nodo)
	{
		$this->_objeto_toba_salida->separacion($this->_pdf_sep_tabla);
		$formateo = $this->_cuadro->get_instancia_clase_formateo('pdf');
		$columnas = $this->_cuadro->get_columnas();
		$datos_cuadro = $this->_cuadro->get_datos();

		//-- Valores de la tabla
		$datos = array();
		foreach($filas as $f) {
			$clave_fila = $this->_cuadro->get_clave_fila($f);
			//---> Creo las CELDAS de una FILA <----
			$datos[] = $this->generar_layout_fila($columnas, $datos_cuadro, $f, $formateo);
		}
		list($titulos, $estilos) = $this->pdf_get_titulos();

	        //-- Para la tabla simple se sacan los totales como parte de la tabla
		if (isset($totales) || isset($nodo['acumulador'])) {
			/* Como el pdf no admite continuar una tabla luego de construirla (pdf_cuadro)
			   Se opta por generar aquí los totales de niveles > 0
			   'rompiendo' la separación establecida por el proceso general en pos de una mejor visualización
			*/
			if (! isset($totales)) {
				$totales = $nodo['acumulador'];
				$nodo['pdf_acumulador_generado'] = 1; //Esto evita que se muestre la tabla con totales ya que se va a mostrar en esta misma tabla
			} else {
				$this->_pdf_total_generado = true;
			}
			$temp = null;
			$datos[] = $this->pdf_get_fila_totales($totales, $temp, true);
		}

		//-- Genera la tablas
		$ancho = null;
		if (strpos($this->_pdf_tabla_ancho, '%') !== false) {
			$ancho = $this->_objeto_toba_salida->get_ancho(str_replace('%', '', $this->_pdf_tabla_ancho));
		} else {
			$ancho = $this->_pdf_tabla_ancho;
		}
		$opciones = array('width' => $ancho, 'cols' => $estilos);
		$opciones = array_merge($opciones, $this->get_opciones_columnas());
		$this->_objeto_toba_salida->tabla(array('datos_tabla'=>$datos, 'titulos_columnas'=>$titulos), true, $this->_pdf_letra_tabla, $opciones);
		$this->_objeto_toba_salida->separacion($this->_pdf_sep_tabla);
	}

	/**
	 * @ignore
	 */
	function get_opciones_columnas()
	{
		return $this->_pdf_tabla_opciones;
	}

	/**
	 * @ignore
	 * @param <type> $columnas
	 * @param <type> $datos_cuadro
	 * @param <type> $id_fila
	 * @param <type> $formateo
	 * @return string
	 */
	function generar_layout_fila($columnas, $datos_cuadro, $id_fila, $formateo)
	{
		$fila = array();
		foreach (array_keys($columnas) as $a) {
			$valor = "";
			if(isset($columnas[$a]["clave"])){
				if(isset($datos_cuadro[$id_fila][$columnas[$a]["clave"]])){
					$valor_real = $datos_cuadro[$id_fila][$columnas[$a]["clave"]];
				}else{
					$valor_real = '';
				}
				//Hay que formatear?
				if(isset($columnas[$a]["formateo"])){
					$funcion = "formato_" . $columnas[$a]["formateo"];
					//Formateo el valor
					$valor = $formateo->$funcion($valor_real);
				} else {
					$valor = $valor_real;
				}
			}
			$fila[$columnas[$a]["clave"]] = $valor;
		}
		return $fila;
	}

	/**
	 * @ignore
	 */
	protected function pdf_get_titulos()
	{
		$titulos = array();
		$estilos = array();
		$columnas = $this->_cuadro->get_columnas();
		foreach(array_keys($columnas) as $id) {
			$titulos[$id] = $columnas[$id]['titulo'];
			$estilo = $this->pdf_get_estilo($columnas[$id]['estilo']);
			if (isset($estilo)) {
				$estilos[$id] = $estilo;
			}
		}
		return array($titulos, $estilos);
	}

	/**
	 * Muestra el mensaje correspondiente al cuadro sin datos
	 * @param string $texto
	 * @ignore
	 */
	function pdf_mensaje_cuadro_vacio($texto)
	{
		$this->_objeto_toba_salida->texto($texto);
	}

	//-------------------------------------------------------------------------
	//--------------------- Cortes de Control --------------------------
	//-------------------------------------------------------------------------
	
	/**
	 * Deduce el metodo que utilizara para generar la cabecera
	 * @param array $nodo
	 * @ignore
	 */
	function pdf_cabecera_corte_control(&$nodo )
	{
		//Dedusco el metodo que tengo que utilizar para generar el contenido
		$metodo = 'pdf_cabecera_cc_contenido';
		$metodo_redeclarado = $metodo . '__' . $nodo['corte'];
		if(method_exists($this, $metodo_redeclarado)){
			$metodo = $metodo_redeclarado;
		}
		$this->$metodo($nodo);
	}

	/**
	 * Grafica el contenido de la cabecera del corte de control
	 * @param array $nodo
	 */
	protected function pdf_cabecera_cc_contenido(&$nodo)
	{
		//Obtengo los cortes desde el cuadro
		$indice_cortes = $this->_cuadro->get_indice_cortes();

		$descripcion = $indice_cortes[$nodo['corte']]['descripcion'];
		$valor = implode(", ",$nodo['descripcion']);
		if ($nodo['profundidad'] > 0) {
			$opciones = $this->_pdf_cabecera_cc_1_opciones;
			$size = $this->_pdf_cabecera_cc_1_letra;
		} else {
			$opciones = $this->_pdf_cabecera_cc_0_opciones;
			$size = $this->_pdf_cabecera_cc_0_letra;
		}
		$this->_objeto_toba_salida->separacion($this->_pdf_sep_cc);
		if (trim($descripcion) != '') {
			$this->_objeto_toba_salida->texto("<b>$descripcion " . $valor . '</b>', $size, $opciones);
		} else {
			$this->_objeto_toba_salida->texto('<b>' . $valor . '</b>', $size, $opciones);
		}
		$this->_objeto_toba_salida->separacion($this->_pdf_sep_cc);
	}

	/**
	 * Genera el contenido de la 'cabecera' ubicada en el pie del corte de control
	 * @param array $nodo
	 * @return string
	 */
	function pdf_cabecera_pie_cc_contenido(&$nodo)
	{
		//Obtengo los cortes desde el cuadro
		$indice_cortes = $this->_cuadro->get_indice_cortes();

		$descripcion = $indice_cortes[$nodo['corte']]['descripcion'];
		$valor = implode(", ",$nodo['descripcion']);
		if (trim($descripcion) != '') {
			return 'Resumen ' . $descripcion . ': <b>' . $valor . '</b>';
		} else {
			return 'Resumen <b>' . $valor . '</b>';
		}
	}

	 /**
     * @ignore
     */
	protected function pdf_cuadro_totales_columnas($totales,$nivel=null,$agregar_titulos=false, $estilo_linea=null)
	{
		/* Como el pdf no admite continuar una tabla luego de construirla (pdf_cuadro)
		   Se opta por sacar los totales del mayor nivel dentro de la generación misma del cuadro general
		   'rompiendo' la separación establecida por el proceso general en pos de una mejor visualización
		   Ese nivel nNo entra por aqui porque se le hizo un $nodo['pdf_acumulador_generado'] = 1;
		*/
		list($titulos, $estilos) = $this->pdf_get_titulos();
		$datos = $this->pdf_get_fila_totales($totales, $titulos);
		$datos = array($datos);
		$this->_objeto_toba_salida->separacion($this->_pdf_sep_cc);
		if ($nivel > 0) {
			$opciones = $this->_pdf_totales_cc_1_opciones;
		} else {
			$opciones = $this->_pdf_totales_cc_0_opciones;
		}
		$opciones['cols'] = $estilos;
		$this->_objeto_toba_salida->tabla(array('datos_tabla'=>$datos, 'titulos_columnas'=>$titulos), $agregar_titulos, $this->_pdf_letra_tabla, $opciones);
		$this->_objeto_toba_salida->separacion($this->_pdf_sep_cc);
	}

	/**
	 * @ignore
	 */
	protected function pdf_get_fila_totales($totales, &$titulos=null, $resaltar=false)
	{
		$formateo = $this->_cuadro->get_instancia_clase_formateo('pdf');
		$columnas = $this->_cuadro->get_columnas();
		$datos = array();
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
				if ($resaltar) {
					$valor = '<b>'.$valor.'</b>';
				}
				$datos[$clave] = $valor;
			}else{
				unset($titulos[$clave]);
				$datos[$clave] = null;
			}
		}
		return $datos;
	}

	/**
	 * Grafica  la sumarizacion del cuadro
	 * @param array $datos
	 * @param string $titulo
	 * @param integer $ancho
	 * @param string $css
	 * @ignore
	 */
	protected function pdf_cuadro_sumarizacion($datos, $titulo=null , $ancho=null, $css='col-num-p1')
	{
		//Titulo
		if(isset($titulo)){
			$this->_objeto_toba_salida->subtitulo($titulo);
		}
		//Datos
		foreach($datos as $desc => $valor){
			$this->_objeto_toba_salida->texto($desc.': '.$valor);
		}
	}

	function pdf_pie_corte_control( &$nodo, $es_ultimo )
	{
		//-----  Cabecera del PIE --------
		$indice_cortes = $this->_cuadro->get_indice_cortes();
		$acumulador_usuario = $this->_cuadro->get_acumulador_usuario();

		$this->pdf_cabecera_pie($indice_cortes, $nodo);
		//----- Totales de columna -------
		if (isset($nodo['acumulador']) && ! isset($nodo['pdf_acumulador_generado'])) {
			$this->pdf_cuadro_totales_columnas($nodo['acumulador'],
												$nodo['profundidad'],
												true,
												null);
		}
		//------ Sumarizacion AD-HOC del usuario --------
		if(isset($nodo['sum_usuario'])){
			foreach($nodo['sum_usuario'] as $id => $valor){
				$desc = $acumulador_usuario[$id]['descripcion'];
				$datos[$desc] = $valor;
			}
			$this->pdf_cuadro_sumarizacion($datos,null,300,$nodo['profundidad']);
		}
		//----- Contar Filas
		if($indice_cortes[$nodo['corte']]['pie_contar_filas']){
			$etiqueta = $this->etiqueta_cantidad_filas($nodo['profundidad']) . count($nodo['filas']);
			$this->_objeto_toba_salida->texto("<i>".$etiqueta.'</i>', $this->_pdf_letra_tabla, $this->_pdf_contar_filas_op);
		}

		//----- Contenido del usuario al final del PIE
		$this->pdf_pie_pie($nodo, $es_ultimo);
	}

	function pdf_cabecera_pie($indice_cortes, $nodo)
	{
		if($indice_cortes[$nodo['corte']]['pie_mostrar_titular']) {
			$metodo_redeclarado = 'pdf_pie_cc_cabecera__' . $nodo['corte'];
			if(method_exists($this, $metodo_redeclarado)){
				$descripcion = $this->$metodo_redeclarado($nodo);
			}else{
			 	$descripcion = $this->pdf_cabecera_pie_cc_contenido($nodo);
			}
			if ($nodo['profundidad'] > 0) {
				$opciones = $this->_pdf_cabecera_pie_cc_1_op;
			} else {
				$opciones = $this->_pdf_cabecera_pie_cc_0_op;
			}
			$this->_objeto_toba_salida->texto($descripcion, $this->_pdf_letra_tabla, $opciones);
		}
	}

	function pdf_pie_pie($nodo, $es_ultimo)
	{
		$metodo = 'pdf_pie_cc_contenido__' . $nodo['corte'];
		if(method_exists($this, $metodo)){
			$this->$metodo($nodo);
		}
		if (!$es_ultimo && $nodo['profundidad'] == 0 && $this->_pdf_cortar_hoja_cc_0) {
			$this->_objeto_toba_salida->salto_pagina();
		} elseif (!$es_ultimo && $nodo['profundidad'] > 0 && $this->_pdf_cortar_hoja_cc_1) {
			$this->_objeto_toba_salida->salto_pagina();
		}
	}

	  /**
     * @ignore
     */
	function pdf_acumulador_usuario()
	{
		$sumarizacion = $this->_cuadro->calcular_totales_sumarizacion_usuario();
		if (! empty($sumarizacion)) {
			$css = 'cuadro-cc-sum-nivel-1';
			$this->pdf_cuadro_sumarizacion($sumarizacion,null,300,$css);
		}
	}

	function pdf_cc_inicio_nivel()
	{
	}

	function pdf_cc_fin_nivel()
	{
	}

	function pdf_inicio_zona_colapsable()
	{		
	}
	
	function pdf_fin_zona_colapsable()
	{		
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------

	/**
	 * @ignore
	 */
	protected function pdf_get_estilo($estilo)
	{
    	switch($estilo) {
    		case 'col-num-p1':
    		case 'col-num-p2':
    		case 'col-num-p3':
    		case 'col-num-p4':
    			return array('justification' => 'right');
    			break;
    		case 'col-tex-p1':
    		case 'col-tex-p2':
    		case 'col-tex-p3':
    		case 'col-tex-p4':
    		    return array('justification' => 'left');
    			break;
    		case 'col-cen-s1':
    		case 'col-cen-s2':
    		case 'col-cen-s3':
    		case 'col-cen-s4':
    		    return array('justification' => 'left');
    			break;
    	}
	}
}
?>