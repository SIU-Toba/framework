<?php
define("apex_cuadro_cc_tabular","t");
define("apex_cuadro_cc_anidado","a");

/**
 * Un ei_cuadro es una grilla de registros.
 * Puede contener cortes de control, paginado y ordenamiento de columnas. 
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei_cuadro ei_cuadro 
 * @wiki Referencia/Objetos/ei_cuadro
 */
class toba_ei_cuadro extends toba_ei
{
	protected $_info_cuadro = array();
	protected $_info_cuadro_columna = array();
	protected $_info_cuadro_columna_indices = array();
	protected $_info_cuadro_cortes;
	protected $_prefijo = 'cuadro';	
 	protected $_columnas;
    protected $_cantidad_columnas;                 	// Cantidad de columnas a mostrar
    protected $_cantidad_columnas_extra = 0;        	// Cantidad de columnas utilizadas para eventos
    protected $_cantidad_columnas_total;            	// Cantidad total de columnas
    protected $datos;                             	// Los datos que constituyen el contenido del cuadro
    protected $_columnas_clave;                    	
	protected $_clave_seleccionada;
	protected $_estructura_datos;					// Estructura de datos esperados por el cuadro
	protected $_acumulador;							// Acumulador de totales generales
	protected $_acumulador_sum_usuario;				// Acumulador general de las sumarizaciones del usuario
	protected $_sum_usuario;
	protected $_submit_orden_sentido;
	protected $_submit_orden_columna;
	protected $_submit_paginado;
	protected $_submit_seleccion;
	protected $_submit_extra;
	protected $_agrupacion_columnas = array();
	//Orden
    protected $_orden_columna;                     	// Columna utilizada para realizar el orden
    protected $_orden_sentido;                     	// Sentido del orden ('asc' / 'desc')
    protected $_ordenado = false;
	//Paginacion
	protected $_pagina_actual;
	protected $_tamanio_pagina;
	protected $_cantidad_paginas;
	//Cortes control
	protected $_cortes_indice;
	protected $_cortes_def;
	protected $_cortes_control;
	protected $_cortes_modo;
	protected $_cortes_anidado_colap;
	//Salida
	protected $_clase_formateo = 'toba_formateo';
	protected $_mostrar_titulo_antes_cc = false;
	protected $_tipo_salida;
	protected $_salida;
	protected $_total_registros; 
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
	
	
    function __construct($id)
    {
    	$propiedades = array();
		$propiedades[] = "tamanio_pagina";
		$this->set_propiedades_sesion($propiedades);
        parent::__construct($id);
		$this->procesar_definicion();
		$this->inicializar_manejo_clave();	
		if($this->existe_paginado())
			$this->inicializar_paginado();
		if (isset($this->_memoria['ordenado'])) {
			$this->_ordenado = $this->_memoria['ordenado'];
		}
		$this->inspeccionar_sumarizaciones_usuario();
	}

	/**
	 * Método interno para iniciar el componente una vez construido
	 * @ignore 
	 */
	function inicializar($parametros)
	{
		parent::inicializar($parametros);
		$this->_submit_orden_columna = $this->_submit."__orden_columna";
		$this->_submit_orden_sentido = $this->_submit."__orden_sentido";
		$this->_submit_seleccion = $this->_submit."__seleccion";
		$this->_submit_extra = $this->_submit."__extra";
		$this->_submit_paginado = $this->_submit."__pagina_actual";
	}
	
	/**
	 * @ignore 
	 */
	protected function procesar_definicion()
	{
		$estructura_datos = array();
		//Armo una estructura que describa las caracteristicas de los cortes
		if($this->existen_cortes_control()){
			for($a=0;$a<count($this->_info_cuadro_cortes);$a++){
				$id_corte = $this->_info_cuadro_cortes[$a]['identificador'];						// CAMBIAR !
				//Genero el Indice
				$this->_cortes_indice[$id_corte] =& $this->_info_cuadro_cortes[$a];
				//Genero la tabla de definiciones	
				$col_id = explode(',',$this->_info_cuadro_cortes[$a]['columnas_id']);
				$col_id = array_map('trim',$col_id);
				$this->_cortes_def[$id_corte]['clave'] = $col_id;
				$col_desc = explode(',',$this->_info_cuadro_cortes[$a]['columnas_descripcion']);
				$col_desc = array_map('trim',$col_desc);
				$this->_cortes_def[$id_corte]['descripcion'] = $col_desc;
  				$this->_cortes_def[$id_corte]['colapsa'] = $this->_info_cuadro_cortes[$a]['modo_inicio_colapsado']; 				
				$estructura_datos = array_merge($estructura_datos, $col_desc, $col_id);
			}
			$this->_cortes_modo = $this->_info_cuadro['cc_modo'];
			$this->_cortes_anidado_colap = $this->_info_cuadro['cc_modo_anidado_colap'];
		}
		//Procesamiento de columnas
		$this->_columnas = array();
		for($a=0;$a<count($this->_info_cuadro_columna);$a++){
			// Indice de columnas
			$clave = $this->_info_cuadro_columna[$a]['clave'];
			$this->_info_cuadro_columna_indices[$clave] = $a;
			$this->_columnas[ $clave ] =& $this->_info_cuadro_columna[$a];
			//Sumarizacion general
			if ($this->_columnas[ $clave ]['total'] == 1 && ! isset($this->_acumulador[$clave])) {
				$this->_acumulador[$clave]=0;
			}
			// Sumarizacion de columnas por corte
			if(trim($this->_columnas[ $clave ]['total_cc'])!=''){
				$cortes = explode(',',$this->_columnas[ $clave ]['total_cc']);
				$cortes = array_map('trim',$cortes);
				foreach($cortes as $corte){
					$this->_cortes_def[$corte]['total'][] = $clave;	
				}
			}
			//Agrupacion de columnas
			$grupo = isset($this->_columnas[$clave]['grupo']) ? $this->_columnas[$clave]['grupo'] : null;
			if (isset($grupo) && $grupo != '') {
				$this->_agrupacion_columnas[$grupo][] = $clave;
			}
		}
		$this->_estructura_datos = array_unique($estructura_datos);
	}

	/**
	 * Elimina columnas del cuadro
	 * @param array $columnas. Ids de las columnas a eliminar
	 */
	function eliminar_columnas($columnas)
	{
		foreach($columnas as $clave) {
			$id = $this->_info_cuadro_columna_indices[$clave];
			array_splice($this->_info_cuadro_columna, $id, 1);
			$this->procesar_definicion();	//Se re ejecuta por eliminación para actualizar $this->_info_cuadro_columna_indices
		}		
	}
	
	/**
	 * Chequea si una columna existe en la definicion del cuadro.
	 * @param $columna. Id de la columna.
	 */
	function existe_columna($columna){
		$id = $this->_info_cuadro_columna_indices[$columna];
		return isset($this->_info_cuadro_columna[$id]) && ($this->_info_cuadro_columna[$id]['clave'] == $columna);
	}
	
	/**
	 * Chequea si un conjunto de columnas existen en la definicion del cuadro.
	 * @param array $columnas. Ids de las columnas.
	 */	
	function existen_columnas($columnas){
		$existen = true;
		foreach ($columnas as $columna){
			$existen = $existen && $this->existe_columna($columna);
			if (!$existen) {
				break;
			}
		}
		return $existen;
	}
	
	/**
	 * Elimina todas las columnas actualmente definidas en el cuadro
	 */
	function limpiar_columnas()
	{
		$this->_info_cuadro_columna = array();
		$this->procesar_definicion(); //Se re ejecuta por eliminación para actualizar $this->_info_cuadro_columna_indices
	}

	/**
	 * Agrega nuevas definiciones de columnas al cuadro
	 * @param array $columnas
	 */
	function agregar_columnas($columnas)
	{
		foreach ($columnas as $clave => $valor) {
			if (!isset($valor['estilo']))
				$columnas[$clave]['estilo'] = 'col-tex-p1';
			if (!isset($valor['estilo_titulo']))
				$columnas[$clave]['estilo_titulo'] = 'ei-cuadro-col-tit';
			if (!isset($valor['estilo_titulo']))
				$columnas[$clave]['total_cc'] = '';
			if (!isset($valor['total']))
				$columnas[$clave]['total'] =  0;				
		}
		$this->_info_cuadro_columna = array_merge($this->_info_cuadro_columna, array_values($columnas));
		$this->procesar_definicion(); //Se re ejecuta por eliminación para actualizar $this->_info_cuadro_columna_indices
	}	
	
	/**
	 * Agrupa columnas adyacentes bajo una etiqueta común
	 *
	 * @param string $nombre_grupo Etiqueta que toma el grupo
	 * @param array $columnas Id. de las columnas a agrupar, deben ser adyacentes
	 */
	function set_grupo_columnas($nombre_grupo, $columnas)
	{
		$this->_agrupacion_columnas[$nombre_grupo] = $columnas;
		foreach ($columnas as $columna) {
			if (! isset($this->_columnas[$columna])) {
				throw new toba_error_def("No es posible agrupar las columnas, la columna '$columna' no existe");
			}
			$this->_columnas[$columna]['grupo'] = $nombre_grupo;
		}
	}
	
	/**
	 * Retorna la definición de las columnas actuales del cuadro
	 * @return array
	 */
	function get_columnas()
	{
		return $this->_columnas;	
	}
	
	/**
	 * Si el usuario declaro funciones de sumarizacion por algun corte,
	 * esta funcion las agrega en la planificacion de la ejecucion.
	 * @ignore 
	 */
	protected function inspeccionar_sumarizaciones_usuario()
	{
		//Si soy una subclase
		if($this->_info['subclase']){
			$this->_sum_usuario = array();
			$clase = new ReflectionClass(get_class($this));
			foreach ($clase->getMethods() as $metodo){
				$id = null;
				if (substr($metodo->getName(), 0, 12) == 'sumarizar_cc'){		//** Sumarizacion Corte de control
					$temp = explode('__', $metodo->getName());
					if(count($temp)!=3){
						throw new toba_error_def("La funcion de sumarizacion esta mal definida");	
					}
					$id = $temp[2];
					$corte = $temp[1];
					if(!isset($this->_cortes_def[$corte])){	//El corte esta definido?
						throw new toba_error_def("La funcion de sumarizacion no esta direccionada a un CORTE existente");	
					}
					//Agrego la sumarizacion al corte
					$this->_cortes_def[$corte]['sum_usuario'][]=$id;
				}elseif(substr($metodo->getName(), 0, 11) == 'sumarizar__'){ 	//** Sumarizacion GENERAL
					$temp = explode('__', $metodo->getName());
					$id = $temp[1];
					$corte = 'toba_total';
					$this->_acumulador_sum_usuario[$id] = 0;
				}
				if($id){
					if(isset($this->_sum_usuario[$id])){
						throw new toba_error_def("Las funciones de sumarizacion deben tener IDs unicos. El id '$id' ya existe");	
					}
					// Agrego la sumarizacion en la pila de sumarizaciones.
					$this->_sum_usuario[$id]['metodo'] = $metodo->getName();
					$this->_sum_usuario[$id]['corte'] = $corte;
					$this->_sum_usuario[$id]['descripcion'] = $this->get_desc_sumarizacion($metodo->getDocComment());
				}
			}
		}		
	}

	/**
	 * @ignore 
	 */
	protected function get_desc_sumarizacion($texto)
	{
	    $desc =  parsear_doc_comment( $texto );
		return trim($desc != '')? $desc : 'Descripcion no definida';
	}
	
	function destruir()
	{
		if (isset($this->_ordenado)) {
			$this->_memoria['ordenado'] = $this->_ordenado;	
		}
		$this->finalizar_seleccion();
		$this->finalizar_ordenamiento();
		$this->finalizar_paginado();
		parent::destruir();
	}
	
