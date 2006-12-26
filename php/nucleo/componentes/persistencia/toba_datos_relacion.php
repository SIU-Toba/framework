<?
require_once("nucleo/componentes/toba_componente.php");
require_once("toba_relacion_entre_tablas.php");
require_once("nucleo/componentes/interface/toba_ei_esquema.php");
require_once("3ros/Graph/Graph.php");	//Necesario para el calculo de orden topologico de las tablas

/**
 * Mantiene un conjunto relacionado de {@link toba_datos_tabla datos_tabla}, brindando servicios para cargar y sincronizar esta relación con algún medio de persistencia (general una BD relacional)
 * 
 * 	@package Componentes
 *  @subpackage Persistencia
 *  @todo En el dump_esquema incluir la posición actual de los cursores
 */
class toba_datos_relacion extends toba_componente 
{
	protected $relaciones = array();		
	protected $tablas_raiz;
	protected $persistidor;
	protected $cargado = false;
	protected $relaciones_mapeos=array();			//Mapeo entre filas de las tablas
	static protected $debug_pasadas;				//Mantiene la cantidad de pasadas para generar ids unicos en js

	function __construct($id)
	{
		$propiedades[] = "relaciones_mapeos";
		$propiedades[] = "cargado";
		$this->set_propiedades_sesion($propiedades);			
		parent::__construct($id);	
		$this->crear_tablas();
		$this->crear_relaciones();
		if ($this->info_estructura['debug']) {
			$this->dump_esquema("INICIO: ".$this->info['nombre']);	
		}
	}
	
	function destruir()
	{
		//Esta clase es la encargada de guardarle los valores en sesion a cada relación
		//Se asume que las relaciones siempre se cargan en el mismo orden
		$this->relaciones_mapeos = array();
		foreach ($this->relaciones as $relacion) {
			$this->relaciones_mapeos[] = $relacion->get_mapeo_filas();
		}
		if ($this->info_estructura['debug']) {
			$this->dump_esquema("FIN: ".$this->info['nombre']);	
		}		
		parent::destruir();
	}

	/**
	 * Carga los datos_tabla y les pone los topes mínimos y máximos
	 */
	private function crear_tablas()
	{
		foreach( $this->lista_dependencias as $dep){
			$this->cargar_dependencia($dep);
			//La cantidad minima y maxima se pasan a traves de dos parametros genericos del objeto
			$posicion = $this->indice_dependencias[$dep];
			$cant_min = $this->info_dependencias[$posicion]['parametros_a'];
			$cant_max = $this->info_dependencias[$posicion]['parametros_b'];
			$this->dependencias[$dep]->set_tope_min_filas($cant_min);
			$this->dependencias[$dep]->set_tope_max_filas($cant_max);
			$this->dependencias[$dep]->set_controlador($this, $dep);
		}
	}

	/**
	 * Para cada relación definida crea una toba_relacion_entre_tablas
	 * Determina cual es la tabla raiz
	 */
	private function crear_relaciones()
	{
		if(count($this->info_relaciones)>0){
			for($a=0;$a<count($this->info_relaciones);$a++)
			{
				$id_padre = $this->info_relaciones[$a]['padre_id'];
				$id_hijo = $this->info_relaciones[$a]['hijo_id'];
				$id_relacion = $id_padre.'-'.$id_hijo;
				$this->relaciones[$id_relacion] = new toba_relacion_entre_tablas(	$this->dependencias[ $id_padre ],
																	explode(",",$this->info_relaciones[$a]['padre_clave']),
																	$id_padre,
																	$this->dependencias[ $id_hijo ],
																	explode(",",$this->info_relaciones[$a]['hijo_clave']),
																	$id_hijo
																);
				$padres[] = $this->info_relaciones[$a]['padre_id'];
				$hijos[] = $this->info_relaciones[$a]['hijo_id'];
				
				//Se recuperan los mapeos anteriores, si es que hay
				if (isset($this->relaciones_mapeos[$a])) {
					$this->relaciones[$id_relacion]->set_mapeo_filas($this->relaciones_mapeos[$a]);
				}
			}
			//Padres sin hijos
			$this->tablas_raiz = array_diff( array_unique($padres), array_unique($hijos) );
		}else{
			//No hay relaciones
			$this->relaciones = array();
			$this->tablas_raiz = array_keys($this->dependencias);
		}
	}

