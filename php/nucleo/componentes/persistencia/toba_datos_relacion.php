<?php
require_once(toba_dir()."/php/3ros/Graph/Graph.php");	//Necesario para el calculo de orden topologico de las tablas

/**
 * Mantiene un conjunto relacionado de {@link toba_datos_tabla datos_tabla}, brindando servicios para cargar y sincronizar esta relación con algún medio de persistencia (general una BD relacional)
 * 
 * 	@package Componentes
 *  @subpackage Persistencia
 *  @todo En el dump_esquema incluir la posición actual de los cursores
 */
class toba_datos_relacion extends toba_componente 
{
	protected $_info_estructura;
	protected $_relaciones = array();		
	protected $_tablas_raiz;
	protected $_persistidor;
	protected $_cargado = false;
	protected $_relaciones_mapeos=array();			//Mapeo entre filas de las tablas
	protected $_relaciones_mapeos_eliminados=array();//Mapeo entre filas eliminadas en el hijo
	static protected $debug_pasadas;				//Mantiene la cantidad de pasadas para generar ids unicos en js
	protected $_info_columnas_asoc_rel;
	protected $_tablas_inactivas = array();

	/**
	 * @ignore
	 */
	final function __construct($id)
	{
		$propiedades[] = "_relaciones_mapeos_eliminados";
		$propiedades[] = "_relaciones_mapeos";
		$propiedades[] = "_cargado";
		$this->set_propiedades_sesion($propiedades);
		parent::__construct($id);
		$this->crear_tablas();
	}
	
	/**
	 * Método interno para iniciar el componente una vez construido
	 * @ignore
	 */
	function inicializar($parametros=array())
	{
		parent::inicializar($parametros);
		$this->crear_relaciones();
		if ($this->_info_estructura['debug']) {
			$this->dump_esquema("INICIO: ".$this->_info['nombre']);	
		}
		foreach ($this->_dependencias as $dep) {
			$dep->inicializar($parametros);
		}
	}
	
	/**
	 * Ventana para agregar configuraciones particulares al inicio de la vida completa del componente
	 * @ventana
	 */
	function ini(){}
	
	/**
	 *  @ignore 
	 */
	function destruir()
	{
		//Esta clase es la encargada de guardarle los valores en sesion a cada relación
		//Se asume que las relaciones siempre se cargan en el mismo orden
		$this->_relaciones_mapeos = array();
		$this->_relaciones_mapeos_eliminados = array();
		foreach ($this->_relaciones as $relacion) {
			$this->_relaciones_mapeos[] = $relacion->get_mapeo_filas();
			$this->_relaciones_mapeos_eliminados[] = $relacion->get_mapeo_filas_eliminadas();
		}
		if ($this->_info_estructura['debug']) {
			$this->dump_esquema("FIN: ".$this->_info['nombre']);	
		}		
		parent::destruir();
	}

	/**
	 * Carga los datos_tabla y les pone los topes mínimos y máximos
	 */
	private function crear_tablas()
	{
		foreach( $this->_lista_dependencias as $dep){
			$this->cargar_dependencia($dep);
			//La cantidad minima y maxima se pasan a traves de dos parametros genericos del objeto
			$posicion = $this->_indice_dependencias[$dep];
			$cant_min = $this->_info_dependencias[$posicion]['parametros_a'];
			$cant_max = $this->_info_dependencias[$posicion]['parametros_b'];
			$this->_dependencias[$dep]->set_tope_min_filas($cant_min);
			$this->_dependencias[$dep]->set_tope_max_filas($cant_max);
			$this->_dependencias[$dep]->set_controlador($this, $dep);
		}
	}

