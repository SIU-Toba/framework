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
	protected $_tipo_salida;
	protected $_total_registros; 
    
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
				$estructura_datos = array_merge($estructura_datos, $col_desc, $col_id);
			}
			$this->_cortes_modo = $this->_info_cuadro['cc_modo'];
			$this->_cortes_anidado_colap = $this->_info_cuadro['cc_modo_anidado_colap'];
		}
		//Procesamiento de columnas
		for($a=0;$a<count($this->_info_cuadro_columna);$a++){
			// Indice de columnas
			$clave = $this->_info_cuadro_columna[$a]['clave'];
			$this->_columnas[ $clave ] =& $this->_info_cuadro_columna[$a];
			//Sumarizacion general
			if ($this->_info_cuadro_columna[$a]['total'] == 1) {
				$this->_acumulador[$clave]=0;
			}
			//Estructura de datos
			//$estructura_datos[] = $clave;
			// Sumarizacion de columnas por corte
			if(trim($this->_info_cuadro_columna[$a]['total_cc'])!=''){
				$cortes = explode(',',$this->_info_cuadro_columna[$a]['total_cc']);
				$cortes = array_map('trim',$cortes);
				foreach($cortes as $corte){
					$this->_cortes_def[$corte]['total'][] = $clave;	
				}
			}
		}
		$this->_estructura_datos = array_unique($estructura_datos);
	}

	/**
	 * Elimina todas las columnas actualmente definidas en el cuadro
	 */
	function limpiar_columnas()
	{
		$this->_info_cuadro_columna = array();
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
		}
		$this->_info_cuadro_columna = array_merge($this->_info_cuadro_columna, $columnas);
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
						$parametros = $this->_clave_seleccionada;
						$this->reportar_evento( $evento, $parametros );						
				}
			}
		}
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
			// - 3 - Paginacion
			if( $this->existe_paginado() ){
				$this->generar_paginado();
			}
			// - 4 - Cortes de control
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
				throw new toba_error("El cuadro necesita recibir la cantidad total de registros con el metodo set_total_registros para poder paginar");
			}
			$this->_cantidad_paginas = ceil($this->_total_registros/$this->_tamanio_pagina);
			if ($this->_pagina_actual > $this->_cantidad_paginas)  {
				$this->_pagina_actual = 1;
			}
		} elseif($this->_info_cuadro["tipo_paginado"] == 'P') {
			// 1) Calculo la cantidad total de registros
			$this->_total_registros = count($this->datos);
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
	function set_tamanio_pagina($tam=null)
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
	        foreach ($this->datos as $fila) { 
	            $ordenamiento[] = $fila[$this->_orden_columna]; 
	        }
	        //Ordeno segun el sentido
	        if($this->_orden_sentido == "asc"){
	            array_multisort($ordenamiento, SORT_ASC , $this->datos);
	        } elseif ($this->_orden_sentido == "des"){
	            array_multisort($ordenamiento, SORT_DESC , $this->datos);
	        }
		}
    }

//################################################################################
//###############################    API basica    ###############################
//################################################################################

	/**
	 * Cambia el título o descripción de una columna dada del cuadro
	 */
	function set_titulo_columna($id_columna, $titulo)
	{
		$this->_columnas[$id_columna]["titulo"] = $titulo;
	}    

	/**
	 * Retorna el conjunto de datos que actualmente posee el cuadro
	 */
    function get_datos()
    {
        return $this->datos;    
    }	

    function get_estructura_datos()
	{
		return $this->_estructura_datos;		
	}
	
	/**
	 * Retorna la definición de las columnas actuales del cuadro
	 */
	function get_columnas()
	{
		return $this->_columnas;	
	}
	
