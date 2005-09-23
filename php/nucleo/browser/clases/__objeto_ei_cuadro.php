<?
require_once("nucleo/browser/interface/form.php");// Elementos STANDART de formulario
require_once("objeto_ei.php");

/*
	Falta:
	
		- controlar la estructura del ARREGLO con el cual se carga el cuadro
		- totales generales por columna
		- Eventos con button
		- ventana de sumarizacion
		- ventana de filtrado de eventos por fila
		- modificar la composicion de la clave devuelta
		- como queda el tema vinculos?
*/

class objeto_ei_cuadro extends objeto_ei
{
 	protected $submit;
	protected $id_en_padre;
 	protected $indice_columnas;
    protected $cantidad_columnas;                 	// Cantidad de columnas a mostrar
	protected $filas = 0;
    protected $datos;                             	// Los datos que constituyen el contenido del cuadro
    protected $columnas_clave;                    	
	protected $clave_seleccionada;
	protected $acumulador;							// Acumulador de totales generales
	//Orden
    protected $orden_columna;                     	// Columna utilizada para realizar el orden
    protected $orden_sentido;                     	// Sentido del orden ('asc' / 'desc')
	//Paginacion
	protected $pagina_actual;
	protected $tamanio_pagina;
	protected $cantidad_paginas;
	//Cortes control
	protected $cortes_def;
	protected $cortes_control;
	//Salida
	protected $tipo_salida;
	//html
	protected $html_colspan;
 
    function __construct($id)
    {
        parent::__construct($id);
		$this->procesar_definicion();
        $this->submit = "ei_cuadro" . $this->id[1];
		$this->submit_orden_columna = $this->submit."__orden_columna";
		$this->submit_orden_sentido = $this->submit."__orden_sentido";
		$this->submit_seleccion = $this->submit."__seleccion";
		$this->submit_paginado = $this->submit."__pagina_actual";
		$this->objeto_js = "objeto_cuadro_{$id[1]}";
		$this->inicializar_manejo_clave();	
		if($this->existe_paginado())
			$this->inicializar_paginado();
	}

	function procesar_definicion()
	{
		$this->cantidad_columnas = count($this->info_cuadro_columna);		
		//Armo una estructura que describa las caracteristicas de los cortes
		if($this->existen_cortes_control()){
			for($a=0;$a<count($this->info_cuadro_cortes);$a++){
				$id_corte = $this->info_cuadro_cortes[$a]['identificador'];						// CAMBIAR !
				$col_id = explode(',',$this->info_cuadro_cortes[$a]['columnas_id']);
				$col_id = array_map('trim',$col_id);
				$this->cortes_def[$id_corte]['clave'] = $col_id;
				$col_desc = explode(',',$this->info_cuadro_cortes[$a]['columnas_descripcion']);
				$col_desc = array_map('trim',$col_desc);
				$this->cortes_def[$id_corte]['descripcion'] = $col_desc;
			}
		}
		//Procesamiento de columnas
		for($a=0;$a<count($this->info_cuadro_columna);$a++){
			// Indice de columnas
			$clave = $this->info_cuadro_columna[$a]['clave'];
			$this->indice_columnas[ $clave ] = $a;
			//Sumarizacion general
			if(isset($this->info_cuadro_columna[$a]['total'])){
				$this->acumulador[$clave]=0;
			}
			// Sumarizacion de columnas por corte
			if(isset($this->info_cuadro_columna[$a]['total_cc'])){
				$cortes = explode(',',$this->info_cuadro_columna[$a]['total_cc']);
				$cortes = array_map('trim',$cortes);
				foreach($cortes as $corte){
					$this->cortes_def[$corte]['total'][] = $clave;	
				}
			}
		}
	}

	function elemento_toba()
	{
		require_once('api/elemento_objeto_ei_cuadro.php');
		return new elemento_objeto_ei_cuadro();
	}	
	
	function destruir()
	{
		$this->memoria["eventos"] = array();
		if(isset($this->eventos)){
			foreach($this->eventos as $id => $evento ){
				$this->memoria["eventos"][$id] = true;
			}
		}
		$this->finalizar_seleccion();
		$this->finalizar_ordenamiento();
		$this->finalizar_paginado();
		parent::destruir();
	}

