<?php
class toba_ei_cuadro_salida_excel extends toba_ei_cuadro_salida
{
	//Salida Excel
	protected $_excel_total_generado = false;
	protected $_excel_cabecera_cc_0_opciones = array('font' => array('bold'=>true, 'size' => '12'), 'alignment'=> array('horizontal' => 'center', 'vertical'=>'bottom'));
	protected $_excel_cabecera_cc_0_altura = 30;
	protected $_excel_cabecera_cc_1_opciones = array('font' => array('bold'=>true, 'size' => '11'), 'alignment'=> array('horizontal' => 'left', 'vertical'=>'bottom'));
	protected $_excel_cabecera_cc_1_altura = 20;
	protected $_excel_totales_cc_0_opciones = array('font' => array('bold'=>true), 'borders' => array(
																				'top' => array('style'=>'thick')));
	protected $_excel_totales_cc_1_opciones = array('font' => array('bold'=>true), 'borders' => array());
	protected $_excel_totales_opciones = array('font' => array('bold'=>true, 'size' => 12),
									 			'fill' => array(
								             		'type' => 'solid' ,
										            'rotation'   => 0,
										            'startcolor' => array('rgb' => 'E6E6E6')),
													 'borders' => array(
														'top' => array('style'=>'thin'),
														'bottom' => array('style'=>'thin'),
														'left' => array('style'=>'thin'),
														'right' => array('style'=>'thin')),
											);
	protected $_excel_cabecera_pie_cc_0_op =  array();
	protected $_excel_cabecera_pie_cc_1_op = array();
	protected $_excel_contar_filas_op = array('alignment'=> array('horizontal' => 'right'));
	protected $_excel_cortar_hoja_cc_0 = false;										//Crea una hoja (worksheet) por corte
	protected $_excel_usar_formulas = true;											//Para hacer la sumatoria de los cortes usa formulas excel, sino suma en PHP

	//--------------------------------------------------------------------------------
	//----------------------  SALIDA EXCEL  ----------------------------------
	//--------------------------------------------------------------------------------

	function get_resultado_generacion()
	{
		return $this->_objeto_toba_salida;
	}

	/**
	 * @ignore
	 */
	function excel_inicio(){}

	/**
	 * @ignore
	 */
	function excel_fin()
	{
		if( $this->_cuadro->tabla_datos_es_general() ) {
			$acumulador = $this->_cuadro->get_acumulador_general();
			if (isset($acumulador) && !$this->_excel_total_generado) {
				$this->excel_cuadro_totales_columnas(array('acumulador'=>$acumulador), 0, false, true);
			}
		}
	}
	
	/**
	 * @ignore
	 * $nodo se pasa para poder mostrar los totales aqui mismo en caso de cortes con nivel > 0
	 */
	function excel_cuadro(&$filas, &$totales, &$nodo)
	{
		$formateo = $this->_cuadro->get_instancia_clase_formateo('excel');
		$columnas = $this->_cuadro->get_columnas();
		$datos_cuadro = $this->_cuadro->get_datos();

		//-- Valores de la tabla
		$datos = array();
		$estilos = array();
		foreach($filas as $f) {
			$datos[] = $this->generar_layout_fila($columnas, $datos_cuadro, $f, $formateo, $estilos);
		}
		$titulos = $this->excel_get_titulos();

		//-- Para la tabla simple se sacan los totales como parte de la tabla
		$col_totales = array();
		if (isset($totales)) {
			$this->_excel_total_generado = true;
			$col_totales = array_keys($totales);
		}

		//-- Genera la tabla
		$coordenadas = $this->_objeto_toba_salida->tabla($datos, $titulos, $estilos, $col_totales);
		$nodo['excel_rango'] = $coordenadas;
		$nodo['excel_rango_hoja'] = $this->_objeto_toba_salida->get_hoja_nombre();
	}