	/**
	 * @ignore
	 */
	function aplicar_restricciones_funcionales()
	{
		parent::aplicar_restricciones_funcionales();

		//-- Restricción funcional Columnas no-visibles ------
		$no_visibles = toba::perfil_funcional()->get_rf_cuadro_cols_no_visibles($this->_id[1]);
		if (! empty($no_visibles)) {
			$alguno_eliminado = false;			
			for($a=0; $a<count($this->_info_cuadro_columna); $a++){
				if (in_array($this->_info_cuadro_columna[$a]['objeto_cuadro_col'], $no_visibles)) {
					$clave = $this->_info_cuadro_columna[$a]['clave'];
					array_splice($this->_info_cuadro_columna, $a, 1);
					$alguno_eliminado = true;
					toba::logger()->debug("Restricción funcional. Se filtro la columna: $clave", 'toba');
				}
			}
			if ($alguno_eliminado) {
				$this->procesar_definicion();
			}
		}
		//----------------

	}	

//################################################################################
//############################        EVENTOS        #############################
//################################################################################

	/**
	 * @ignore 
	 */
	protected function cargar_lista_eventos()
	{
		parent::cargar_lista_eventos();
		if($this->_info_cuadro["ordenar"]) { 
			$this->_eventos['ordenar'] = array('maneja_datos' => true);
		}
		if ($this->_info_cuadro["paginar"]) {
			$this->_eventos['cambiar_pagina'] = array('maneja_datos' => true);
		}
	}

	/**
	 * @ignore 
	 */	
	function disparar_eventos()
	{
		if (isset($this->_memoria['eventos']['ordenar'])) {
			$this->refrescar_ordenamiento();
		}
		if (isset($_POST[$this->_submit]) && $_POST[$this->_submit]!="") {
			$evento = $_POST[$this->_submit];		
			//El evento estaba entre los ofrecidos?
			if(isset($this->_memoria['eventos'][$evento]) ) {
				switch ($evento) {
					case 'ordenar':
						if (isset($this->_orden_columna) && isset($this->_orden_sentido)) {
							$parametros = array('sentido'=> $this->_orden_sentido, 'columna'=>$this->_orden_columna);
							$exitoso = $this->reportar_evento( $evento, $parametros );
							if ($exitoso !== apex_ei_evt_sin_rpta && $exitoso === false) {
								$this->_ordenado = true;
							} else {
								$this->_ordenado = false;	
							}
						}
						break;
					case 'cambiar_pagina':
						$this->cargar_cambio_pagina();
						$parametros = $this->_pagina_actual;
						$this->reportar_evento( $evento, $parametros );
						break;
					default:
						$this->cargar_seleccion();
						$parametros = null;
						if (isset($this->_clave_seleccionada)) {
							$parametros = $this->_clave_seleccionada;
						} else {
							if (isset($_POST[$this->_submit_extra])) {
								$parametros = $_POST[$this->_submit_extra];
							}
						}
						$this->reportar_evento( $evento, $parametros );						
				}
			}
		}
		$this->borrar_memoria_eventos_atendidos();
	}

	/**
	 * Carga el cuadro con un conjunto de datos
	 * @param array $datos Arreglo en formato RecordSet
	 */
    function set_datos($datos)
    {
		$this->datos = $datos;
		if (!is_array($this->datos)) {
			throw new toba_error_def( $this->get_txt() . 
					" El parametro para cargar el cuadro posee un formato incorrecto:" .
						"Se esperaba un arreglo de dos dimensiones con formato recordset.");
		}
		if (count($this->datos) > 0 ) {
			$this->validar_estructura_datos();
			// - 2 - Ordenamiento
			if($this->hay_ordenamiento()){
				$this->ordenar();
			}
			
			// - 3 - Cuento los registros disponibles en caso de no haber seteo explicito
			$this->contar_registros();			
						
			// - 4 - Paginacion
			if( $this->existe_paginado() ){
				$this->generar_paginado();
			}
			
			// - 5 - Cortes de control
			if ( $this->existen_cortes_control() ){
				$this->planificar_cortes_control();
			} else {
				$this->calcular_totales_generales();
			}
		}
	}

    
//################################################################################
//############################   Procesos GENERALES   ############################
//################################################################################

	/**
	 * @ignore 
	 */
	protected function validar_estructura_datos()
	{
		$muestra = current($this->datos);
		if (!is_array($muestra)) {
			$error = array_values($this->_estructura_datos);
		} else {
			$error = array();
			foreach($this->_estructura_datos as $columna){
				if(!isset($muestra[$columna])){
					$error[] = $columna;
				}
			}
		}
		if(count($error)>0){
			throw new toba_error_def( $this->get_txt() . 
					" El array provisto para cargar el cuadro posee un formato incorrecto\n" .
					" Las columnas: '". implode("', '",$error) ."' NO EXISTEN");
		}
	}

	/**
	 * El cuadro posee datos?
	 * @return boolean
	 */
	function datos_cargados()
	{
		return isset($this->datos) && is_array($this->datos) && (count($this->datos) > 0);
	}

	/**
	 * @ignore 
	 * Esto esta duplicado en el calculo de cortes de control por optimizacion
	 */
	protected function calcular_totales_generales()
	{
		foreach(array_keys($this->datos) as $dato) {
			//Incremento el acumulador general
			if(isset($this->_acumulador)){
				foreach(array_keys($this->_acumulador) as $columna){
					$this->_acumulador[$columna] += $this->datos[$dato][$columna];
				}	
			}
		}
	}
	
	/**
	 * Cambia el mensaje a mostrar cuando el cuadro no tiene datos
	 * @param string $mensaje
	 */
	function set_eof_mensaje($mensaje)
	{
		$this->_info_cuadro["eof_customizado"] = $mensaje;		
	}

	/**
	 * Habilita o deshabilita el mensaje a mostrar cuando el cuadro no tiene datos que mostrar
	 * @param boolean $mostrar
	 */
	function set_eof_mostrar($mostrar=true)
	{
		$valor = ($mostrar) ? 0 : 1;
		$this->_info_cuadro["eof_invisible"] = $valor;
	}
	
	/**
	 * El cuadro muestra su título una única vez antes de los cortes de control
	 * @param boolean $unico
	 */
	function set_mostrar_titulo_antes_cc($unico=true)
	{
		$this->_mostrar_titulo_antes_cc = $unico;
	}

	/**
	 * Define si la exportacion a excel utilizara formulas o no 
	 * @param boolean $usar_formulas
	 */
	function set_excel_usar_formulas($usar_formulas)
	{
		$this->_excel_usar_formulas = $usar_formulas;
	}
	
	/**
	 * @ignore
	 */
	protected function contar_registros()
	{		
		if (! empty($this->datos) && (!isset($this->_total_registros) || ! is_numeric($this->_total_registros))){ 
			$this->_total_registros = count($this->datos);		
		}
	}

//################################################################################
//############################   CLAVE  y  SELECCION   ###########################
//################################################################################

	/**
	 * @ignore 
	 */
	protected function inicializar_manejo_clave()
	{
        if($this->_info_cuadro["clave_datos_tabla"]){										//Clave del DT
			$this->_columnas_clave = array( apex_datos_clave_fila );
        }elseif(trim($this->_info_cuadro["columnas_clave"])!=''){
            $this->_columnas_clave = explode(",",$this->_info_cuadro["columnas_clave"]);		//Clave usuario
            $this->_columnas_clave = array_map("trim",$this->_columnas_clave);
        }else{
			$this->_columnas_clave = null;
        }		
		//Agrego las columnas de la clave en la definicion de la estructura de datos
		if(is_array($this->_columnas_clave)){
			$estructura_datos = array_merge( $this->_columnas_clave, $this->_estructura_datos);
			$this->_estructura_datos = array_unique($estructura_datos);
		}
		//Inicializo la seleccion
		$this->_clave_seleccionada = null;
	}

	/**
	 * @ignore 
	 */
	protected function finalizar_seleccion()
	{
		if (isset($this->_clave_seleccionada)) {
			$this->_memoria['clave_seleccionada'] = $this->_clave_seleccionada;
		} else {
			unset($this->_memoria['clave_seleccionada']);
		}
	}

	/**
	 * @ignore 
	 */	
	protected function cargar_seleccion()
	{	
		$this->_clave_seleccionada = null;
		//La seleccion se inicializa con el del pedido anterior
		if (isset($this->_memoria['clave_seleccionada']))
			$this->_clave_seleccionada = $this->_memoria['clave_seleccionada'];
		//La seleccion se actualiza cuando el cliente lo pide explicitamente
		if(isset($_POST[$this->_submit_seleccion])) {
			$clave = $_POST[$this->_submit_seleccion];
			if ($clave != '') {
				if (! isset($this->_memoria['claves_enviadas']) || ! in_array($clave, $this->_memoria['claves_enviadas'])) {
					throw new toba_error_seguridad($this->get_txt()." La clave '$clave' del cuadro no estaba entre las enviadas");
				}
				$clave = explode(apex_qs_separador, $clave);				
				//Devuelvo un array asociativo con el nombre de las claves
				for($a=0;$a<count($clave);$a++) {
					$this->_clave_seleccionada[$this->_columnas_clave[$a]] = $clave[$a];		
				}
			}
		}	
	}

	/**
	 * Deja al cuadro sin selección alguna de fila
	 */
	function deseleccionar()
	{
		$this->_clave_seleccionada = null;
	}

	/**
	*	Indica al cuadro cual es la clave seleccionada. 
	*	A la hora de mostrar la grilla se crea un feedback gráfico sobre la fila que posea esta clave
	*	@param array $clave Arreglo asociativo id_clave => valor_clave
	*/
	function seleccionar($clave)
	{
		$this->_clave_seleccionada = $clave;
	}

	/**
	 * Retorna verdadero si existe alguna fila seleccionada
	 * @return boolean
	 */
	function hay_seleccion()
	{
		return isset($this->_clave_seleccionada);
	}

	/**
	 * Retorna la clave serializada de una fila dada
	 * @param integer $fila Numero de fila
	 * @return string Clave serializada
	 */
    function get_clave_fila($fila)
    {
        $id_fila = "";
        if (isset($this->_columnas_clave)) {
	        foreach($this->_columnas_clave as $clave){
	            $id_fila .= $this->datos[$fila][$clave] . apex_qs_separador;
	        }
        }
        $id_fila = substr($id_fila,0,(strlen($id_fila)-(strlen(apex_qs_separador))));   
        return $id_fila;
    }

    /**
     * Retorna un arreglo con las claves de la fila dada
     * @param integer $fila Numero de fila
     * @return array Arreglo columna=>valor
     */
	function get_clave_fila_array($fila)
	{
        if (isset($this->_columnas_clave)) {
	        foreach($this->_columnas_clave as $clave){
	            $array[$clave] = $this->datos[$fila][$clave];
	        }
	        return $array;
        }
	}

    /**
    *	@deprecated Desde 0.8.3. Usar get_clave_seleccionada
    */
	function get_clave()
	{
		toba::logger()->obsoleto(__CLASS__, __FUNCTION__, "0.8.3", "Usar get_clave_seleccionada");
		return $this->get_clave_seleccionada();
	}
	
    /**
    *	En caso de existir una fila seleccionada, retorna su clave
	*	@return array Arreglo asociativo id_clave => valor_clave    
    */
	function get_clave_seleccionada()
	{
		return $this->_clave_seleccionada;
	}

//################################################################################
//##############################    PAGINACION    ################################
//################################################################################

	/**
	 * Retorna verdadero si el cuadro se pagina en caso de superar una cantidad dada de registros
	 * @return boolean
	 */
	function existe_paginado()
	{
		//-- Se busca el tipo de salida antes de producirse la etapa de servicios porque el paginado no es reversible
		$servicio = toba::memoria()->get_servicio_solicitado();
		if (($servicio == 'vista_excel' || $servicio == 'vista_pdf') && !$this->_info_cuadro['exportar_paginado']) {
			return false;
		}
		return $this->_info_cuadro["paginar"];
	}
	
