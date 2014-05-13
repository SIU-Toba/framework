<?php
class toba_ei_cuadro_salida_xml extends toba_ei_cuadro_salida
{
	protected $xml_ns = '';
	protected $xml_ns_url = '';
	protected $xml_atts_ei = '';
	protected $xml_ancho;
	protected $xml_alto;
	protected $xml_tabla_cols;
	protected $xml_incluir_pie = true;
	protected $xml_incluir_cabecera = true;
	protected $xml_pie;
	protected $xml_cabecera;
	protected $xml_alto_pie;
	protected $xml_alto_cabecera;
	protected $xml_copia;
	protected $xml_margenes=array("sup"=>false,"inf"=>false, "izq"=>false, "der"=>false);
	
	//------------------------------------------------------------------------
	//------------------------- SALIDA XML -----------------------------
	//------------------------------------------------------------------------

	function __construct(toba_ei_cuadro $cuadro)
	{
		parent::__construct($cuadro);
		//Solicito al ei los parametros basicos para la vista xml
		list ($this->xml_ns,
				$this->xml_ns_url,
				$this->xml_atts_ei,
				$this->xml_ancho,
				$this->xml_alto,
				$this->xml_tabla_cols,
				$this->xml_incluir_pie,
				$this->xml_incluir_cabecera,
				$this->xml_pie,
				$this->xml_cabecera,
				$this->xml_alto_pie,
				$this->xml_alto_cabecera,
				$this->xml_copia,
				$this->xml_margenes ) = $this->_cuadro->xml_get_informacion_basica_vista();
	}

	function get_resultado_generacion()
	{
		return $this->_objeto_toba_salida;
	}

	/**
	 * @ignore
	 */
	function xml_inicio(){}

	/**
	 * @ignore
	 */
	function xml_fin()
	{
		if( $this->_cuadro->tabla_datos_es_general() ) {
			$acumulador = $this->_cuadro->get_acumulador_general();
			if (isset($acumulador) && ! $this->_xml_total_generado) {
				$this->xml_cuadro_totales_columnas($acumulador, 0, true);
			}
			$this->xml_acumulador_usuario();
		}
	}

	/**
	* @ignore
	* $nodo se pasa para poder mostrar los totales aqui mismo en caso de cortes con nivel > 0
	*/
	function xml_cuadro(&$filas, &$totales, &$nodo)
	{
		$this->_objeto_toba_salida .='<'.$this->xml_ns.'datos>';
		$formateo = $this->_cuadro->get_instancia_clase_formateo('xml');
		$columnas = $this->_cuadro->get_columnas();
		$datos_cuadro = $this->_cuadro->get_datos();

		//-- Valores de la tabla
		foreach($filas as $f) {
			$this->_objeto_toba_salida .='<'.$this->xml_ns.'fila>';
			$this->generar_layout_fila($columnas, $datos_cuadro, $f, $formateo);
			$this->_objeto_toba_salida .='</'.$this->xml_ns.'fila>';
		}
		list($titulos, $estilos) = $this->xml_get_titulos();

		//-- Para la tabla simple se sacan los totales como parte de la tabla
		if (isset($totales) || isset($nodo['acumulador'])) {
			/* Como el xml no admite continuar una tabla luego de construirla (xml_cuadro)
			Se opta por generar aquí los totales de niveles > 0
			'rompiendo' la separación establecida por el proceso general en pos de una mejor visualización
			*/
			if (! isset($totales)) {
				$totales = $nodo['acumulador'];
				$nodo['xml_acumulador_generado'] = 1; //Esto evita que se muestre la tabla con totales ya que se va a mostrar en esta misma tabla
			} else {
				$this->_xml_total_generado = true;
			}
			$temp = null;
			$this->xml_get_fila_totales($totales, $temp, true);
		}
		$this->_objeto_toba_salida .='</'.$this->xml_ns.'datos>';		
	}