	function mantener_estado_sesion()								// ATENCION! esto no se esta invocando
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "tamanio_pagina";
		$propiedades[] = "cantidad_paginas";
		return $propiedades;
	}
	
	function obtener_definicion_db()
	{
		$sql = parent::obtener_definicion_db();
		//------------- Cuadro ----------------
		$sql["info_cuadro"]["sql"] = "SELECT	titulo as titulo,		
								c.subtitulo						as	subtitulo,		
								c.sql							as	sql,			
								c.columnas_clave				as	columnas_clave,		 
								c.archivos_callbacks			as	archivos_callbacks,		
								c.ancho							as	ancho,			
								c.ordenar						as	ordenar,			
								c.exportar						as	exportar_xls,		 
								c.exportar_rtf					as	exportar_pdf,		 
								c.paginar						as	paginar,			
								c.tamano_pagina					as	tamano_pagina,
								c.tipo_paginado					as	tipo_paginado,
								c.scroll						as	scroll,
								c.scroll_alto					as	alto,
								c.eof_invisible					as	eof_invisible,		 
								c.eof_customizado				as	eof_customizado,
								c.pdf_respetar_paginacion		as	pdf_respetar_paginacion,	
								c.pdf_propiedades				as	pdf_propiedades,
								c.asociacion_columnas			as	asociacion_columnas,
								c.dao_nucleo_proyecto			as  dao_nucleo_proyecto,	
								c.dao_nucleo					as  dao_clase,			
								c.dao_metodo					as  dao_metodo,
								c.dao_parametros				as  dao_parametros,
								n.archivo 						as	dao_archivo
					 FROM		apex_objeto_cuadro c
					 			LEFT OUTER JOIN	apex_nucleo n
					 			ON c.dao_nucleo_proyecto = n.proyecto
					 			AND c.dao_nucleo = n.nucleo
					 WHERE	objeto_cuadro_proyecto='".$this->id[0]."'	
					 AND		objeto_cuadro='".$this->id[1]."';";
		$sql["info_cuadro"]["estricto"]="1";
		$sql["info_cuadro"]["tipo"]="1";
		//------------ Columnas ----------------
		$sql["info_cuadro_columna"]["sql"] = "SELECT	c.orden	as orden,		
								c.titulo						as titulo,
								c.estilo_titulo					as estilo_titulo,		
								e.css							as estilo,	 
								c.ancho							as ancho,	 
								c.clave							as clave,		
								f.funcion						as formateo,	 
								c.vinculo_indice				as vinculo_indice,	
								c.no_ordenar					as no_ordenar,
								c.mostrar_xls					as mostrar_xls,
								c.mostrar_pdf					as mostrar_pdf,
								c.pdf_propiedades				as pdf_propiedades,
								c.total							as total,
								c.total_cc						as total_cc
					 FROM		apex_columna_estilo e,
								apex_objeto_ei_cuadro_columna	c
								LEFT OUTER JOIN apex_columna_formato f	
								ON	f.columna_formato	= c.formateo
					 WHERE	objeto_cuadro_proyecto = '".$this->id[0]."'
					 AND		objeto_cuadro = '".$this->id[1]."'
					 AND		c.estilo = e.columna_estilo	
					 AND		( c.desabilitado != '1' OR c.desabilitado IS NULL )
					 ORDER BY orden;";
		$sql["info_cuadro_columna"]["tipo"]="x";
		$sql["info_cuadro_columna"]["estricto"]="1";		
		//------------ Cortes de Control ----------------
		$sql["info_cuadro_cortes"]["sql"] = "SELECT	orden,		
													columnas_id,	    		
													columnas_descripcion,	
													identificador		,	
													pie_contar_filas	,	
													pie_mostrar_titulos	,	
													imp_paginar				
					 FROM		apex_objeto_cuadro_cc	
					 WHERE		objeto_cuadro_proyecto = '".$this->id[0]."'
					 AND		objeto_cuadro = '".$this->id[1]."'
					 ORDER BY orden;";
		$sql["info_cuadro_cortes"]["tipo"]="x";
		$sql["info_cuadro_cortes"]["estricto"]="0";		
		return $sql;
	}
  