	/**
	 * Para cada relación definida crea una toba_relacion_entre_tablas
	 * Determina cual es la tabla raiz
	 */
	private function crear_relaciones()
	{
		if(count($this->_info_relaciones)>0){
			for($a=0;$a<count($this->_info_relaciones);$a++)
			{
				$id_padre = $this->_info_relaciones[$a]['padre_id'];
				$id_hijo = $this->_info_relaciones[$a]['hijo_id'];
				$id_relacion = $id_padre.'-'.$id_hijo;
				$columnas_padre = $this->get_columnas_tabla_padre($this->_info_relaciones[$a]);
				$columnas_hijas = $this->get_columnas_tabla_hija($this->_info_relaciones[$a]);
				$this->_relaciones[$id_relacion] = new toba_relacion_entre_tablas(	$this->_dependencias[ $id_padre ],
																	$columnas_padre,
																	$id_padre,
																	$this->_dependencias[ $id_hijo ],
																	$columnas_hijas,
																	$id_hijo
																);
				$padres[] = $this->_info_relaciones[$a]['padre_id'];
				$hijos[] = $this->_info_relaciones[$a]['hijo_id'];
				
				//Se recuperan los mapeos anteriores, si es que hay
				if (isset($this->_relaciones_mapeos[$a])) {
					$this->_relaciones[$id_relacion]->set_mapeo_filas($this->_relaciones_mapeos[$a]);
				}
				if (isset($this->_relaciones_mapeos_eliminados[$a])) {
					$this->_relaciones[$id_relacion]->set_mapeo_filas_eliminadas($this->_relaciones_mapeos_eliminados[$a]);
				}
			}
			//Padres sin hijos
			$this->_tablas_raiz = array_diff( array_unique($padres), array_unique($hijos) );
		}else{
			//No hay relaciones
			$this->_relaciones = array();
			$this->_tablas_raiz = array_keys($this->_dependencias);
		}
	}

	/**
	 * Deshabilita la tabla o las tablas recibidas para la carga y la sincronización
	 */
	function desactivar_tablas($tablas)
	{
		if (is_array($tablas)) {
			$this->_tablas_inactivas = $tablas;
		} else {
			$this->_tablas_inactivas = array($tablas);
		}
	}

	//-------------------------------------------------------------------------------
	//-- DEBUG  ---------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	
	/**
	 * Muestra un dump de los datos y los cambios realizados a los mismos desde la carga
	 */
	function dump_contenido($etiqueta=null)
	{
		$etiqueta = isset($etiqueta) ? $etiqueta : 'DATOS_RELACION: ' . $this->_info['nombre'];
		foreach($this->_dependencias as $id => $dependencia){
			$info[$id]['cambios'] = $dependencia->get_cambios();
			$info[$id]['datos'] = $dependencia->get_conjunto_datos_interno();
		}
		ei_arbol( $info, $etiqueta, null, true);
	}
	
	/**
	 * Muestra un esquema de las tablas y los mapeos de las filas
	 * SOLO USAR PARA DEBUG! envia todos los datos al cliente en forma plana!
	 */
	function dump_esquema($titulo=null)
	{
		//Se mantiene la cantidad de pasadas en este pedido de pagina para generar variables js unicas
		if (isset(self::$debug_pasadas)) {
			self::$debug_pasadas++;	
		} else {
			self::$debug_pasadas = 1;	
		}
		$grafo = self::grafo_relaciones($this->_info_dependencias, $this->_info_relaciones);
		$diagrama = "digraph G {
						rankdir=LR;
						fontsize=8;
						node [fontsize=6, fillcolor=white,shape=box, style=rounded,style=filled, color=gray];
						";
		if (isset($titulo)) {
			$diagrama .= "label=\"$titulo\";\n";
		}
		foreach ($grafo->getNodes() as $nodo) {
			$datos = $nodo->getData();
			
			//Se determina la tabla
			$id_tabla = $datos['identificador'];
			$tabla = $this->_dependencias[$id_tabla];

			//Se incluye el javascript para poder dumpear los datos de la tabla
			$var_tabla = $id_tabla.self::$debug_pasadas;
			echo toba_js::abrir();
			echo "var $var_tabla = ".toba_js::arreglo($tabla->get_filas(null, true, false), true).";\n";
			echo toba_js::cerrar();
			
			//Se incluye la tabla como nodo
			$label = "$id_tabla (".count($tabla->get_id_filas(false)).")";
			$diagrama .=  "$id_tabla [label=\"$label\",".
							//Esta truchada es para arreglar otra ceguera del IE
							"URL=\"javascript: padre=(window.parent.var_dump)? window.parent : window; padre.var_dump(padre.$var_tabla)\"];\n";

			$diagrama .= $this->dump_esquema_relaciones($nodo);
		}
		$diagrama .= "}";
		$parametros = array('contenido' => $diagrama, 'formato' => 'svg', 'es_dirigido' => 1);
		$indice = uniqid();
		toba::memoria()->set_dato_instancia($indice, $parametros);
		$url = toba::vinculador()->get_url(toba_editor::get_id(), '1000045', array('esquema' => $indice), 
						array('validar' => false, 'celda_memoria' => 'debug'));
		toba_ei_esquema::generar_sentencia_incrustacion($url, 'svg', "100%", "200px");
	}
	