	function generar_layout_fila($columnas, $datos_cuadro, $id_fila, $formateo)
	{
		//---> Creo las CELDAS de una FILA <----
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
				$this->_objeto_toba_salida .= '<'.$this->xml_ns.'dato clave="'.$columnas[$a]["clave"].'" valor="'.$valor.'"/>';
			}
	}
	
	/**
	 * @ignore
	 */
	protected function xml_get_titulos()
	{
		$columnas = $this->_cuadro->get_columnas();

		foreach(array_keys($columnas) as $id) {
			$this->_objeto_toba_salida .= '<'.$this->xml_ns.'col titulo="'.$columnas[$id]['titulo'].'"';
			$estilo = $this->xml_get_estilo($columnas[$id]['estilo']);
			if (isset($estilo)) {
				$this->_objeto_toba_salida .= ' text-align="'.$estilo.'"';
			}
			if(isset($this->xml_columna[$id]) && isset($this->xml_columna[$id]['ancho'])) {
				$this->_objeto_toba_salida .= ' width="'.$this->xml_columna[$id]['ancho'].'"';
			}
			$this->_objeto_toba_salida .= '/>';
		}
	}

	/**
	 * Muestra el mensaje correspondiente al cuadro sin datos
	 * @param string $texto
	 * @ignore
	 */
	function xml_mensaje_cuadro_vacio($texto)
	{
		$this->_objeto_toba_salida .= '<'.$this->xml_ns.'texto valor="'.$texto.'"/>';
	}

	//--------------------------------------------------------
	//---------------- Cortes de Control --------------
	//--------------------------------------------------------

	/**
	 * Deduce el metodo que utilizara para generar la cabecera
	 * @param array $nodo
	 * @ignore
	 */
	function xml_cabecera_corte_control(&$nodo )
	{
		//Dedusco el metodo que tengo que utilizar para generar el contenido
		$metodo = 'xml_cabecera_cc_contenido';
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
	protected function xml_cabecera_cc_contenido(&$nodo)
	{
		$indice_cortes = $this->_cuadro->get_indice_cortes();
		$descripcion = $indice_cortes[$nodo['corte']]['descripcion'];
		$valor = implode(", ",$nodo['descripcion']);
		$this->_objeto_toba_salida .= '<'.$this->xml_ns.'cc>';
		if (trim($descripcion) != '') {
			$this->_objeto_toba_salida .= strip_tags($descripcion.' '.$valor);
		} else {
			$this->_objeto_toba_salida .= strip_tags($valor);
		}
		$this->_objeto_toba_salida .= '</'.$this->xml_ns.'cc>';
	}

	/**
	 * Genera el contenido de la 'cabecera' ubicada en el pie del corte de control
	 * @param array $nodo
	 * @return string
	 */
	protected function xml_cabecera_pie_cc_contenido(&$nodo)
	{
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
	protected function xml_cuadro_totales_columnas($totales,$nivel=null,$agregar_titulos=false, $estilo_linea=null)
	{
		/* Como el xml no admite continuar una tabla luego de construirla (xml_cuadro)
		   Se opta por sacar los totales del mayor nivel dentro de la generación misma del cuadro general
		   'rompiendo' la separación establecida por el proceso general en pos de una mejor visualización
		   Ese nivel nNo entra por aqui porque se le hizo un $nodo['xml_acumulador_generado'] = 1;
		*/
		if($agregar_titulos) {
			$this->xml_get_titulos();
		}
		$this->xml_get_fila_totales($totales, $titulos);
	}

	/**
	 * @ignore
	 */
	protected function xml_get_fila_totales($totales, &$titulos=null, $resaltar=false)
	{
		$formateo = $this->_cuadro->get_instancia_clase_formateo('xml');
		$columnas = $this->_cuadro->get_columnas();
		
		$this->_objeto_toba_salida .= '<'.$this->xml_ns.'fila>';
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
				$this->_objeto_toba_salida .= '<'.$this->xml_ns.'dato clave="'.$clave.'"';
				if ($resaltar) {
					$this->_objeto_toba_salida .= ' font-weight="bold"';
				}
				$this->_objeto_toba_salida .= ' valor="'.$valor.'"/>';
			}else{
				unset($titulos[$clave]);
				$this->_objeto_toba_salida .= '<'.$this->xml_ns.'dato clave="'.$clave.'"/>';
			}
		}
		$this->_objeto_toba_salida .= '</'.$this->xml_ns.'fila>';
	}

	/**
	 * Grafica  la sumarizacion del cuadro
	 * @param array $datos
	 * @param string $titulo
	 * @param integer $ancho
	 * @param string $css
	 * @ignore
	 */
	protected function xml_cuadro_sumarizacion($datos, $titulo=null , $ancho=null, $css='col-num-p1')
	{
		//Titulo
		$this->_objeto_toba_salida .= '<'.$this->xml_ns.'sumarizar';
		if(isset($titulo)){
			$this->_objeto_toba_salida .= ' titulo="'.$titulo.'"';
		}
		$this->_objeto_toba_salida .= '>';
		//Datos
		foreach($datos as $desc => $valor){
			$this->_objeto_toba_salida .= '<'.$this->xml_ns.'dato>'.$desc.': '.$valor.'</'.$this->xml_ns.'dato>';
		}
		$this->_objeto_toba_salida .= '</'.$this->xml_ns.'sumarizar>';
	}

	/**
	 * @ignore
	 */
	function xml_pie_corte_control( &$nodo, $es_ultimo )
	{
		//-----  Cabecera del PIE --------
		$indice_cortes = $this->_cuadro->get_indice_cortes();
		$sumarizador = $this->_cuadro->get_acumulador_usuario();

		$this->xml_cabecera_pie($indice_cortes, $nodo);
		//----- Totales de columna -------
		if (isset($nodo['acumulador']) && ! isset($nodo['xml_acumulador_generado'])) {
			$this->xml_cuadro_totales_columnas($nodo['acumulador'],
												$nodo['profundidad'],
												true,
												null);
		}
		//------ Sumarizacion AD-HOC del usuario --------
		if(isset($nodo['sum_usuario'])){
			foreach($nodo['sum_usuario'] as $id => $valor){
				$desc = $sumarizador[$id]['descripcion'];
				$datos[$desc] = $valor;
			}
			$this->xml_cuadro_sumarizacion($datos,null,300,$nodo['profundidad']);
		}
		//----- Contar Filas
		if($indice_cortes[$nodo['corte']]['pie_contar_filas']){
			$etiqueta = $this->etiqueta_cantidad_filas($nodo['profundidad']) . count($nodo['filas']);
			$this->_objeto_toba_salida .= '<'.$this->xml_ns.'pie tipo="texto">'.$etiqueta.'</'.$this->xml_ns.'pie>';
		}

		//----- Contenido del usuario al final del PIE
		$this->xml_pie_pie($nodo);
	}

	function xml_cabecera_pie($indice_cortes, $nodo)
	{
		if($indice_cortes[$nodo['corte']]['pie_mostrar_titular']){
			$metodo_redeclarado = 'xml_pie_cc_cabecera__' . $nodo['corte'];
			if(method_exists($this, $metodo_redeclarado)){
				$descripcion = $this->$metodo_redeclarado($nodo);
			}else{
			 	$descripcion = $this->xml_cabecera_pie_cc_contenido($nodo);
			}
			$this->_objeto_toba_salida .= '<'.$this->xml_ns.'pie tipo="texto">'.$descripcion.'</'.$this->xml_ns.'pie>';
		}
	}

	function xml_pie_pie($nodo)
	{
		$metodo = 'xml_pie_cc_contenido__' . $nodo['corte'];
		if(method_exists($this, $metodo)){
			$this->$metodo($nodo);
		}
	}

	/**
	 * @ignore
	 */
	function xml_cc_inicio_nivel()
	{
	}

	/**
	 * @ignore
	 */
	function xml_cc_fin_nivel()
	{
	}

	/**
	 * @ignore
	 */
	function xml_inicio_zona_colapsable()
	{
	}
	
	/**
	 * @ignore
	 */
	function xml_fin_zona_colapsable()
	{
	}	
	
	function xml_acumulador_usuario()
	{
		$sumarizacion = $this->_cuadro->calcular_totales_sumarizacion_usuario();
		if (! empty($sumarizacion)) {
			$css = 'cuadro-cc-sum-nivel-1';
			$this->xml_cuadro_sumarizacion($sumarizacion,null,300,$css);
		}
	}

	/**
	 * Permite definir el ancho de las columnas
	 * @param mixed $efs Arreglo asociativo con la forma 'id_columna'=>'ancho', o un string con el id_columna.
	 * @param integer $ancho Ancho de la columna. Válido sólo si el parámetro anterior es un id_columna.
	 */
	function xml_set_columna_ancho($efs, $ancho=null)
	{
		if(is_array($efs)) {
			foreach($efs as $ef=>$ancho) {
				$this->xml_columna[$ef]['ancho'] = $ancho;
			}
		} elseif($ancho) {
			$this->xml_columna[$efs]['ancho'] = $ancho;
		}
	}

	/**
	 * @ignore
	 */
	protected function xml_get_estilo($estilo)
	{
    	switch($estilo) {
    		case 'col-num-p1':
    		case 'col-num-p2':
    		case 'col-num-p3':
    		case 'col-num-p4':
    			return 'right';
    			break;
    		case 'col-tex-p1':
    		case 'col-tex-p2':
    		case 'col-tex-p3':
    		case 'col-tex-p4':
    		    return 'left';
    			break;
    		case 'col-cen-s1':
    		case 'col-cen-s2':
    		case 'col-cen-s3':
    		case 'col-cen-s4':
    		    return 'left';
    			break;
    	}
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------
	//													WRAPPERS DE METODOS A LA CLASE TOBA_EI
	//-----------------------------------------------------------------------------------------------------------------------------------------------------
	/**
	 * @ignore
	 */
	function xml_imagen($src, $tipo='jpg', $titulo=false, $caption=false)
	{
		return $this->_cuadro->xml_imagen($src, $tipo, $titulo, $caption);
	}

	function xml_tabla($datos=array(), $es_formulario=true) 
	{
		return $this->_cuadro->xml_tabla($datos, $es_formulario);
	}

	function xml_texto($texto, $atts=array())
	{
		return $this->_cuadro->xml_texto($texto, $atts);
	}

	function xml_get_elem_comunes()
	{
		return $this->_cuadro->xml_get_elem_comunes();
	}

	function xml_get_att_comunes()
	{
		return $this->_cuadro->xml_get_att_comunes();
	}
}
?>