//################################################################################
//############################        EVENTOS        #############################
//################################################################################

	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		if($this->info_cuadro["ordenar"]) { 
			$eventos += eventos::ordenar();		
		}
		if ($this->info_cuadro["paginar"]) {
			$eventos += eventos::cambiar_pagina();
		}
		return $eventos;
	}
	

	function disparar_eventos()
	{
		if(isset($_POST[$this->submit]) && $_POST[$this->submit]!="") {
			$evento = $_POST[$this->submit];		
			//El evento estaba entre los ofrecidos?
			if(isset($this->memoria['eventos'][$evento]) ) {
				switch ($evento) {
					case 'ordenar':
						$this->cargar_ordenamiento();
						$parametros = array('sentido'=> $this->orden_sentido, 'columna'=>$this->orden_columna);
						break;
					case 'cambiar_pagina':
						$this->cargar_cambio_pagina();
						$parametros = $this->pagina_actual;
						break;
					default:
						$this->cargar_seleccion();					
						$parametros = $this->clave_seleccionada;
				}
				$this->reportar_evento( $evento, $parametros );
			}
		}
	}

    function cargar_datos($datos=null,$memorizar=true)
    {
		// - 1 - Asigno DATOS
		if(isset($datos)){
	        $this->datos = $datos;
		}else{													//ATENCION: Esto tiene sentido?
			if(trim($this->info_cuadro['dao_metodo'])!=""){
				include_once($this->info_cuadro['dao_archivo']);
				$sentencia = "\$this->datos = " . $this->info_cuadro['dao_clase'] 
											. "::" .  $this->info_cuadro['dao_metodo']
											. "(".$this->info_cuadro['dao_parametros'].");";
				eval($sentencia);
			}
		}
		if(isset($this->datos))
		{
			$this->validar_estructura_datos();
			// - 2 - Paginacion
			if( $this->existe_paginado() ){
				$this->generar_paginado();
			}
			// - 3 - Ordenamiento
			if($this->hay_ordenamiento()){
				$this->ordenar();
			}
			// - 4 - Cortes de control
			if( $this->existen_cortes_control() ){
				$this->planificar_cortes_control();
			}else{
				$this->calcular_totales_generales();
			}
			//total del filas;
			$this->filas = count($this->datos);
		}		
		return true;
    }