	/**
	 * @ignore 
	 */
	protected function inicializar_paginado()
	{
		if(isset($this->_memoria["pagina_actual"])){
			$this->_pagina_actual = $this->_memoria["pagina_actual"];
		}else{
			$this->_pagina_actual = 1;
		}
		$this->set_tamanio_pagina();
	}
	
	/**
	 * @ignore 
	 */	
	protected function finalizar_paginado()
	{
		if (isset($this->_pagina_actual)) {
			$this->_memoria['pagina_actual']= $this->_pagina_actual;
		} else {
			unset($this->_memoria['pagina_actual']);
		}		
	}

	/**
	 * Pagina los datos actuales del cuadro
	 * Restringe los datos a la pagina actual y calcula la cantidad de paginas posibles
	 * @ignore 
	 */
	protected function generar_paginado()
	{
		if($this->_info_cuadro["tipo_paginado"] == 'C') {
			if (!isset($this->_total_registros) || ! is_numeric($this->_total_registros)) {
				throw new toba_error_def("El cuadro necesita recibir la cantidad total de registros con el metodo set_total_registros para poder paginar");
			}
			$this->_cantidad_paginas = ceil($this->_total_registros/$this->_tamanio_pagina);
			if ($this->_pagina_actual > $this->_cantidad_paginas)  {
				$this->_pagina_actual = 1;
			}
		} elseif($this->_info_cuadro["tipo_paginado"] == 'P') {
			// 1) Calculo la cantidad total de registros
			if($this->_total_registros > 0) {
				// 2) Calculo la cantidad de paginas
				$this->_cantidad_paginas = ceil($this->_total_registros/$this->_tamanio_pagina);            
				if ($this->_pagina_actual > $this->_cantidad_paginas) 
					$this->_pagina_actual = 1;
				$offset = ($this->_pagina_actual - 1) * $this->_tamanio_pagina;
				$this->datos = array_slice($this->datos, $offset, $this->_tamanio_pagina);
			}
		}else{
			$this->_cantidad_paginas = 1;
		}
	}

	/**
	 * Informa al cuadro la cantidad total de registros que posee el set de datos
	 * Este método se utiliza cuando el paginado no lo hace el propio cuadro, en este caso
	 * es necesario informarle la cantidad total de registros así puede armar la barra de paginado
	 * @param integer $cant
	 */
	function set_total_registros($cant)
	{
		$this->_total_registros = $cant;
	}

	/**
	 * Retorna el tamaño de página actual en el paginado (si está presente el paginado)
	 * @return integer
	 */
	function get_tamanio_pagina()
	{
		return $this->_tamanio_pagina;
	}
	
	/**
	 * Cambia el tamaño de página a usar en el paginado
	 * @param integer $tam
	 */
	function set_tamanio_pagina($tamanio=null)
	{
		if(isset($tamanio)){
			$this->_tamanio_pagina = $tamanio;
		} else {
	        $this->_tamanio_pagina = isset($this->_info_cuadro["tamano_pagina"]) ? $this->_info_cuadro["tamano_pagina"] : 80;
		}
	}
	
	/**
	 * Retorna la página actualmente seleccionada por el usuario, si existe el paginado
	 * @return integer
	 */
	function get_pagina_actual()
	{
		return $this->_pagina_actual;
	}
	
	/**
	 * Fuerza al cuadro a mostrar una página específica 
	 * @param integer $pag
	 */
	function set_pagina_actual($pag)
	{
		$this->_pagina_actual = $pag;	
	}
	
	/**
	 * @ignore 
	 */	
	protected function cargar_cambio_pagina()
	{	
		if(isset($_POST[$this->_submit_paginado]) && trim($_POST[$this->_submit_paginado]) != '') 
			$this->_pagina_actual = $_POST[$this->_submit_paginado];
	}

//################################################################################
//###########################    CORTES de CONTROL    ############################
//################################################################################

	/**
	 * Fuerza a que los cortes de control se inicien de manera colapsada. Por defecto true
	 * @param boolean $colapsado 
	 */
	function set_cortes_colapsados($colapsado=true){
		$this->_cortes_anidado_colap = $colapsado;
	}

	/**
	 * Indica la existencia o no de cortes de control en el cuadro.
	 * @return boolean
	 */
	function existen_cortes_control()
	{
		return (count($this->_info_cuadro_cortes)>0);
	}
	
	/**
	 * @ignore 
	 */	
	function planificar_cortes_control()
	/*
		Primera pasada por las filas del SET de datos.
		Creo la estructura de los cortes.
			La idea de este proceso es dejar informacion para que la generacion 
			de customizaciones sea simple. El costo es que se guardan muchos datos
			que en varios casos son innecesarios, hay que controlar la performance
			del elemento para ver si se justifica.
	*/
	{
		$this->_cortes_niveles = count($this->_info_cuadro_cortes);
		$this->_cortes_control = array();
		foreach(array_keys($this->datos) as $dato)
		{
			//Punto de partida desde donde construir el arbol
			$ref =& $this->_cortes_control;
			$profundidad = 0;
			foreach(array_keys($this->_cortes_def) as $corte)
			{
				$clave_array=array();
				//-- Recupero la clave de la fila en el nivel
				foreach($this->_cortes_def[$corte]['clave'] as $id_corte){
					$clave_array[$id_corte] = $this->datos[$dato][$id_corte];
				}
				$clave = implode('_|_',$clave_array);
				//---------- Inicializacion el NODO ----------
				if(!isset($ref[$clave])){
					$ref[$clave]=array();
					$ref[$clave]['corte']=$corte;
					$ref[$clave]['profundidad']=$profundidad;
					//Agrego la clave
					$ref[$clave]['clave']=$clave_array;
					//Agrego la descripcion
					foreach($this->_cortes_def[$corte]['descripcion'] as $desc_corte){
						$ref[$clave]['descripcion'][$desc_corte] = $this->datos[$dato][$desc_corte];
					}
					//Inicializo el ACUMULADOR de columnas
					if(isset($this->_cortes_def[$corte]['total'])){
						foreach($this->_cortes_def[$corte]['total'] as $columna){
							$ref[$clave]['acumulador'][$columna] = 0;
						}
					}
					$ref[$clave]['hijos']=null;
				}
				//---------- Fin inic. NODO ------------------
				//Agrego la fila actual a la lista de filas
				$ref[$clave]['filas'][]=$dato;
				if(isset($ref[$clave]['acumulador'])){
					foreach(array_keys($ref[$clave]['acumulador']) as $columna){
						$ref[$clave]['acumulador'][$columna] += $this->datos[$dato][$columna];
					}
				}
				//Cambio el punto de partida				
				$ref =& $ref[$clave]['hijos'];
				$profundidad++;
			}
			//Incremento el acumulador general
			if(isset($this->_acumulador)){
				foreach(array_keys($this->_acumulador) as $columna){
					$this->_acumulador[$columna] += $this->datos[$dato][$columna];
				}	
			}
		}
	}

//################################################################################
//#################################    ORDEN    ##################################
//################################################################################

	/**
	 * @ignore 
	 */
	protected function finalizar_ordenamiento()
	{
		if (isset($this->_orden_columna)) {
			$this->_memoria['orden_columna']= $this->_orden_columna;
		} else {
			unset($this->_memoria['orden_columna']);
		}
		if (isset($this->_orden_sentido)) {
			$this->_memoria['orden_sentido']= $this->_orden_sentido;
		} else {
			unset($this->_memoria['orden_sentido']);
		}		
	}

	/**
	 * Actualiza el estado actual del ordenamiento en base a la memoria anterior y lo que dice el usuario a través del POST
	 * @ignore 
	 */
	protected function refrescar_ordenamiento()
	{
		//¿Viene seteado de la memoria?
        if(isset($this->_memoria['orden_columna']))
			$this->_orden_columna = $this->_memoria['orden_columna'];
		if(isset($this->_memoria['orden_sentido']))
			$this->_orden_sentido = $this->_memoria['orden_sentido'];

		//¿Lo cargo el usuario?
		if (isset($_POST[$this->_submit_orden_columna]) && $_POST[$this->_submit_orden_columna] != '') {
			$nueva_col = $_POST[$this->_submit_orden_columna];
		}
		if (isset($_POST[$this->_submit_orden_sentido]) && $_POST[$this->_submit_orden_sentido] != '') {
			$nuevo_sent = $_POST[$this->_submit_orden_sentido];
		}
		if (isset($nueva_col) && isset($nuevo_sent)) {
			//Si se vuelve a pedir el mismo ordenamiento, se anula			
			if (isset($this->_orden_columna) && $nueva_col == $this->_orden_columna &&
				isset($this->_orden_sentido) && $nuevo_sent == $this->_orden_sentido) {
				unset($this->_orden_columna);
				unset($this->_orden_sentido);
			} else {
				$this->_orden_columna = $nueva_col;
				$this->_orden_sentido = $nuevo_sent;
			}
		}
	}

	/**
	 * Retorna verdadero si el cuadro actualmente se encuentra ordenado por alguna columna por parte del usuario
	 * @return boolean
	 */
	function hay_ordenamiento()
	{
        return (isset($this->_orden_sentido) && isset($this->_orden_columna));
	}

	/**
	 * Método estandar de ordenamiento de los datos, utilizando array_multisort
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 */
    protected function ordenar()
	{
		if (! $this->_ordenado) {
			$ordenamiento = array();

			switch( $this->_columnas[$this->_orden_columna]['formateo'] )
			{		
				case "fecha":
					// Paso los valores de la columna a timestamp para ordenar por fecha	
			    	foreach ($this->datos as $fila) {
			   			$ordenamiento[] = strtotime($fila[$this->_orden_columna])  ;
					}
					//Ordeno segun el sentido
			        if($this->_orden_sentido == "asc"){
			            array_multisort($ordenamiento, SORT_ASC , SORT_NUMERIC, $this->datos);
			        } elseif ($this->_orden_sentido == "des"){
			            array_multisort($ordenamiento, SORT_DESC , SORT_NUMERIC, $this->datos);
			        }
			    break;
		        
		    	default:
					foreach ($this->datos as $fila){
						$ordenamiento[] = $fila[$this->_orden_columna];
			        }
			        //Ordeno segun el sentido
			        if($this->_orden_sentido == "asc"){
			            array_multisort($ordenamiento, SORT_ASC , $this->datos);
			        } elseif ($this->_orden_sentido == "des"){
			            array_multisort($ordenamiento, SORT_DESC , $this->datos);
			        }
		    } //SWITCH
		} //IF
    }

//################################################################################
//###############################    API basica    ###############################
//################################################################################

	/**
	 * Cambia el título o descripción de una columna dada del cuadro
	 * @param string $id_columna Id de la columna a cambiar
	 * @param string $titulo
	 */
	function set_titulo_columna($id_columna, $titulo)
	{
		$this->_columnas[$id_columna]["titulo"] = $titulo;
	}    

	/**
	 * Cambia la forma en que se le da formato a una columna
	 * @param string $id_columna
	 * @param string $funcion Nombre de la función de formateo, sin el prefijo 'formato_'
	 * @param string $clase Nombre de la clase que contiene la funcion, por defecto toba_formateo
	 */
	function set_formateo_columna($id_columna, $funcion, $clase=null)
	{
		$this->_columnas[$id_columna]["formateo"] = $funcion;
		if (isset($clase)) {
			$this->_clase_formateo = $clase;
		}
	}
	
	/**
	 * Retorna el conjunto de datos que actualmente posee el cuadro
	 * @return array
	 */
    function get_datos()
    {
        return $this->datos;    
    }	

