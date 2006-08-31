<?
define("apex_cuadro_compatible",1);
require_once("toba_ei.php");
require_once("nucleo/lib/interface/toba_form.php");
require_once("nucleo/lib/interface/toba_formateo.php"); 
define("apex_cuadro_cc_tabular","t");
define("apex_cuadro_cc_anidado","a");

/**
 * Un ei_cuadro es una grilla de registros pensados para visualización. 
 * @package Objetos
 * @subpackage  Ei
 */
class toba_ei_cuadro extends toba_ei
{
	protected $prefijo = 'cuadro';	
 	protected $columnas;
    protected $cantidad_columnas;                 	// Cantidad de columnas a mostrar
    protected $cantidad_columnas_extra = 0;        	// Cantidad de columnas utilizadas para eventos
    protected $cantidad_columnas_total;            	// Cantidad total de columnas
    protected $datos;                             	// Los datos que constituyen el contenido del cuadro
    protected $columnas_clave;                    	
	protected $clave_seleccionada;
	protected $estructura_datos;					// Estructura de datos esperados por el cuadro
	protected $acumulador;							// Acumulador de totales generales
	protected $acumulador_sum_usuario;				// Acumulador general de las sumarizaciones del usuario
	protected $sum_usuario;
	//Orden
    protected $orden_columna;                     	// Columna utilizada para realizar el orden
    protected $orden_sentido;                     	// Sentido del orden ('asc' / 'desc')
    protected $ordenado = false;
	//Paginacion
	protected $pagina_actual;
	protected $tamanio_pagina;
	protected $cantidad_paginas;
	//Cortes control
	protected $cortes_indice;
	protected $cortes_def;
	protected $cortes_control;
	protected $cortes_modo;
	//Salida
	protected $tipo_salida;
 
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
		if (isset($this->memoria['ordenado'])) {
			$this->ordenado = $this->memoria['ordenado'];
		}
		$this->inspeccionar_sumarizaciones_usuario();
	}

	function inicializar()
	{
		$this->submit_orden_columna = $this->submit."__orden_columna";
		$this->submit_orden_sentido = $this->submit."__orden_sentido";
		$this->submit_seleccion = $this->submit."__seleccion";
		$this->submit_paginado = $this->submit."__pagina_actual";		
	}
	
	function procesar_definicion()
	{
		$estructura_datos = array();
		//Armo una estructura que describa las caracteristicas de los cortes
		if($this->existen_cortes_control()){
			for($a=0;$a<count($this->info_cuadro_cortes);$a++){
				$id_corte = $this->info_cuadro_cortes[$a]['identificador'];						// CAMBIAR !
				//Genero el Indice
				$this->cortes_indice[$id_corte] =& $this->info_cuadro_cortes[$a];
				//Genero la tabla de definiciones	
				$col_id = explode(',',$this->info_cuadro_cortes[$a]['columnas_id']);
				$col_id = array_map('trim',$col_id);
				$this->cortes_def[$id_corte]['clave'] = $col_id;
				$col_desc = explode(',',$this->info_cuadro_cortes[$a]['columnas_descripcion']);
				$col_desc = array_map('trim',$col_desc);
				$this->cortes_def[$id_corte]['descripcion'] = $col_desc;
				$estructura_datos = array_merge($estructura_datos, $col_desc, $col_id);
			}
			$this->cortes_modo = $this->info_cuadro['cc_modo'];
		}
		//Procesamiento de columnas
		for($a=0;$a<count($this->info_cuadro_columna);$a++){
			// Indice de columnas
			$clave = $this->info_cuadro_columna[$a]['clave'];
			$this->columnas[ $clave ] =& $this->info_cuadro_columna[$a];
			//Sumarizacion general
			if ($this->info_cuadro_columna[$a]['total'] == 1) {
				$this->acumulador[$clave]=0;
			}
			//Estructura de datos
			//$estructura_datos[] = $clave;
			// Sumarizacion de columnas por corte
			if(trim($this->info_cuadro_columna[$a]['total_cc'])!=''){
				$cortes = explode(',',$this->info_cuadro_columna[$a]['total_cc']);
				$cortes = array_map('trim',$cortes);
				foreach($cortes as $corte){
					$this->cortes_def[$corte]['total'][] = $clave;	
				}
			}
		}
		$this->estructura_datos = array_unique($estructura_datos);
	}

	/**
		Si el usuario declaro funciones de sumarizacion por algun corte,
		esta funcion las agrega en la planificacion de la ejecucion.
	*/
	private function inspeccionar_sumarizaciones_usuario()
	{
		//Si soy una subclase
		if($this->info['subclase']){
			$this->sum_usuario = array();
			$clase = new ReflectionClass(get_class($this));
			foreach ($clase->getMethods() as $metodo){
				$id = null;
				if (substr($metodo->getName(), 0, 12) == 'sumarizar_cc'){		//** Sumarizacion Corte de control
					$temp = explode('__', $metodo->getName());
					if(count($temp)!=3){
						throw new toba_excepcion_def("La funcion de sumarizacion esta mal definida");	
					}
					$id = $temp[2];
					$corte = $temp[1];
					if(!isset($this->cortes_def[$corte])){	//El corte esta definido?
						throw new toba_excepcion_def("La funcion de sumarizacion no esta direccionada a un CORTE existente");	
					}
					//Agrego la sumarizacion al corte
					$this->cortes_def[$corte]['sum_usuario'][]=$id;
				}elseif(substr($metodo->getName(), 0, 11) == 'sumarizar__'){ 	//** Sumarizacion GENERAL
					$temp = explode('__', $metodo->getName());
					$id = $temp[1];
					$corte = 'toba_total';
					$this->acumulador_sum_usuario[$id] = 0;
				}
				if($id){
					if(isset($this->sum_usuario[$id])){
						throw new toba_excepcion_def("Las funciones de sumarizacion deben tener IDs unicos. El id '$id' ya existe");	
					}
					// Agrego la sumarizacion en la pila de sumarizaciones.
					$this->sum_usuario[$id]['metodo'] = $metodo->getName();
					$this->sum_usuario[$id]['corte'] = $corte;
					$this->sum_usuario[$id]['descripcion'] = $this->obtener_desc_sumarizacion($metodo->getDocComment());
				}
			}
		}		
	}

	private function obtener_desc_sumarizacion($texto)
	{
	    $desc =  parsear_doc_comment( $texto );
		return trim($desc != '')? $desc : 'Descripcion no definida';
	}
	
	function destruir()
	{
		if (isset($this->ordenado)) {
			$this->memoria['ordenado'] = $this->ordenado;	
		}
		$this->finalizar_seleccion();
		$this->finalizar_ordenamiento();
		$this->finalizar_paginado();
		parent::destruir();
	}

