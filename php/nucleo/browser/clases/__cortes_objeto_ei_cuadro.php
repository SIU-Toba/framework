<?
require_once("nucleo/browser/interface/form.php");// Elementos STANDART de formulario
require_once("objeto_ei.php");

/*
	Falta controlar la estructura del ARREGLO con el cual se carga el cuadro
*/

class objeto_ei_cuadro extends objeto_ei
{
    var $cantidad_columnas;                 //protegido | int | Cantidad de columnas a mostrar
	var $filas;
    var $orden_columna;                     //protegido | int | Columna utilizada para realizar el orden
    var $orden_sentido;                     //protegido | string | Sentido del orden ('asc' / 'desc')
    var $datos;                             //protegido | array | Los datos que constituyen el contenido del cuadro
    var $columnas_clave;                    //protegido | 
 	var $submit;
	var $clave_seleccionada;
	var $id_en_padre;
 	var $indice_columnas;
	//Paginacion
	var $pagina_actual;
	var $tamanio_pagina;
	var $cantidad_paginas;
 
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
				$this->cortes_def[$id_corte]['col_id'] = $col_id;
				$col_desc = explode(',',$this->info_cuadro_cortes[$a]['columnas_descripcion']);
				$col_desc = array_map('trim',$col_desc);
				$this->cortes_def[$id_corte]['col_desc'] = $col_desc;
			}
		}
		//Procesamiento de columnas
		for($a=0;$a<count($this->info_cuadro_columna);$a++){
			// Indice de columnas
			$clave = $this->info_cuadro_columna[$a]['clave'];
			$this->indice_columnas[ $clave ] = $a;
			// Sumarizacion de columnas por corte
			if(isset($this->info_cuadro_columna[$a]['vinculo_indice'])){
				$cortes = explode(',',$this->info_cuadro_columna[$a]['vinculo_indice']);
				$cortes = array_map('trim',$cortes);
				foreach($cortes as $corte){
					$this->cortes_def[$corte]['acumulador_columna'][] = $clave;	
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
								c.total							as total
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
		}
		$this->filas = count($this->datos);
		return true;
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

	protected $cortes_def;
	protected $cortes_niveles;
	protected $cortes_estructura;
	protected $cortes_control;

	function existen_cortes_control()
	{
		return (count($this->info_cuadro_cortes)>0);
	}
	
	function planificar_cortes_control()
	{
		$this->crear_cortes_control();
		$this->disparar_sumarizaciones_usuario();
		ei_arbol($this->cortes_def);
		//ei_arbol($this->info_cuadro_cortes);
		//ei_arbol($this->cortes_estructura);
		//ei_arbol($this->cortes_control);

	}
	
	function crear_cortes_control()
	/*
		Creo la estructura, le pongo descripciones agrego totalizaciones basicas
	*/
	{
		//ei_arbol($this->cortes_def);
		$this->cortes_niveles = count($this->info_cuadro_cortes);
		foreach(array_keys($this->datos) as $dato){
			//Identifico la posicion de la fila la estructura de cortes
			$cortes_fila = array();
			foreach(array_keys($this->cortes_def) as $corte){
				//Armo el ID del corte
				$cortes_fila[$corte]='';
				$paso=false;
				foreach($this->cortes_def[$corte]['col_id'] as $id_corte){
					if($paso) $cortes_fila[$corte] .= '_|_';
					$cortes_fila[$corte] .= $this->datos[$dato][$id_corte];
					$paso = true;
				}

			/*
				//Completo los acumuladores del corte
				if(isset($this->cortes_def[$corte]['acumulador_columna'])){
					foreach($this->cortes_def[$corte]['acumulador_columna'] as $columna => $valor){
						$this->cortes_def[$corte]['acumulador_columna'][$columna] += $this->datos[$dato][$columna];
					}
				}
			}
			*/
			//Creo la estructura de cortes para esta fila
			$php = "\$this->cortes_estructura['" . implode("']['contenido']['",$cortes_fila) . "']['contenido'][]=$dato;";
			eval($php);//echo "$php<br>";



/*
			//Busco la descripcion
			$desc = array();
			for($a=0;$a<count($this->cortes_def);$a++){
				foreach($this->cortes_def[$a]['col_desc'] as $desc_corte){
					$desc[] = $desc_corte;
				}
			}
*/
			$this->cortes_control[$dato] = $cortes_fila;
		}
	}
	
	function disparar_sumarizaciones_usuario()
	{
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

	function set_titulo_columna($id_columna, $titulo)
	{
		$this->info_cuadro_columna[ $this->indice_columnas[$id_columna] ]["titulo"] = $titulo;
	}    

    function obtener_datos()
    {
        return $this->datos;    
    }	
	
//################################################################################
//#########################    INTERFACE GRAFICA   ###############################
//################################################################################

	protected $tipo_salida;

	function generar_salida($tipo)
	{
		if(($tipo!="html")&&($tipo!="pdf")){
			throw new excepcion_toba_def("El tipo de salida '$tipo' es invalida");	
		}
		$this->tipo_salida = $tipo;
		$this->generar_titulo();
		if($this->existen_cortes_control()){
			$this->generar_cortes_control();
		}else{
			$this->generar_cuadro();	
		}
		$this->generar_pie();
	}

	function generar_titulo()
	{
		$metodo = $this->tipo_salida . '_generar_titulo';
		$this->$metodo();
	}
	
	function generar_cuadro()
	{
		$metodo = $this->tipo_salida . '_generar_cuadro';
		$this->$metodo();
	}

	function generar_pie()
	{
		$metodo = $this->tipo_salida . '_generar_pie';
		$this->$metodo();
	}

	//-- Cortes de Control --

	function generar_cortes_control()
	{
		
	}

	function generar_titulo_nivel()
	{
		$metodo = $this->tipo_salida . '_generar_titulo';
		$this->$metodo();
	}
	
	function generar_pie_nivel()
	{
	}

//#################################    HTML    #####################################

	function html_titulo()
	{
	}
	
	function html_cuadro()
	{
	}
	
	function html_pie()
	{
	}
	
	function html_titulo_nivel()
	{
	}

	function html_pie_nivel()
	{
	}

//#################################    PDF    #####################################

	function pdf_titulo()
	{
	}
	
	function pdf_pie()
	{
	}

	function pdf_cuadro()
	{
	}

	function pdf_titulo_nivel()
	{
	}

	function pdf_pie_nivel()
	{
	}

//################################################################################

    function obtener_html($mostrar_cabecera=true, $titulo=null)
    //Genera el HTML del cuadro
    {
		//Campos de comunicación con JS
		echo form::hidden($this->submit, '');
		echo form::hidden($this->submit_seleccion, '');
		echo form::hidden($this->submit_orden_columna, '');
		echo form::hidden($this->submit_orden_sentido, '');
		echo form::hidden($this->submit_paginado, '');
		
		//Reproduccion del titulo
		if(isset($titulo)){
			$this->memoria["titulo"] = $titulo;
		}else{
			if(isset($this->memoria["titulo"])){
				$titulo = $this->memoria["titulo"];
			}
		}
		//Manejo del EOF
        if($this->filas == 0){
            //La consulta no devolvio datos!
            if ($this->info_cuadro["eof_invisible"]!=1){
                if(trim($this->info_cuadro["eof_customizado"])!=""){
                    echo ei_mensaje($this->info_cuadro["eof_customizado"]);
                }else{
                    echo ei_mensaje("La consulta no devolvio datos!");
                }
            }
			//De todas formas incluir los botones si hay
			if ($this->hay_botones()) {
				$this->obtener_botones();
			}			
        }else{
            if(!($ancho=$this->info_cuadro["ancho"])) $ancho = "80%";
			$colspan = $this->cantidad_columnas + $this->cant_eventos_sobre_fila();
				
            //echo "<br>\n";
            
			//--Scroll       
	        if($this->info_cuadro["scroll"]){
				$ancho = isset($this->info_cuadro["ancho"]) ? $this->info_cuadro["ancho"] : "500";
				$alto = isset($this->info_cuadro["alto"]) ? $this->info_cuadro["alto"] : "auto";
				echo "<div style='overflow: scroll; height: $alto; width: $ancho; border: 1px inset; padding: 0px;'>";
			//	echo "<table class='tabla-0'>\n";
			}else{
				$ancho = isset($this->info_cuadro["ancho"]) ? $this->info_cuadro["ancho"] : "100";
			//	echo "<table width='$ancho' class='tabla-0'>\n";
			}
            
            echo "<table class='objeto-base' width='$ancho'>\n\n\n";

            if($mostrar_cabecera){
                echo "<tr><td>";
                $this->barra_superior(null, true,"objeto-ei-barra-superior");
                echo "</td></tr>\n";
            }
            if($this->info_cuadro["subtitulo"]<>""){
                echo"<tr><td class='lista-subtitulo'>". $this->info_cuadro["subtitulo"] ."</td></tr>\n";
            }
            echo "<tr><td>";
            echo "<TABLE width='100%' class='tabla-0'  id='cuerpo_{$this->objeto_js}'>";
            //------------------------ Genero los titulos
            echo "<tr>\n";
            for ($a=0;$a<$this->cantidad_columnas;$a++)
            {
                if(isset($this->info_cuadro_columna[$a]["ancho"])){
                    $ancho = " width='". $this->info_cuadro_columna[$a]["ancho"] . "'";
                }else{
                    $ancho = "";
                }
                echo "<td class='lista-col-titulo' $ancho>\n";
                $this->cabecera_columna(    $this->info_cuadro_columna[$a]["titulo"],
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
			//-------------------------------------------------------------------------
            //----------------------- Genero VALORES del CUADRO -----------------------
			//-------------------------------------------------------------------------
            for ($f=0; $f< $this->filas; $f++)
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
            //----------------------- Genero totales??
			$this->generar_html_totales();			
            echo "</table>\n";
            echo "</td></tr>\n";
			echo "<td class='ei-base' colspan='$colspan'>\n";
			if ($this->info_cuadro["paginar"]) {
            	$this->generar_html_barra_paginacion();
			}
			if ($this->hay_botones()) {
				$this->obtener_botones();
			}
			echo "</td>\n";
            echo "</table>\n";
            
			//Y por cierto......... si esto tenia scroll, cierro el div !!!
			if($this->info_cuadro["scroll"]){
				echo "</div>";
			}
		            
            //echo "<br>\n";
        }
    }

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
	
	function generar_html_barra_paginacion()
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

    function cabecera_columna($titulo,$columna,$indice)
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
	//---- JAVASCRIPT ---------------------------------------------------------------
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
}
//-------------------------------------------------------------------------------
?>