	function generar_layout_fila ($columnas, $datos_cuadro, $id_fila, $formateo, &$estilos)
	{
		$fila = array();
		//---> Creo las CELDAS de una FILA <----
		foreach (array_keys($columnas) as $clave) {
			$valor = "";
			if(isset($columnas[$clave]["clave"])) {
				if(isset($datos_cuadro[$id_fila][$clave])) {
					$valor_real = $datos_cuadro[$id_fila][$clave];
				} else {
					$valor_real = '';
				}
				//Hay que formatear?
				$estilo = array();
				if(isset($columnas[$clave]["formateo"])) {
					$funcion = "formato_" . $columnas[$clave]["formateo"];
					//Formateo el valor
					list($valor, $estilo) = $formateo->$funcion($valor_real);					
					if (is_null($estilo)) {
						$estilo = array();
					}
				} else {
					$valor = $valor_real;
				}				
				$estilos[$clave]['estilo'] = $this->excel_get_estilo($columnas[$clave]['estilo']);
				$estilos[$clave]['estilo'] = array_merge($estilo, $estilos[$clave]['estilo']);
				$estilos[$clave]['ancho'] = 'auto';
				if (isset($columnas[$clave]['grupo']) && $columnas[$clave]['grupo'] != '') {
					$estilos[$clave]['grupo'] = $columnas[$clave]['grupo'];
				}
			}
			$fila[$clave] = $valor;
		}
		return $fila;
	}

		/**
	 * @ignore
	 */
	protected function excel_get_titulos()
	{
		$columnas = $this->_cuadro->get_columnas();
		$titulos = array();
		foreach(array_keys($columnas) as $id) {
			$titulos[$id] = $columnas[$id]['titulo'];
		}
		return $titulos;
	}

	/**
	 * Emite el mensaje correspondiente al cuadro sin datos
	 * @param string $texto
	 * @ignore
	 */
	function excel_mensaje_cuadro_vacio($texto)
	{
		$this->_objeto_toba_salida->texto($texto);
	}

		/**
	 * Define que metodo utilizara para generar el contenido de la cabecera
	 * @param array $nodo
	 * @ignore
	 */
	function excel_cabecera_corte_control(&$nodo )
	{
		//Dedusco el metodo que tengo que utilizar para generar el contenido
		$metodo = 'excel_cabecera_cc_contenido';
		$metodo_redeclarado = $metodo . '__' . $nodo['corte'];
		if(method_exists($this, $metodo_redeclarado)){
			$metodo = $metodo_redeclarado;
		}
		$this->$metodo($nodo);
	}

	/**
	 * Genera el contenido de la cabecera del corte de control
	 * @param array $nodo
	 */
	protected function excel_cabecera_cc_contenido(&$nodo)
	{
		$indice = $this->_cuadro->get_indice_cortes();
		
		$descripcion = $indice[$nodo['corte']]['descripcion'];
		$valor = implode(", ",$nodo['descripcion']);
		if ($nodo['profundidad'] > 0) {
			$opciones = $this->_excel_cabecera_cc_1_opciones;
			$altura = $this->_excel_cabecera_cc_1_altura;
		} else {
			$opciones = $this->_excel_cabecera_cc_0_opciones;
			$altura = $this->_excel_cabecera_cc_0_altura;
		}
		$span = $this->_cuadro->get_cantidad_columnas_total();
		if (trim($descripcion) != '') {
			$contenido = "$descripcion " . $valor;
		} else {
			$contenido = $valor;
		}
		$this->_objeto_toba_salida->texto($contenido, $opciones, $span, $altura);
		if ($nodo['profundidad'] == 0 && $this->_excel_cortar_hoja_cc_0) {
			$this->_objeto_toba_salida->set_hoja_nombre($contenido);
		}
	}

	/**
	 * Genera el contenido de la 'cabecera' ubicada en el pie del corte de control
	 * @param array $nodo
	 * @return string
	 */
	protected function excel_cabecera_pie_cc_contenido(&$nodo)
	{
		$indice_cortes = $this->_cuadro->get_indice_cortes();
		$descripcion = $indice_cortes[$nodo['corte']]['descripcion'];
		$valor = implode(", ",$nodo['descripcion']);
		if (trim($descripcion) != '') {
			return 'Resumen ' . $descripcion . ': '.$valor;
		} else {
			return 'Resumen ' . $valor;
		}
	}
	
	  /**
     * @ignore
     */
	protected function excel_cuadro_totales_columnas($nodo, $nivel=null,$agregar_titulos=false, $es_total_general = false)
	{
		$titulos = $this->excel_get_titulos();
		if ($es_total_general) {
			$estilo_base = $this->_excel_totales_opciones;
			$this->_objeto_toba_salida->separacion(1);
		} elseif ($nivel > 0) {
			$estilo_base = $this->_excel_totales_cc_1_opciones;
		} else {
			$estilo_base = $this->_excel_totales_cc_0_opciones;
		}				

		//Creo una hoja nueva para el total general
		if ($es_total_general && $this->_excel_cortar_hoja_cc_0) {
			$this->_objeto_toba_salida->crear_hoja('Totales');
			$agregar_titulos = true;
		}
		//Armo los datos de la fila para enviar a la tabla
		list($datos, $estilos, $titulos) = $this->get_fila_totales($nodo, $titulos, $estilo_base, $es_total_general);			
		if (! $agregar_titulos) {
			$titulos = null;
		}
		$this->_objeto_toba_salida->tabla(array($datos), $titulos, $estilos);
	}