	/**
	 * @ignore 
	 */
	protected function dump_esquema_relaciones($nodo)
	{
		$datos = $nodo->getData();		
		$diagrama = '';
		foreach ($nodo->getNeighbours() as $nodo_vecino) {
			$datos_vecino = $nodo_vecino->getData();
			
			//Busco los toba_relacion_entre_tablas correspondientes
			$hijo_id = $datos_vecino['identificador'];
			$padre_id = $datos['identificador'];
			$relacion = $this->_relaciones[$padre_id."-".$hijo_id];
			$mapeo = $relacion->get_mapeo_filas();
			
			//Incluyo el mapeo en JS para poder dumpearlo
			$var_mapeo = $padre_id."_".$hijo_id.self::$debug_pasadas;
			echo toba_js::abrir();
			echo "var $var_mapeo = ".toba_js::arreglo($mapeo, true).";\n";
			echo toba_js::cerrar();
	
			//Calculo la cantidad de filas padres e hijas involucradas en la relación
			$cant_padres = 0;
			$cant_hijos = 0;
			$mapeo_hijos = array();
			foreach ($mapeo as $padre => $hijos) {
				if (count($hijos) > 0) {
					$cant_padres++;
				}
				$mapeo_hijos = array_merge($mapeo_hijos, $hijos);
			}
			$cant_hijos = count(array_unique($mapeo_hijos));
			
			//Incluyo la relación
			$diagrama .=  $padre_id . " -> " . $hijo_id . 
						" [fontsize=6,color=gray, label=\"$cant_padres - $cant_hijos\"".
						//Esta truchada es para arreglar otra ceguera del IE
						",URL=\"javascript: padre=(window.parent.var_dump)? window.parent : window;padre.var_dump(padre.$var_mapeo)\"];\n";
		}
		return $diagrama;
	}


	/**
	 * Retorna el orden hacia adelante en el cual se deben sincronizar las tablas
	 * El orden predeterminado es el orden topologico de las tablas
	 * @return array Arreglo id_tabla => toba_datos_tabla
	 */
	function orden_sincronizacion()
	{
		if ($this->_info_estructura['sinc_orden_automatico']) {
			//-- Se construye el orden topológico
			$sorter = new Structures_Graph_Manipulator_TopologicalSorter();
			$deps = $this->get_tablas_activas();
			$rel = $this->get_relaciones_activas();
			$grafo = self::grafo_relaciones($deps, $rel);
			$parciales = $sorter->sort($grafo);
			$ordenes = array();
			for ($i =0; $i<count($parciales) ; $i++) {
				for ($j=0; $j<count($parciales[$i]); $j++) {
					$ordenes[] = $parciales[$i][$j]->getData();
				}
			}
			$tablas = array();
			foreach ($ordenes as $orden) {
				$tablas[$orden['identificador']] = $this->_dependencias[$orden['identificador']];
			}
			return $tablas;
		} else {
			//-- Se toma el orden natural en el cual se definieron las tablas
			$ordenes = $this->get_tablas_activas();
			$tablas = array();
			foreach ($ordenes as $orden) {
				$tablas[$orden['identificador']] = $this->_dependencias[$orden['identificador']];
			}
			return $tablas;
		}
	}