    function get_estructura_datos()
	{
		return $this->_estructura_datos;		
	}
	
	
//################################################################################
//#####################    INTERFACE GRAFICA GENERICA  ###########################
//################################################################################

	/**
	 * Wrapper que genera los distintos tipos de salida necesario de acuerdo al parametro especificado
	 * @param string $tipo
	 */
	protected function generar_salida($tipo)
	{
		if($tipo!="html" && $tipo!="impresion_html" && $tipo!="pdf" && $tipo!='excel'){
			throw new toba_error_seguridad("El tipo de salida '$tipo' es invalida");	
		}
		$this->_tipo_salida = $tipo;
		if( $this->datos_cargados() ){
			$this->inicializar_generacion();
			$this->generar_inicio();
			//Generacion de contenido
			if($this->existen_cortes_control()){
				$this->generar_cortes_control();
			}else{
				$filas = array_keys($this->datos);
				$this->generar_cuadro($filas, $this->_acumulador);
			}
			$this->generar_fin();
			if( false && $this->existen_cortes_control() ){
				ei_arbol($this->_sum_usuario,"\$this->_sum_usuario");
				ei_arbol($this->_cortes_def,"\$this->_cortes_def");
				ei_arbol($this->_cortes_control,"\$this->_cortes_control");
			}
		}else{
            if ($this->_info_cuadro["eof_invisible"]!=1){
                if(trim($this->_info_cuadro["eof_customizado"])!=""){
					$texto = $this->_info_cuadro["eof_customizado"];
                }else{
					$texto = "No hay datos cargados";
                }
				$this->generar_mensaje_cuadro_vacio($texto);
            }
		}
	}

	/**
	 * @ignore 
	 */
	protected function inicializar_generacion()
	{
		$this->_cantidad_columnas = count($this->_columnas);
		if ( $this->_tipo_salida == 'html' ) {
			$this->_cantidad_columnas_extra = $this->cant_eventos_sobre_fila();
		}
		$this->_cantidad_columnas_total = $this->_cantidad_columnas + $this->_cantidad_columnas_extra;
	}

	/**
	 * @ignore 
	 */
	 protected function generar_inicio(){
		$metodo = $this->_tipo_salida . '_inicio';
		$this->$metodo();
	}

	/**
	 * @ignore 
	 */
	protected function generar_cuadro(&$filas, &$totales=null, &$nodo=null){
		$metodo = $this->_tipo_salida . '_cuadro';
		$this->$metodo($filas, $totales, $nodo);
	}

	/**
	 * @ignore 
	 */
	protected function generar_fin(){
		$metodo = $this->_tipo_salida . '_fin';
		$this->$metodo();
	}

	/**
	 * @ignore 
	 */
	protected function generar_mensaje_cuadro_vacio($texto){
		$metodo = $this->_tipo_salida . '_mensaje_cuadro_vacio';
		$this->$metodo($texto);
	}

	//-------------------------------------------------------------------------------
	//-- Cortes de Control
	//-------------------------------------------------------------------------------

	/**
	 * @ignore
	 */
	protected function generar_cortes_control()
	{
		$this->generar_cc_inicio_nivel();
		$i = 0;
		foreach(array_keys($this->_cortes_control) as $corte){
			$es_ultimo = ($i == count($this->_cortes_control) -1);
			$this->crear_corte( $this->_cortes_control[$corte], $es_ultimo);
			$i++;
		}
		$this->generar_cc_fin_nivel();
	}

	/**
	 * Genera el corte de control para el nodo especificado, de ser necesario saca el HTML necesario para la barra de colapsado
	 *@ignore
	 */
	protected function crear_corte(&$nodo, $es_ultimo)
	{
		static $id_corte_control = 0;
		$id_corte_control++;
		$id_unico = $this->_submit . '__cc_' .$id_corte_control;
		//Disparo las funciones de sumarizacion creadas por el usuario para este corte
		if(isset($this->_cortes_def[$nodo['corte']]['sum_usuario'])){
			foreach($this->_cortes_def[$nodo['corte']]['sum_usuario'] as $sum){
				$metodo = $this->_sum_usuario[$sum]['metodo'];
				$nodo['sum_usuario'][$sum] = $this->$metodo($nodo['filas']);
			}
		}
		$this->generar_cabecera_corte_control($nodo, $id_unico);
		//Genero el corte
		if ($this->_cortes_anidado_colap && ($this->_tipo_salida == 'html' ||
											$this->_tipo_salida == 'impresion_html')) {
			$estilo = $this->get_estilo_inicio_colapsado($nodo);
			echo "<table class='tabla-0' id='$id_unico' width='100%' border='1' $estilo><tr><td>\n";
		}

		//Disparo la generacion recursiva de hijos
		if(isset($nodo['hijos'])){
			$this->generar_cc_inicio_nivel();
			$i = 0;
			foreach(array_keys($nodo['hijos']) as $corte){
				$hijo_es_ultimo = ($i == count($nodo['hijos']) -1);
				$this->crear_corte( $nodo['hijos'][$corte] , $hijo_es_ultimo);
				$i++;
			}
			$this->generar_cc_fin_nivel();
		}else{	
			//Disparo la construccion del ultimo nivel
			$temp = null;
			$this->generar_cuadro( $nodo['filas'], $temp, $nodo); //Se pasa el nodo para las salidas no-html
		}
		if ($this->_cortes_anidado_colap && ($this->_tipo_salida == 'html' ||
											$this->_tipo_salida == 'impresion_html')) {
			echo "</td></tr></table>\n";
		}
		$this->generar_pie_corte_control($nodo, $es_ultimo);
	}

	/**
	 * Genera la llamada a la ventana para la cabecera del corte de acuerdo al tipo de salida.
	 * @ignore
	 */
	protected function generar_cabecera_corte_control(&$nodo, $id_unico = null){
		$metodo = $this->_tipo_salida . '_cabecera_corte_control';
		$this->$metodo($nodo, $id_unico);
	}

	/**
	 * Genera la llamada a la ventana para el pie del corte de acuerdo al tipo de salida.
	 * @ignore
	 */
	protected function generar_pie_corte_control(&$nodo, $es_ultimo){
		$metodo = $this->_tipo_salida . '_pie_corte_control';
		$this->$metodo($nodo, $es_ultimo);
	}

	/**
	 * Genera la llamada a la ventana para el inicio del corte de control de nivel X
	 * @ignore
	 */
	protected function generar_cc_inicio_nivel(){
		$metodo = $this->_tipo_salida . '_cc_inicio_nivel';
		$this->$metodo();
	}

	/**
	 * Genera la llamada a la ventana para el inicio del corte de control de nivel X
	 * @ignore
	 */
	protected function generar_cc_fin_nivel(){
		$metodo = $this->_tipo_salida . '_cc_fin_nivel';
		$this->$metodo();
	}

	/**
	 * Decide para un nodo, el estilo con el que iniciara graficamente el corte de control correspondiente
	 * @param array $nodo
	 * @return string
	 * @ignore
	 */
	protected function get_estilo_inicio_colapsado(&$nodo)
	{
		$estilo = '';
		if($this->_cortes_def[$nodo['corte']]['colapsa'] == '1'){       //El corte debe colapsarse al inicio.
			$estilo = " style='display:none'";              //Si uso clase css javascript despues no me da bola
		}
		return $estilo;
	}	

//################################################################################
//#################################    HTML    ###################################
//################################################################################

	/**
	 *  Dispara la generacion de la salida HTML del cuadro
	 */
	function generar_html()
	{
		$this->generar_salida("html");
	}

	/**
	 * @ignore
	 */
	protected function html_generar_campos_hidden()
	{
		//Campos de comunicación con JS
		echo toba_form::hidden($this->_submit, '');
		echo toba_form::hidden($this->_submit_seleccion, '');
		echo toba_form::hidden($this->_submit_extra, '');
		echo toba_form::hidden($this->_submit_orden_columna, '');
		echo toba_form::hidden($this->_submit_orden_sentido, '');
		echo toba_form::hidden($this->_submit_paginado, '');		
	}

	/**
	 * @ignore
	 */
	protected function html_inicio()
	{
		$this->_memoria['claves_enviadas'] = array();
		$this->html_generar_campos_hidden();
		//-- Scroll
		if($this->_info_cuadro["scroll"]){
			$ancho = isset($this->_info_cuadro["ancho"]) ? $this->_info_cuadro["ancho"] : "";
			$alto = isset($this->_info_cuadro["alto"]) ? $this->_info_cuadro["alto"] : "auto";
			echo "<div class='ei-cuadro-scroll' style='height: $alto; width: $ancho; '>\n";
		}else{
			$ancho = isset($this->_info_cuadro["ancho"]) ? $this->_info_cuadro["ancho"] : "";
		}
		//-- Tabla BASE
		$mostrar_cabecera = true;
		$ancho = convertir_a_medida_tabla($ancho);
		echo "\n<table class='ei-base ei-cuadro-base' $ancho>\n";
		echo"<tr><td style='padding:0;'>\n";
		echo $this->get_html_barra_editor();
		
		if($mostrar_cabecera){
			$this->generar_html_barra_sup(null, true,"ei-cuadro-barra-sup");
		}
		//-- INICIO zona COLAPSABLE

		$colapsado = (isset($this->_colapsado) && $this->_colapsado) ? "style='display:none'" : "";
		echo "<TABLE class='ei-cuadro-cuerpo' $colapsado id='cuerpo_{$this->objeto_js}'>";
		// Cabecera
		echo "<tr><td class='ei-cuadro-cabecera'>";
		$this->html_cabecera();
		echo "</td></tr>\n";
		//--- INICIO CONTENIDO  -----
		echo "<tr><td class='ei-cuadro-cc-fondo'>\n";
		// Si el layout es cortes/tabular se genera una sola tabla, que empieza aca
		if( $this->tabla_datos_es_general() ){
			$this->html_cuadro_inicio();
		}
		//-- Se puede por api cambiar a que los titulos de las columnas se muestren antes que los cortes
		if ($this->_mostrar_titulo_antes_cc) {
			$this->html_cuadro_cabecera_columnas();
		}
	}

	/**
	 * @ignore
	 */
	protected function html_fin()
	{
		if( $this->tabla_datos_es_general() ){
			if (isset($this->_acumulador)) {
				$this->html_cuadro_totales_columnas($this->_acumulador);
			}
			$this->html_acumulador_usuario();
			$this->html_cuadro_fin();					
		}
		echo "</td></tr>\n";
		//--- FIN CONTENIDO  ---------
		// Pie
		echo"<tr><td class='ei-cuadro-pie'>";
		$this->html_pie();		
		echo "</td></tr>\n";
		//Paginacion
		if ($this->_info_cuadro["paginar"]) {
			echo"<tr><td>";
           	$this->html_barra_paginacion();
			echo "</td></tr>\n";
		}
		
		//Barra que muestra el total de registros disponibles
		$this->html_barra_total_registros();
		
		//Botonera
		if ($this->hay_botones()) {
			echo"<tr><td>";
			$this->generar_botones();
			echo "</td></tr>\n";
		}
		echo "</TABLE>\n";
		//-- FIN zona COLAPSABLE
		echo"</td></tr>\n";
		echo "</table>\n";
		if($this->_info_cuadro["scroll"]){
			echo "</div>\n";
		}
	}