	/**
	 *  Define como sera la fila de totales, devuelve un arreglo especificando como se calcula cada columna
	 * @param array $nodo
	 * @param array $columnas
	 * @param array $titulos
	 * @param string $estilo_base
	 * @param boolean $es_total_general
	 * @return array
	 * @ignore
	 */
	protected function get_fila_totales($nodo,  $titulos, $estilo_base, $es_total_general)
	{
		$datos = array();
		$estilos = array();
		$columnas = $this->_cuadro->get_columnas();				
		$cortes_control = $this->_cuadro->get_cortes_control();
		$formateo = $this->_cuadro->get_instancia_clase_formateo('excel');
		$a = 0;
		foreach(array_keys($columnas) as $clave) {
			$estilos[$clave] = array('estilo' => $estilo_base, 'borrar_estilos_nulos' => 1);
			//--Acumulador
			if (isset($nodo['acumulador'][$clave])) {
				//Calcula el valor de la celda
				if ($this->_excel_usar_formulas) {		//Usando formulas
					$rangos = array();					
					if (! $es_total_general) {
						$rangos = $this->excel_get_rangos($nodo, $a);
					} else {
						foreach ($cortes_control as $nodo_cc) {
							$rangos = array_merge($rangos, $this->excel_get_rangos($nodo_cc, $a));
						}
					}
					$formula = '=SUM'.implode(' + SUM', $rangos);
				} else {
					//-- En lugar de hacer una formula, incluir directamente el importe
					$formula = $nodo['acumulador'][$clave];
				}
				
				//La columna lleva un formateo?
				$estilos[$clave]['estilo'] = array_merge($estilos[$clave]['estilo'], $this->excel_get_estilo($columnas[$clave]['estilo']));
				if(isset($columnas[$clave]["formateo"])){
					$metodo = "formato_" . $columnas[$clave]["formateo"];
					list($temp, $estilo) = $formateo->$metodo($formula);
					if (isset($estilo)) {
						$estilos[$clave]['estilo'] = array_merge($estilo, $estilos[$clave]['estilo']);
					}
				}				
				$datos[$clave] = $formula;				
			} else {
				$titulos[$clave] = null;
				$datos[$clave] = null;
			}
			$a++;
		}
		return array($datos, $estilos, $titulos);		
	}
	
	/**
	 * Grafica la sumarizacion de los datos
	 * @param array $datos
	 * @param string $titulo
	 * @param integer $ancho
	 * @param string $css
	 */
	protected function excel_cuadro_sumarizacion($datos, $titulo=null , $ancho=null, $css='col-num-p1')
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

	/**
	 * Genera el pie del corte de control
	 * @param array $nodo
	 * @param boolean $es_ultimo
	 */
	function excel_pie_corte_control( &$nodo, $es_ultimo )
	{
		$span = $this->_cuadro->get_cantidad_columnas();
		$indice_cortes = $this->_cuadro->get_indice_cortes();
		$acumulador = $this->_cuadro->get_acumulador_usuario();

		//-----  Cabecera del PIE --------
		$this->excel_cabecera_pie($indice_cortes, $nodo, $span);

		//----- Totales de columna -------
		if (isset($nodo['acumulador'])) {
			$titulos = false;
			if($indice_cortes[$nodo['corte']]['pie_mostrar_titulos']){
				$titulos = true;
			}
			$this->excel_cuadro_totales_columnas($nodo,
												$nodo['profundidad'],
												$titulos,
												false);
		}
		//------ Sumarizacion AD-HOC del usuario --------
		if(isset($nodo['sum_usuario'])){
			foreach($nodo['sum_usuario'] as $id => $valor){
				$desc = $acumulador[$id]['descripcion'];
				$datos[$desc] = $valor;
			}
			$this->excel_cuadro_sumarizacion($datos,null,300,$nodo['profundidad']);
		}
		//----- Contar Filas
		if($indice_cortes[$nodo['corte']]['pie_contar_filas']) {
			$rangos = $this->excel_get_rangos($nodo);
			$etiqueta = $this->etiqueta_cantidad_filas($nodo['profundidad']);
			$cursor = $this->_objeto_toba_salida->get_cursor();

			$this->_objeto_toba_salida->texto($etiqueta, $this->_excel_contar_filas_op, $span-1);
			$cursor[0] = $cursor[0] + ($span-1);
			$letra = PHPExcel_Cell::stringFromColumnIndex($cursor[0]);
			$formula = '=ROWS'.implode(' + ROWS', $rangos);
			$this->_objeto_toba_salida->texto($formula, $this->_excel_contar_filas_op, 1, null, $cursor);
		}
		//----- Contenido del usuario al final del PIE
		$this->excel_pie_pie($nodo, $es_ultimo);
	}