	/**
	 * Retorna el orden hacia adelante en el cual se deben cargar las tablas
	 * Por defecto es el mismo que el orden de sincronización
	 * @return array Arreglo id_tabla => toba_datos_tabla
	 */
	function orden_carga()
	{
		return $this->orden_sincronizacion();
	}
	
	/**
	 * Retorna un grafo representando un conjunto de tablas y sus relaciones
	 * @return Structures_Graph
	 */
	static function grafo_relaciones($tablas, $relaciones)
	{
		$grafo = new Structures_Graph(true);
		// Se construyen los nodos
		$obj = array();
		$nodo = null;
		foreach ($tablas as $tabla) {
			unset($nodo);
			$nodo = new Structures_Graph_Node();
			$proveedor = isset($tabla['objeto_proveedor']) ? $tabla['objeto_proveedor'] : $tabla['objeto'];
			$obj[$proveedor] = $nodo;
			$nodo->setData($tabla);
			$grafo->addNode($obj[$proveedor]);
		}
		//Se agregan los arcos
		foreach ($relaciones as $asoc) {
			$padre = $asoc['padre_objeto'];
			$hijo = $asoc['hijo_objeto'];
			$obj[$padre]->connectTo($obj[$hijo]);
		}
		return $grafo;
	}


	//-------------------------------------------------------------------------------
	//-- Servicios basicos
	//-------------------------------------------------------------------------------

	/**
	 * Retorna los identificadores de los datos_tabla incluídos en la relación
	 * @return array
	 */
	function get_lista_tablas()
	{
		return array_keys($this->_dependencias);	
	}

	/**
	 * Retorna una referencia a una tabla perteneciente a la relación
	 * @param string $tabla Id. de la tabla en la relación
	 * @return toba_datos_tabla
	 */
	function tabla($tabla)
	{
		if($this->existe_tabla($tabla)){
			return $this->_dependencias[$tabla];
		}else{
			throw new toba_error_def("El datos_tabla '$tabla' solicitado no existe.");
		}
	}

	/**
	 * Retorna las tablas de una relación
	 * @return array de toba_datos_tabla
	 */
	function get_tablas()
	{
		return $this->_dependencias;
	}

	/**
	 * Retorna las tablas que están habilitadas para la carga y la sincronización
	 * @return array de toba_datos_tabla
	 */
	function get_tablas_activas()
	{
		if (empty($this->_tablas_inactivas)) {
			return $this->_info_dependencias;
		} else {
			$tablas_activas = array();
			foreach ($this->_info_dependencias as $indx => $dep) {
				if (! in_array($dep['identificador'], $this->_tablas_inactivas)) {
					$tablas_activas[] = $dep;
				}
			}
			return $tablas_activas;
		}
	}

		/**
	 * Retorna relaciones de las tablas que están habilitadas para la carga y la sincronización
	 * @return array de toba_datos_tabla
	 */
	function get_relaciones_activas()
	{
		if (empty($this->_tablas_inactivas)) {
			return $this->_info_relaciones;
		} else {
			$relaciones_activas = array();
			foreach ($this->_info_relaciones as $indx => $rel) {
				if (! in_array($rel['padre_id'], $this->_tablas_inactivas) && ! in_array($rel['hijo_id'], $this->_tablas_inactivas)) {
					$relaciones_activas[] = $rel;
				}
			}
			return $relaciones_activas;
		}
	}

	/**
	 * Determina si una tabla es parte de la relación
	 * @param string $tabla Id. de la tabla en la relación
	 * @return boolean
	 */
	function existe_tabla($tabla)
	{
		return $this->dependencia_cargada($tabla);
	}

	/**
	 * Retorna al estado inicial todas las tablas incluídas
	 * Para volver a utilizar estas tablas se debe cargar nuevamente la relación con datos
	 */
	function resetear()
	{
		foreach($this->_dependencias as $dependencia){
			$dependencia->resetear();
		}
		$this->_cargado = false;
	}
	