	/**
	 * Genera la cabecera del cuadro, por defecto muestra el titulo, si tiene
	 */
	protected function html_cabecera()
	{
        if (isset($this->_info_cuadro) && $this->_info_cuadro['exportar_pdf'] == 1) {
        	$img = toba_recurso::imagen_toba('extension_pdf.png', true);
        	echo "<a href='javascript: {$this->objeto_js}.exportar_pdf()' title='Exporta el listado a formato PDF'>$img</a>";
        }    		
        if (isset($this->_info_cuadro) && $this->_info_cuadro['exportar_xls'] == 1) {
        	$img = toba_recurso::imagen_toba('exp_xls.gif', true);
        	echo "<a href='javascript: {$this->objeto_js}.exportar_excel()' title='Exporta el listado a formato Excel (.xls)'>$img</a>";
        }
        if(trim($this->_info_cuadro["subtitulo"])<>""){
            echo $this->_info_cuadro["subtitulo"];
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
	protected function html_mensaje_cuadro_vacio($texto){
		$this->html_generar_campos_hidden();
		echo $this->get_html_barra_editor();
		echo ei_mensaje($texto);
		if ($this->hay_botones()) {
			$this->generar_botones();
		}		
	}
	
	//-------------------------------------------------------------------------------
	//-- Generacion de los CORTES de CONTROL
	//-------------------------------------------------------------------------------
	/**
	 * @ignore
	 */
	protected function html_cc_inicio_nivel()
	{
		if($this->_cortes_modo == apex_cuadro_cc_anidado){
			echo "<ul>\n";
		}
	}

	/**
	 * @ignore
	 */
	protected function html_cc_fin_nivel()
	{
		if($this->_cortes_modo == apex_cuadro_cc_anidado){
			echo "</ul>\n";
		}
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
	protected function html_cabecera_corte_control(&$nodo, $id_unico = null)
	{
		//Dedusco el metodo que tengo que utilizar para generar el contenido
		$metodo = 'html_cabecera_cc_contenido';
		$metodo_redeclarado = $metodo . '__' . $nodo['corte'];
		if(method_exists($this, $metodo_redeclarado)){
			$metodo = $metodo_redeclarado;
		}		
		$nivel_css = $this->get_nivel_css($nodo['profundidad']);
		$class = "ei-cuadro-cc-tit-nivel-$nivel_css";
		if($this->_cortes_modo == apex_cuadro_cc_tabular){
			$js = "onclick=\"{$this->objeto_js}.colapsar_corte('$id_unico');\"";			
			if ($this->_cortes_anidado_colap){
				echo "<table width='100%' class='tabla-0' border='0'><tr><td width='100%' $js class='$class ei-cuadro-cc-colapsable'>";			
			} else {
				echo "<tr><td  colspan='$this->_cantidad_columnas_total' class='$class'>\n";
			}
			$this->$metodo($nodo);
			if ($this->_cortes_anidado_colap){
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
		$descripcion = $this->_cortes_indice[$nodo['corte']]['descripcion'];
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
	protected function html_pie_corte_control(&$nodo, $es_ultimo)
	{
		if($this->_cortes_modo == apex_cuadro_cc_tabular){				//MODO TABULAR
			if( ! $this->tabla_datos_es_general() ) {
				echo "<table class='tabla-0'  width='100%'>";
			}
			$nivel_css = $this->get_nivel_css($nodo['profundidad']);
			$css_pie = 'ei-cuadro-cc-pie-nivel-' . $nivel_css;
			$css_pie_cab = 'ei-cuadro-cc-pie-cab-nivel-'.$nivel_css;
			//-----  Cabecera del PIE --------
			if($this->_cortes_indice[$nodo['corte']]['pie_mostrar_titular']){
				$metodo_redeclarado = 'html_pie_cc_cabecera__' . $nodo['corte'];
				if(method_exists($this, $metodo_redeclarado)){
					$descripcion = $this->$metodo_redeclarado($nodo);
				}else{
				 	$descripcion = $this->html_cabecera_pie_cc_contenido($nodo);
				}
				echo "<tr><td class='$css_pie' colspan='$this->_cantidad_columnas_total'>\n";
				echo "<div class='$css_pie_cab'>$descripcion<div>";
				echo "</td></tr>\n";
			}
			//----- Totales de columna -------
			if (isset($nodo['acumulador'])) {
				$titulos = false;
				if($this->_cortes_indice[$nodo['corte']]['pie_mostrar_titulos']){
					$titulos = true;	
				}
				$this->html_cuadro_totales_columnas($nodo['acumulador'], 
													'ei-cuadro-cc-sum-nivel-'.$nivel_css, 
													$titulos,
													$css_pie);
			}
			//------ Sumarizacion AD-HOC del usuario --------
			if(isset($nodo['sum_usuario'])){
				$nivel_css = $this->get_nivel_css($nodo['profundidad']);
				$css = 'ei-cuadro-cc-sum-nivel-'.$nivel_css;
				foreach($nodo['sum_usuario'] as $id => $valor){
					$desc = $this->_sum_usuario[$id]['descripcion'];
					$datos[$desc] = $valor;
				}
				echo "<tr><td  class='$css_pie' colspan='$this->_cantidad_columnas_total'>\n";
				$this->html_cuadro_sumarizacion($datos,null,300,$css);
				echo "</td></tr>\n";
			}
			//----- Contar Filas
			if($this->_cortes_indice[$nodo['corte']]['pie_contar_filas']){
				echo "<tr><td  class='$css_pie' colspan='$this->_cantidad_columnas_total'>\n";
				echo $this->etiqueta_cantidad_filas($nodo['profundidad']) . count($nodo['filas']);
				echo "</td></tr>\n";
			}
			//----- Contenido del usuario al final del PIE
			$metodo = 'html_pie_cc_contenido__' . $nodo['corte'];
			if(method_exists($this, $metodo)){
				echo "<tr><td  class='$css_pie' colspan='$this->_cantidad_columnas_total'>\n";
				$this->$metodo($nodo, $es_ultimo);
				echo "</td></tr>\n";
			}
			if( ! $this->tabla_datos_es_general() ) {
				echo "</table>";
			}
		}else{																//MODO ANIDADO
			echo "</li>\n";
		}
	}

	
	/**
	 * Retorna el texto que sumariza la cantidad de filas de un nivel de corte
	 * @param integer $profundidad Nivel de profundidad actual
	 * @return string
	 */
	protected function etiqueta_cantidad_filas($profundidad)
	{
		return "Cantidad de filas: ";
	}
	
			
	/**
	 * Retorna el CONTENIDO de la cabecera del PIE del corte de control
	 * Muestra las columnas seleccionadas como descripcion del corte separadas por comas
	 * @return string
	 * @ignore 
	 */
	protected function html_cabecera_pie_cc_contenido(&$nodo)
	{
		$descripcion = $this->_cortes_indice[$nodo['corte']]['descripcion'];
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
	 *@ignore
	 */
	function tabla_datos_es_general()
	{
		if(! $this->existen_cortes_control() ) {
			return true;	
		}else{
			return ($this->_cortes_modo == apex_cuadro_cc_tabular) && ! $this->_cortes_anidado_colap;
		}
	}

	/**
	 * Genera el html correspondiente a las filas del cuadro
	 */
	protected function html_cuadro(&$filas)
	{
		//Si existen cortes de control y el layout es tabular, el encabezado de la tabla ya se genero
		if( ! $this->tabla_datos_es_general() ){
			$this->html_cuadro_inicio();
		}
		//-- Se puede por api cambiar a que los titulos de las columnas se muestren antes que los cortes, en ese caso se evita hacerlo aqui		
		if (! $this->_mostrar_titulo_antes_cc) {
			$this->html_cuadro_cabecera_columnas();
		}
		$par = false;
		$formateo = new $this->_clase_formateo('html');
        foreach($filas as $f)
        {
        	$estilo_fila = $par ? 'ei-cuadro-celda-par' : 'ei-cuadro-celda-impar';
			$clave_fila = $this->get_clave_fila($f);
			if (is_array($this->_clave_seleccionada)) {
				$clave_seleccionada = implode(apex_qs_separador, $this->_clave_seleccionada);	
			} else {
				$clave_seleccionada = $this->_clave_seleccionada;	
			}
			
			$esta_seleccionada = ($clave_fila != '') && ($clave_fila == $clave_seleccionada);
			$estilo_seleccion = ($esta_seleccionada) ? "ei-cuadro-fila-sel" : "ei-cuadro-fila";
            echo "<tr class='$estilo_fila' >\n";
 			//---> Creo las CELDAS de una FILA <----
 			foreach (array_keys($this->_columnas) as $a) {
                //*** 1) Recupero el VALOR
				$valor = "";
                if(isset($this->_columnas[$a]["clave"])){
					if(isset($this->datos[$f][$this->_columnas[$a]["clave"]])){
						$valor_real = $this->datos[$f][$this->_columnas[$a]["clave"]];
						//-- Hace el saneamiento para evitar inyección XSS
						if (!isset($this->_columnas[$a]['permitir_html']) || $this->_columnas[$a]['permitir_html'] == 0) {
							  $valor_real = texto_plano($valor_real);
						}
					}else{
						$valor_real = null;
						//ATENCION!! hay una columna que no esta disponible!
					}
	                //Hay que formatear?
	                if(isset($this->_columnas[$a]["formateo"])){
	                    $funcion = "formato_" . $this->_columnas[$a]["formateo"];
	                    //Formateo el valor
	                    $valor = $formateo->$funcion($valor_real);
	                } else {
	                	$valor = $valor_real;	
	                }
	            }
	            //*** 2) La celda posee un vinculo??
				if ( ($this->_tipo_salida == 'html') && ( $this->_columnas[$a]['usar_vinculo'] ) ) {
					// Armo el vinculo.
					$clave_columna = isset($this->_columnas[$a]['vinculo_indice']) ? $this->_columnas[$a]['vinculo_indice'] : $this->_columnas[$a]['clave'];
					$opciones = array();
					if($this->_columnas[$a]['vinculo_celda']) {
						$opciones['celda_memoria'] = $this->_columnas[$a]['vinculo_celda'];
					}
					
					if ((isset($this->_columnas[$a]['vinculo_servicio']) && !is_null($this->_columnas[$a]['vinculo_servicio']))){
						$opciones['servicio'] = $this->_columnas[$a]['vinculo_servicio'];						
					}
					
					$parametros = $this->get_clave_fila_array($f);
					$parametros[$clave_columna] = $valor_real;
					$item = $this->_columnas[$a]['vinculo_item'];
					$url = toba::vinculador()->get_url(toba::proyecto()->get_id(),$item,$parametros,$opciones);
					// Armo el disparo
					if ( $this->_columnas[$a]['vinculo_popup'] ) {
						$popup_parametros = array();
						if($this->_columnas[$a]['vinculo_popup_param']) {
							//Esto se puede optimizar (1 por columna en vez de columna/fila)!
							$temp = explode(',',$this->_columnas[$a]['vinculo_popup_param']);
							$temp = array_map('trim',$temp);
							foreach($temp as $opcion) {
								$o = explode(':',$opcion);
								$o = array_map('trim',$o);
								$popup_parametros[$o[0]] = $o[1];
							}	
						}
						$opciones = toba_js::arreglo($popup_parametros, true);
						$js = "abrir_popup('$clave_columna','$url',$opciones);";
						$valor = "<a href='#' onclick=\"$js\">$valor</a>";
					} else {
						$target = (isset($this->_info_cuadro_columna[$a]['vinculo_target'])) ? "target='".$this->_info_cuadro_columna[$a]['vinculo_target']."'" : '';
						$valor = "<a href='$url' $target>$valor</a>";
					}
				}
                //*** 3) Genero el HTML
            	if(isset($this->_columnas[$a]["ancho"])){
	                $ancho = " width='". $this->_columnas[$a]["ancho"] . "'";
	            }else{
	                $ancho = "";
	            }                
                echo "<td class='$estilo_seleccion ".$this->_columnas[$a]["estilo"]."' $ancho>\n";
                if (trim($valor) !== '') {
                	echo $valor;
                } else {
                	echo '&nbsp;';
                }
                echo "</td>\n";
                //Termino la CELDA
            }
 			//---> Creo los EVENTOS de la FILA <---
			if ( $this->_tipo_salida == 'html' ) {
				$hay_evento_maneja_datos = false;
				foreach ($this->get_eventos_sobre_fila() as $id => $evento) {
					echo "<td class='ei-cuadro-fila-evt' width='1%'>\n";
					if( ! $evento->esta_anulado() ) { //Si el evento viene desactivado de la conf, no lo utilizo
						//1: Posiciono al evento en la fila
						$evento->set_parametros($clave_fila);
						if($evento->posee_accion_vincular()){
							//-- Si es un vinculo, fuerza a crear una nueva instancia del vinculo en el evento asi aloja al id de la fila y sus conf.
							$parametros = $this->get_clave_fila_array($f);
							$parametros[apex_ei_evento] = $id;
							$evento->vinculo(true)->set_parametros($parametros);
						}
						//2: Ventana de modificacion del evento por fila
						//- a - ¿Existe una callback de modificacion en el CONTROLADOR?
						$callback_modificacion_eventos_contenedor = 'conf_evt__' . $this->_parametros['id'] . '__' . $id;
						if (method_exists($this->controlador, $callback_modificacion_eventos_contenedor)) {
							$this->controlador->$callback_modificacion_eventos_contenedor($evento, $f);
						} else {
							//- b - ¿Existe una callback de modificacion una subclase?
							$callback_modificacion_eventos = 'conf_evt__' . $id;
							if (method_exists($this, $callback_modificacion_eventos)) {
								$this->$callback_modificacion_eventos($evento, $f);
							}
						}
						//3: Genero el boton
						if( ! $evento->esta_anulado() ) {
							$hay_evento_maneja_datos = true;
							echo $evento->get_html($this->_submit, $this->objeto_js, $this->_id);
						} else {
							$evento->restituir();	//Lo activo para la proxima fila
						}
					}
	            	echo "</td>\n";
				}
				//Si algun evento permite seleccionar valores
				if ($hay_evento_maneja_datos) {
					$this->_memoria['claves_enviadas'][] = $clave_fila;
				}
			}
			
			//--------------------------------------
            echo "</tr>\n";
            $par = !$par;
        }
		if( ! $this->tabla_datos_es_general() ){
			$this->html_acumulador_usuario();
			$this->html_cuadro_fin();
		}
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
		foreach(array_keys($this->_columnas) as $clave) {
        	if (trim($this->_columnas[$clave]["titulo"]) != '') {
        		$alguna_tiene_titulo = true;
        		break;
        	}			
		}
        if ($alguna_tiene_titulo) {
        	$rowspan = empty($this->_agrupacion_columnas) ? '' : "rowspan='2'";
        	$html_columnas_agrupadas = '';
        	$grupo_actual = null;
	        echo "<tr>\n";
	        foreach (array_keys($this->_columnas) as $a) {
	        	$html_columna = '';
	        	//El alto de la columna, si esta agrupada es uno sino es el general
	        	$rowspan_col = isset($this->_columnas[$a]['grupo']) ? "" : $rowspan;
	        	
	            if(isset($this->_columnas[$a]["ancho"])){
	                $ancho = " width='". $this->_columnas[$a]["ancho"] . "'";
	            }else{
	                $ancho = "";
	            }
	            $estilo_columna = $this->_columnas[$a]["estilo_titulo"];
	            if(!$estilo_columna){
	            	$estilo_columna = 'ei-cuadro-col-tit';
	            }
	            $html_columna .= "<td $rowspan_col class='$estilo_columna' $ancho>\n";
	            $html_columna .= $this->html_cuadro_cabecera_columna(    $this->_columnas[$a]["titulo"],
	                                        $this->_columnas[$a]["clave"],
	                                        $a );
	            $html_columna .= "</td>\n";
           
	        	if (! isset($this->_columnas[$a]['grupo']) || $this->_columnas[$a]['grupo'] == '') {
	        		//Si no es una columna agrupada,saca directamente su html
	        		echo $html_columna;
	        		$grupo_actual = null;
	        	} else {
	        		//Guarda el html de la columna para sacarlo una fila mas abajo
	        		$html_columnas_agrupadas .= $html_columna;
	        		//Si es la primera columna de la agrupación saca un unico <td> del ancho de la agrupacion
	        		if (! isset($grupo_actual) || $grupo_actual != $this->_columnas[$a]['grupo']) {
		        		$grupo_actual = $this->_columnas[$a]['grupo'];
		        		$cant_col = count($this->_agrupacion_columnas[$grupo_actual]);
		        		echo "<td class='ei-cuadro-col-tit ei-cuadro-col-tit-grupo' colspan='$cant_col'>$grupo_actual</td>";
	        		}
	        	}
	        }
	        //-- Eventos sobre fila
			if($this->_cantidad_columnas_extra > 0){
				foreach ($this->get_eventos_sobre_fila() as $evento) {
					echo "<td $rowspan class='ei-cuadro-col-tit'>&nbsp;";
					if (toba_editor::modo_prueba()) {
						echo toba_editor::get_vinculo_evento($this->_id, $this->_info['clase_editor_item'], $evento->get_id())."\n";
					}
					echo "</td>\n";
				}
			}
	        echo "</tr>\n";			
			//-- Columnas Agrupadas
			if ($html_columnas_agrupadas != '') {
				echo "<tr>\n";
				echo $html_columnas_agrupadas;
				echo "</tr>\n";
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
        //--- ¿Es ordenable?
		if (	isset($this->_eventos['ordenar']) 
				&& $this->_columnas[$indice]["no_ordenar"] != 1
				&& $this->_tipo_salida == 'html' ) {
			$sentido = array();
			$sentido[] = array('asc', 'Ordenar ascendente');
			$sentido[] = array('des', 'Ordenar descendente');
			$salida .= "<span class='ei-cuadro-orden'>";			
			foreach($sentido as $sen){
			    $sel="";
			    if ($this->hay_ordenamiento() && ($columna==$this->_orden_columna)&&($sen[0]==$this->_orden_sentido)) {
					$sel = "_sel";//orden ACTIVO
			    }

				//Comunicación del evento
				$parametros = array('orden_sentido'=>$sen[0], 'orden_columna'=>$columna);
				$evento_js = toba_js::evento('ordenar', $this->_eventos['ordenar'], $parametros);
				$js = "{$this->objeto_js}.set_evento($evento_js);";
			    $src = toba_recurso::imagen_toba("nucleo/sentido_". $sen[0] . $sel . ".gif");
				$salida .= toba_recurso::imagen($src, null, null, $sen[1], '', "onclick=\"$js\"", 'cursor: pointer; cursor:hand;');
			}
			$salida .= "</span>";			
		}    	
		//--- Nombre de la columna
		if (trim($columna) != '' || trim($this->_columnas[$indice]["vinculo_indice"])!="") {           
            $salida .= $titulo;
        }	
		//---Editor de la columna
		if ( toba_editor::modo_prueba() && $this->_tipo_salida == 'html' ){
			$item_editor = "1000253";
			$param_editor = array( apex_hilo_qs_zona => implode(apex_qs_separador,$this->_id),
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
		if (isset($this->_sum_usuario)) {
			foreach($this->_sum_usuario as $sum) {
				if($sum['corte'] == 'toba_total') {
					$metodo = $sum['metodo'];
					$sumarizacion[$sum['descripcion']] = $this->$metodo($this->datos);
				}
			}
		}
		if (isset($sumarizacion)) {
			$css = 'cuadro-cc-sum-nivel-1';
			echo "<tr><td colspan='$this->_cantidad_columnas_total'>\n";
			$this->html_cuadro_sumarizacion($sumarizacion,null,300,$css);
			echo "</td></tr>\n";
		}
	}

    /**
     * @ignore
     */
	function pdf_acumulador_usuario()
	{
		if (isset($this->_sum_usuario)) {
			foreach($this->_sum_usuario as $sum) {
				if($sum['corte'] == 'toba_total') {
					$metodo = $sum['metodo'];
					$sumarizacion[$sum['descripcion']] = $this->$metodo($this->datos);
				}
			}
		}
		if (isset($sumarizacion)) {
			$css = 'cuadro-cc-sum-nivel-1';
			$this->pdf_cuadro_sumarizacion($sumarizacion,null,300,$css);
		}		
	}
	
    /**
     * @ignore 
     */
	protected function html_cuadro_totales_columnas($totales,$estilo=null,$agregar_titulos=false, $estilo_linea=null)
	{
		$formateo = new $this->_clase_formateo('html');
		$clase_linea = isset($estilo_linea) ? "class='$estilo_linea'" : "";
		if($agregar_titulos || (! $this->tabla_datos_es_general()) ){
			echo "<tr>\n";
			foreach (array_keys($this->_columnas) as $clave) {
			    if(isset($totales[$clave])){
					$valor = $this->_columnas[$clave]["titulo"];
					echo "<td class='".$this->_columnas[$clave]["estilo_titulo"]."'><strong>$valor</strong></td>\n";
				}else{
					echo "<td $clase_linea>&nbsp;</td>\n";
				}
			}
	        //-- Eventos sobre fila
			if($this->_cantidad_columnas_extra > 0){
				echo "<td colspan='$this->_cantidad_columnas_extra'></td>\n";
			}		
			echo "</tr>\n";
		}
		if ($totales !== null){ 				
			echo "<tr class='ei-cuadro-totales'>\n";
			foreach (array_keys($this->_columnas) as $clave) {
				//Defino el valor de la columna
			    if(isset($totales[$clave])){
					$valor = $totales[$clave];
					if(!isset($estilo)){
						$estilo = $this->_columnas[$clave]["estilo"];
					}
					//La columna lleva un formateo?
					if(isset($this->_columnas[$clave]["formateo"])){
						$metodo = "formato_" . $this->_columnas[$clave]["formateo"];
						$valor = $formateo->$metodo($valor);
					}
					echo "<td class='ei-cuadro-total $estilo'><strong>$valor</strong></td>\n";
				}else{
					echo "<td $clase_linea>&nbsp;</td>\n";
				}
			}
	        //-- Eventos sobre fila
			if($this->_cantidad_columnas_extra > 0){
				echo "<td colspan='$this->_cantidad_columnas_extra'>&nbsp;</td>\n";
			}		
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
		echo "<div class='ei-cuadro-pag'>";
		if( isset($this->_total_registros) && !($this->_tamanio_pagina >= $this->_total_registros) ) {
			//Calculo los posibles saltos
			//Primero y Anterior
			if($this->_pagina_actual == 1) {
				$anterior = toba_recurso::imagen_toba("nucleo/paginacion/anterior_deshabilitado.gif",true);
				$primero = toba_recurso::imagen_toba("nucleo/paginacion/primero_deshabilitado.gif",true);       
			} else {
				$evento_js = toba_js::evento('cambiar_pagina', $this->_eventos["cambiar_pagina"], $this->_pagina_actual - 1);
				$js = "{$this->objeto_js}.set_evento($evento_js);";
				$img = toba_recurso::imagen_toba("nucleo/paginacion/anterior.gif");
				$anterior = toba_recurso::imagen($img, null, null, 'Página Anterior', '', "onclick=\"$js\"", 'cursor: pointer;cursor:hand;');
			
				$evento_js = toba_js::evento('cambiar_pagina', $this->_eventos["cambiar_pagina"], 1);
				$js = "{$this->objeto_js}.set_evento($evento_js);";
				$img = toba_recurso::imagen_toba("nucleo/paginacion/primero.gif");
				$primero = toba_recurso::imagen($img, null, null, 'Página Inicial', '', "onclick=\"$js\"", 'cursor: pointer;cursor:hand;');
			}
			//Ultimo y Siguiente
			if( $this->_pagina_actual == $this->_cantidad_paginas ) {
				$siguiente = toba_recurso::imagen_toba("nucleo/paginacion/siguiente_deshabilitado.gif",true);
				$ultimo = toba_recurso::imagen_toba("nucleo/paginacion/ultimo_deshabilitado.gif",true);     
			} else {
				$evento_js = toba_js::evento('cambiar_pagina', $this->_eventos["cambiar_pagina"], $this->_pagina_actual + 1);
				$js = "{$this->objeto_js}.set_evento($evento_js);";
				$img = toba_recurso::imagen_toba("nucleo/paginacion/siguiente.gif");
				$siguiente = toba_recurso::imagen($img, null, null, 'Página Siguiente', '', "onclick=\"$js\"", 'cursor: pointer;cursor:hand;');
				
				$evento_js = toba_js::evento('cambiar_pagina', $this->_eventos["cambiar_pagina"], $this->_cantidad_paginas);
				$js = "{$this->objeto_js}.set_evento($evento_js);";
				$img = toba_recurso::imagen_toba("nucleo/paginacion/ultimo.gif");
				$ultimo = toba_recurso::imagen($img, null, null, 'Página Final', '', "onclick=\"$js\"", 'cursor: pointer;cursor:hand;');
			}
			
			echo "$primero $anterior Página <strong>{$this->_pagina_actual}</strong> de ";
			echo "<strong>{$this->_cantidad_paginas}</strong> $siguiente $ultimo";
		} 
		echo "</div>";
	}

	/**
	 * @ignore 
	 */	
	protected function html_barra_total_registros()
	{
		echo"<tr><td>";		
		$plural = ($this->_total_registros == 1) ? '' : 's';
		echo "<div class='ei-cuadro-pag ei-cuadro-pag-total'>Encontrado$plural {$this->_total_registros} registro$plural</div>";
		echo "</td></tr>\n";

	}	
	
	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT --
	//-------------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		$id = toba_js::arreglo($this->_id, false);
		echo $identado."window.{$this->objeto_js} = new ei_cuadro($id, '{$this->objeto_js}', '{$this->_submit}');\n";
	}

	/**
	 * @ignore 
	 */
	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'componentes/ei_cuadro';
		return $consumo;
	}	

	//---------------------------------------------------------------
	//----------------------  SALIDA Impresion  ---------------------
	//---------------------------------------------------------------

	/**
	 * @ignore 
	 */
	function vista_impresion_html( $salida )
	{
		$salida->subtitulo( $this->get_titulo() );
		$this->generar_salida("impresion_html");
	}

    /**
     * @ignore
     */
	protected function impresion_html_inicio()
	{
		$ancho = isset($this->_info_cuadro["ancho"]) ? $this->_info_cuadro["ancho"] : "";
        echo "<TABLE width='$ancho' class='ei-base ei-cuadro-base'>";
		// Cabecera
		echo"<tr><td class='ei-cuadro-cabecera'>";
		$this->impresion_html_cabecera();		
		echo "</td></tr>\n";
		//--- INICIO CONTENIDO  -----
		echo "<tr><td class='ei-cuadro-cc-fondo'>\n";
		// Si el layout es cortes/tabular se genera una sola tabla, que empieza aca
		if( $this->tabla_datos_es_general() ){
			$this->html_cuadro_inicio();
		}
	}

    /**
     * @ignore
     */
	protected function impresion_html_cabecera()
	{
		if(trim($this->_info_cuadro["subtitulo"])<>""){
			echo $this->_info_cuadro["subtitulo"];
		}
	}

	/**
	 * @ignore 
	 */
	protected function impresion_html_fin()
	{
		if( $this->tabla_datos_es_general() ){
			$this->html_cuadro_totales_columnas($this->_acumulador);
			$this->html_acumulador_usuario();
			$this->html_cuadro_fin();
		}
		echo "</td></tr>\n";		
		//--- FIN CONTENIDO  ---------
		// Pie
		echo"<tr><td class='ei-cuadro-pie'>";
		$this->html_pie();		
		echo "</td></tr>\n";
		echo "</TABLE>\n";
	}

	/**
	 * @ignore 
	 */
	protected function impresion_html_cuadro(&$filas, &$totales){
		$this->html_cuadro( $filas, $totales );
	}

    /**
     * @ignore
     */
	protected function impresion_html_mensaje_cuadro_vacio($texto){
		$this->html_mensaje_cuadro_vacio($texto);
	}

	//-- Cortes de Control --

    /**
     * @ignore
     */
	protected function impresion_html_cabecera_corte_control(&$nodo ){
		$this->html_cabecera_corte_control($nodo);
	}

    /**
     * @ignore
     */
	protected function impresion_html_pie_corte_control( &$nodo , $es_ultimo){
		$this->html_pie_corte_control($nodo, $es_ultimo);
	}

	protected function impresion_html_cc_inicio_nivel(){
	}

    protected function impresion_html_cc_fin_nivel(){
	}
	
	//---------------------------------------------------------------
	//----------------------  SALIDA PDF  ---------------------------
	//---------------------------------------------------------------

	function vista_pdf(toba_vista_pdf $salida )
	{
		$this->salida = $salida;		
		$titulo = $this->get_titulo();
		if ($titulo != '') {
			$this->salida->titulo($titulo);
		}
		if ($this->_info_cuadro["subtitulo"] != '') {
			$this->salida->subtitulo($this->_info_cuadro["subtitulo"]);
		}
		$this->salida->separacion($this->_pdf_sep_titulo);		
		$this->generar_salida("pdf");
	}	
	
	/**
	 * @ignore 
	 */
	protected function pdf_inicio(){}
	
	/**
	 * @ignore 
	 */
	protected function pdf_fin() 
	{
		if( $this->tabla_datos_es_general() ){
			if (isset($this->_acumulador) && ! $this->_pdf_total_generado) {
				$this->salida->separacion($this->_pdf_sep_titulo);
				$this->pdf_cuadro_totales_columnas($this->_acumulador, 0, true);
			}
			$this->pdf_acumulador_usuario();
			/*$this->html_cuadro_fin();					*/
		}		
	}

	/**
	 * @ignore 
	 * $nodo se pasa para poder mostrar los totales aqui mismo en caso de cortes con nivel > 0
	 */
	protected function pdf_cuadro(&$filas, &$totales, &$nodo)
	{
		$this->salida->separacion($this->_pdf_sep_tabla);
		$formateo = new $this->_clase_formateo('pdf');
		//-- Valores de la tabla
		$datos = array();		
        foreach($filas as $f) {
			$clave_fila = $this->get_clave_fila($f);
			$fila = array();
 			//---> Creo las CELDAS de una FILA <----
 			foreach (array_keys($this->_columnas) as $a) {
				$valor = "";
                if(isset($this->_columnas[$a]["clave"])){
					if(isset($this->datos[$f][$this->_columnas[$a]["clave"]])){
						$valor_real = $this->datos[$f][$this->_columnas[$a]["clave"]];
					}else{
						$valor_real = '';
					}
	                //Hay que formatear?
	                if(isset($this->_columnas[$a]["formateo"])){
	                    $funcion = "formato_" . $this->_columnas[$a]["formateo"];
	                    //Formateo el valor
	                    $valor = $formateo->$funcion($valor_real);
	                } else {
	                	$valor = $valor_real;	
	                }
	            }
	            $fila[$this->_columnas[$a]["clave"]] = $valor;
            }
            $datos[] = $fila;
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
        	$ancho = $this->salida->get_ancho(str_replace('%', '', $this->_pdf_tabla_ancho));	
        } else {
        	$ancho = $this->_pdf_tabla_ancho;
        }
        $opciones = $this->_pdf_tabla_opciones;
        $opciones['width'] = $ancho;
        $opciones['cols'] = $estilos;
        $this->salida->tabla(array('datos_tabla'=>$datos, 'titulos_columnas'=>$titulos), true, $this->_pdf_letra_tabla, $opciones);
		$this->salida->separacion($this->_pdf_sep_tabla);

	}
	
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
	
	/**
	 * @ignore 
	 */
	protected function pdf_get_titulos()
	{
        $titulos = array();
        $estilos = array();
        foreach(array_keys($this->_columnas) as $id) {
        	$titulos[$id] = $this->_columnas[$id]['titulo'];
        	$estilo = $this->pdf_get_estilo($this->_columnas[$id]['estilo']);
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
	protected function pdf_mensaje_cuadro_vacio($texto)
	{
		$this->salida->texto($texto);
	}

	//-- Cortes de Control --
	/**
	 * Deduce el metodo que utilizara para generar la cabecera
	 * @param array $nodo
	 * @ignore
	 */
	protected function pdf_cabecera_corte_control(&$nodo )
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
		$descripcion = $this->_cortes_indice[$nodo['corte']]['descripcion'];
		$valor = implode(", ",$nodo['descripcion']);
		if ($nodo['profundidad'] > 0) {
			$opciones = $this->_pdf_cabecera_cc_1_opciones;
			$size = $this->_pdf_cabecera_cc_1_letra;
		} else {
			$opciones = $this->_pdf_cabecera_cc_0_opciones;
			$size = $this->_pdf_cabecera_cc_0_letra;
		}
		$this->salida->separacion($this->_pdf_sep_cc);		
		if (trim($descripcion) != '') {
			$this->salida->texto("<b>$descripcion " . $valor . '</b>', $size, $opciones);
		} else {
			$this->salida->texto('<b>' . $valor . '</b>', $size, $opciones);
		}
		$this->salida->separacion($this->_pdf_sep_cc);
	}	
	
	/**
	 * Genera el contenido de la 'cabecera' ubicada en el pie del corte de control
	 * @param array $nodo
	 * @return string
	 */
	protected function pdf_cabecera_pie_cc_contenido(&$nodo)
	{
		$descripcion = $this->_cortes_indice[$nodo['corte']]['descripcion'];
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
		$this->salida->separacion($this->_pdf_sep_cc);
		if ($nivel > 0) {
			$opciones = $this->_pdf_totales_cc_1_opciones;
		} else {
			$opciones = $this->_pdf_totales_cc_0_opciones;
		}
		$opciones['cols'] = $estilos;
		$this->salida->tabla(array('datos_tabla'=>$datos, 'titulos_columnas'=>$titulos), $agregar_titulos, $this->_pdf_letra_tabla, $opciones);
		$this->salida->separacion($this->_pdf_sep_cc);
	}
	
	/**
	 * @ignore 
	 */
	protected function pdf_get_fila_totales($totales, &$titulos=null, $resaltar=false)
	{
		$formateo = new $this->_clase_formateo('pdf');		
		$datos = array();		
		foreach (array_keys($this->_columnas) as $clave) {
			//Defino el valor de la columna
		    if(isset($totales[$clave])){
				$valor = $totales[$clave];
				if(!isset($estilo)){
					$estilo = $this->_columnas[$clave]["estilo"];
				}
				//La columna lleva un formateo?
				if(isset($this->_columnas[$clave]["formateo"])){
					$metodo = "formato_" . $this->_columnas[$clave]["formateo"];
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
			$this->salida->subtitulo($titulo);
		}
		//Datos
		foreach($datos as $desc => $valor){
			$this->salida->texto($desc.': '.$valor);
		}
	}	
	

	protected function pdf_pie_corte_control( &$nodo, $es_ultimo )
	{
		//-----  Cabecera del PIE --------
		if($this->_cortes_indice[$nodo['corte']]['pie_mostrar_titular']){
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
			$this->salida->texto($descripcion, $this->_pdf_letra_tabla, $opciones);
		}
		//----- Totales de columna -------
		if (isset($nodo['acumulador']) && ! isset($nodo['pdf_acumulador_generado'])) {
			/*$titulos = false;
			if($this->_cortes_indice[$nodo['corte']]['pie_mostrar_titulos']){
				$titulos = true;	
			} Se fuerza los titulos porque si no no se entiende a cual pertenece */
			$this->pdf_cuadro_totales_columnas($nodo['acumulador'], 
												$nodo['profundidad'], 
												true,
												null);
		}
		//------ Sumarizacion AD-HOC del usuario --------
		if(isset($nodo['sum_usuario'])){
			foreach($nodo['sum_usuario'] as $id => $valor){
				$desc = $this->_sum_usuario[$id]['descripcion'];
				$datos[$desc] = $valor;
			}
			$this->pdf_cuadro_sumarizacion($datos,null,300,$nodo['profundidad']);
		}
		//----- Contar Filas
		if($this->_cortes_indice[$nodo['corte']]['pie_contar_filas']){
			$etiqueta = $this->etiqueta_cantidad_filas($nodo['profundidad']) . count($nodo['filas']);
			$this->salida->texto("<i>".$etiqueta.'</i>', $this->_pdf_letra_tabla, $this->_pdf_contar_filas_op);
		}
		
		//----- Contenido del usuario al final del PIE
		$metodo = 'pdf_pie_cc_contenido__' . $nodo['corte'];
		if(method_exists($this, $metodo)){
			$this->$metodo($nodo);
		}
		if (!$es_ultimo && $nodo['profundidad'] == 0 && $this->_pdf_cortar_hoja_cc_0) {
			$this->salida->salto_pagina();
		} elseif (!$es_ultimo && $nodo['profundidad'] > 0 && $this->_pdf_cortar_hoja_cc_1) {
			$this->salida->salto_pagina();
		}
	}

	protected function pdf_cc_inicio_nivel()
	{
	}

	protected function pdf_cc_fin_nivel()
	{
	}
	
	
	//---------------------------------------------------------------
	//----------------------  SALIDA EXCEL  ---------------------------
	//---------------------------------------------------------------
	
	function vista_excel(toba_vista_excel $salida )
	{
		$this->salida = $salida;		
		$titulo = $this->get_titulo();
		$this->salida->set_hoja_nombre($titulo);
		$cant_columnas = count($this->_columnas);
		if ($titulo != '') {
			$this->salida->titulo($titulo, $cant_columnas);
		}
		if ($this->_info_cuadro["subtitulo"] != '') {
			$this->salida->titulo($this->_info_cuadro["subtitulo"], $cant_columnas);
		}
		$this->generar_salida("excel");
	}		
	
	/**
	 * @ignore 
	 */
	protected function excel_inicio(){}
	
	/**
	 * @ignore 
	 */
	protected function excel_fin() 
	{
		if( $this->tabla_datos_es_general() ){
			if (isset($this->_acumulador) && !$this->_excel_total_generado) {
				$this->excel_cuadro_totales_columnas(array('acumulador'=>$this->_acumulador), 0, false, true);
			}
			//$this->html_acumulador_usuario();
		}		
	}

	/**
	 * @ignore 
	 * $nodo se pasa para poder mostrar los totales aqui mismo en caso de cortes con nivel > 0
	 */
	protected function excel_cuadro(&$filas, &$totales, &$nodo)
	{
		$formateo = new $this->_clase_formateo('excel');
		//-- Valores de la tabla
		$datos = array();		
		$estilos = array();
        foreach($filas as $f) {
			$clave_fila = $this->get_clave_fila($f);
			$fila = array();
 			//---> Creo las CELDAS de una FILA <----
 			foreach (array_keys($this->_columnas) as $clave) {
				$valor = "";
                if(isset($this->_columnas[$clave]["clave"])){
					if(isset($this->datos[$f][$clave])){
						$valor_real = $this->datos[$f][$clave];
					}else{
						$valor_real = '';
					}
	                //Hay que formatear?
	                $estilo = array();
	                if(isset($this->_columnas[$clave]["formateo"])){
	                    $funcion = "formato_" . $this->_columnas[$clave]["formateo"];
	                    //Formateo el valor
	                    list($valor, $estilo) = $formateo->$funcion($valor_real);
	                    if (! isset($estilo)) {
	                    	$estilo = array();
	                    }
	                } else {
	                	$valor = $valor_real;	
	                }
	                $estilos[$clave]['estilo'] = $this->excel_get_estilo($this->_columnas[$clave]['estilo']);
	                $estilos[$clave]['estilo'] = array_merge($estilo, $estilos[$clave]['estilo']);
	                $estilos[$clave]['ancho'] = 'auto';
	                if (isset($this->_columnas[$clave]['grupo']) && $this->_columnas[$clave]['grupo'] != '') {
	                	$estilos[$clave]['grupo'] = $this->_columnas[$clave]['grupo'];
	                }
	            }
	            $fila[$clave] = $valor;
            }
            $datos[] = $fila;
        }
		$titulos = $this->excel_get_titulos();

		//-- Para la tabla simple se sacan los totales como parte de la tabla
		$col_totales = array();
		if (isset($totales)) {
			$this->_excel_total_generado = true;
			$col_totales = array_keys($totales);
		}
  		
        //-- Genera la tabla
       $coordenadas = $this->salida->tabla($datos, $titulos, $estilos, $col_totales);
       $nodo['excel_rango'] = $coordenadas;
       $nodo['excel_rango_hoja'] = $this->salida->get_hoja_nombre();
	}
	
	
	/**
	 * @ignore 
	 */
	protected function excel_get_titulos()
	{
        $titulos = array();
        foreach(array_keys($this->_columnas) as $id) {
        	$titulos[$id] = $this->_columnas[$id]['titulo'];
        }		
        return $titulos;
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
    	}		
	}

	/**
	 * Emite el mensaje correspondiente al cuadro sin datos
	 * @param string $texto
	 * @ignore
	 */
	protected function excel_mensaje_cuadro_vacio($texto)
	{
		$this->salida->texto($texto);
	}

	//-- Cortes de Control --

	/**
	 * Define que metodo utilizara para generar el contenido de la cabecera
	 * @param array $nodo
	 * @ignore
	 */
	protected function excel_cabecera_corte_control(&$nodo )
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
		$descripcion = $this->_cortes_indice[$nodo['corte']]['descripcion'];
		$valor = implode(", ",$nodo['descripcion']);
		if ($nodo['profundidad'] > 0) {
			$opciones = $this->_excel_cabecera_cc_1_opciones;
			$altura = $this->_excel_cabecera_cc_1_altura;			
		} else {
			$opciones = $this->_excel_cabecera_cc_0_opciones;
			$altura = $this->_excel_cabecera_cc_0_altura;			
		}
		$span = $this->_cantidad_columnas;
		if (trim($descripcion) != '') {
			$contenido = "$descripcion " . $valor;
		} else {
			$contenido = $valor;
		}
		$this->salida->texto($contenido, $opciones, $span, $altura);		
		if ($nodo['profundidad'] == 0 && $this->_excel_cortar_hoja_cc_0) {
			$this->salida->set_hoja_nombre($contenido);
		}
	}	
	
	/**
	 * Genera el contenido de la 'cabecera' ubicada en el pie del corte de control
	 * @param array $nodo
	 * @return string
	 */
	protected function excel_cabecera_pie_cc_contenido(&$nodo)
	{
		$descripcion = $this->_cortes_indice[$nodo['corte']]['descripcion'];
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
		} elseif ($nivel > 0) {
			$estilo_base = $this->_excel_totales_cc_1_opciones;
		} else {
			$estilo_base = $this->_excel_totales_cc_0_opciones;
		}
		$formateo = new $this->_clase_formateo('excel');		
		$datos = array();		
		$estilos = array();
		if ($es_total_general) {
			$this->salida->separacion(1);
		}
		$a = 0;
		foreach(array_keys($this->_columnas) as $clave) {
			$estilos[$clave]['estilo'] = $estilo_base;
			$estilos[$clave]['borrar_estilos_nulos'] = 1;
			//--Acumulador
		    if (isset($nodo['acumulador'][$clave])){
		    	if ($this->_excel_usar_formulas) {
		    		//-- Calcular la sumatoria de celdas
					if ($es_total_general) {
						$rangos = array();
						foreach ($this->_cortes_control as $nodo) {
				    		$rangos = array_merge($rangos, $this->excel_get_rangos($nodo, $a));
						}
				    	$formula = '=SUM'.implode(' + SUM', $rangos);	
					} else {
				    	$rangos = $this->excel_get_rangos($nodo, $a);
				    	$formula = '=SUM'.implode(' + SUM', $rangos);
					}
		    	} else {
		    		//-- En lugar de hacer una formula, incluir directamente el importe
		    		$formula = $nodo['acumulador'][$clave];
		    	}
				//La columna lleva un formateo?
                $estilos[$clave]['estilo'] = array_merge($estilos[$clave]['estilo'], $this->excel_get_estilo($this->_columnas[$clave]['estilo']));
				if(isset($this->_columnas[$clave]["formateo"])){
					$metodo = "formato_" . $this->_columnas[$clave]["formateo"];
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
		if ($es_total_general && $this->_excel_cortar_hoja_cc_0) {
			$this->salida->crear_hoja('Totales');
			$agregar_titulos = true;
		}
		if (! $agregar_titulos) {
			$titulos = null;
		}	
		$this->salida->tabla(array($datos), $titulos, $estilos);
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
			$this->salida->subtitulo($titulo);
		}
		//Datos
		foreach($datos as $desc => $valor){
			$this->salida->texto($desc.': '.$valor);
		}
	}	
	
	/**
	 * Genera el pie del corte de control
	 * @param array $nodo
	 * @param boolean $es_ultimo
	 */
	protected function excel_pie_corte_control( &$nodo, $es_ultimo )
	{
		$span = $this->_cantidad_columnas;
		//-----  Cabecera del PIE --------
		if($this->_cortes_indice[$nodo['corte']]['pie_mostrar_titular']){
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
			$this->salida->texto($descripcion, $opciones, $span);
		}
		
		//----- Totales de columna -------
		if (isset($nodo['acumulador'])) {
			$titulos = false;
			if($this->_cortes_indice[$nodo['corte']]['pie_mostrar_titulos']){
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
				$desc = $this->_sum_usuario[$id]['descripcion'];
				$datos[$desc] = $valor;
			}
			$this->excel_cuadro_sumarizacion($datos,null,300,$nodo['profundidad']);
		}
		//----- Contar Filas
		if($this->_cortes_indice[$nodo['corte']]['pie_contar_filas']) {
			$rangos = $this->excel_get_rangos($nodo);
			$etiqueta = $this->etiqueta_cantidad_filas($nodo['profundidad']);
			$cursor = $this->salida->get_cursor();
			
			$this->salida->texto($etiqueta, $this->_excel_contar_filas_op, $span-1);
			$cursor[0] = $cursor[0] + ($span-1);
			$letra = PHPExcel_Cell::stringFromColumnIndex($cursor[0]);
			$formula = '=ROWS'.implode(' + ROWS', $rangos);
			$this->salida->texto($formula, $this->_excel_contar_filas_op, 1, null, $cursor);
		}
		//----- Contenido del usuario al final del PIE
		$metodo = 'excel_pie_cc_contenido__' . $nodo['corte'];
		if(method_exists($this, $metodo)){
			$this->$metodo($nodo);
		}
		if (!$es_ultimo && $nodo['profundidad'] == 0 && $this->_excel_cortar_hoja_cc_0) {
			$this->salida->crear_hoja();
		}
	}

	/**
	 *@ignore
	 */
	protected function excel_get_rangos($nodo, $columna=null)
	{
		$hoja_actual = $this->salida->get_hoja_nombre();
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
	protected function excel_cc_inicio_nivel()
	{
	}

	/**
	 *  Ventana de extension para realizar tareas al finalizar el corte de control
	 * @ignore
	 */
	protected function excel_cc_fin_nivel()
	{
	}
}
?>