	function excel_cabecera_pie($indice_cortes, $nodo, $span)
	{
		if($indice_cortes[$nodo['corte']]['pie_mostrar_titular']){
			$metodo_redeclarado = 'excel_pie_cc_cabecera__' . $nodo['corte'];
			if(method_exists($this, $metodo_redeclarado)){
				$descripcion = $this->$metodo_redeclarado($nodo);
			}else{
			 	$descripcion = $this->excel_cabecera_pie_cc_contenido($nodo);
			}
			if ($nodo['profundidad'] > 0) {
				$opciones = $this->_excel_cabecera_pie_cc_1_op;
			} else {
				$opciones = $this->_excel_cabecera_pie_cc_0_op;
			}
			$this->_objeto_toba_salida->texto($descripcion, $opciones, $span);
		}
	}

	function excel_pie_pie($nodo, $es_ultimo)
	{
		$metodo = 'excel_pie_cc_contenido__' . $nodo['corte'];
		if(method_exists($this, $metodo)){
			$this->$metodo($nodo);
		}
		if (!$es_ultimo && $nodo['profundidad'] == 0 && $this->_excel_cortar_hoja_cc_0) {
			$this->_objeto_toba_salida->crear_hoja();
		}
	}

	/**
	 *@ignore
	 */
	protected function excel_get_rangos($nodo, $columna=null)
	{
		$hoja_actual = $this->_objeto_toba_salida->get_hoja_nombre();
		$rangos = array();
		if (isset($nodo['excel_rango'])) {
			if (! isset($columna)) {
				$col_ini_ref = $nodo['excel_rango'][0][0];
				$col_fin_ref = $nodo['excel_rango'][1][0];
			} else {
				$col_ini_ref = $nodo['excel_rango'][0][0] + $columna;
				$col_fin_ref = $col_ini_ref;
			}
			$col_ini = PHPExcel_Cell::stringFromColumnIndex($col_ini_ref);
			$col_fin = PHPExcel_Cell::stringFromColumnIndex($col_fin_ref);
			$hoja = '';
			if ($hoja_actual != $nodo['excel_rango_hoja']) {
				$hoja = "'".$nodo['excel_rango_hoja']."'!";
			}
			$rangos[] = '('.$hoja.$col_ini.$nodo['excel_rango'][0][1].
							':'.$col_fin.$nodo['excel_rango'][1][1].')';
		}
		if (isset($nodo['hijos'])) {
			foreach ($nodo['hijos'] as $nodo_hijo) {
				$rangos = array_merge($rangos, $this->excel_get_rangos($nodo_hijo, $columna));
			}
		}
		return $rangos;
	}

	/**
	 *  Ventana de extension para realizar tareas al iniciar el corte de control
	 * @ignore
	 */
	function excel_cc_inicio_nivel()
	{
	}

	/**
	 *  Ventana de extension para realizar tareas al finalizar el corte de control
	 * @ignore
	 */
	function excel_cc_fin_nivel()
	{
	}

	function excel_inicio_zona_colapsable()
	{
	}
	
	function excel_fin_zona_colapsable()
	{
	}
	
	/**
	 * Define que constante de estilos PHPExcel retornar basandose en la entrada
	 * @param string $estilo
	 * @return array
	 * @ignore
	 */
	protected function excel_get_estilo($estilo)
	{
		switch($estilo) {
			case 'col-num-p1':
			case 'col-num-p2':
			case 'col-num-p3':
			case 'col-num-p4':
				return array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));
				break;
			case 'col-tex-p1':
			case 'col-tex-p2':
			case 'col-tex-p3':
			case 'col-tex-p4':
				return array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT));
				break;
			case 'col-cen-s1':
			case 'col-cen-s2':
			case 'col-cen-s3':
			case 'col-cen-s4':
				return array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
				break;
			default:
				return array();
		}
		return array();
	}
}
?>