	/**
	 * Asegura que ningún cursor de alguna tabla se encuentre posicionado en ninguna fila específica
	 */	
	function resetear_cursores()
	{
		foreach($this->_dependencias as $dependencia){
			$dependencia->resetear_cursor();
		}
	}
	
	/**
	 * Ventana para validaciones específicas, se ejecuta justo antes de la sincronización
	 * @ventana
	 */
	protected function evt__validar(){}

	/**
	 *	Valida cada una de las tablas incluídas en la relación
	 */
	function disparar_validacion_tablas()
	{
		foreach($this->_dependencias as $dependencia){
			$dependencia->validar();
		}
	}

	/**
	 * Retorna la estructura de datos utilizada por las tablas para mantener registro del estado de sus datos
	 * @return array
	 */
	function get_conjunto_datos_interno()
	{
		foreach($this->_dependencias as $id => $dependencia){
			$datos[$id] = $dependencia->get_conjunto_datos_interno();
		}
		return $datos;		
	}
	
	//-------------------------------------------------------------------------------
	//-- PERSISTENCIA  -------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Retorna una referenca al Adm.Persistencia de la relación
	 * @return toba_ap_relacion_db
	 */
	function persistidor()
	{
		if (!isset($this->_persistidor)) {		
			//Se incluye el archivo
			$particular = ($this->_info_estructura['ap'] == 3);
			if ($particular	&& isset($this->_info_estructura['ap_archivo']) && isset($this->_info_estructura['ap_clase']) ) {
				$clase = $this->_info_estructura['ap_clase'];
				if( ! class_exists($clase) ) {
					$punto = toba::puntos_montaje()->get_por_id($this->_info_estructura['punto_montaje']);
					$path  = $punto->get_path_absoluto().'/'.$this->_info_estructura['ap_archivo'];
					require_once($path);
				}
			} else {
				$clase = "toba_ap_relacion_db";
			}
			$this->_persistidor = new $clase( $this );
			if ($this->_info_estructura['sinc_susp_constraints']) {
				$this->_persistidor->retrasar_constraints();
			}
			$this->_persistidor->set_lock_optimista($this->_info_estructura['sinc_lock_optimista']);
		}
		return $this->_persistidor;
	}
	
	/**
	 * @deprecated usar persistidor() a secas
	 */
	function get_persistidor()
	{
		return $this->persistidor();
	}
	
	/**
	 * Utiliza la carga por clave del administrador de persistencia
	 * Carga la tabla raiz de la relación y a partir de allí ramifica la carga a sus relaciones
	 * @param array $clave Arreglo asociativo campo-valor por el cual filtrar la relación, si no se explicita se cargan todos los datos disponibles
	 * @return boolean Falso, si no se encontraron registros
	 */
	function cargar($clave=array())
	{
		//ATENCION: hay que controlar el formato de la clave
		$this->log('***   Inicio CARGAR ****************************');
		$ap = $this->persistidor();
		if($ap->cargar_por_clave($clave) === true){
			$this->log("***   Fin CARGAR (OK) *************************");
			return true;
		}else{
			$this->log("***   Fin CARGAR (No se cargaron datos) ***************");
			return false;
		}
	}
	

	/**
	 * La relacion ha sido cargada con datos?
	 * @return boolean
	 */
	function esta_cargada()
	{
		return $this->_cargado;	
	}
	
	/**
	 * Notifica a la relacion que sus tablas han sido o no cargadas
	 * @param boolean $cargado
	 */
	function set_cargado($cargado)
	{
		$this->_cargado = $cargado;
	}

	/**
	* Fuerza a que los datos_tabla contenidos marquen todos sus filas como nuevas
	* Esto implica que a la hora de la sincronización se van a generar INSERTS para todas las filas.
	* Se utiliza para forzar una clonación completa de los datos una relación.
	*/
	function forzar_insercion()
	{
		foreach($this->_dependencias as $id => $dependencia){
			$dependencia->forzar_insercion();
		}
	}
	