//################################################################################
//#####################    INTERFACE GRAFICA GENERICA  ###########################
//################################################################################

	private function generar_salida($tipo)
	{
		if(($tipo!="html")&&($tipo!="pdf")){
			throw new toba_error_def("El tipo de salida '$tipo' es invalida");	
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
				ei_arbol($this->_sum_usuario,"\$this->sum_usuario");
				ei_arbol($this->_cortes_def,"\$this->cortes_def");
				ei_arbol($this->_cortes_control,"\$this->_cortes_control");
			}
		}else{
            if ($this->_info_cuadro["eof_invisible"]!=1){
                if(trim($this->_info_cuadro["eof_customizado"])!=""){
					$texto = $this->_info_cuadro["eof_customizado"];
                }else{
					$texto = "No se cargaron datos!";
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
		$this->_cantidad_columnas = count($this->_info_cuadro_columna);
		if ( $this->_tipo_salida != 'pdf' ) {
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
	protected function generar_cuadro(&$filas, &$totales=null){
		$metodo = $this->_tipo_salida . '_cuadro';
		$this->$metodo($filas, $totales);
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

	private function generar_cortes_control()
	{
		$this->generar_cc_inicio_nivel();
		foreach(array_keys($this->_cortes_control) as $corte){
			$this->crear_corte( $this->_cortes_control[$corte] );
		}
		$this->generar_cc_fin_nivel();
	}
	
	private function crear_corte(&$nodo)
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
		if ($this->_cortes_anidado_colap) {
			echo "<table class='tabla-0' id='$id_unico' width='100%' border='1'><tr><td>\n";
		}

		//Disparo la generacion recursiva de hijos
		if(isset($nodo['hijos'])){
			$this->generar_cc_inicio_nivel();
			foreach(array_keys($nodo['hijos']) as $corte){
				$this->crear_corte( $nodo['hijos'][$corte] , $id_unico);
			}
			$this->generar_cc_fin_nivel();
		}else{	
			//Disparo la construccion del ultimo nivel
			$this->generar_cuadro( $nodo['filas']); //, $nodo['acumulador']
		}
		if ($this->_cortes_anidado_colap) {
			echo "</td></tr></table>\n";
		}
		$this->generar_pie_corte_control($nodo);
	}


	private function generar_cabecera_corte_control(&$nodo, $id_unico = null){
		$metodo = $this->_tipo_salida . '_cabecera_corte_control';
		$this->$metodo($nodo, $id_unico);
	}
	
	private function generar_pie_corte_control(&$nodo){
		$metodo = $this->_tipo_salida . '_pie_corte_control';
		$this->$metodo($nodo);
	}

	private function generar_cc_inicio_nivel(){
		$metodo = $this->_tipo_salida . '_cc_inicio_nivel';
		$this->$metodo();
	}

	private function generar_cc_fin_nivel(){
		$metodo = $this->_tipo_salida . '_cc_fin_nivel';
		$this->$metodo();
	}

//################################################################################
//#################################    HTML    ###################################
//################################################################################

	function generar_html()
	{
		$this->generar_salida("html");
	}

	private function html_inicio()
	{
		//Campos de comunicación con JS
		echo toba_form::hidden($this->_submit, '');
		echo toba_form::hidden($this->_submit_seleccion, '');
		echo toba_form::hidden($this->_submit_orden_columna, '');
		echo toba_form::hidden($this->_submit_orden_sentido, '');
		echo toba_form::hidden($this->_submit_paginado, '');
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
	}
	
	private function html_fin()
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
		//Paginacion
		if ($this->_info_cuadro["paginar"]) {
			echo"<tr><td>";
           	$this->html_barra_paginacion();
			echo "</td></tr>\n";
		}
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
		echo $this->get_html_barra_editor();
		echo ei_mensaje($texto);
	}
	
	//-------------------------------------------------------------------------------
	//-- Generacion de los CORTES de CONTROL
	//-------------------------------------------------------------------------------

	private function html_cc_inicio_nivel()
	{
		if($this->_cortes_modo == apex_cuadro_cc_anidado){
			echo "<ul>\n";
		}
	}

	private function html_cc_fin_nivel()
	{
		if($this->_cortes_modo == apex_cuadro_cc_anidado){
			echo "</ul>\n";
		}
	}
	
	private function get_nivel_css($profundidad)
	{
		return ($profundidad > 2) ? 2 : $profundidad;
	}		

	/**
		Genera la CABECERA del corte de control
	*/
	private function html_cabecera_corte_control(&$nodo, $id_unico = null)
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
			if ($this->_cortes_anidado_colap){
				echo "<table width='100%' class='tabla-0' border='0'><tr><td width='100%' class='$class'>";			
			} else {
				echo "<tr><td  colspan='$this->_cantidad_columnas_total' class='$class'>\n";
			}
			$this->$metodo($nodo);
			if ($this->_cortes_anidado_colap){
				$js = 	"onclick=\"{$this->objeto_js}.colapsar_corte('$id_unico');\"";						
				$img = toba_recurso::imagen_toba('colapsado.gif', true, null, null, null, null, $js);
				echo "</td><td class='$class impresion-ocultable'>$img</td></tr></table>";
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
	protected function html_pie_corte_control(&$nodo)
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
				echo "<em>" . $this->etiqueta_cantidad_filas($nodo['profundidad']) . count($nodo['filas']) . "<em>";
				echo "</td></tr>\n";
			}
			//----- Contenido del usuario al final del PIE
			$metodo = 'html_pie_cc_contenido__' . $nodo['corte'];
			if(method_exists($this, $metodo)){
				echo "<tr><td  class='$css_pie' colspan='$this->_cantidad_columnas_total'>\n";
				$this->$metodo($nodo);
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

	function tabla_datos_es_general()
	{
		if(! $this->existen_cortes_control() ) {
			return true;	
		}else{
			return ($this->_cortes_modo == apex_cuadro_cc_tabular) && ! $this->_cortes_anidado_colap;
		}
	}
	
	protected function html_cuadro(&$filas, &$totales=null)
	{
		//Si existen cortes de control y el layout es tabular, el encabezado de la tabla ya se genero
		if( ! $this->tabla_datos_es_general() ){
			$this->html_cuadro_inicio();
		}
		$this->html_cuadro_cabecera_columnas();
		$par = false;
        foreach($filas as $f)
        {
        	$estilo_fila = $par ? 'ei-cuadro-celda-par' : 'ei-cuadro-celda-impar';
			$clave_fila = $this->get_clave_fila($f);
			if (is_array($this->_clave_seleccionada)) {
				$clave_seleccionada = implode(apex_qs_separador, $this->_clave_seleccionada);	
			} else {
				$clave_seleccionada = $this->_clave_seleccionada;	
			}
			
			$esta_seleccionada = ($clave_fila == $clave_seleccionada);
			$estilo_seleccion = ($esta_seleccionada) ? "ei-cuadro-fila-sel" : "ei-cuadro-fila";
            echo "<tr class='$estilo_fila' >\n";
 			//---> Creo las CELDAS de una FILA <----
            for ($a=0;$a< $this->_cantidad_columnas;$a++)
            {
                //*** 1) Recupero el VALOR
				$valor = "";
                if(isset($this->_info_cuadro_columna[$a]["clave"])){
					if(isset($this->datos[$f][$this->_info_cuadro_columna[$a]["clave"]])){
						$valor_real = $this->datos[$f][$this->_info_cuadro_columna[$a]["clave"]];
					}else{
						$valor_real = '&nbsp;';
						//ATENCION!! hay una columna que no esta disponible!
					}
	                //Hay que formatear?
	                if(isset($this->_info_cuadro_columna[$a]["formateo"])){
	                    $funcion = "formato_" . $this->_info_cuadro_columna[$a]["formateo"];
	                    //Formateo el valor
	                    $valor = $funcion($valor_real);
	                } else {
	                	$valor = $valor_real;	
	                }
	            }
	            //*** 2) La celda posee un vinculo??
				if ( ($this->_tipo_salida != 'pdf') && ( $this->_info_cuadro_columna[$a]['usar_vinculo'] ) ) {
					// Armo el vinculo.
					$clave_columna = isset($this->_info_cuadro_columna[$a]['vinculo_indice']) ? $this->_info_cuadro_columna[$a]['vinculo_indice'] : $this->_info_cuadro_columna[$a]['clave'];
					$opciones = array(); $parametros = array();
					if($this->_info_cuadro_columna[$a]['vinculo_celda']) {
						$opciones['celda_memoria'] = $this->_info_cuadro_columna[$a]['vinculo_celda'];
					} else {
						$opciones['celda_memoria'] = $clave_columna;
					}
					$parametros[$clave_columna] = $valor_real;
					$item = $this->_info_cuadro_columna[$a]['vinculo_item'];
					$url = toba::vinculador()->crear_vinculo(toba::proyecto()->get_id(),$item,$parametros,$opciones);
					// Armo el disparo
					if ( $this->_info_cuadro_columna[$a]['vinculo_popup'] ) {
						$popup_parametros = array();
						if($this->_info_cuadro_columna[$a]['vinculo_popup_param']) {
							//Esto se puede optimizar (1 por columna en vez de columna/fila)!
							$temp = explode(',',$this->_info_cuadro_columna[$a]['vinculo_popup_param']);
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
						$target = ($this->_info_cuadro_columna[$a]['target']) ? "target='".$this->_info_cuadro_columna[$a]['target']."'" : '';
						$valor = "<a href='$url' $target>$valor</a>";
					}
				}
                //*** 3) Genero el HTML
                echo "<td class='$estilo_seleccion ".$this->_info_cuadro_columna[$a]["estilo"]."'>\n";
                echo $valor;
                echo "</td>\n";
                //Termino la CELDA
            }
 			//---> Creo los EVENTOS de la FILA <---
			if ( $this->_tipo_salida != 'pdf' ) {
				foreach ($this->get_eventos_sobre_fila() as $id => $evento) {
					echo "<td class='ei-cuadro-fila-evt' width='1%'>\n";
					if( ! $evento->esta_anulado() ) { //Si el evento viene desactivado de la conf, no lo utilizo
						//1: Posiciono al evento en la fila
						$evento->set_parametros($clave_fila);
						if($evento->posee_accion_vincular()){
							//-- Si es un vinculo, fuerza a crear una nueva instancia del vinculo en el evento asi aloja al id de la fila y sus conf.
							$evento->vinculo(true)->set_parametros($this->get_clave_fila_array($f));	
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
							echo $evento->get_html($this->_submit, $this->objeto_js, $this->_id);
						} else {
							$evento->restituir();	//Lo activo para la proxima fila
						}
					}
	            	echo "</td>\n";
				}
			}
			//--------------------------------------
            echo "</tr>\n";
            $par = !$par;
        }
		if(isset($totales)){
			$this->html_cuadro_totales_columnas($totales);
		}
		if( ! $this->tabla_datos_es_general() ){
			$this->html_acumulador_usuario();
			$this->html_cuadro_fin();
		}
	}

	protected function html_cuadro_inicio()
	{
		echo "<TABLE width='100%' class='tabla-0' border='0'>\n";
	}
	
	protected function html_cuadro_fin()
	{
		echo "</TABLE>\n";
	}

	protected function html_cuadro_cabecera_columnas()
	{
		//¿Alguna columna tiene título?
		$alguna_tiene_titulo = false;
        for ($a=0;$a<$this->_cantidad_columnas;$a++) {		
        	if (trim($this->_info_cuadro_columna[$a]["titulo"]) != '') {
        		$alguna_tiene_titulo = true;
        		break;
        	}
        }
        if ($alguna_tiene_titulo) {
	        echo "<tr>\n";
	        for ($a=0;$a<$this->_cantidad_columnas;$a++)
	        {
	            if(isset($this->_info_cuadro_columna[$a]["ancho"])){
	                $ancho = " width='". $this->_info_cuadro_columna[$a]["ancho"] . "'";
	            }else{
	                $ancho = "";
	            }
	            $estilo_columna = $this->_info_cuadro_columna[$a]["estilo_titulo"];
	            if(!$estilo_columna){
	            	$estilo_columna = 'ei-cuadro-col-tit';
	            }
	            echo "<td class='$estilo_columna' $ancho>\n";
	            $this->html_cuadro_cabecera_columna(    $this->_info_cuadro_columna[$a]["titulo"],
	                                        $this->_info_cuadro_columna[$a]["clave"],
	                                        $a );
	            echo "</td>\n";
	        }
	        //-- Eventos sobre fila
			if($this->_cantidad_columnas_extra > 0){
				foreach ($this->get_eventos_sobre_fila() as $evento) {
					echo "<td class='ei-cuadro-col-tit'>&nbsp;";
					if (toba_editor::modo_prueba()) {
						echo toba_editor::get_vinculo_evento($this->_id, $this->_info['clase_editor_item'], $evento->get_id())."\n";
					}
					echo "</td>\n";
				}
			}
	        echo "</tr>\n";
        }
	}

	/**
	 * Genera la cabecera de una columna
	 * @ignore 
	 */
	protected function html_cuadro_cabecera_columna($titulo,$columna,$indice)
    {
        //--- ¿Es ordenable?
		if (	isset($this->_eventos['ordenar']) 
				&& $this->_info_cuadro_columna[$indice]["no_ordenar"] != 1
				&& $this->_tipo_salida != 'pdf' ) {
			$sentido = array();
			$sentido[] = array('asc', 'Ordenar ascendente');
			$sentido[] = array('des', 'Ordenar descendente');
			echo "<span class='ei-cuadro-orden'>";			
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
				echo toba_recurso::imagen($src, null, null, $sen[1], '', "onclick=\"$js\"", 'cursor: pointer; cursor:hand;');
			}
			echo "</span>";			
		}    	
		//--- Nombre de la columna
		if (trim($columna) != '' || trim($this->_info_cuadro_columna[$indice]["vinculo_indice"])!="") {           
            echo $titulo;
        }	
		//---Editor de la columna
		$editor = '';
		if ( toba_editor::modo_prueba() && $this->_tipo_salida != 'pdf' ){
			$item_editor = "/admin/objetos_toba/editores/ei_cuadro";
			$param_editor = array( apex_hilo_qs_zona => implode(apex_qs_separador,$this->_id),
									'columna' => $columna );
			$editor = toba_editor::get_vinculo_subcomponente($item_editor, $param_editor);
		}	
		echo $editor;
    }

    /**
     * @ignore 
     */
	function html_acumulador_usuario()
	{
		if (isset($this->sum_usuario)) {
			foreach($this->sum_usuario as $sum) {
				if($sum['corte'] == 'toba_total') {
					$metodo = $sum['metodo'];
					$sumarizacion[$sum['descripcion']] = $this->$metodo($this->datos);
				}
			}
		}
		if (isset($sumarizacion)) {
			$css = 'cuadro-cc-sum-nivel-1';
			echo "<tr><td colspan='$this->cantidad_columnas_total'>\n";
			$this->html_cuadro_sumarizacion($sumarizacion,null,300,$css);
			echo "</td></tr>\n";
		}
	}

    /**
     * @ignore 
     */
	protected function html_cuadro_totales_columnas($totales,$estilo=null,$agregar_titulos=false, $estilo_linea=null)
	{
		$clase_linea = isset($estilo_linea) ? "class='$estilo_linea'" : "";
		if($agregar_titulos || (! $this->tabla_datos_es_general()) ){
			echo "<tr>\n";
			for ($a=0;$a<$this->_cantidad_columnas;$a++){
				$clave = $this->_info_cuadro_columna[$a]["clave"];
			    if(isset($totales[$clave])){
					$valor = $this->_info_cuadro_columna[$a]["titulo"];
					echo "<td class='".$this->_info_cuadro_columna[$a]["estilo_titulo"]."'><strong>$valor</strong></td>\n";
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
		echo "<tr class='ei-cuadro-totales'>\n";
		for ($a=0;$a<$this->_cantidad_columnas;$a++){
			$clave = $this->_info_cuadro_columna[$a]["clave"];
			//Defino el valor de la columna
		    if(isset($totales[$clave])){
				$valor = $totales[$clave];
				if(!isset($estilo)){
					$estilo = $this->_info_cuadro_columna[$a]["estilo"];
				}
				//La columna lleva un formateo?
				if(isset($this->_info_cuadro_columna[$a]["formateo"])){
					$metodo = "formato_" . $this->_info_cuadro_columna[$a]["formateo"];
					$valor = $metodo($valor);
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
	}

	//-------------------------------------------------------------------------------
	//-- Elementos visuales independientes
	//-------------------------------------------------------------------------------

	private function html_cuadro_sumarizacion($datos, $titulo=null , $ancho=null, $css='col-num-p1')
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

	private function html_barra_paginacion()
	{
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
			echo "<div class='ei-cuadro-pag'>";
			echo "$primero $anterior Página <strong>{$this->_pagina_actual}</strong> de ";
			echo "<strong>{$this->_cantidad_paginas}</strong> $siguiente $ultimo";
			echo "</div>";
		}
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
		echo $identado."window.{$this->objeto_js} = new ei_cuadro('{$this->objeto_js}', '{$this->_submit}');\n";
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
		$this->generar_salida("pdf");
	}

	private function pdf_inicio()
	{
		$ancho = isset($this->_info_cuadro["ancho"]) ? $this->_info_cuadro["ancho"] : "";
        echo "<TABLE width='$ancho' class='tabla-0'>";
		// Cabecera
		echo"<tr><td class='ei-cuadro-cabecera'>";
		$this->html_cabecera();		
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
	protected function pdf_fin()
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
	protected function pdf_cuadro(&$filas, &$totales){
		$this->html_cuadro( $filas, $totales );
	}

	protected function pdf_mensaje_cuadro_vacio($texto){
		$this->html_mensaje_cuadro_vacio($texto);
	}

	//-- Cortes de Control --

	protected function pdf_cabecera_corte_control(&$nodo ){
		$this->html_cabecera_corte_control($nodo);
	}

	protected function pdf_pie_corte_control( &$nodo ){
		$this->html_pie_corte_control($nodo);
	}

	private function pdf_cc_inicio_nivel(){
	}

	private function pdf_cc_fin_nivel(){
	}
}
?>