//################################################################################
//############################        EVENTOS        #############################
//################################################################################

	protected function cargar_lista_eventos()
	{
		parent::cargar_lista_eventos();
		if($this->info_cuadro["ordenar"]) { 
			$this->eventos += eventos::ordenar();		
		}
		if ($this->info_cuadro["paginar"]) {
			$this->eventos += eventos::cambiar_pagina();
		}
	}

	function disparar_eventos()
	{
		if (isset($this->memoria['eventos']['ordenar'])) {
			$this->refrescar_ordenamiento();
		}
		if (isset($_POST[$this->submit]) && $_POST[$this->submit]!="") {
			$evento = $_POST[$this->submit];		
			//El evento estaba entre los ofrecidos?
			if(isset($this->memoria['eventos'][$evento]) ) {
				switch ($evento) {
					case 'ordenar':
						if (isset($this->orden_columna) && isset($this->orden_sentido)) {
							$parametros = array('sentido'=> $this->orden_sentido, 'columna'=>$this->orden_columna);
							$exitoso = $this->reportar_evento( $evento, $parametros );
							if ($exitoso !== apex_ei_evt_sin_rpta && $exitoso !== false) {
								$this->ordenado = true;
							} else {
								$this->ordenado = false;	
							}
						}
						break;
					case 'cambiar_pagina':
						$this->cargar_cambio_pagina();
						$parametros = $this->pagina_actual;
						$this->reportar_evento( $evento, $parametros );
						break;
					default:
						$this->cargar_seleccion();
						$parametros = $this->clave_seleccionada;
						$this->reportar_evento( $evento, $parametros );						
				}
			}
		}
	}

    function set_datos($datos)
    {
		$this->datos = $datos;
		if (!is_array($this->datos)) {
			throw new toba_excepcion_def( $this->get_txt() . 
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
			if( $this->existen_cortes_control() ){
				$this->planificar_cortes_control();
			}else{
				$this->calcular_totales_generales();
			}
		}
   }

    
//################################################################################
//############################   Procesos GENERALES   ############################
//################################################################################

	private function validar_estructura_datos()
	{
		$muestra = current($this->datos);
		if (!is_array($muestra)) {
			$error = array_values($this->estructura_datos);
		} else {
			$error = array();
			foreach($this->estructura_datos as $columna){
				if(!isset($muestra[$columna])){
					$error[] = $columna;
				}
			}
		}
		if(count($error)>0){
			throw new toba_excepcion_def( $this->get_txt() . 
					" El array provisto para cargar el cuadro posee un formato incorrecto\n" .
					" Las columnas: '". implode("', '",$error) ."' NO EXISTEN");
		}
	}

	private function datos_cargados()
	{
		return (count($this->datos) > 0);
	}

	private function calcular_totales_generales()
	//Esto esta duplicado en el calculo de cortes de control por optimizacion
	{
		foreach(array_keys($this->datos) as $dato)
		{
			//Incremento el acumulador general
			if(isset($this->acumulador)){
				foreach(array_keys($this->acumulador) as $columna){
					$this->acumulador[$columna] += $this->datos[$dato][$columna];
				}	
			}
		}
	}

//################################################################################
//############################   CLAVE  y  SELECCION   ###########################
//################################################################################

	function inicializar_manejo_clave()
	{
        if($this->info_cuadro["clave_datos_tabla"]){										//Clave del DT
			$this->columnas_clave = array( apex_datos_clave_fila );
        }elseif(trim($this->info_cuadro["columnas_clave"])!=''){
            $this->columnas_clave = explode(",",$this->info_cuadro["columnas_clave"]);		//Clave usuario
            $this->columnas_clave = array_map("trim",$this->columnas_clave);
        }else{
			$this->columnas_clave = null;
        }		
		//Agrego las columnas de la clave en la definicion de la estructura de datos
		if(is_array($this->columnas_clave)){
			$estructura_datos = array_merge( $this->columnas_clave, $this->estructura_datos);
			$this->estructura_datos = array_unique($estructura_datos);
		}
		//Inicializo la seleccion
		$this->clave_seleccionada = null;
	}

	function finalizar_seleccion()
	{
		if (isset($this->clave_seleccionada)) {
			$this->memoria['clave_seleccionada'] = $this->clave_seleccionada;
		} else {
			unset($this->memoria['clave_seleccionada']);
		}
	}

	function cargar_seleccion()
	{	
		$this->clave_seleccionada = null;
		//La seleccion se inicializa con el del pedido anterior
		if (isset($this->memoria['clave_seleccionada']))
			$this->clave_seleccionada = $this->memoria['clave_seleccionada'];
		//La seleccion se actualiza cuando el cliente lo pide explicitamente
		if(isset($_POST[$this->submit_seleccion])) {
			$clave = $_POST[$this->submit_seleccion];
			if ($clave != '') {
				$clave = explode(apex_qs_separador, $clave);				
				//Devuelvo un array asociativo con el nombre de las claves
				for($a=0;$a<count($clave);$a++) {
					$this->clave_seleccionada[$this->columnas_clave[$a]] = $clave[$a];		
				}
			}
		}	
	}

	function deseleccionar()
	{
		$this->clave_seleccionada = null;
	}

	/**
	*	Indica al cuadro cual es la clave seleccionada. 
	*	A la hora de mostrar la grilla se crea un feedback gráfico sobre la fila que posea esta clave
	*	@param array $clave Arreglo asociativo id_clave => valor_clave
	*/
	function seleccionar($clave)
	{
		$this->clave_seleccionada = $clave;
	}

	function hay_seleccion()
	{
		return isset($this->clave_seleccionada);
	}

    function obtener_clave_fila($fila)
	//Genero la CLAVE
    {
        $id_fila = "";
        if (isset($this->columnas_clave)) {
	        foreach($this->columnas_clave as $clave){
	            $id_fila .= $this->datos[$fila][$clave] . apex_qs_separador;
	        }
        }
        $id_fila = substr($id_fila,0,(strlen($id_fila)-(strlen(apex_qs_separador))));   
        return $id_fila;
    }

	function obtener_clave_fila_array($fila)
	{
        if (isset($this->columnas_clave)) {
	        foreach($this->columnas_clave as $clave){
	            $array[$clave] = $this->datos[$fila][$clave];
	        }
	        return $array;
        }
	}

    /**
    *	@deprecated Desde 0.8.3. Usar obtener_clave_seleccionada
    */
	function obtener_clave()
	{
		toba::get_logger()->obsoleto(__CLASS__, __FUNCTION__, "0.8.3", "Usar obtener_clave_seleccionada");
		return $this->obtener_clave_seleccionada();
	}
	
    /**
    *	En caso de existir una fila seleccionada, retorna su clave
	*	@return array Arreglo asociativo id_clave => valor_clave    
    */
	function obtener_clave_seleccionada()
	{
		return $this->clave_seleccionada;
	}

//################################################################################
//##############################    PAGINACION    ################################
//################################################################################

	function existe_paginado()
	{
		return $this->info_cuadro["paginar"];
	}

	function inicializar_paginado()
	{
		if(isset($this->memoria["pagina_actual"])){
			$this->pagina_actual = $this->memoria["pagina_actual"];
		}else{
			$this->pagina_actual = 1;
		}
		if (! isset($this->tamanio_pagina))  {
        	$this->tamanio_pagina = isset($this->info_cuadro["tamano_pagina"]) ? $this->info_cuadro["tamano_pagina"] : 80;
		}
	}
	
	function finalizar_paginado()
	{
		if (isset($this->pagina_actual)) {
			$this->memoria['pagina_actual']= $this->pagina_actual;
		} else {
			unset($this->memoria['pagina_actual']);
		}		
	}

	function generar_paginado()
	{
		if($this->info_cuadro["tipo_paginado"] == 'C') {
			if (!isset($this->total_registros) || ! is_numeric($this->total_registros)) {
				throw new toba_excepcion("El paginado del cuadro necesita recibir la cantidad total de registros para poder paginar");
			}
			$this->cantidad_paginas = ceil($this->total_registros/$this->tamanio_pagina);
			if ($this->pagina_actual > $this->cantidad_paginas)  {
				$this->pagina_actual = 1;
			}
		} elseif($this->info_cuadro["tipo_paginado"] == 'P') {
			// 1) Calculo la cantidad total de registros
			$this->total_registros = count($this->datos);
			if($this->total_registros > 0) {
				// 2) Calculo la cantidad de paginas
				$this->cantidad_paginas = ceil($this->total_registros/$this->tamanio_pagina);            
				if ($this->pagina_actual > $this->cantidad_paginas) 
					$this->pagina_actual = 1;
				$this->datos = $this->obtener_datos_paginados($this->datos);
			}
		}else{
			$this->cantidad_paginas = 1;
		}
	}

	function obtener_datos_paginados($datos)
	{
		$offset = ($this->pagina_actual - 1) * $this->tamanio_pagina;
		return array_slice($datos, $offset, $this->tamanio_pagina);
	}

	function set_total_registros($cant)
	{
		$this->total_registros = $cant;
	}
	
	function get_tamanio_pagina()
	{
		return $this->tamanio_pagina;
	}
	
	function set_tamanio_pagina($tam)
	{
		$this->tamanio_pagina = $tam;	
	}
	
	function get_pagina_actual()
	{
		return $this->pagina_actual;
	}

	function cargar_cambio_pagina()
	{	
		if(isset($_POST[$this->submit_paginado]) && trim($_POST[$this->submit_paginado]) != '') 
			$this->pagina_actual = $_POST[$this->submit_paginado];
	}

//################################################################################
//###########################    CORTES de CONTROL    ############################
//################################################################################

	function existen_cortes_control()
	{
		return (count($this->info_cuadro_cortes)>0);
	}
	
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
		$this->cortes_niveles = count($this->info_cuadro_cortes);
		$this->cortes_control = array();
		foreach(array_keys($this->datos) as $dato)
		{
			//Punto de partida desde donde construir el arbol
			$ref =& $this->cortes_control;
			$profundidad = 0;
			foreach(array_keys($this->cortes_def) as $corte)
			{
				$clave_array=array();
				//-- Recupero la clave de la fila en el nivel
				foreach($this->cortes_def[$corte]['clave'] as $id_corte){
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
					foreach($this->cortes_def[$corte]['descripcion'] as $desc_corte){
						$ref[$clave]['descripcion'][$desc_corte] = $this->datos[$dato][$desc_corte];
					}
					//Inicializo el ACUMULADOR de columnas
					if(isset($this->cortes_def[$corte]['total'])){
						foreach($this->cortes_def[$corte]['total'] as $columna){
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
			if(isset($this->acumulador)){
				foreach(array_keys($this->acumulador) as $columna){
					$this->acumulador[$columna] += $this->datos[$dato][$columna];
				}	
			}
		}
	}

//################################################################################
//#################################    ORDEN    ##################################
//################################################################################

	function finalizar_ordenamiento()
	{
		if (isset($this->orden_columna)) {
			$this->memoria['orden_columna']= $this->orden_columna;
		} else {
			unset($this->memoria['orden_columna']);
		}
		if (isset($this->orden_sentido)) {
			$this->memoria['orden_sentido']= $this->orden_sentido;
		} else {
			unset($this->memoria['orden_sentido']);
		}		
	}

	function refrescar_ordenamiento()
	{
		//¿Viene seteado de la memoria?
        if(isset($this->memoria['orden_columna']))
			$this->orden_columna = $this->memoria['orden_columna'];
		if(isset($this->memoria['orden_sentido']))
			$this->orden_sentido = $this->memoria['orden_sentido'];

		//¿Lo cargo el usuario?
		if (isset($_POST[$this->submit_orden_columna]) && $_POST[$this->submit_orden_columna] != '') {
			$nueva_col = $_POST[$this->submit_orden_columna];
		}
		if (isset($_POST[$this->submit_orden_sentido]) && $_POST[$this->submit_orden_sentido] != '') {
			$nuevo_sent = $_POST[$this->submit_orden_sentido];
		}
		if (isset($nueva_col) && isset($nuevo_sent)) {
			//Si se vuelve a pedir el mismo ordenamiento, se anula			
			if (isset($this->orden_columna) && $nueva_col == $this->orden_columna &&
				isset($this->orden_sentido) && $nuevo_sent == $this->orden_sentido) {
				unset($this->orden_columna);
				unset($this->orden_sentido);
			} else {
				$this->orden_columna = $nueva_col;
				$this->orden_sentido = $nuevo_sent;
			}
		}
	}

	function hay_ordenamiento()
	{
        return (isset($this->orden_sentido) && isset($this->orden_columna));
	}

    function ordenar()
	{
		if (! $this->ordenado) {
			$ordenamiento = array();
	        foreach ($this->datos as $fila) { 
	            $ordenamiento[] = $fila[$this->orden_columna]; 
	        }
	        //Ordeno segun el sentido
	        if($this->orden_sentido == "asc"){
	            array_multisort($ordenamiento, SORT_ASC , $this->datos);
	        } elseif ($this->orden_sentido == "des"){
	            array_multisort($ordenamiento, SORT_DESC , $this->datos);
	        }
		}
    }

//################################################################################
//###############################    API basica    ###############################
//################################################################################

	public function set_titulo_columna($id_columna, $titulo)
	{
		$this->columnas[$id_columna]["titulo"] = $titulo;
	}    

    public function obtener_datos()
    {
        return $this->datos;    
    }	

	public function set_titulo()
	{
	}
	
	public function ocultar_cabecera()
	{
	}
	
	public function get_estructura_datos()
	{
		return $this->estructura_datos;		
	}
	
	public function get_columnas()
	{
		return $this->columnas;	
	}
//################################################################################
//#####################    INTERFACE GRAFICA GENERICA  ###########################
//################################################################################

	private function generar_salida($tipo)
	{
		if(($tipo!="html")&&($tipo!="pdf")){
			throw new toba_excepcion_def("El tipo de salida '$tipo' es invalida");	
		}
		$this->tipo_salida = $tipo;
		if( $this->datos_cargados() ){
			$this->inicializar_generacion();
			$this->generar_inicio();
			//Generacion de contenido
			if($this->existen_cortes_control()){
				$this->generar_cortes_control();
			}else{
				$filas = array_keys($this->datos);
				$this->generar_cuadro($filas, $this->acumulador);
			}
			$this->generar_fin();
			if( false && $this->existen_cortes_control() ){
				ei_arbol($this->sum_usuario,"\$this->sum_usuario");
				ei_arbol($this->cortes_def,"\$this->cortes_def");
				ei_arbol($this->cortes_control,"\$this->cortes_control");
			}
		}else{
            if ($this->info_cuadro["eof_invisible"]!=1){
                if(trim($this->info_cuadro["eof_customizado"])!=""){
					$texto = $this->info_cuadro["eof_customizado"];
                }else{
					$texto = "No se cargaron datos!";
                }
				$this->generar_mensaje_cuadro_vacio($texto);
            }
		}
	}

	function inicializar_generacion()
	{
		$this->cantidad_columnas = count($this->info_cuadro_columna);
		if ( $this->tipo_salida != 'pdf' ) {
			$this->cantidad_columnas_extra = $this->cant_eventos_sobre_fila();
		}
		$this->cantidad_columnas_total = $this->cantidad_columnas + $this->cantidad_columnas_extra;
	}

	private function generar_inicio(){
		$metodo = $this->tipo_salida . '_inicio';
		$this->$metodo();
	}

	private function generar_cuadro(&$filas, &$totales=null){
		$metodo = $this->tipo_salida . '_cuadro';
		$this->$metodo($filas, $totales);
	}

	private function generar_fin(){
		$metodo = $this->tipo_salida . '_fin';
		$this->$metodo();
	}

	private function generar_mensaje_cuadro_vacio($texto){
		$metodo = $this->tipo_salida . '_mensaje_cuadro_vacio';
		$this->$metodo($texto);
	}

	//-------------------------------------------------------------------------------
	//-- Cortes de Control
	//-------------------------------------------------------------------------------

	private function generar_cortes_control()
	{
		$this->generar_cc_inicio_nivel();
		foreach(array_keys($this->cortes_control) as $corte){
			$this->crear_corte( $this->cortes_control[$corte] );
		}
		$this->generar_cc_fin_nivel();
	}
	
	private function crear_corte(&$nodo)
	{
		//Disparo las funciones de sumarizacion creadas por el usuario para este corte
		if(isset($this->cortes_def[$nodo['corte']]['sum_usuario'])){
			foreach($this->cortes_def[$nodo['corte']]['sum_usuario'] as $sum){
				$metodo = $this->sum_usuario[$sum]['metodo'];
				$nodo['sum_usuario'][$sum] = $this->$metodo($nodo['filas']);
			}
		}
		//Genero el corte
		$this->generar_cabecera_corte_control($nodo);
		//Disparo la generacion recursiva de hijos
		if(isset($nodo['hijos'])){
			$this->generar_cc_inicio_nivel();
			foreach(array_keys($nodo['hijos']) as $corte){
				$this->crear_corte( $nodo['hijos'][$corte] );
			}
			$this->generar_cc_fin_nivel();
		}else{	
			//Disparo la construccion del ultimo nivel
			$this->generar_cuadro( $nodo['filas']); //, $nodo['acumulador']
		}
		$this->generar_pie_corte_control($nodo);
	}

	private function generar_cabecera_corte_control(&$nodo){
		$metodo = $this->tipo_salida . '_cabecera_corte_control';
		$this->$metodo($nodo);
	}
	
	private function generar_pie_corte_control(&$nodo){
		$metodo = $this->tipo_salida . '_pie_corte_control';
		$this->$metodo($nodo);
	}

	private function generar_cc_inicio_nivel(){
		$metodo = $this->tipo_salida . '_cc_inicio_nivel';
		$this->$metodo();
	}

	private function generar_cc_fin_nivel(){
		$metodo = $this->tipo_salida . '_cc_fin_nivel';
		$this->$metodo();
	}

//################################################################################
//#################################    HTML    ###################################
//################################################################################

	public function generar_html($mostrar_cabecera=true, $titulo=null)
	{
		/*	¿Los parametros hay que destruirlos?	*/
		
		$this->generar_salida("html");
	}

	private function html_inicio()
	{
		//Campos de comunicación con JS
		echo toba_form::hidden($this->submit, '');
		echo toba_form::hidden($this->submit_seleccion, '');
		echo toba_form::hidden($this->submit_orden_columna, '');
		echo toba_form::hidden($this->submit_orden_sentido, '');
		echo toba_form::hidden($this->submit_paginado, '');
		//-- Scroll       
        if($this->info_cuadro["scroll"]){
			$ancho = isset($this->info_cuadro["ancho"]) ? $this->info_cuadro["ancho"] : "";
			$alto = isset($this->info_cuadro["alto"]) ? $this->info_cuadro["alto"] : "auto";
			echo "<div style='overflow: auto; height: $alto; width: $ancho; border: 1px inset; padding: 0px;'>\n";
		}else{
			$ancho = isset($this->info_cuadro["ancho"]) ? $this->info_cuadro["ancho"] : "";
		}
		//-- Tabla BASE
		$mostrar_cabecera = true;
		$ancho = convertir_a_medida_tabla($ancho);
        echo "\n<table class='ei-base ei-cuadro-base' $ancho>\n";
        if($mostrar_cabecera){
            echo "<tr><td>";
            $this->barra_superior(null, true,"ei-cuadro-barra-sup");
            echo "</td></tr>\n";
        }
		//-- INICIO zona COLAPSABLE
		echo"<tr><td>\n";
		$colapsado = (isset($this->colapsado) && $this->colapsado) ? "style='display:none'" : "";		
        echo "<TABLE class='ei-cuadro-cuerpo' $colapsado id='cuerpo_{$this->objeto_js}'>";
		// Cabecera
		echo"<tr><td class='ei-cuadro-cabecera'>";
		$this->html_cabecera();		
		echo "</td></tr>\n";
		//--- INICIO CONTENIDO  -----
		echo "<tr><td class='ei-cuadro-cc-fondo'>\n";
		// Si el layout es cortes/tabular se genera una sola tabla, que empieza aca
		if($this->existen_cortes_control() && $this->cortes_modo == apex_cuadro_cc_tabular ){
			$this->html_cuadro_inicio();
		}
	}

	private function html_fin()
	{
		if($this->existen_cortes_control() && $this->cortes_modo == apex_cuadro_cc_tabular ){
			$this->html_cuadro_totales_columnas($this->acumulador);
			$this->html_cuadro_fin();					
		}
		echo "</td></tr>\n";
		//--- FIN CONTENIDO  ---------
		// Pie
		echo"<tr><td class='ei-cuadro-pie'>";
		$this->html_pie();		
		echo "</td></tr>\n";
		//Paginacion
		if ($this->info_cuadro["paginar"]) {
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
		if($this->info_cuadro["scroll"]){
			echo "</div>\n";
		}
	}

	protected function html_cabecera()
	{
        if(trim($this->info_cuadro["subtitulo"])<>""){
            echo $this->info_cuadro["subtitulo"];
        }
	}
	
	protected function html_pie()
	{
	}

	protected function html_mensaje_cuadro_vacio($texto){
		echo ei_mensaje($texto);
	}
	
	//-------------------------------------------------------------------------------
	//-- Generacion de los CORTES de CONTROL
	//-------------------------------------------------------------------------------

	private function html_cc_inicio_nivel()
	{
		if($this->cortes_modo == apex_cuadro_cc_anidado){
			echo "<ul>\n";
		}
	}

	private function html_cc_fin_nivel()
	{
		if($this->cortes_modo == apex_cuadro_cc_anidado){
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
	private function html_cabecera_corte_control(&$nodo)
	{
		//Dedusco el metodo que tengo que utilizar para generar el contenido
		$metodo = 'html_cabecera_cc_contenido';
		$metodo_redeclarado = $metodo . '__' . $nodo['corte'];
		if(method_exists($this, $metodo_redeclarado)){
			$metodo = $metodo_redeclarado;
		}		
		$nivel_css = $this->get_nivel_css($nodo['profundidad']);
		$class = "ei-cuadro-cc-tit-nivel-$nivel_css";
		if($this->cortes_modo == apex_cuadro_cc_tabular){
			echo "<tr><td  colspan='$this->cantidad_columnas_total' class='$class'>\n";
			$this->$metodo($nodo);
			echo "</td></tr>\n";
		}else{
			echo "<li class='$class'>\n";
			$this->$metodo($nodo);
		}
	}

	/**
		Genera el COTENIDO de la cabecera del corte de control
			Muestra las columnas seleccionadas como descripcion del corte separadas por comas
	*/
	protected function html_cabecera_cc_contenido(&$nodo)
	{
		$descripcion = $this->cortes_indice[$nodo['corte']]['descripcion'];
		$valor = implode(", ",$nodo['descripcion']);
		if (trim($descripcion) != '') {
			echo $descripcion . ': <strong>' . $valor . '</strong>';			
		} else {
			echo '<strong>' . $valor . '</strong>';
		}
	}

	/**
		Genera el PIE del corte de control
			Estaria bueno que esto consuma primitivas para:
				- no pisarse con el contenido anidado.
				- reutilizar en la regeneracion completa.
	*/
	private function html_pie_corte_control(&$nodo)
	{
		if($this->cortes_modo == apex_cuadro_cc_tabular){				//MODO TABULAR
			$nivel_css = $this->get_nivel_css($nodo['profundidad']);
			$css_pie = 'ei-cuadro-cc-pie-nivel-' . $nivel_css;
			$css_pie_cab = 'ei-cuadro-cc-pie-cab-nivel-'.$nivel_css;
			//-----  Cabecera del PIE --------
			if($this->cortes_indice[$nodo['corte']]['pie_mostrar_titular']){
				$metodo_redeclarado = 'html_pie_cc_cabecera__' . $nodo['corte'];
				if(method_exists($this, $metodo_redeclarado)){
					$descripcion = $this->$metodo_redeclarado($nodo);
				}else{
				 	$descripcion = $this->html_cabecera_pie_cc_contenido($nodo);
				}
				echo "<tr><td class='$css_pie' colspan='$this->cantidad_columnas_total'>\n";
				echo "<div class='$css_pie_cab'>$descripcion<div>";
				echo "</td></tr>\n";
			}
			//----- Totales de columna -------
			if (isset($nodo['acumulador'])) {
				$titulos = false;
				if($this->cortes_indice[$nodo['corte']]['pie_mostrar_titulos']){
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
					$desc = $this->sum_usuario[$id]['descripcion'];
					$datos[$desc] = $valor;
				}
				echo "<tr><td  class='$css_pie' colspan='$this->cantidad_columnas_total'>\n";
				$this->html_cuadro_sumarizacion($datos,null,300,$css);
				echo "</td></tr>\n";
			}
			//----- Contar Filas
			if($this->cortes_indice[$nodo['corte']]['pie_contar_filas']){
				echo "<tr><td  class='$css_pie' colspan='$this->cantidad_columnas_total'>\n";
				echo "<em>" . $this->etiqueta_cantidad_filas($nodo['profundidad']) . count($nodo['filas']) . "<em>";
				echo "</td></tr>\n";
			}
			//----- Contenido del usuario al final del PIE
			$metodo = 'html_pie_cc_contenido__' . $nodo['corte'];
			if(method_exists($this, $metodo)){
				echo "<tr><td  class='$css_pie' colspan='$this->cantidad_columnas_total'>\n";
				$this->$metodo($nodo);
				echo "</td></tr>\n";
			}
		}else{																//MODO ANIDADO
			echo "</li>\n";
		}
	}

	function etiqueta_cantidad_filas($profundidad)
	{
		return "Cantidad de filas: ";
	}
	
	/**
		Genera el CONTENIDO de la cabecera del PIE del corte de control
			Muestra las columnas seleccionadas como descripcion del corte separadas por comas
	*/
	protected function html_cabecera_pie_cc_contenido(&$nodo)
	{
		$descripcion = $this->cortes_indice[$nodo['corte']]['descripcion'];
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

	private function html_cuadro(&$filas, &$totales=null)
	{
		//Si existen cortes de control y el layout es tabular, el encabezado de la tabla ya se genero
		if(!($this->existen_cortes_control() && $this->cortes_modo == apex_cuadro_cc_tabular )){
			$this->html_cuadro_inicio();
		}
		$this->html_cuadro_cabecera_columnas();
        foreach($filas as $f)
        {
			$resaltado = "";
			$clave_fila = $this->obtener_clave_fila($f);
			if (is_array($this->clave_seleccionada)) 
				$clave_seleccionada = implode(apex_qs_separador, $this->clave_seleccionada);	
			else
				$clave_seleccionada = $this->clave_seleccionada;	
			
			$esta_seleccionada = ($clave_fila == $clave_seleccionada);
			$estilo_seleccion = ($esta_seleccionada) ? "ei-cuadro-fila-sel" : "";
            echo "<tr>\n";
 			//---> Creo las CELDAS de una FILA <----
            for ($a=0;$a< $this->cantidad_columnas;$a++)
            {
                //*** 1) Recupero el VALOR
				$valor = "";
                if(isset($this->info_cuadro_columna[$a]["clave"])){
					if(isset($this->datos[$f][$this->info_cuadro_columna[$a]["clave"]])){
						$valor = $this->datos[$f][$this->info_cuadro_columna[$a]["clave"]];
					}else{
						//ATENCION!! hay una columna que no esta disponible!
					}
	                //Hay que formatear?
	                if(isset($this->info_cuadro_columna[$a]["formateo"])){
	                    $funcion = "formato_" . $this->info_cuadro_columna[$a]["formateo"];
	                    //Formateo el valor
	                    $valor = $funcion($valor);
	                }
	            }
                //*** 2) Genero el HTML
                echo "<td class='".$this->info_cuadro_columna[$a]["estilo"]. $resaltado .' '.$estilo_seleccion."'>\n";
                echo $valor;
                echo "</td>\n";
                //Termino la CELDA
            }
 			//---> Creo los EVENTOS de la FILA <---
			if ( $this->tipo_salida != 'pdf' ) {
				foreach ($this->get_eventos_sobre_fila() as $id => $evento) {
					echo "<td class='ei-cuadro-col-tit' width='1%'>\n";
					//1: Posiciono al evento en la fila
					$evento->set_parametros($clave_fila);
					if($evento->posee_accion_vincular()){
						$evento->vinculo()->set_parametros($this->obtener_clave_fila_array($f));	
					}
					//2: Ventana de modificacion del evento por fila
					$callback_modificacion_eventos = 'conf_evt__' . $id;
					if(method_exists($this, $callback_modificacion_eventos)){
						$this->$callback_modificacion_eventos($evento, $f);
					}
					//3: Genero el boton
					if( $evento->esta_activado() ) {
						$evento->generar_boton($this->submit, $this->objeto_js);
					} else {
						$evento->activar();	//Lo activo para la proxima fila
					}
	            	echo "</td>\n";
				}
			}
			//--------------------------------------
            echo "</tr>\n";
        }
		if(isset($totales)){
			$this->html_cuadro_totales_columnas($totales);
		}
		//Si existen cortes de control y el layout es tabular, el encabezado de la tabla ya se genero
		if(!($this->existen_cortes_control() && $this->cortes_modo == apex_cuadro_cc_tabular )){
			$this->html_cuadro_fin();
		}
	}

	private function html_cuadro_inicio()
	{
		echo "<TABLE width='100%' class='tabla-0'>\n";
	}
	
	private function html_cuadro_fin()
	{
		echo "</TABLE>\n";
	}

	private function html_cuadro_cabecera_columnas()
	{
		//¿Alguna columna tiene título?
		$alguna_tiene_titulo = false;
        for ($a=0;$a<$this->cantidad_columnas;$a++) {		
        	if (trim($this->info_cuadro_columna[$a]["titulo"]) != '') {
        		$alguna_tiene_titulo = true;
        		break;
        	}
        }
        if ($alguna_tiene_titulo) {
	        echo "<tr>\n";
	        for ($a=0;$a<$this->cantidad_columnas;$a++)
	        {
	            if(isset($this->info_cuadro_columna[$a]["ancho"])){
	                $ancho = " width='". $this->info_cuadro_columna[$a]["ancho"] . "'";
	            }else{
	                $ancho = "";
	            }
	            $estilo_columna = $this->info_cuadro_columna[$a]["estilo_titulo"];
	            if(!$estilo_columna){
	            	$estilo_columna = 'ei-cuadro-col-tit';
	            }
	            echo "<td class='$estilo_columna' $ancho>\n";
	            $this->html_cuadro_cabecera_columna(    $this->info_cuadro_columna[$a]["titulo"],
	                                        $this->info_cuadro_columna[$a]["clave"],
	                                        $a );
	            echo "</td>\n";
	        }
	        //-- Eventos sobre fila
			if($this->cantidad_columnas_extra > 0){
				foreach ($this->get_eventos_sobre_fila() as $evento) {
					echo "<td class='ei-cuadro-col-tit'>&nbsp;";
					if (toba_editor::modo_prueba()) {
						echo toba_editor::get_vinculo_evento($this->id, $this->info['clase_editor_item'], $evento->get_id())."\n";
					}
					echo "</td>\n";
				}
			}
	        echo "</tr>\n";
        }
	}

	protected function html_cuadro_cabecera_columna($titulo,$columna,$indice)
    //Genera la cabecera de una columna
    {
        //--- ¿Es ordenable?
		if (isset($this->eventos['ordenar']) && $this->info_cuadro_columna[$indice]["no_ordenar"]!=1) {
			$sentido = array();
			$sentido[] = array('asc', 'Ordenar ascendente');
			$sentido[] = array('des', 'Ordenar descendente');
			echo "<span class='ei-cuadro-orden'>";			
			foreach($sentido as $sen){
			    $sel="";
			    if ($this->hay_ordenamiento() && ($columna==$this->orden_columna)&&($sen[0]==$this->orden_sentido)) 
					$sel = "_sel";//orden ACTIVO

				//Comunicación del evento
				$parametros = array('orden_sentido'=>$sen[0], 'orden_columna'=>$columna);
				$evento_js = eventos::a_javascript('ordenar', $this->eventos['ordenar'], $parametros);
				$js = "{$this->objeto_js}.set_evento($evento_js);";
			    $src = toba_recurso::imagen_apl("sentido_". $sen[0] . $sel . ".gif");
				echo toba_recurso::imagen($src, null, null, $sen[1], '', "onclick=\"$js\"", 'cursor: pointer; cursor:hand;');
			}
			echo "</span>";			
		}    	
		//--- Nombre de la columna
		if (trim($columna) != '' || trim($this->info_cuadro_columna[$indice]["vinculo_indice"])!="") {           
            echo $titulo;
        }	
		//---Editor de la columna
		$editor = '';
		if ( toba_editor::modo_prueba() && $this->tipo_salida != 'pdf' ){
			$item_editor = "/admin/objetos_toba/editores/ei_cuadro";
			$param_editor = array( apex_hilo_qs_zona => implode(apex_qs_separador,$this->id),
									'columna' => $columna );
			$editor = toba_editor::get_vinculo_subcomponente($item_editor, $param_editor);
		}	
		echo $editor;
    }

	function html_cuadro_totales_columnas($totales,$estilo=null,$agregar_titulos=false, $estilo_linea=null)
	{
		$clase_linea = isset($estilo_linea) ? "class='$estilo_linea'" : "";
		if($agregar_titulos){
			echo "<tr>\n";
			for ($a=0;$a<$this->cantidad_columnas;$a++){
				$clave = $this->info_cuadro_columna[$a]["clave"];
			    if(isset($totales[$clave])){
					$valor = $this->info_cuadro_columna[$a]["titulo"];
					echo "<td class='".$this->info_cuadro_columna[$a]["estilo_titulo"]."'><strong>$valor</strong></td>\n";
				}else{
					echo "<td $clase_linea>&nbsp;</td>\n";
				}
			}
	        //-- Eventos sobre fila
			if($this->cantidad_columnas_extra > 0){
				echo "<td colspan='$this->cantidad_columnas_extra'></td>\n";
			}		
			echo "</tr>\n";
		}
		echo "<tr>\n";
		for ($a=0;$a<$this->cantidad_columnas;$a++){
			$clave = $this->info_cuadro_columna[$a]["clave"];
			//Defino el valor de la columna
		    if(isset($totales[$clave])){
				$valor = $totales[$clave];
				if(!isset($estilo)){
					$estilo = $this->info_cuadro_columna[$a]["estilo"];
				}
				//La columna lleva un formateo?
				if(isset($this->info_cuadro_columna[$a]["formateo"])){
					$metodo = "formato_" . $this->info_cuadro_columna[$a]["formateo"];
					$valor = $metodo($valor);
				}
				echo "<td class='$estilo'><strong>$valor</strong></td>\n";
			}else{
				echo "<td $clase_linea>&nbsp;</td>\n";
			}
		}
        //-- Eventos sobre fila
		if($this->cantidad_columnas_extra > 0){
			echo "<td colspan='$this->cantidad_columnas_extra'></td>\n";
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
		if( isset($this->total_registros) && !($this->tamanio_pagina >= $this->total_registros) ) {
			//Calculo los posibles saltos
			//Primero y Anterior
			if($this->pagina_actual == 1) {
				$anterior = toba_recurso::imagen_apl("paginacion/anterior_deshabilitado.gif",true);
				$primero = toba_recurso::imagen_apl("paginacion/primero_deshabilitado.gif",true);       
			} else {
				$evento_js = eventos::a_javascript('cambiar_pagina', $this->eventos["cambiar_pagina"], $this->pagina_actual - 1);
				$js = "{$this->objeto_js}.set_evento($evento_js);";
				$img = toba_recurso::imagen_apl("paginacion/anterior.gif");
				$anterior = toba_recurso::imagen($img, null, null, 'Página Anterior', '', "onclick=\"$js\"", 'cursor: pointer;cursor:hand;');
			
				$evento_js = eventos::a_javascript('cambiar_pagina', $this->eventos["cambiar_pagina"], 1);
				$js = "{$this->objeto_js}.set_evento($evento_js);";
				$img = toba_recurso::imagen_apl("paginacion/primero.gif");
				$primero = toba_recurso::imagen($img, null, null, 'Página Inicial', '', "onclick=\"$js\"", 'cursor: pointer;cursor:hand;');
			}
			//Ultimo y Siguiente
			if( $this->pagina_actual == $this->cantidad_paginas ) {
				$siguiente = toba_recurso::imagen_apl("paginacion/siguiente_deshabilitado.gif",true);
				$ultimo = toba_recurso::imagen_apl("paginacion/ultimo_deshabilitado.gif",true);     
			} else {
				$evento_js = eventos::a_javascript('cambiar_pagina', $this->eventos["cambiar_pagina"], $this->pagina_actual + 1);
				$js = "{$this->objeto_js}.set_evento($evento_js);";
				$img = toba_recurso::imagen_apl("paginacion/siguiente.gif");
				$siguiente = toba_recurso::imagen($img, null, null, 'Página Siguiente', '', "onclick=\"$js\"", 'cursor: pointer;cursor:hand;');
				
				$evento_js = eventos::a_javascript('cambiar_pagina', $this->eventos["cambiar_pagina"], $this->cantidad_paginas);
				$js = "{$this->objeto_js}.set_evento($evento_js);";
				$img = toba_recurso::imagen_apl("paginacion/ultimo.gif");
				$ultimo = toba_recurso::imagen($img, null, null, 'Página Final', '', "onclick=\"$js\"", 'cursor: pointer;cursor:hand;');
			}
			echo "<div class='ei-cuadro-pag'>";
			echo "$primero $anterior Página <strong>{$this->pagina_actual}</strong> de ";
			echo "<strong>{$this->cantidad_paginas}</strong> $siguiente $ultimo";
			echo "</div>";
		}
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT --
	//-------------------------------------------------------------------------------

	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		echo $identado."window.{$this->objeto_js} = new ei_cuadro('{$this->objeto_js}', '{$this->submit}');\n";
	}


	public function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'componentes/ei_cuadro';
		return $consumo;
	}	

	//---------------------------------------------------------------
	//----------------------  SALIDA Impresion  ---------------------
	//---------------------------------------------------------------

	function vista_impresion_html( $salida )
	{
		$salida->subtitulo( $this->get_titulo() );
		$this->generar_salida("pdf");
	}

	private function pdf_inicio()
	{
		$ancho = isset($this->info_cuadro["ancho"]) ? $this->info_cuadro["ancho"] : "";
        echo "<TABLE width='$ancho' class='tabla-0'>";
		// Cabecera
		echo"<tr><td class='ei-cuadro-cabecera'>";
		$this->html_cabecera();		
		echo "</td></tr>\n";
		//--- INICIO CONTENIDO  -----
		echo "<tr><td class='ei-cuadro-cc-fondo'>\n";
		// Si el layout es cortes/tabular se genera una sola tabla, que empieza aca
		if($this->existen_cortes_control() && $this->cortes_modo == apex_cuadro_cc_tabular ){
			$this->html_cuadro_inicio();
		}
	}

	private function pdf_fin()
	{
		if($this->existen_cortes_control() && $this->cortes_modo == apex_cuadro_cc_tabular ){
			$this->html_cuadro_totales_columnas($this->acumulador);
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

	private function pdf_cuadro(&$filas, &$totales){
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