	/**
	 * Sincroniza los cambios con el medio de persistencia
	 */
	function sincronizar($usar_cursores=false)
	{
		if (!$usar_cursores) {
			$this->disparar_validacion_tablas();
			$this->evt__validar();
			$this->persistidor()->sincronizar();
			//Se notifica el fin de la sincronización a las tablas
			foreach ($this->_dependencias as $dependencia) {
				$dependencia->notificar_fin_sincronizacion();
			}
		} else {
			//Se sincroniza con cursores
			foreach ($this->_dependencias as $dependencia) {
				$filas = $dependencia->get_id_filas_filtradas_por_cursor(true);
				if($filas) {
					$dependencia->validar($filas);
				}
			}
			$this->evt__validar();
			$this->persistidor()->sincronizar($usar_cursores);
			foreach ($this->_dependencias as $dependencia) {
				$filas = $dependencia->get_id_filas_filtradas_por_cursor(true);
				if($filas) {
					$dependencia->notificar_fin_sincronizacion($filas);
				}
			}
		}
	}

	/**
	 * Sincroniza los cambios con el medio de persistencia
	 */
	function sincronizar_filas($filas_tablas)
	{
		foreach ($this->_dependencias as $id => $dependencia) {
			if (isset($filas_tablas[$id])) {
				$dependencias->validar($filas_tablas[$id]);
			}
		}
		$this->evt__validar();
		$this->persistidor()->sincronizar(false, $filas_tablas);
		//Se notifica el fin de la sincronización a las tablas
		foreach ($this->_dependencias as $id => $dependencia) {
			if (isset($filas_tablas[$id])) {
				$dependencias->notificar_fin_sincronizacion($filas_tablas[$id]);
			}
		}
	}
	
	/**
	 * Elimina y sincroniza en el medio de persistencia todos los datos cargados en la relación
	 */
	function eliminar_todo()
	{
		$this->persistidor()->eliminar_todo();
		$this->resetear();
	}
	
	/**
	 * Usar eliminar_todo, es más explícito
	 * @deprecated Desde 0.8.4, usar eliminar_todo, es más explícito
	 * @see eliminar_todo()
	 */
	function eliminar()
	{
		toba::logger()->obsoleto(__CLASS__, __METHOD__, "0.8.4", "Usar eliminar_todo");
		$this->eliminar_todo();	
	}
	
	/**
	 * Retorna el id de las tablas que no tienen padres en la relación
	 * @return array
	 */
	function get_tablas_raiz()
	{
		return $this->_tablas_raiz;
	}
	
	/**
	 * Fuente de datos que utiliza el objeto y sus dependencias
	 * @return string
	 */
	function get_fuente()
	{
		return $this->_info["fuente"];
	}
	
	/**
	 * Determina si los datos cargados difieren de los datos existentes en el medio de persistencia
	 * @return boolean
	 */
	function hay_cambios()
	{
		$hay_cambios = false;
		foreach ($this->_dependencias as $dependencia) {
			if ($dependencia->hay_cambios()) {
				return true;
			}
		}
		return false;
	}

	function get_columnas_tabla_padre($datos)
	{
		$resultado = array();
		foreach($this->_info_columnas_asoc_rel as $info_rel){
			if ($info_rel['asoc_id'] == $datos['asoc_id'] && $info_rel['padre_objeto']  == $datos['padre_objeto']){
				$resultado[] = $info_rel['col_padre'];
			}
		}
		return $resultado;
	}

	function get_columnas_tabla_hija($datos)
	{
		$resultado = array();
		foreach($this->_info_columnas_asoc_rel as $info_rel){
			if ($info_rel['asoc_id'] == $datos['asoc_id'] && $info_rel['hijo_objeto']  == $datos['hijo_objeto']){
				$resultado[] = $info_rel['col_hija'];
			}
		}
		return $resultado;
	}

	/**
	 * Carga en el nodo xml los datos cargados en el DR. Funciona sólo para relaciones que se modelan como un árbol, no grafos.
	 * @param SimpleXMLElement $xml Es el nodo XML donde se van a cargar todos los datos
	 */
	