	//-------------------------------------------------------------------------------
	//-- DEBUG  ---------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	
	/**
	 * Muestra un dump de los datos y los cambios realizados a los mismos desde la carga
	 */
	function dump_contenido()
	{
		foreach($this->dependencias as $id => $dependencia){
			$info[$id]['cambios'] = $dependencia->get_cambios();
			$info[$id]['datos'] = $dependencia->get_conjunto_datos_interno();
		}
		ei_arbol( $info, 'DATOS_RELACION: ' . $this->info['nombre'], null, true);
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
		$grafo = self::grafo_relaciones($this->info_dependencias, $this->info_relaciones);
		$diagrama = "digraph G {
						rankdir=LR;
						node [fillcolor=white,shape=box, style=rounded,style=filled, color=gray];
						";
		if (isset($titulo)) {
			$diagrama .= "label=\"$titulo\";\n";
		}
		foreach ($grafo->getNodes() as $nodo) {
			$datos = $nodo->getData();
			
			//Se determina la tabla
			$id_tabla = $datos['identificador'];
			$tabla = $this->dependencias[$id_tabla];

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
		toba::memoria()->set_dato_sincronizado($indice, $parametros, 'debug');
		$url = toba::vinculador()->crear_vinculo(toba_editor::get_id(), '1000045', array('esquema' => $indice), 
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
			$relacion = $this->relaciones[$padre_id."-".$hijo_id];
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
						" [label=\"$cant_padres - $cant_hijos\"".
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
		$sorter = new Structures_Graph_Manipulator_TopologicalSorter();
		$grafo = self::grafo_relaciones($this->info_dependencias, $this->info_relaciones);
		$parciales = $sorter->sort($grafo);
		$ordenes = array();
		for ($i =0; $i<count($parciales) ; $i++) {
			for ($j=0; $j<count($parciales[$i]); $j++) {
				$ordenes[] = $parciales[$i][$j]->getData();
			}
		}
		$tablas = array();
		foreach ($ordenes as $orden) {
			$tablas[$orden['identificador']] = $this->dependencias[$orden['identificador']];
		}
		return $tablas;
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
		foreach ($tablas as $tabla) {
			$nodo =& new Structures_Graph_Node();
			$proveedor = isset($tabla['objeto_proveedor']) ? $tabla['objeto_proveedor'] : $tabla['objeto'];
			$obj[$proveedor] =& $nodo;
			$nodo->setData($tabla);
			$grafo->addNode($nodo);
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
		return array_keys($this->dependencias);	
	}

	/**
	 * Retorna una referencia a una tabla perteneciente a la relación
	 * @param string $tabla Id. de la tabla en la relación
	 * @return toba_datos_tabla
	 */
	function tabla($tabla)
	{
		if($this->existe_tabla($tabla)){
			return $this->dependencias[$tabla];
		}else{
			throw new toba_error("El datos_tabla '$tabla' solicitado no existe.");
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
		foreach($this->dependencias as $dependencia){
			$dependencia->resetear();
		}
		$this->cargado = false;
	}
	
	/**
	 * Asegura que ningún cursor de alguna tabla se encuentre posicionado en ninguna fila específica
	 */	
	function resetear_cursores()
	{
		foreach($this->dependencias as $dependencia){
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
		foreach($this->dependencias as $dependencia){
			$dependencia->validar();
		}
	}

	/**
	 * Retorna la estructura de datos utilizada por las tablas para mantener registro del estado de sus datos
	 * @return array
	 */
	function get_conjunto_datos_interno()
	{
		foreach($this->dependencias as $id => $dependencia){
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
	function get_persistidor()
	{
		if (!isset($this->persistidor)) {		
			//Se incluye el archivo
			$archivo = "toba_ap_relacion_db.php";
			$particular = ($this->info_estructura['ap'] == 3);
			if ($particular	&& isset($this->info_estructura['ap_archivo'])) {
				$archivo = $this->info_estructura['ap_archivo'];
			}
			require_once($archivo);

			//Se crea la clase		
			$clase = "toba_ap_relacion_db";
			if ($particular && isset($this->info_estructura['ap_clase'])) {
				$clase = $this->info_estructura['ap_clase'];
			}
			$this->persistidor = new $clase( $this );
		}
		return $this->persistidor;
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
		$ap = $this->get_persistidor();
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
	function esta_cargado()
	{
		return $this->cargado;	
	}
	
	/**
	 * Notifica a la relacion que sus tablas han sido o no cargadas
	 * @param boolean $cargado
	 */
	function set_cargado($cargado)
	{
		$this->cargado = $cargado;
	}

	/**
	* Fuerza a que los datos_tabla contenidos marquen todos sus filas como nuevas
	* Esto implica que a la hora de la sincronización se van a generar INSERTS para todas las filas.
	* Se utiliza para forzar una clonación completa de los datos una relación.
	*/
	function forzar_insercion()
	{
		foreach($this->dependencias as $id => $dependencia){
			$dependencia->forzar_insercion();
		}
	}
	
	/**
	 * Sincroniza los cambios con el medio de persistencia
	 */
	function sincronizar()
	{
		$this->disparar_validacion_tablas();
		$this->evt__validar();
		$this->get_persistidor()->sincronizar();
		//Se notifica el fin de la sincronización a las tablas
		foreach ($this->dependencias as $dependencia) {
			$dependencia->notificar_fin_sincronizacion();
		}
	}
	
	/**
	 * Elimina y sincroniza en el medio de persistencia todos los datos cargados en la relación
	 */
	function eliminar_todo()
	{
		$this->get_persistidor()->eliminar_todo();
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
		return $this->tablas_raiz;
	}
	
	/**
	 * Fuente de datos que utiliza el objeto y sus dependencias
	 * @return string
	 */
	function get_fuente()
	{
		return $this->info["fuente"];
	}
	
}
?>