//################################################################################
//############################   Procesos GENERALES   ############################
//################################################################################

	private function validar_estructura_datos()
	{
		/*
			Aca hay que controlar que la estructura de los datos
			pasados como parametros es correcta
		*/
	}

	private function datos_cargados()
	{
		return ($this->filas > 0);
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
        if(isset($this->info_cuadro["columnas_clave"])){
            $this->columnas_clave = explode(",",$this->info_cuadro["columnas_clave"]);
            $this->columnas_clave = array_map("trim",$this->columnas_clave);
        }else{
            $this->columnas_clave = null;
        }		
		$this->clave_seleccionada = null;
		if(!isset($this->columnas_clave)){
			$this->columnas_clave = array( apex_db_registros_clave );
		}
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
		if (isset($this->memoria['clave_seleccionada']))
			$this->clave_seleccionada = $this->memoria['clave_seleccionada'];
		if(isset($_POST[$this->submit_seleccion])) {
			$clave = $_POST[$this->submit_seleccion];
			if ($clave != '') {
				if(count($this->columnas_clave) > 1 )
				{
					//La clave es un array, devuelvo un array asociativo con el nombre de las claves
					$clave = explode(apex_qs_separador, $clave);
					for($a=0;$a<count($clave);$a++) {
						$this->clave_seleccionada[$this->columnas_clave[$a]] = $clave[$a];		
					}
				}else{
					$this->clave_seleccionada = $clave;			
				}
			}
		}	
	}

	function deseleccionar()
	{
		$this->clave_seleccionada = null;
	}

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
        foreach($this->columnas_clave as $clave){
            $id_fila .= $this->datos[$fila][$clave] . apex_qs_separador;
        }
        $id_fila = substr($id_fila,0,(strlen($id_fila)-(strlen(apex_qs_separador))));   
        return $id_fila;
    }

	function obtener_clave()
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
        $this->tamanio_pagina = isset($this->info_cuadro["tamano_pagina"]) ? $this->info_cuadro["tamano_pagina"] : 80;
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
			$this->total_registros = $this->reportar_evento("cant_reg", null);
			$this->cantidad_paginas = ceil($this->total_registros/$this->tamanio_pagina);
			if ($this->pagina_actual > $this->cantidad_paginas) 
				$this->pagina_actual = 1;
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

	function get_tamanio_pagina()
	{
		return $this->tamanio_pagina;
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

	function cargar_ordenamiento()
	{
		//Estado inicial
		unset($this->orden_columna);
		unset($this->orden_sentido);

		//¿Viene seteado de la memoria?
        if(isset($this->memoria['orden_columna']))
			$this->orden_columna = $this->memoria['orden_columna'];
		if(isset($this->memoria['orden_sentido']))
			$this->orden_sentido = $this->memoria['orden_sentido'];

		//¿Lo cargo el usuario?
		if (isset($_POST[$this->submit_orden_columna]) && $_POST[$this->submit_orden_columna] != '')
			$this->orden_columna = $_POST[$this->submit_orden_columna];
		if (isset($_POST[$this->submit_orden_sentido]) && $_POST[$this->submit_orden_sentido] != '')
			$this->orden_sentido = $_POST[$this->submit_orden_sentido];
	}

	function hay_ordenamiento()
	{
        return (isset($this->orden_sentido) && isset($this->orden_columna));
	}

    function ordenar()
    //Ordenamiento de array de dos dimensiones
    {
        //echo "ordenar: " . $this->orden_columna;
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

//################################################################################
//###############################    API basica    ###############################
//################################################################################

	public function set_titulo_columna($id_columna, $titulo)
	{
		$this->info_cuadro_columna[ $this->indice_columnas[$id_columna] ]["titulo"] = $titulo;
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
	
//################################################################################
//#####################    INTERFACE GRAFICA GENERICA  ###########################
//################################################################################

	private function generar_salida($tipo)
	{
		if(($tipo!="html")&&($tipo!="pdf")){
			throw new excepcion_toba_def("El tipo de salida '$tipo' es invalida");	
		}
		$this->tipo_salida = $tipo;
		$this->generar_inicio();
		if( $this->datos_cargados() ){
			//Generacion de contenido
			if($this->existen_cortes_control()){
				$this->generar_cortes_control();
			}else{
				$this->generar_cuadro( array_keys($this->datos) );
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
		$this->generar_fin();
	/*
		if( $this->existen_cortes_control() ){
			ei_arbol($this->cortes_def,"Mapa CORTES");
			ei_arbol($this->cortes_control,"Estructura CORTES");
		}
	*/
	}

	private function generar_inicio(){
		$metodo = $this->tipo_salida . '_inicio';
		$this->$metodo();
	}

	private function generar_cuadro(&$filas){
		$metodo = $this->tipo_salida . '_cuadro';
		$this->$metodo($filas);
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
		//Disparo las funciones de usuario para este corte
		//- FALTA!!!!!
		//Genero el corte
		$this->generar_cc_inicio_corte();
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
			$this->generar_cuadro( $nodo['filas'] );
		}
		$this->generar_pie_corte_control($nodo);
		$this->generar_cc_fin_corte();
	}

	private function generar_cabecera_corte_control(&$nodo){
		$metodo = $this->tipo_salida . '_cabecera_corte_control';
		$metodo_redeclarado = $metodo . '_' . $nodo['corte'];
		if(method_exists($this, $metodo_redeclarado)){
			$metodo = $metodo_redeclarado;
		}		
		$this->$metodo($nodo);
	}
	
	private function generar_pie_corte_control(&$nodo){
		$metodo = $this->tipo_salida . '_pie_corte_control';
		$metodo_redeclarado = $metodo . '_' . $nodo['corte'];
		if(method_exists($this, $metodo_redeclarado)){
			$metodo = $metodo_redeclarado;
		}
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

	private function generar_cc_inicio_corte(){
		$metodo = $this->tipo_salida . '_cc_inicio_corte';
		$this->$metodo();
	}

	private function generar_cc_fin_corte(){
		$metodo = $this->tipo_salida . '_cc_fin_corte';
		$this->$metodo();
	}

//################################################################################
//#################################    HTML    ###################################
//################################################################################

	public function obtener_html($mostrar_cabecera=true, $titulo=null)
	{
		/*	¿Los parametros hay que destruirlos?	*/
		$this->generar_salida("html");
	}

	private function html_inicio()
	{
		//Campos de comunicación con JS
		echo form::hidden($this->submit, '');
		echo form::hidden($this->submit_seleccion, '');
		echo form::hidden($this->submit_orden_columna, '');
		echo form::hidden($this->submit_orden_sentido, '');
		echo form::hidden($this->submit_paginado, '');
		//-- Scroll       
        if($this->info_cuadro["scroll"]){
			$ancho = isset($this->info_cuadro["ancho"]) ? $this->info_cuadro["ancho"] : "500";
			$alto = isset($this->info_cuadro["alto"]) ? $this->info_cuadro["alto"] : "auto";
			echo "<div style='overflow: scroll; height: $alto; width: $ancho; border: 1px inset; padding: 0px;'>\n";
		}else{
			$ancho = isset($this->info_cuadro["ancho"]) ? $this->info_cuadro["ancho"] : "100";
		}
		//-- Tabla BASE
		$mostrar_cabecera = true;
        echo "\n<table class='objeto-base' width='$ancho'>\n";
        if($mostrar_cabecera){
            echo "<tr><td>";
            $this->barra_superior(null, true,"objeto-ei-barra-superior");
            echo "</td></tr>\n";
        }
		$this->html_colspan = $this->cantidad_columnas + $this->cant_eventos_sobre_fila();
		//-- INICIO zona COLAPSABLE
		echo"<tr><td>\n";
        echo "<TABLE width='100%' class='tabla-0'  id='cuerpo_{$this->objeto_js}'>";
		// Cabecera
		echo"<tr><td class='lista-subtitulo'>";
		$this->html_cabecera();		
		echo "</td></tr>\n";
		//--- INICIO CONTENIDO  -----
		echo "<tr><td>\n";
	}

	private function html_fin()
	{
		echo "</td></tr>\n";
		//--- FIN CONTENIDO  ---------
		// Pie
		echo"<tr><td>";
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
			echo"<tr><td class='lista-subtitulo'>";
			$this->obtener_botones();
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
        if($this->info_cuadro["subtitulo"]<>""){
            echo $this->info_cuadro["subtitulo"];
        }
	}
	
	protected function html_pie()
	{
		if( $this->existen_cortes_control() ){
			if(isset($this->acumulador)){
				echo "<hr>";
				foreach($this->acumulador as $col => $total){
					echo "<p>$col: $total</p>\n";
				}
				echo "<hr>";
			}
		}
	}

	protected function html_mensaje_cuadro_vacio($texto){
		echo ei_mensaje($texto);
	}
	
	//-------------------------------------------------------------------------------
	//-- Generacion de los CORTES de CONTROL
	//-------------------------------------------------------------------------------

	protected function html_cabecera_corte_control(&$nodo)
	{
		$ancho = $nodo['profundidad'] + 3;
		echo "<h$ancho>". implode(", ",$nodo['descripcion']). "</h2>\n";
	}

	protected function html_pie_corte_control(&$nodo)
	{
		foreach($nodo['acumulador'] as $col => $total){
			echo "<p>$col: $total</p>\n";
		}
	}

	private function html_cc_inicio_nivel()
	{
		echo "<ul>\n";
	}

	private function html_cc_fin_nivel()
	{
		echo "</ul>\n";
	}

	private function html_cc_inicio_corte()
	{
		echo "<li>\n";
	}

	private function html_cc_fin_corte()
	{
		echo "</li>\n";
	}

	//-------------------------------------------------------------------------------
	//-- Generacion del CUADRO 
	//-------------------------------------------------------------------------------

	private function html_cuadro(&$filas)
	{
        echo "<TABLE width='100%' class='tabla-0'  id='cuerpo_{$this->objeto_js}'>";
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
			$estilo_seleccion = ($esta_seleccionada) ? "lista-seleccion" : "";
            echo "<tr>\n";
            for ($a=0;$a< $this->cantidad_columnas;$a++)
            {
                //----------> Comienzo una CELDA!!
                //*** 1) Recupero el VALOR
                if(isset($this->info_cuadro_columna[$a]["clave"])){
                    $valor = $this->datos[$f][$this->info_cuadro_columna[$a]["clave"]];
                    //Hay que formatear?
                    if(isset($this->info_cuadro_columna[$a]["formateo"])){
                        $funcion = "formato_" . $this->info_cuadro_columna[$a]["formateo"];
                        //Formateo el valor
                        $valor = $funcion($valor);
                    }
                }else{
                    $valor = "";
                }
                //*** 2) Generacion de VINCULOS!
                if(trim($this->info_cuadro_columna[$a]["vinculo_indice"])!=""){
                    $id_fila = $this->obtener_clave_fila($f);
                    //Genero el VINCULO
                    $vinculo = $this->solicitud->vinculador->obtener_vinculo_de_objeto( $this->id,
                                                                            $this->info_cuadro_columna[$a]["vinculo_indice"],
                                                                            $id_fila, true, $valor);
                    //El vinculador puede no devolver nada en dos casos: 
                    //No hay permisos o el indice no existe
                    if(isset($vinculo)){
                        $valor = $vinculo;
                    }
                }
                //*** 4) Genero el HTML
                echo "<td class='".$this->info_cuadro_columna[$a]["estilo"]. $resaltado .' '.$estilo_seleccion."'>\n";
                echo $valor;
                echo "</td>\n";
                //----------> Termino la CELDA!!
            }
            //-- Eventos aplicados a una fila
			foreach ($this->eventos as $id => $evento) {
				if ($evento['sobre_fila']) {
					echo "<td class='lista-col-titulo'>\n";
					$evento_js = eventos::a_javascript($id, $evento, $this->obtener_clave_fila($f));
					$js = "{$this->objeto_js}.set_evento($evento_js);";
					if (isset($evento['imagen_recurso_origen']))
						$img = recurso::imagen_de_origen($evento['imagen'], $evento['imagen_recurso_origen']);
					else
						$img = $evento['imagen'];
					echo recurso::imagen($img, null, null, $evento['ayuda'], '', "onclick=\"$js\"", 'cursor: pointer');
	            	echo "</td>\n";
				}
			}
			//----------------------------
            echo "</tr>\n";
        }
		echo "</TABLE>";
	}

	private function html_cuadro_cabecera_columnas()
	{
        echo "<tr>\n";
        for ($a=0;$a<$this->cantidad_columnas;$a++)
        {
            if(isset($this->info_cuadro_columna[$a]["ancho"])){
                $ancho = " width='". $this->info_cuadro_columna[$a]["ancho"] . "'";
            }else{
                $ancho = "";
            }
            echo "<td class='lista-col-titulo' $ancho>\n";
            $this->html_cuadro_cabecera_columna(    $this->info_cuadro_columna[$a]["titulo"],
                                        $this->info_cuadro_columna[$a]["clave"],
                                        $a );
            echo "</td>\n";
        }
        //-- Eventos sobre fila
		$cant_sobre_fila = $this->cant_eventos_sobre_fila();
		if($cant_sobre_fila > 0){
			echo "<td class='lista-col-titulo' colspan='$cant_sobre_fila'>\n";
            echo "</td>\n";
		}
        echo "</tr>\n";
	}

	protected function html_cuadro_cabecera_columna($titulo,$columna,$indice)
    //Genera la cabecera de una columna
    {
		//Editor de la columna
		$editor = "";
		if(apex_pa_acceso_directo_editor){
			$item_editor = "/admin/objetos_toba/editores/ei_cuadro";
			if ( $this->id[0] == toba::get_hilo()->obtener_proyecto() ) {
				$param_editor = array( apex_hilo_qs_zona => implode(apex_qs_separador,$this->id),
										'columna' => $columna );
				$editor = " ".toba::get_vinculador()->obtener_vinculo_a_item("toba",$item_editor, $param_editor, true);
			}
		}	
        //Solo son ordenables las columnas extraidas del recordse!!!
        //Las generadas de otra forma llegan con el nombre vacio
        if(trim($columna)!=""){
			if (isset($this->eventos['ordenar'])) {
				$sentido[0][0]="asc";
				$sentido[0][1]="Ordenar ascendente";
				$sentido[1][0]="des";
				$sentido[1][1]="Ordenar descendente";
				if($this->info_cuadro_columna[$indice]["no_ordenar"]!=1)
				{							
					echo  "<table class='tabla-0'>\n";
					echo  "<tr>\n";
	                echo  "<td width='95%' align='center' class='".$this->info_cuadro_columna[$indice]["estilo_titulo"]."'>&nbsp;" . $titulo ."&nbsp;</td>\n";
					echo  "<td width='5%'>";
					foreach($sentido as $sen){
					    $sel="";
					    if ($this->hay_ordenamiento() && ($columna==$this->orden_columna)&&($sen[0]==$this->orden_sentido)) 
							$sel = "_sel";//orden ACTIVO

						//Comunicación del evento
						$parametros = array('orden_sentido'=>$sen[0], 'orden_columna'=>$columna);
						$evento_js = eventos::a_javascript('ordenar', $this->eventos['ordenar'], $parametros);
						$js = "{$this->objeto_js}.set_evento($evento_js);";
					    $src = recurso::imagen_apl("sentido_". $sen[0] . $sel . ".gif");
						echo recurso::imagen($src, null, null, $sen[1], '', "onclick=\"$js\"", 'cursor: pointer');
					}
					echo  "</td>\n";        
					echo  "</tr>\n";
					echo  "</table>\n";
				}else{
				    echo $titulo;
				}				
            }else{
                echo $titulo;
            }
        }
        else            //Modificacion para que muestre los titulos de los vinculos
        {
            if(trim($this->info_cuadro_columna[$indice]["vinculo_indice"])!="") {           
                echo $titulo;
            }
        }
		echo $editor;
    }

	//-------------------------------------------------------------------------------
	//---- Paginacion
	//-------------------------------------------------------------------------------

	function html_barra_paginacion()
	//Barra para navegar la paginacion
	{
		if( isset($this->total_registros) && !($this->tamanio_pagina >= $this->total_registros) ) {
			//Calculo los posibles saltos
			//Primero y Anterior
			if($this->pagina_actual == 1) {
				$anterior = recurso::imagen_apl("paginacion/anterior_deshabilitado.gif",true);
				$primero = recurso::imagen_apl("paginacion/primero_deshabilitado.gif",true);       
			} else {
				$evento_js = eventos::a_javascript('cambiar_pagina', $this->eventos["cambiar_pagina"], $this->pagina_actual - 1);
				$js = "{$this->objeto_js}.set_evento($evento_js);";
				$img = recurso::imagen_apl("paginacion/anterior.gif");
				$anterior = recurso::imagen($img, null, null, 'Página Anterior', '', "onclick=\"$js\"", 'cursor: pointer');
			
				$evento_js = eventos::a_javascript('cambiar_pagina', $this->eventos["cambiar_pagina"], 1);
				$js = "{$this->objeto_js}.set_evento($evento_js);";
				$img = recurso::imagen_apl("paginacion/primero.gif");
				$primero = recurso::imagen($img, null, null, 'Página Inicial', '', "onclick=\"$js\"", 'cursor: pointer');
			}
			//Ultimo y Siguiente
			if( $this->pagina_actual == $this->cantidad_paginas ) {
				$siguiente = recurso::imagen_apl("paginacion/siguiente_deshabilitado.gif",true);
				$ultimo = recurso::imagen_apl("paginacion/ultimo_deshabilitado.gif",true);     
			} else {
				$evento_js = eventos::a_javascript('cambiar_pagina', $this->eventos["cambiar_pagina"], $this->pagina_actual + 1);
				$js = "{$this->objeto_js}.set_evento($evento_js);";
				$img = recurso::imagen_apl("paginacion/siguiente.gif");
				$siguiente = recurso::imagen($img, null, null, 'Página Siguiente', '', "onclick=\"$js\"", 'cursor: pointer');
				
				$evento_js = eventos::a_javascript('cambiar_pagina', $this->eventos["cambiar_pagina"], $this->cantidad_paginas);
				$js = "{$this->objeto_js}.set_evento($evento_js);";
				$img = recurso::imagen_apl("paginacion/ultimo.gif");
				$ultimo = recurso::imagen($img, null, null, 'Página Final', '', "onclick=\"$js\"", 'cursor: pointer');
			}
			//Creo la barra de paginacion
			if($this->info_cuadro["paginar"]) {
				echo "<table class='tabla-0'><tr>";
				echo "<td  class='lista-pag-bot'>&nbsp;</td>";
				echo "<td  class='lista-pag-bot'>$primero</td>";
				echo "<td  class='lista-pag-bot'>$anterior</td>";
				echo "<td  class='lista-pag-bot'>&nbsp;Página&nbsp;<b>{$this->pagina_actual}</b>&nbsp;de&nbsp;<b>{$this->cantidad_paginas}</b>&nbsp;</td>";
				echo "<td  class='lista-pag-bot'>$siguiente</td>";
				echo "<td class='lista-pag-bot' >$ultimo</td>";
				echo "<td  class='lista-pag-bot'>&nbsp;</td>";
				echo "<td  class='lista-pag-bot'>";
				echo "</td>";
				echo "</tr></table>";
				echo "</div>";              
			}
		}
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT --
	//-------------------------------------------------------------------------------

	protected function crear_objeto_js()
	{
		$identado = js::instancia()->identado();
		echo $identado."var {$this->objeto_js} = new objeto_ei_cuadro('{$this->objeto_js}', '{$this->submit}');\n";
	}


	public function consumo_javascript_global()
	{
		$consumo = parent::consumo_javascript_global();
		$consumo[] = 'clases/objeto_ei_cuadro';
		return $consumo;
	}	

//################################################################################
//################################    PDF    #####################################
//################################################################################

	private function pdf_inicio(){
		$this->pdf_cabecera();		
	}

	private function pdf_cuadro(){
	}

	private function pdf_fin(){
		$this->pdf_pie();
	}

	protected function pdf_cabecera(){
	}

	protected function pdf_pie(){
	}

	protected function pdf_mensaje_cuadro_vacio($texto){
	}

	//-- Cortes de Control --

	protected function pdf_cabecera_corte_control(){
	}

	protected function pdf_pie_corte_control(){
	}

	private function pdf_cc_inicio_nivel(){
	}

	private function pdf_cc_fin_nivel(){
	}

	private function pdf_cc_inicio_corte(){
	}

	private function pdf_cc_fin_corte(){
	}

//################################################################################

	function generar_html_totales()
	{
		//Selecciono registros a sumarizar
		$total = array();
		for ($a=0;$a<$this->cantidad_columnas;$a++){
		    if(isset($this->info_cuadro_columna[$a]["total"])){
				$total[$this->info_cuadro_columna[$a]["clave"]]=0;
				$pie_columna[$a] =& $total[$this->info_cuadro_columna[$a]["clave"]];
				$pie_columna_estilo[$a] = $this->info_cuadro_columna[$a]["estilo"];
				if(isset($this->info_cuadro_columna[$a]["formateo"])){
					$total_funcion[$this->info_cuadro_columna[$a]["clave"]] = $this->info_cuadro_columna[$a]["formateo"];
				}
		    }else{
		    	$pie_columna[$a] = "&nbsp;";
		    	$pie_columna_estilo[$a] = 'lista-col-titulo';
			}
		}		
		if(count($total)==0) return;
		//Sumarizo
		for ($f=0; $f< $this->filas; $f++){
			foreach(array_keys($total) as $columna){
				$total[$columna] +=  $this->datos[$f][$columna];
			}
		}
		//Aplico el formato de la columna a la sumarizacion
		if(isset($total_funcion) && is_array($total_funcion)){
			foreach(array_keys($total_funcion) as $tot){
				$funcion = "formato_" . $total_funcion[$tot];
				$total[$tot] = $funcion($total[$tot]);
			}		
		}
		//Genero el HTML
		echo "<tr>\n";
		for($a=0; $a<count($pie_columna);$a++){
			echo "<td class='".$pie_columna_estilo[$a]."'><strong>\n";
			echo $pie_columna[$a];
			echo "</strong></td>\n";
		}
        //-- Eventos sobre fila
		$cant_sobre_fila = $this->cant_eventos_sobre_fila();
		if($cant_sobre_fila > 0){
			echo "<td colspan='$cant_sobre_fila'>\n";
            echo "</td>\n";
		}		
		echo "</tr>\n";
	}
}
//-------------------------------------------------------------------------------
?>