	function get_xml($xml){

		// Controla que haya una única tabla raiz
		if(count($this->_tablas_raiz) != 1)
			throw new toba_error_def("El datos_relacion no posee una única tabla raiz.");

		// Recupera los registros de la tabla raiz para armar cada unidad del XML.
		$datos_raiz = $this->_dependencias[$this->_tablas_raiz[0]]->get_filas();
		
		// Para cada registro agrega el nodo XML correspondiente y manda a armar el contenido de cada uno
		foreach($datos_raiz as $clave => $valor){
			$entidad = $xml->addChild($this->_tablas_raiz[0]);
			$this->armar_xml($entidad,$this->_tablas_raiz[0],$clave);
		}
	}
	
	/**
	 * Arma un nodo XML para un registro de un datos tabla, con sus columnas como atributos y sus registros de tablas hijas como nuevos nodos internos
	 * Es un método recursivo con la siguiente estructura:
	 * a) Setea el cursor en el registro dado de la tabla dada
	 * b) agrega los datos del registro en el que se está parado como atributos del nodo xml recibido
	 * c) Para cada tabla hija agrega un nodo al nodo dado.
	 * d) Para cada registro de cada tabla hija, agrega un nodo al nodo creado en c) y llama recursivamente a la función
	 *
	 * @param SimpleXMLElement $xml es el nodo donde se va a agregar la información
	 * @param string $tabla la tabla de la que se van a sacar los datos
	 * @clave int $clave es la clave del registro de la tabla del que se van a sacar los datos
	 */

	protected function armar_xml($xml,$tabla,$clave)
	{
		// Setea el cursor de la tabla (esto además está seteando un cursor en las tablas hijas
		$this->_dependencias[$tabla]->set_cursor($clave);
		
		// Agrega los datos del registro seleccionado como atributos
		$this->_dependencias[$tabla]->get_xml($xml);
		
		// Recupera las tablas hijas de la tabla que se está recorriendo
		$tablas_hijas = $this->get_tablas_hijas($tabla);

		// Para cada tabla hija, agrega un nodo al nodo dado
		foreach($tablas_hijas as $tabla_hija){
			$id_filas_hijas = $this->_dependencias[$tabla_hija]->get_id_filas();
			$xml2 = $xml->addChild($tabla_hija);
			
			// Para cada registro de las tablas hijas seleccionado según el cursor, agrego un nodo y llamo recursivamente
			foreach($id_filas_hijas as $id_fila_hija){
				$xml3 = $xml2->addChild('registro');
				$this->armar_xml($xml3,$tabla_hija,$id_fila_hija);
			}
		}
	}

	/**
	 * Dada una tabla del DR, recupera los identificadores de las tablas hijas
	 * @param string $tabla es el identificador de la tabla
	 * @return mixed es el conjunto de identificadores de las tablas hijas
	 */
	protected function get_tablas_hijas($tabla)
	{
		// Busco el id de la tabla
		$id_objeto_padre = null;
		$i = 0;
		while(!$id_objeto_padre && $i < count($this->_info_dependencias)){
			if($this->_info_dependencias[$i]['identificador'] == $tabla){
				$id_objeto_padre = $this->_info_dependencias[$i]['objeto'];
			}
			$i++;
		}
		
		// Controlo que se haya encontrado la tabla
		if(!$id_objeto_padre)
			throw new toba_error_def("No se puede obtener el conjunto de tablas hija de la tabla $tabla que no pertenece a la relación.");

		// Busco todos los id de objeto de las tablas hijas
		$objetos_hijos = array();
		foreach($this->_info_columnas_asoc_rel as $relacion){
			if($relacion['padre_objeto'] == $id_objeto_padre){
				$objetos_hijos[] = $relacion['hijo_objeto'];
			}			
		}
		
		// convierto los id de objetos en nombres de tablas
		$tablas_hijas = array();
		
		for($i = 0; $i < count($this->_info_dependencias); $i++){
			if(in_array($this->_info_dependencias[$i]['objeto'],$objetos_hijos)){
				$tablas_hijas[] = $this->_info_dependencias[$i]['identificador'];
			}
		}
		
		return $tablas_hijas;
	}
}
?>
