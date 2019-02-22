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
	protected $datos;                             	// Los datos que constituyen el contenido del cuadro
	protected $_info_cuadro = array();
	protected $_info_cuadro_cortes;
	protected $_info_cuadro_columna = array();
	protected $_info_cuadro_columna_indices = array();
	protected $_info_sum_cuadro_cortes = array();
	protected $_prefijo = 'cuadro';

	protected $_columnas_clave;
	protected $_cantidad_columnas;                 	// Cantidad de columnas a mostrar
	protected $_cantidad_columnas_extra = 0;        	// Cantidad de columnas utilizadas para eventos
	protected $_cantidad_columnas_total;            	// Cantidad total de columnas

	protected $_clave_seleccionada;
	
	protected $_salida_sin_cortes;		//Variable que marcara si la salida en cuestion debe llevar cortes de control o no.
	protected $_estructura_datos = array();		// Estructura de datos esperados por el cuadro
 	protected $_columnas;
	protected $_acumulador;							// Acumulador de totales generales
	protected $_sum_usuario;
	protected $_acumulador_sum_usuario;				// Acumulador general de las sumarizaciones del usuario
	
	//Cortes control
	protected $_cortes_indice;
	protected $_cortes_def;
	protected $_cortes_niveles;
	protected $_cortes_modo;
	protected $_cortes_anidado_colap;
	protected $_cortes_control;
	protected $_mostrar_titulo_antes_cc = false;

	//Paginacion
	protected $_pagina_actual;
	protected $_tamanio_pagina;
	protected $_total_registros;
	protected $_cantidad_paginas;


	//Parametros para el submit
	protected $_submit_orden_sentido;
	protected $_submit_orden_columna;
	protected $_submit_paginado;
	protected $_submit_seleccion;
	protected $_submit_extra;
	protected $_submit_orden_multiple;

	//Eventos
	protected $_eventos_multiples = array();						//Mantiene los nombres de los eventos multiples (simil cache)
	protected $_datos_eventos_multiples = array();			 //Mantiene los datos de los evt multiples

	//Ordenamiento
	protected $_orden_columna;                     	// Columna utilizada para realizar el orden
	protected $_orden_sentido;                     	// Sentido del orden ('asc' / 'desc')
	protected $_columnas_orden_mul;			// Columnas para el ordenamiento multiple
	protected $_sentido_orden_mul;				//Sentido de las columnas para el ordenamiento multiple
	protected $_ordenado = false;
	protected $_ordenar_con_cortes = false;		//Indica si se contemplan los cortes de control en el ordenamiento

	protected $_tipo_salida;
	protected $_salida;
	protected $_manejador_tipo_salida = array();

	protected $_agrupacion_columnas = array();
	protected $_layout_cant_filas = null;
	protected $_excel_usar_formulas;
	protected static $_mostrar_excel_sin_cortes = false;    // Especifica si se tiene que dar la opción de renderizar como excel sin cortes
	protected $_clase_formateo = 'toba_formateo';
	
	protected $_etiqueta_cantidad_filas;

	//Modo de clave segura
	protected $_modo_clave_segura = true;				//Switchea a modo compatibilidad hacia atras
	private $_index_mapeo_clave_segura = 1;			//Indice que se devolvera al cliente
	protected $_mapeo_clave_segura = array();			//Arreglo que contendra el mapeo hasta que sea enviado a sesion

	final function __construct($id)
	{
		$this->set_propiedades_sesion(array('tamanio_pagina', '_eventos_multiples', '_modo_clave_segura'));		//Guardo en sesion aquello que me interesa
		parent::__construct($id);

		$this->analizar_cortes_excel();
		$this->procesar_definicion();			//Evaluar si no se puede retrasar hasta el inicializar
		$this->inicializar_manejo_clave();
		if($this->existe_paginado()) {
			$this->inicializar_paginado();
		}		
		if (isset($this->_memoria['ordenado'])) {
			$this->_ordenado = $this->_memoria['ordenado'];
		}
		if ($this->existen_cortes_control()) {
			$this->inspeccionar_sumarizaciones_usuario();
		}
	}


	function destruir()
	{
		if (isset($this->_ordenado)) {
			$this->_memoria['ordenado'] = $this->_ordenado;
		}
		$this->finalizar_seleccion();
		$this->finalizar_ordenamiento();
		$this->finalizar_paginado();
		$this->finalizar_ids_seguros();		
		parent::destruir();
	}

	/**
	* Método interno para iniciar el componente una vez construido
	* @ignore
	*/
	function inicializar($parametros=array())
	{
		parent::inicializar($parametros);
		$this->_submit_orden_columna = $this->_submit."__orden_columna";
		$this->_submit_orden_sentido = $this->_submit."__orden_sentido";
		$this->_submit_seleccion = $this->_submit."__seleccion";
		$this->_submit_extra = $this->_submit."__extra";
		$this->_submit_paginado = $this->_submit."__pagina_actual";
		$this->_submit_orden_multiple = $this->_submit . '__ordenamiento_multiple';			
		$this->inicializar_ids_seguros();	
	}
	
	/**
	 * @ignore
	 */
	function analizar_cortes_excel()
	{
		$this->_salida_sin_cortes = toba::memoria()->get_parametro('es_plano');
 		if ($this->_salida_sin_cortes) {
 			$this->aplanar_cortes_control();
 		}
	}
	
	/**
	 * Espacio donde el componente cierra su configuración
	 * @ignore
	 */
	function post_configurar()
	{
		parent::post_configurar();
		if (empty($this->_eventos_multiples)) {
			$this->_eventos_multiples = $this->get_ids_evento_aplicacion_multiple();
		}
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
			$limite = count($this->_info_cuadro_columna);				//Para evitar el recalculo en cada vuelta				
			for($a=0; $a < $limite; $a++) {
				if (in_array($this->_info_cuadro_columna[$a]['objeto_cuadro_col'], $no_visibles)) {
					$clave = $this->_info_cuadro_columna[$a]['clave'];
					array_splice($this->_info_cuadro_columna, $a, 1);		//Elimina el elemento y reorganiza indices
					$a--; $limite--;									//por eso vuelvo puntero atras y descuento 1 del maximo
					$alguno_eliminado = true;
					toba::logger()->debug("Restricción funcional. Se filtro la columna: $clave", 'toba');
				}
			}
			if ($alguno_eliminado) {
				$this->procesar_definicion_columnas();
			}
		}
		//----------------
	}

	/**
	 * @ignore
	 * @return array
	 */
	function get_nombres_parametros()
	{
		//Podria hacerse mediante reflexion pero seria menos performante aunque mas generico
		return array ('orden_columna' => $this->_submit_orden_columna,
								'orden_sentido' => $this->_submit_orden_sentido,
								'seleccion' => $this->_submit_seleccion,
								'extra' => $this->_submit_extra,
								'paginado' => $this->_submit_paginado,
								'orden_multiple'  => $this->_submit_orden_multiple,
								'submit' => $this->_submit);
	}
	//################################################################################
	//###########################      DEFINICION DEL CUADRO  ############################
	//################################################################################
	/**
	* @ignore
	*/
	protected function procesar_definicion()
	{
		//Procesamiento de columnas (No hay razon por la cual no hacerlo antes de los cortes)
		$this->procesar_definicion_columnas();

		//Armo una estructura que describa las caracteristicas de los cortes
		if($this->existen_cortes_control()){
			$this->procesar_definicion_cortes_control();
		}		
	}

	/**
	 * @ignore
	 */
	protected function procesar_definicion_cortes_control()
	{
		$estructura_datos = array();
		$cantidad_cortes = count($this->_info_cuadro_cortes);
		for($a=0; $a< $cantidad_cortes; $a++) {
			$id_corte = $this->_info_cuadro_cortes[$a]['identificador'];
			//Genero el Indice
			$this->_cortes_indice[$id_corte] =& $this->_info_cuadro_cortes[$a];

			//Separo las posibles columnas involucradas en la descripcion e id de corte de filas
			$col_id = explode(',',$this->_info_cuadro_cortes[$a]['columnas_id']);
			$col_id = array_map('trim', $col_id);
			$col_desc = explode(',',$this->_info_cuadro_cortes[$a]['columnas_descripcion']);
			$col_desc = array_map('trim',$col_desc);

			//Genero la tabla de definiciones
			$this->_cortes_def[$id_corte]['clave'] = $col_id;
			$this->_cortes_def[$id_corte]['descripcion'] = $col_desc;
			$this->_cortes_def[$id_corte]['colapsa'] = $this->_info_cuadro_cortes[$a]['modo_inicio_colapsado'];
			$this->_cortes_def[$id_corte]['habilitado'] = true;

			//Acumulo las columnas que necesito como datos para el cuadro.
			$estructura_datos = array_merge($estructura_datos, $col_desc, $col_id);
		}
		//Genero la estructura de columnas que necesito
		$this->_estructura_datos = array_unique($estructura_datos);

		//Recupero el modo del corte y si arranca colapsado
		$this->_cortes_modo = $this->_info_cuadro['cc_modo'];
		$this->_cortes_anidado_colap = $this->_info_cuadro['cc_modo_anidado_colap'];

		// Sumarizacion de columnas por corte
		foreach($this->_info_sum_cuadro_cortes as $suma) {
			if ($suma['total'] == '1') {
				$id_corte = $suma['identificador'];
				$this->_cortes_def[$id_corte]['total'][] = $suma['clave'];
			}
		}
	}

	/**
	 * @ignore
	 */
	protected function procesar_definicion_columnas()
	{
		$this->_columnas = array();
		$cantidad_columnas = count($this->_info_cuadro_columna);
		for($a=0; $a < $cantidad_columnas; $a++) {
			//Genero la estructura de las columnas
			$clave = $this->_info_cuadro_columna[$a]['clave'];
			$this->_columnas[ $clave ] =& $this->_info_cuadro_columna[$a];

			//Sumarizacion general
			if ($this->_columnas[ $clave ]['total'] == '1' && ! isset($this->_acumulador[$clave])) {
				$this->_acumulador[$clave]=0;
			}
			
			//Agrupacion de columnas
			$grupo = isset($this->_columnas[$clave]['grupo']) ? $this->_columnas[$clave]['grupo'] : null;
			if (! is_null($grupo) && $grupo != '') {
				$this->_agrupacion_columnas[$grupo][] = $clave;
			}

			// Indice de columnas
			$this->_info_cuadro_columna_indices[$clave] = $a;
		}
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
			$this->procesar_definicion_columnas();		//Se re ejecuta por eliminación para actualizar $this->_info_cuadro_columna_indices
		}
	}

	/**
	* Chequea si una columna existe en la definicion del cuadro.
	* @param $columna. Id de la columna.
	*/
	function existe_columna($columna)
	{
		$id = $this->_info_cuadro_columna_indices[$columna];
		return isset($this->_info_cuadro_columna[$id]) && ($this->_info_cuadro_columna[$id]['clave'] == $columna);
	}

	/**
	 * Chequea si un conjunto de columnas existen en la definicion del cuadro.
	 * @param array $columnas. Ids de las columnas.
	 */
	function existen_columnas($columnas)
	{
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
		$this->procesar_definicion_columnas(); //Se re ejecuta por eliminación para actualizar $this->_info_cuadro_columna_indices
	}


	/**
	 * Agrega nuevas definiciones de columnas al cuadro
	 * @param array $columnas componentes obligatoras: clave, titulo
	 */
	function agregar_columnas($columnas)
	{
		$this->agregar_columnas_perezoso($columnas);
		$this->procesar_definicion_columnas();  //Se re ejecuta por eliminación para actualizar $this->_info_cuadro_columna_indices
	}	

	/**
	 * @ignore
	 */
	private function agregar_columnas_perezoso($columnas, $columnas_al_inicio=false)
	{
		$arreglo_default = array('estilo' => 'col-tex-p1', 'estilo_titulo' => 'ei-cuadro-col-tit', 'total_cc' => '',
			'total' => '0', 'usar_vinculo' => '0', 'no_ordenar' => '0', 'formateo' => null);
		
		foreach ($columnas as $clave => $valor) {
			$columnas[$clave] = array_merge($arreglo_default, $valor);
		}
		if ($columnas_al_inicio) {
			$this->_info_cuadro_columna = array_merge(array_values($columnas), $this->_info_cuadro_columna);
		} else {
			$this->_info_cuadro_columna = array_merge($this->_info_cuadro_columna, array_values($columnas));
		}
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
	 * @ignore
	 */
	function get_columnas_agrupadas()
	{
		return $this->_agrupacion_columnas;
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
		if(isset($this->_info['subclase']) && $this->_info['subclase'] =! '') {
			$this->_sum_usuario = array();
			$clase = new ReflectionClass(get_class($this));
			foreach ($clase->getMethods() as $metodo) {
				$id = null;
				$nombre = $metodo->getName();
				if (substr($nombre, 0, 12) == 'sumarizar_cc') {		//** Sumarizacion Corte de control
					$temp = explode('__', $nombre);
					if(count($temp) != 3){
						throw new toba_error_def("La funcion de sumarizacion esta mal definida");
					}
					$id = $temp[2];
					$corte = $temp[1];
					if(!isset($this->_cortes_def[$corte])){	//El corte esta definido?
						throw new toba_error_def("La funcion de sumarizacion no esta direccionada a un CORTE existente");
					}
					//Agrego la sumarizacion al corte
					$this->_cortes_def[$corte]['sum_usuario'][]=$id;
				} elseif(substr($nombre, 0, 11) == 'sumarizar__') { 	//** Sumarizacion GENERAL
					$temp = explode('__', $nombre);
					$id = $temp[1];
					$corte = 'toba_total';
					$this->_acumulador_sum_usuario[$id] = 0;
				}

				if(! is_null($id)) {
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

	/**
	 * @ignore
	 */
	function es_cuadro_colapsado()
	{
		$colapsado = (isset($this->_colapsado) && $this->_colapsado) ? true: false;
		return $colapsado;
	}

	/**
	 * @ignore
	 */
	function get_informacion_basica_cuadro()
	{
		return $this->_info_cuadro;	
	}

	/**
	 * @ignore
	 * @return array
	 */
	function get_informacion_basica_componente()
	{
		return $this->_info;
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

	/**
	 * @ignore
	 * @return <type>
	 */
	function get_estructura_datos()
	{
		return $this->_estructura_datos;
	}

	/**
	 * Permite configurar una clase especifica para atender la generacion de un tipo de salida particular
	 * @param string $tipo_salida  Alguno de los tipos de salida estandar que genera el cuadro
	 * @param string $clase Nombre de clase que implementa dicha salida, la misma debe incluirse en el esquema de autoload
	 */
	function set_manejador_salida ($tipo_salida, $clase)
	{
		if (is_null($tipo_salida)){
			throw new toba_error_seguridad('Se debe indicar un tipo de salida válido');
		}
		if (is_null($clase)) {
			throw new toba_error_seguridad('Se debe indicar un nombre de clase válido para el tipo de salida seleccionado ');
		}
		$this->_manejador_tipo_salida[$tipo_salida] = $clase;
	}
	
	function desactivar_modo_clave_segura()
	{
		$this->_modo_clave_segura = false;
	}
	
	function usa_modo_seguro()
	{
		return $this->_modo_clave_segura;
	}

	/**
	 * Recupera de la sesion el mapeo original de las claves del cuadro
	 * @param integer $cuadro Id del componente
	 * @param integer $clave Id de la fila a recuperar
	 * @return mixed 
	 */
	static function recuperar_clave_fila($cuadro, $clave)
	{
		$indice = 'ids_seguros_'. $cuadro;
		$valores_seguros = toba::memoria()->get_dato($indice);
		if (isset($valores_seguros[$clave])) {
			return $valores_seguros[$clave];
		} else {
			return null;
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
				if(!isset($muestra[$columna]) && !is_null($muestra[$columna])){
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

	function get_descripcion_resp_popup($fila)
	{
		$resultado = '';
		$clave = $this->_info_cuadro['columna_descripcion'];
		if (! is_null($clave) && isset($this->datos[$fila][$clave])) {
			$resultado = $this->datos[$fila][$clave];
		}
		return $resultado;
	}

	/**
	 * Grafica el cuadro agrupando las filas en N-columnas
	 * @param integer $cant_filas
	 */
	function set_layout_cant_filas($cant_filas)
	{
		$this->_layout_cant_filas = $cant_filas;		//TODO: Ver si esto no va en el de salida
	}

	/**
	 * @ignore
	 */
	function get_layout_cant_columnas()
	{
		return $this->_layout_cant_filas;
	}

	/**
	 * @ignore
	 */
	function get_total_registros()
	{
		return $this->_total_registros;
	}

	function debe_mostrar_total_registros()
	{
		return ($this->_info_cuadro['mostrar_total_registros'] == '1');
	}

	//################################################################################
	//############################   CLAVE  y  SELECCION   ###########################
	//################################################################################

	/**
	* @ignore
	*/
	protected function inicializar_manejo_clave()
	{
		if (isset($this->_info_cuadro["clave_datos_tabla"]) && $this->_info_cuadro["clave_datos_tabla"] == '1') {			//Se usa Clave del DT
			$this->_columnas_clave = array( apex_datos_clave_fila );
		} elseif (trim($this->_info_cuadro["columnas_clave"]) != '') {
			$this->_columnas_clave = explode(",",$this->_info_cuadro["columnas_clave"]);		//Clave usuario
			$this->_columnas_clave = array_map("trim", $this->_columnas_clave);
		} else {
			$this->_columnas_clave = null;
		}
		//Agrego las columnas de la clave en la definicion de la estructura de datos
		if(is_array($this->_columnas_clave)) {
			$estructura_datos = array_merge($this->_columnas_clave, $this->_estructura_datos);
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
		//TODO:Pensar algo para eventos multiples
		if (! empty($this->_clave_seleccionada)) {
			$this->_memoria['clave_seleccionada'] = $this->_clave_seleccionada;
		} else {
			unset($this->_memoria['clave_seleccionada']);
		}
	}
	
	protected function inicializar_ids_seguros()
	{
		if ($this->usa_modo_seguro()) {
			$indice = 'ids_seguros_' . $this->_id[1];
			$this->_mapeo_clave_segura = toba::memoria()->get_dato($indice);
		}
	}
	
	protected function finalizar_ids_seguros()
	{
		if ($this->usa_modo_seguro()) {
			$indice = 'ids_seguros_' . $this->_id[1];
			toba::memoria()->set_dato($indice, $this->_mapeo_clave_segura);
		}
	}

	/**
	 * @ignore
	 */
	protected function cargar_seleccion()
	{
		//TODO: Hay que ver como selecciona el evento multiple
		$this->_clave_seleccionada = null;
		//La seleccion se inicializa con el del pedido anterior
		if (isset($this->_memoria['clave_seleccionada'])) {
			$this->_clave_seleccionada = $this->_memoria['clave_seleccionada'];
		}

		//La seleccion se actualiza cuando el cliente lo pide explicitamente
		if(isset($_POST[$this->_submit_seleccion])) {
			$clave = $_POST[$this->_submit_seleccion];
			if ($clave != ''  && ! in_array('seleccion', $this->_eventos_multiples)) {				//Si seleccion es multiple falla el chequeo contra la memoria
				$this->_clave_seleccionada = $this->validar_y_separar_clave($clave);
			}
		}
	}

	/**
	 * @ignore
	 */
	protected function cargar_eventos_multiples()
	{
		$this->_datos_eventos_multiples = array();
		foreach($this->_eventos_multiples as $nombre_evt) {
			$id_evt_post = $this->get_nombre_evento_multiple($nombre_evt);				//ID probable del hidden en HTML
			if (isset($_POST[$id_evt_post])) {
				$clv_multiple = explode(apex_qs_sep_interno, $_POST[$id_evt_post]);		//Trato de separar los varios registros
				foreach($clv_multiple as $clv_pair) {
					if ($clv_pair != '') {							//Si no vino vacio el hidden
						$this->_datos_eventos_multiples[$nombre_evt][] = $this->validar_y_separar_clave($clv_pair);
					}
				}
			}
		}
	}

	private function validar_y_separar_clave(&$klave)
	{
		if (! isset($this->_memoria['claves_enviadas']) || ! in_array($klave, $this->_memoria['claves_enviadas'])) {
			throw new toba_error_seguridad($this->get_txt()." La clave '$klave' del cuadro no estaba entre las enviadas");
		}		
		if ($this->_modo_clave_segura) {		
			$aux = (isset($this->_mapeo_clave_segura[$klave])) ? $this->_mapeo_clave_segura[$klave] : array();
		} else {
			$clave = explode(apex_qs_separador, $klave);
			//Devuelvo un array asociativo con el nombre de las claves
			$aux = array();
			for($a=0;$a<count($clave);$a++) {
				$aux[$this->_columnas_clave[$a]] = $clave[$a];
			}
		}
		return $aux;
	}

	/**
	 * Deja al cuadro sin selección alguna de fila
	 */
	function deseleccionar()
	{
		$this->_clave_seleccionada =  null;
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
		return (! empty($this->_clave_seleccionada));
	}


	/**
	* Retorna la clave serializada de una fila dada
	* @param integer $fila Numero de fila
	* @param boolean $forzar_claves_reales Obliga a devolver los valores reales de las claves, aun cuando se encuentre en modo seguro.
	* @return string Clave serializada
	*/
	function get_clave_fila($fila, $forzar_claves_reales = false)
	{
		$id_fila = "";
		if ($this->_modo_clave_segura && !$forzar_claves_reales) {
			$id_fila = $fila;
			$this->_mapeo_clave_segura[$id_fila] = $this->get_clave_fila_array($fila);			
		} else {
			if (isset($this->_columnas_clave)) {
				foreach($this->_columnas_clave as $clave) {
					$id_fila .= $this->datos[$fila][$clave] . apex_qs_separador;
				}
			}
			$id_fila = substr($id_fila,0,(strlen($id_fila)-(strlen(apex_qs_separador))));
		}
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

	/**
	 * Indica si la clave que se pasa por parametro es igual a la fila actualmente seleccionada.
	 * @param <type> $clave_fila
	 * @return <type>
	 */
	function es_clave_fila_seleccionada($clave_fila)
	{
		$resultado = false;
		if (! empty($this->_clave_seleccionada)) {
			if ($this->usa_modo_seguro()) {
				$fila = $this->_mapeo_clave_segura[$clave_fila];
				$conj_resultado = array_diff_assoc($this->_clave_seleccionada, $fila);			
				$resultado = (empty($conj_resultado));
			} else {
				$temp_claves = array();
				$temp_claves[] = implode(apex_qs_separador, $this->_clave_seleccionada);
				$resultado =  (in_array($clave_fila, $temp_claves));
			}
		}
		return $resultado;
	}

	/**
	 * @ignore
	 */
	function resetear_claves_enviadas()
	{
		unset($this->_memoria['claves_enviadas']);
		$this->_memoria['claves_enviadas'] = array();
	}

	/**
	 * @ignore
	 */
	function agregar_clave_enviada($clave)
	{
		$this->_memoria['claves_enviadas'][] = $clave;
	}
	
	/**
	 * @ignore
	 */
	function get_clave_enviada($clave) {		
		//var_dump($this->_memoria['claves_enviadas']);
	}
	
	//################################################################################
	//###########################    CORTES de CONTROL    ############################
	//################################################################################
	/**
	* Indica la existencia o no de cortes de control en el cuadro.
	* @return boolean
	*/
	function existen_cortes_control()
	{		
		$cortes_activos = 0;		
		if (is_null($this->_cortes_def)) {						//Si no hay cortes procesados aun, tomo los definidos en base
			$cortes_activos = count($this->_info_cuadro_cortes);
		} elseif (! empty($this->_cortes_def)) {					
			$datos_corte = reset($this->_cortes_def);			//Ciclo por los cortes que se procesaron en la definicion
			do {
				if ($datos_corte['habilitado'] == 1) {				//Si el corte esta activo dejo de buscar.
					$cortes_activos++;
				}				
			} while ($datos_corte = next($this->_cortes_def) && $cortes_activos == 0);		
		}
		return (($cortes_activos > 0) && !$this->_salida_sin_cortes);
	}

	/**
	 * Fuerza a que los cortes de control se inicien de manera colapsada. Por defecto true
	 * @param boolean $colapsado
	 */
	function set_cortes_colapsados($colapsado=true)
	{
		$this->_cortes_anidado_colap = $colapsado;
	}

	function agregar_corte_control($corte)
	{
		//Realizo controles sobre aquellos datos necesarios
		if (! isset($corte['identificador'])) {
			throw new toba_error_def('El corte de control requiere un identificador.');
		}
		if (! isset($corte['columnas_id'])) {
			throw new toba_error_def('El corte de control no posee la/s columna/s de corte');
		}
		if (! isset($corte['columnas_descripcion'])) {
			throw new toba_error_def('El corte de control no posee la/s columna/s para la descripción');
		}
		if (! isset($corte['descripcion'])) {
			$corte['descripcion'] = '';
		}

		$hay_que_totalizar = (isset($corte['total']) && $corte['total'] == '1');
		if ($hay_que_totalizar && (!isset($corte['columna']) || empty($corte['columna']))) {
			 throw new toba_error_def('No se especifico la columna por la que debe totalizar el corte');		 
		}

		//Armo el arreglo con los datos basicos del corte
		$db_corte = array('identificador' => $corte['identificador'], 'columnas_id' => $corte['columnas_id'], 'columnas_descripcion' => $corte['columnas_descripcion'], 'descripcion' => $corte['descripcion']);
		//Ahora agrego los opcionales
		$db_corte['pie_contar_filas'] = (isset($corte['pie_contar_filas'])) ? $corte['pie_contar_filas'] : 0;
		$db_corte['pie_mostrar_titulos'] = (isset($corte['pie_mostrar_titulos'])) ? $corte['pie_mostrar_titulos'] : 0;
		$db_corte['pie_mostrar_titular'] = (isset($corte['pie_mostrar_titular'])) ? $corte['pie_mostrar_titular'] : 0;
		$db_corte['imp_paginar'] = (isset($corte['imp_paginar'])) ? $corte['imp_paginar'] : 0;		
		$db_corte['modo_inicio_colapsado'] = (isset($corte['inicio_colapsado'])) ? $corte['inicio_colapsado'] : 0;
		$this->_info_cuadro_cortes[] = $db_corte;

		//Si se totaliza el corte x determinada columa entonces creo el arreglo
		if ($hay_que_totalizar) {
			//TEngo que hacer un ciclo por las columns
			$db_sumatoria_cc = array('identificador' => $corte['identificador'], 'total' => 1);
			foreach($corte['columna'] as $clave) {
				$db_sumatoria_cc['clave'] = $clave;
				$this->_info_sum_cuadro_cortes[] = $db_sumatoria_cc;
			}
		}
		//Regenero la estructura que define los cortes de control
		$this->procesar_definicion_cortes_control();
	}

	function eliminar_corte_control($corte)
	{
		if (! isset($this->_cortes_indice[$corte])) {
			toba::logger()->error("Se quiere eliminar el corte '$corte' y no existe");
			throw new toba_error_def(' Se desea eliminar un corte de control inexistente.');
		}

		unset($this->_cortes_indice[$corte]);
		unset($this->_cortes_def[$corte]);
	}

	function deshabilitar_corte_control($corte)
	{
		if (! isset($this->_cortes_indice[$corte])) {
			toba::logger()->error("Se quiere eliminar el corte '$corte' y no existe");
			throw new toba_error_def(' Se desea eliminar un corte de control inexistente.');
		}
		//Esto solo debe hacer que el corte no se grafique
		$this->_cortes_def[$corte]['habilitado'] = false;
	}

	function habilitar_corte_control($corte)
	{
		if (! isset($this->_cortes_indice[$corte])) {
			toba::logger()->error("Se quiere eliminar el corte '$corte' y no existe");
			throw new toba_error_def(' Se desea eliminar un corte de control inexistente.');
		}
		//Esto restaura el corte para su grafico.
		$this->_cortes_def[$corte]['habilitado'] = true;		
	}

	/**
 	 * Metodo para aplanar los cortes de control
 	 * @ignore
 	*/
 	protected function aplanar_cortes_control()
 	{
 		if (empty($this->_info_cuadro_cortes)) return;		// no hay nada que aplanar

 		$columnas = array();
 		foreach ($this->_info_cuadro_cortes as $cortes) {
 			$ids = explode(',', $cortes['columnas_id']);
			$agregar_id_columna = (count($ids) > 1);
 			foreach ($ids as $id) {				
 				$columna = array(
 					'clave'  => $id,
 					'titulo' => (! $agregar_id_columna) ? $cortes['descripcion'] : $cortes['descripcion'] . "( $id )",
 					'formateo' => 'forzar_cadena'
 				);
 				$columnas[] = $columna;
 			}
 		}
		$this->agregar_columnas_perezoso($columnas, true);
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
		//Busco los indices de datos y definicion de cortes
		$claves_datos = array_keys($this->datos);
		$claves_cortes = array_keys($this->_cortes_def);

		//Estructuras que contendran los cortes
		$this->_cortes_niveles = count($this->_info_cuadro_cortes);
		$this->_cortes_control = array();
		foreach($claves_datos as $dato)
		{
			//Punto de partida desde donde construir el arbol
			$ref =& $this->_cortes_control;
			$profundidad = 0;
			foreach($claves_cortes as $corte)
			{
				if (! $this->_cortes_def[$corte]['habilitado']) {		//Si el corte no va a graficarse ni siquiera lo creo.
						continue;
				}
				$clave_array = $this->armar_clave_corte($dato, $corte);
				$index = implode('_|_',$clave_array);
				
				//---------- Inicializacion del NODO ----------
				if(!isset($ref[$index])){
					$ref[$index]=array('corte' => $corte, 'profundidad' => $profundidad, 'clave' => $clave_array, 'hijos' => null);
					//Agrego la descripcion
					foreach($this->_cortes_def[$corte]['descripcion'] as $desc_corte){
						$ref[$index]['descripcion'][$desc_corte] = $this->datos[$dato][$desc_corte];
					}

					//Inicializo el ACUMULADOR de columnas
					if(isset($this->_cortes_def[$corte]['total'])){
						$ref[$index]['acumulador'] = array_fill_keys($this->_cortes_def[$corte]['total'], 0);		
					}
				}
				//---------- Fin inic. NODO ------------------

				
				//Agrego la fila actual a la lista de filas
				$ref[$index]['filas'][]=$dato;
				//Actualizo el acumulador
				if(isset($ref[$index]['acumulador'])) {
					foreach(array_keys($ref[$index]['acumulador']) as $columna){
						$ref[$index]['acumulador'][$columna] += $this->datos[$dato][$columna];
					}
				}
				//Cambio el punto de partida
				$ref =& $ref[$index]['hijos'];
				$profundidad++;
			}
			//Incremento el acumulador general
			$this->actualizar_acumulador_general($dato);
		}
	}

	/**
	 * Arma la clave de los cortes de control para la fila de datos.
	 * @ignore
	 * @param array $fila_dato
	 * @return string
	 */
	private function armar_clave_corte($fila_dato, $corte)
	{
		$clave_array=array();
		//-- Recupero la clave de la fila en el nivel
		foreach($this->_cortes_def[$corte]['clave'] as $id_corte) {
			$clave_array[$id_corte] = $this->datos[$fila_dato][$id_corte];
		}
		return $clave_array;
	}

	protected function actualizar_acumulador_general($dato)
	{
		if(isset($this->_acumulador)) {
			foreach(array_keys($this->_acumulador) as $columna){
				$this->_acumulador[$columna] += $this->datos[$dato][$columna];
			}
		}
	}

	/**
	 * @ignore
	 */
	function get_acumulador_general()
	{
		if (isset($this->_acumulador)) {
				return $this->_acumulador;
		}
	}

	function get_acumulador_usuario()
	{
		return $this->_sum_usuario;
	}

	/**
	 * @ignore
	 * Esto esta duplicado en el calculo de cortes de control por optimizacion
	 */
	protected function calcular_totales_generales()
	{
		foreach(array_keys($this->datos) as $dato) {
			//Incremento el acumulador general
			$this->actualizar_acumulador_general($dato);
		}
	}

	/**
	 * @ignore
	 */
	function calcular_totales_sumarizacion_usuario()
	{
		$sumarizacion = array();		
		$acumulador_usuario = $this->get_acumulador_usuario();
		if (isset($acumulador_usuario)) {
			foreach($acumulador_usuario as $sum) {
				if($sum['corte'] == 'toba_total') {
					$metodo = $sum['metodo'];
					$sumarizacion[$sum['descripcion']] = $this->$metodo($this->datos);
				}
			}
		}
		return $sumarizacion;		
	}
	
	/**
	 * @ignore
	 */
	function debe_mostrar_titulos_columnas_cc()
	{
		return $this->_mostrar_titulo_antes_cc;
	}

	function get_cortes_modo()
	{
		return $this->_cortes_modo;
	}

	function get_indice_cortes()
	{
		return $this->_cortes_indice;
	}

	function get_cortes_control()
	{
		return $this->_cortes_control;
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
	 * Fuerza al cuadro a mostrar una página específica
	 * @param integer $pag
	 */
	function set_pagina_actual($pag)
	{
		$this->_pagina_actual = $pag;
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
	 * Retorna el tamaño de página actual en el paginado (si está presente el paginado)
	 * @return integer
	 */
	function get_tamanio_pagina()
	{
		return $this->_tamanio_pagina;
	}

	/**
	 * Devuelve el tipo de paginado que esta usando el cuadro
	 */
	function get_tipo_paginado()
	{
			if (isset($this->_info_cuadro['tipo_paginado'])) {
				return $this->_info_cuadro['tipo_paginado'];
			}
	}

	/**
	 * Devuelve la cantidad de paginas que posee el cuadro de acuerdo a la los datos y el tamaño de pagina
	 * @return integer
	 */
	function get_cantidad_paginas()
	{
		return $this->_cantidad_paginas;
	}

	/**
	 * @ignore
	 */
	protected function cargar_cambio_pagina()
	{
		if(isset($_POST[$this->_submit_paginado])
				&& trim($_POST[$this->_submit_paginado]) != ''
				&& is_numeric($_POST[$this->_submit_paginado]))
			$this->_pagina_actual = $_POST[$this->_submit_paginado];
	}

	/**
	 * Pagina los datos actuales del cuadro
	 * Restringe los datos a la pagina actual y calcula la cantidad de paginas posibles
	 * @ignore
	 */
	protected function generar_paginado()
	{
		$this->_cantidad_paginas = 1;		//Inicializo la cantidad de paginas.

		switch ($this->_info_cuadro['tipo_paginado'])
		{
			case 'C':		//Paginado a cargo del CI
				if (!isset($this->_total_registros) || ! is_numeric($this->_total_registros)) {
						throw new toba_error_def("El cuadro necesita recibir la cantidad total de registros con el metodo set_total_registros para poder paginar");
				}
				$this->_cantidad_paginas = ceil($this->_total_registros/$this->_tamanio_pagina);
				if ($this->_pagina_actual > $this->_cantidad_paginas)  {
					$this->_pagina_actual = 1;
				}
				break;
			case 'P':		//Paginado Propio
				if($this->_total_registros > 0) {
					// 2) Calculo la cantidad de paginas
					$this->_cantidad_paginas = ceil($this->_total_registros/$this->_tamanio_pagina);
					if ($this->_pagina_actual > $this->_cantidad_paginas) {
						$this->_pagina_actual = 1;
					}
					$offset = ($this->_pagina_actual - 1) * $this->_tamanio_pagina;
					$this->datos = array_slice($this->datos, $offset, $this->_tamanio_pagina);
				}
				break;
		}
	}
	
 
	//################################################################################
	//#################################    ORDEN    ##################################
	//################################################################################
		/**
	 * Define si los cortes de control seran considerados al ordenar los datos del cuadro
	 * @param boolean $usar
	 */
	function set_usar_ordenamiento_con_cortes($usar = true)
	{
		$this->_ordenar_con_cortes = $usar;
	}

	/**
	 * Actualiza el estado actual del ordenamiento en base a la memoria anterior y lo que dice el usuario a través del POST
	 * @ignore
	 */
	protected function refrescar_ordenamiento()
	{
		$this->refrescar_ordenamiento_simple();
		$this->refrescar_ordenamiento_multiple();
	}


	/**
	 * @ignore
	 */
	private function refrescar_ordenamiento_simple()
	{
		//¿Viene seteado de la memoria?
        if(isset($this->_memoria['orden_columna'])) {
			$this->_orden_columna = $this->_memoria['orden_columna'];
		}
		if(isset($this->_memoria['orden_sentido'])) {
			$this->_orden_sentido = $this->_memoria['orden_sentido'];
		}

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
				//Anulo el ordenamiento multiple
				unset($this->_columnas_orden_mul);
				unset($this->_sentido_orden_mul);
			}
		}
	}

	/**
	 * @ignore
	 */
	private function refrescar_ordenamiento_multiple()
	{
		if (isset($this->_memoria['sentido_orden_multiple'])) {
			$this->_sentido_orden_mul = $this->_memoria['sentido_orden_multiple'];
		}
		if (isset($this->_memoria['columnas_orden_mul'])) {
			$this->_columnas_orden_mul = $this->_memoria['columnas_orden_mul'];
		}
		if (isset($_POST[$this->_submit_orden_multiple]) && ($_POST[$this->_submit_orden_multiple] != '')) {
			$this->_columnas_orden_mul = array();
			$recuperado = explode(apex_qs_separador, $_POST[$this->_submit_orden_multiple]);
			foreach($recuperado as $valores) {		//Ciclo por los distintos pares (columna,sentido)
				if ($valores != '') {
					$par = explode(apex_qs_sep_interno, $valores);
					$clave = $par[0];
					$this->get_sentido_ordenamiento($par[1]);
					$this->_sentido_orden_mul[$clave] = $par[1];		//Sentido de ordenamiento
					$this->_columnas_orden_mul[] = $clave;
				}
			}
			//Anulo la otra forma de ordenamiento por si venia de esa.
			unset($this->_orden_columna);
			unset($this->_orden_sentido);
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

	function hay_ordenamiento_multiple()
	{
		return (isset($this->_columnas_orden_mul) && isset($this->_sentido_orden_mul));
	}

	/**
	 * Método estandar de ordenamiento de los datos, decide el metodo de ordenamiento en base
	 * al tipo de formateo de la columna, sino utiliza ordenamiento por default
	 */
    protected function ordenar()
	{
		if (! $this->_ordenado) {
			$parametros = array();
			$metodos = $sentidos = array();
			//Contemplo los cortes de control para el ordenamiento.
			if ($this->_ordenar_con_cortes	&& $this->existen_cortes_control()) {
					foreach($this->_cortes_def as $corte) {
							$col = current($corte['descripcion']);				//Recupero la columna por la cual ordenar
							$metodos[$col] = 'ordenamiento_default';
							$sentidos[$col] = 'asc';
					}
			}
			if ($this->hay_ordenamiento()) {		//Ordenamiento columna simple
				$metodos[$this->_orden_columna] = 'ordenamiento_' . $this->_columnas[$this->_orden_columna]['formateo'];
				$sentidos[$this->_orden_columna] = $this->_orden_sentido;
			} elseif ($this->hay_ordenamiento_multiple()) {
				foreach($this->_columnas_orden_mul as $col) {
					$metodos[$col] = $funcion_formateo = 'ordenamiento_' . $this->_columnas[$col]['formateo'];
					$sentidos[$col] = $this->_sentido_orden_mul[$col];
				}
			}
			foreach($metodos as $klave => $funcion) {
				$aux = array();
				if (method_exists($this, $funcion)) {
					toba::logger()->debug('Entro en el metodo de ordenamiento: ' . $funcion);
					$aux = $this->$funcion($klave);
				}else{
					toba::logger()->debug('No se encontro el metodo de ordenamiento: ' . $funcion);
					$aux = $this->ordenamiento_default($klave);
				}
				$aux['sentido'] = $this->get_sentido_ordenamiento($sentidos[$klave]);

				//Agrego los parametros para el multisort x referencia como es requerido.
				$parametros[] = &$aux['ordenamiento'];
				$parametros[] = &$aux['sentido'];
				$parametros[] = &$aux['tipo'];

				/* Hacking PHP  5.3.x:
				 * Se guarda el contenido de la variable $aux para que PHP en el momento
				 * de realizar el unset($aux) no me transforme las referencias asignadas a $parametros
				 * en valores concretos.
				 */
				$garbage[] = $aux;
				/*
				 * Se hace el unset de la variable para que se renueve la direccion de memoria
				 * al hacer la asignacion $aux = array(); sino usa siempre la misma y
				 * por tanto nunca ordena.
				 */
				unset($aux);
			}
			$parametros[] = &$this->datos;			//Agrego el arreglo de datos a ordenar x referencia
			//toba::logger()->debug('parametros de ordenamiento');
			//toba::logger()->var_dump($parametros);
			call_user_func_array( 'array_multisort', $parametros );
		} //IF
    }

		/**
	 * Método estandar de ordenamiento por hora
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @param string $columna Nombre de la columna
	 * @return mixed
	 *//*
	protected function ordenamiento_hora($columna)
	{
		return $this->ordenar_numeros($columna);
	}*/

	/**
	 * Método estandar de ordenamiento de fechas
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @param string $columna Nombre de la columna
	 * @return mixed
	 */
	protected function ordenamiento_fecha($columna)
	{
		return $this->ordenar_fechas($columna);
	}

	/**
	 * Método estandar de ordenamiento de timestamps (fecha, hora)
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @param string $columna Nombre de la columna
	 * @return mixed
	 */
	protected function ordenamiento_fecha_hora($columna)
	{
		return $this->ordenar_fechas($columna);
	}

	/**
	 * Método estandar de ordenamiento de monedas
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @param string $columna Nombre de la columna
	 * @return mixed
	 */
	protected function ordenamiento_moneda($columna)
	{
		return $this->ordenar_numeros($columna);
	}

	/**
	 * Método estandar de ordenamiento de numeros
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @param string $columna Nombre de la columna
	 * @return mixed
	 */
	protected function ordenamiento_millares($columna)
	{
		return $this->ordenar_numeros($columna);
	}

	/**
	 * Método estandar de ordenamiento de decimales
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @param string $columna Nombre de la columna
	 * @return mixed
	 */
	protected function ordenamiento_decimal($columna)
	{
		return $this->ordenar_numeros($columna);
	}

	/**
	 * Método estandar de ordenamiento de tiempo expresado en numeros
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @param string $columna Nombre de la columna
	 * @return mixed
	 * @see ordenamiento_fecha
	 */
	protected function ordenamiento_tiempo($columna)
	{
		return $this->ordenar_numeros($columna);
	}

	/**
	 * Método estandar de ordenamiento de porcentajes
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @param string $columna Nombre de la columna
	 * @return mixed
	 */
	protected function ordenamiento_porcentaje($columna)
	{
		return $this->ordenar_numeros($columna);
	}

	/**
	 * Método estandar de ordenamiento de superficie
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @param string $columna Nombre de la columna
	 * @return mixed
	 */
	protected function ordenamiento_superficie($columna)
	{
		return $this->ordenar_numeros($columna);
	}

	/**
	 * Método estandar de ordenamiento de caracteres en mayusculas
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @param string $columna Nombre de la columna
	 * @return mixed
	 */
	protected function ordenamiento_mayusculas($columna)
	{
		return $this->ordenar_caracteres($columna);
	}

	/**
	 * Método estandar de ordenamiento de caracteres
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @param string $columna Nombre de la columna
	 * @return mixed
	 */
	protected function ordenamiento_may_ind($columna)
	{
		return $this->ordenar_caracteres($columna);
	}

	/**
	 * Método estandar de ordenamiento de los datos, utilizando array_multisort
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @param string $columna Nombre de la columna
	 * @return mixed
	 */
	protected function ordenamiento_default($columna)
	{
		$ordenamiento = array();
		foreach ($this->datos as $fila){
			$ordenamiento[] = strtoupper($this->quita_acentos($fila[$columna]));
		}
		$resultado['ordenamiento'] = $ordenamiento;
		$resultado['tipo'] = SORT_REGULAR;
		return $resultado;
	}

	/**
	 * Método estandar de ordenamiento de los datos fecha, utilizando array_multisort
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @ignore
	 */
	protected function ordenar_fechas($columna)
	{
		$ordenamiento = array();
		foreach ($this->datos as $fila) {
			$ordenamiento[] = strtotime($fila[$columna])  ;
		}
		$resultado['ordenamiento'] = $ordenamiento;
		$resultado['tipo'] = SORT_NUMERIC;
		return $resultado;
	}

	/**
	 * Método estandar de ordenamiento de los datos fecha, utilizando array_multisort
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @ignore
	 */
	protected function ordenar_numeros($columna)
	{
		$ordenamiento = array();
		foreach ($this->datos as $fila){
			$ordenamiento[] = $fila[$columna];
		}
		$resultado['ordenamiento'] = $ordenamiento;
		$resultado['tipo'] = SORT_NUMERIC;
		return $resultado;
	}

	/**
	 * Método estandar de ordenamiento de los datos fecha, utilizando array_multisort
	 * Heredar en caso de querer cambiar el mecanismo de ordenamiento
	 * @ignore
	 */
	protected function ordenar_caracteres($columna)
	{
		$ordenamiento = array();
		foreach ($this->datos as $fila){
			$ordenamiento[] = strtoupper($this->quita_acentos($fila[$columna]));
		}
		$resultado['ordenamiento'] = $ordenamiento;
		$resultado['tipo'] = SORT_STRING;
		return $resultado;
	}

	private function get_sentido_ordenamiento($valor = 'asc')
	{
		if ($valor == 'asc') {
			$sentido = SORT_ASC;
		}elseif ($valor == 'des') {
			$sentido = SORT_DESC;
		}else{
			throw new toba_error_def('Sentido de ordenamiento inválido: '. $valor);
		}
		return $sentido;
	}

	function quita_acentos($cadena)
	{
		$tofind = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
		$replac = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
		return(strtr($cadena,$tofind,$replac));
	}

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
		//Ordenamiento multiple
		if (isset($this->_columnas_orden_mul)) {
			$this->_memoria['columnas_orden_mul'] = $this->_columnas_orden_mul;
		}else{
			unset($this->_memoria['columnas_orden_mul']);
		}
		if (isset($this->_sentido_orden_mul)) {
			$this->_memoria['sentido_orden_multiple'] = $this->_sentido_orden_mul;
		}else{
			unset($this->_memoria['sentido_orden_multiple']);
		}
	}

	/**
	 * @ignore
	 * @param <type> $columna
	 * @param <type> $sentido
	 * @return <type>
	 */
	function es_sentido_ordenamiento_seleccionado($columna, $sentido)
	{
		return ($this->hay_ordenamiento() && ($columna == $this->_orden_columna) && ($sentido == $this->_orden_sentido));
	}

	//################################################################################
	//############################        EVENTOS        #############################
	//################################################################################
		/**
	 * Retorna la lista de eventos que fueron definidos a nivel de fila
	 * @return array(id => toba_evento_usuario)
	 */
	function get_eventos_sobre_fila()
	{
		if(!isset($this->_eventos_usuario_utilizados_sobre_fila)) {
			$this->_eventos_usuario_utilizados_sobre_fila = array();
			foreach ($this->_eventos_usuario_utilizados as $id => $evento) {
				if ($evento->esta_sobre_fila() && !$this->es_asociacion_de_vinculo($evento->get_id())) {
					$this->_eventos_usuario_utilizados_sobre_fila[$id]=$evento;
				}
			}
		}
		return $this->_eventos_usuario_utilizados_sobre_fila;
	}

	function es_asociacion_de_vinculo($id_evento)
	{
		$es_asociacion = false;
		foreach($this->_columnas as $col) {
			$es_asociacion = $es_asociacion || (isset($col['evento_asociado']) && $col['evento_asociado'] == $id_evento);
		}
		return $es_asociacion;
	}

	/**
	* Retorna el primer evento del tipo seleccion multiple. Si no existe retorna null
	*/
	function get_ids_evento_aplicacion_multiple()
	{
		$ids = array();
		foreach ($this->_eventos_usuario_utilizados as $id => $evt) {
			if ($evt->es_seleccion_multiple()) {
				$ids[] = $id;
			}
		}
		return $ids;
	}

	/**
	 * @ignore
	 */
	protected function cargar_lista_eventos()
	{
		parent::cargar_lista_eventos();
		if($this->_info_cuadro["ordenar"]) {
			$this->_eventos['ordenar'] = array('maneja_datos' => true);
			$this->_eventos['ordenar_multiple'] = array('maneja_datos' => true);
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
					case 'ordenar_multiple':
						if (isset($this->_columnas_orden_mul)) {
							$parametros = array('columnas' => $this->_columnas_orden_mul, 'sentidos' => $this->_sentido_orden_mul);
							$exitoso = $this->reportar_evento('ordenar_multiple', $parametros);
							$this->_ordenado = ($exitoso !== apex_ei_evt_sin_rpta && $exitoso === false);
						}
						break;
					case 'cambiar_pagina':
						$this->cargar_cambio_pagina();
						$parametros = $this->_pagina_actual;
						$this->reportar_evento( $evento, $parametros );
						break;
					default:
						$this->cargar_seleccion();						//Intento cargar la seleccion comun
						$this->cargar_eventos_multiples();		//Cargo los multiples si hay
						$this->disparar_eventos_multiples();	//Disparo los multiples
						//Ahora disparo el evento que genero la interaccion,
						//pero primero verifico que no sea parte de los multiples
						if (! in_array($evento, $this->_eventos_multiples)) {
							$this->disparar_eventos_simples($evento);
						}
				}
			}
		}
		$this->borrar_memoria_eventos_atendidos();
	}

	function disparar_eventos_multiples()
	{
		foreach($this->_eventos_multiples as $evento) {
			$parametros = null;
			if (isset($this->_datos_eventos_multiples[$evento])) {
				$parametros = $this->_datos_eventos_multiples[$evento];
			}
			$this->reportar_evento($evento, $parametros);
		}
	}

	function disparar_eventos_simples($evento)
	{
		$parametros = null;
		if (! empty($this->_clave_seleccionada)) {
			$parametros = $this->get_clave_seleccionada();
		} else {
			if (isset($_POST[$this->_submit_extra])) {
				$parametros = $_POST[$this->_submit_extra];
			}
		}
		$this->reportar_evento( $evento, $parametros );
	}

	private function get_nombre_evento_multiple($evento)
	{
		return $this->_submit . '__' . $evento;
	}

	function get_nombres_eventos_multiples()
	{
		$evt_multiples = array();
		foreach($this->_eventos_multiples as $nombre_evt) {
			$evt_multiples[] = $this->get_nombre_evento_multiple($nombre_evt);				//ID probable del hidden en HTML
		}
		return $evt_multiples;
	}

	function hay_eventos_multiples()
	{
		return (! empty($this->_eventos_multiples));
	}

	function get_eventos_multiples()
	{
		return $this->_eventos_multiples;
	}

	function get_eventos()
	{
		return $this->_eventos;
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
			if($this->hay_ordenamiento() || $this->hay_ordenamiento_multiple()){
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
	//#####################    VISTAS DE EXPORTACION ###################################
	//################################################################################
	
	/**
	 * @ignore
	 */
	function vista_impresion_html( toba_impresion $salida )
	{
		$salida->subtitulo( $this->get_titulo() );
		$this->generar_salida("impresion_html");
	}

	/**
	 * @ignore
	 * @param toba_vista_pdf $salida
	 */
	function vista_pdf(toba_vista_pdf $salida )
	{
		//$this->salida = $salida;
		$titulo = $this->get_titulo();
		if ($titulo != '') {
			$salida->titulo($titulo);
		}
		if ($this->_info_cuadro["subtitulo"] != '') {
			$salida->subtitulo($this->_info_cuadro["subtitulo"]);
		}		
		$this->generar_salida("pdf", $salida);
	}

	/**
	 * @ignore
	 * @param toba_vista_excel $salida
	 */
	function vista_excel(toba_vista_excel $salida )
	{
		//$this->salida = $salida;
		$titulo = $this->get_titulo();
		$cant_columnas = count($this->_columnas);
		if ($titulo != '') {
			$salida->set_hoja_nombre($titulo);
			$salida->titulo($titulo, $cant_columnas);
		}
		if ($this->_info_cuadro["subtitulo"] != '') {
			$salida->titulo($this->_info_cuadro["subtitulo"], $cant_columnas);
		}
		$this->generar_salida("excel", $salida);
	}

	/**
	 * Genera el xml del componente
	 * @param boolean $inicial Si es el primer elemento llamado desde vista_xml
	 * @param string $xmlns Namespace para el componente
	 * @return string XML del componente
	 */
	function vista_xml($inicial=false, $xmlns=null)
	{
		if ($xmlns) {
			$this->xml_set_ns($xmlns);
		}
		$salida = '<'.$this->xml_ns.'tabla'.$this->xml_ns_url;
		$this->xml_set_titulo($this->get_titulo());
		$salida .= $this->xml_get_att_comunes();
		$salida .= '>';
		$salida .= $this->xml_get_elem_comunes();
		$this->generar_salida("xml", $salida);
		$salida = $this->_salida->get_resultado_generacion();
		$salida .= '</'.$this->xml_ns.'tabla>';
		return $salida;
	}

	//################################################################################
	//#####################    INTERFACE GRAFICA GENERICA  ###########################
	//################################################################################

	/**
	*  Dispara la generacion de la salida HTML del cuadro
	*/
	function generar_html()
	{
		$this->generar_salida("html");
	}

	/**
	 * Wrapper que genera los distintos tipos de salida necesario de acuerdo al parametro especificado
	 * @param string $tipo
	 */
	protected function generar_salida($tipo, $objeto_toba_salida = null)
	{
		if($tipo!="html" && $tipo!="impresion_html" && $tipo!="pdf" && $tipo!='excel' && $tipo!='xml'){
			throw new toba_error_seguridad("El tipo de salida '$tipo' es invalida");
		}
		$this->_tipo_salida = $tipo;
		$this->instanciar_manejador_tipo_salida($tipo);
				
		if (! is_null($objeto_toba_salida)) {		//Si se esta usando una clase particular de toba para la salida
			$this->_salida->set_instancia_toba_salida($objeto_toba_salida);
		}

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

	function instanciar_manejador_tipo_salida($tipo)
	{
		//Si existe seteo explicito de parte del usuario para el tipo de salida
		if (isset($this->_manejador_tipo_salida[$tipo])) {
			$clase =  $this->_manejador_tipo_salida[$tipo];
		} else {
			//Verifico que sea uno de los tipos estandar o disparo excepcion
			switch($tipo) {
				case 'html':
				case 'impresion_html':
				case 'pdf':
				case 'excel':
				case 'xml':
						$clase = 'toba_ei_cuadro_salida_' . $this->_tipo_salida;
						break;
				default:
						throw new toba_error_def('El tipo de salida solicitado carece de una clase que lo soporte');
			}
		}
		if (isset($clase)) {
				$this->_salida = new $clase($this);
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

	function get_cantidad_columnas_total()
	{
		return $this->_cantidad_columnas_total;
	}

	function get_cantidad_columnas()
	{
		return $this->_cantidad_columnas;
	}
	
	/**
	 * @ignore
	 */
	protected function generar_inicio()
	{
		$metodo = $this->_tipo_salida . '_inicio';
		if (isset($this->_salida)) {
			$this->_salida->$metodo();
		} else {
			$this->$metodo();
		}
	}

	/**
	 * @ignore
	 */
	protected function generar_cuadro(&$filas, &$totales=null, &$nodo=null)
	{
		$metodo = $this->_tipo_salida . '_cuadro';
		if (isset($this->_salida)) {
			$this->_salida->$metodo($filas, $totales, $nodo);
		} else {
			$this->$metodo($filas, $totales, $nodo);
		}
	}

	/**
	 * @ignore
	 */
	protected function generar_fin(){
		$metodo = $this->_tipo_salida . '_fin';
		if (isset($this->_salida)) {
			$this->_salida->$metodo();
		} else {
			$this->$metodo();
		}
	}

	/**
	 * @ignore
	 */
	protected function generar_mensaje_cuadro_vacio($texto){
		$metodo = $this->_tipo_salida . '_mensaje_cuadro_vacio';
		if (isset($this->_salida)) {
			$this->_salida->$metodo($texto);
		} else {
			$this->$metodo($texto);
		}
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
	 * Genera la llamada a la ventana para la cabecera del corte de acuerdo al tipo de salida.
	 * @ignore
	 */
	protected function generar_cabecera_corte_control(&$nodo, $id_unico = null)
	{
		$metodo = $this->_tipo_salida . '_cabecera_corte_control';
		if (isset($this->_salida)) {
			$this->_salida->$metodo($nodo, $id_unico);
		} else {
			$this->$metodo($nodo, $id_unico);
		}
	}

	/**
	 * Genera la llamada a la ventana para el pie del corte de acuerdo al tipo de salida.
	 * @ignore
	 */
	protected function generar_pie_corte_control(&$nodo, $es_ultimo)
	{
		$metodo = $this->_tipo_salida . '_pie_corte_control';
		if (isset($this->_salida)) {
			$this->_salida->$metodo($nodo, $es_ultimo);
		} else {
			$this->$metodo($nodo, $es_ultimo);
		}
	}

	/**
	 * Genera la llamada a la ventana para el inicio del corte de control de nivel X
	 * @ignore
	 */
	protected function generar_cc_inicio_nivel()
	{
		$metodo = $this->_tipo_salida . '_cc_inicio_nivel';
		if (isset($this->_salida)) {
			$this->_salida->$metodo();
		} else {
			$this->$metodo();
		}
	}

	/**
	 * Genera la llamada a la ventana para el inicio del corte de control de nivel X
	 * @ignore
	 */
	protected function generar_cc_fin_nivel(){
		$metodo = $this->_tipo_salida . '_cc_fin_nivel';
		if (isset($this->_salida)) {
			$this->_salida->$metodo();
		} else {
			$this->$metodo();
		}
	}

	protected function generar_inicio_zona_colapsable($id_unico, $estilo)
	{
		$metodo = $this->_tipo_salida . '_inicio_zona_colapsable';
		if ($this->debe_colapsar_cortes()) {
				if (isset($this->_salida)) {
					$this->_salida->$metodo($id_unico, $estilo);
				} else {
					$this->$metodo($id_unico, $estilo);
				}
		}
	}

	protected function generar_fin_zona_colapsable()
	{
		$metodo = $this->_tipo_salida . '_fin_zona_colapsable';
		if ($this->debe_colapsar_cortes()) {
				if (isset($this->_salida)) {
					$this->_salida->$metodo();
				} else {
					$this->$metodo();
				}
		}
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
		$colapsa_x_runtime = true;
		$colapsa_x_metadatos = ($this->_cortes_def[$nodo['corte']]['colapsa'] == '1');
		if (method_exists($this->controlador(), 'conf__cc_inicio_colapsado')) {
			$colapsa_x_runtime = $this->controlador()->conf__cc_inicio_colapsado($nodo['clave']);
		}

		if ($colapsa_x_metadatos && $colapsa_x_runtime) {       //El corte debe colapsarse al inicio.
			//Esto deberia solicitarselo a la clase de la salida correspondiente.
			
			$estilo = " style='display:none'";              //Si uso clase css javascript despues no me da bola
		}
		return $estilo;
	}	

	function debe_colapsar_cortes()
	{
		return $this->_cortes_anidado_colap;
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
		$estilo = $this->get_estilo_inicio_colapsado($nodo);
		$this->generar_inicio_zona_colapsable($id_unico, $estilo);

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
		$this->generar_fin_zona_colapsable();
		$this->generar_pie_corte_control($nodo, $es_ultimo);
	}

	//############################################################################################
	//																	FUNCIONES AUXILIARES
	//############################################################################################
	/**
	 * Esta función debe ser utilizada desde los archivos de customización
	 * para mostrar la vista de excel sin cortes de control
	 * @param boolean $valor
	 */
	 static function set_vista_excel_sin_cortes($valor)
	 {
	 	self::$_mostrar_excel_sin_cortes = $valor;
	}

	static function permite_exportacion_excel_plano()
	{
		return self::$_mostrar_excel_sin_cortes;
	}

	/**
	 * Obtiene las filas que estaran disponibles para ordenar.
	 * @return array $posicion_filas
	 */
	function get_filas_disponibles_selector()
	{
		$posicion_filas = array();
		foreach($this->_columnas as $col) {
			if ($col['no_ordenar'] != 1) {
				$posicion_filas[] = $col['clave'];							//Guardo las columnas que envio
			}
		}
		return $posicion_filas;
	}

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
	 * @ignore
	 */
	function get_instancia_clase_formateo($tipo)
	{
		return  new $this->_clase_formateo($tipo);
	}

	function set_etiqueta_cantidad_filas($etiqueta)
	{
		$this->_etiqueta_cantidad_filas = $etiqueta;
	}
	
	function get_etiqueta_cantidad_filas()
	{
		if (isset($this->_etiqueta_cantidad_filas)) {
			return $this->_etiqueta_cantidad_filas;
		} else {
			return null;
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
		$id = toba_js::arreglo($this->_id, false);

		//Si hay seleccion multiple, envia los ids de las filas
		$id_evt_multiple = $this->get_ids_evento_aplicacion_multiple();
		$hay_multiple = (! empty($id_evt_multiple));
		$id_evt_multiple = ', '. toba_js::arreglo($id_evt_multiple);
		$filas = ',[]';
		if ($hay_multiple) {
			$datos = (isset($this->datos) && is_array($this->datos)) ? $this->datos : array();
			$filas = ',' . toba_js::arreglo(array_keys($datos));
		}
		echo $identado."window.{$this->objeto_js} = new ei_cuadro($id, '{$this->objeto_js}', '{$this->_submit}'$filas $id_evt_multiple);\n";
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

}
?>