<?php
/**
 * Puerta de acceso a los metadatos de los componentes del proyecto actual
 * @package Componentes
 */
class toba_cargador
{
	static private $instancia;
	protected $cache_metadatos_simples;
	protected $cache_metadatos_simples_proyecto;	// ATENCION, hay que controlar que coincidan en la solicitud
	protected $cache_metadatos_extendidos;
	protected $cache_metadatos_extendidos_proyecto;
	protected $redefinidos = array();
	
	private function __construct()
	{
		if (PHP_SAPI != 'cli') { 
			$this->redefinidos = toba::memoria()->get_dato_sincronizado("toba_catalogo");
			if (!isset($this->redefinidos)) {
				$this->redefinidos = array();
			}	
		}
	}

	/**
	 * @return toba_cargador
	 */
	static function instancia() {
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_cargador();	
		}
		return self::$instancia;	
	}
	
	/**
	 * @ignore 
	 */
	function destruir()
	{
		toba::memoria()->set_dato_sincronizado("toba_catalogo", $this->redefinidos);
	}

	//----------------------------------------------------------------
	// Servicios
	//----------------------------------------------------------------

	/**
	*	Retorna los metadatos de un componente, tal cual existen en las tablas
	*	del mismo. Este metodo es utilizado por el exportador de componentes
	*	El parametro DB tiene como objetivo brindar este servicio a la consola 
	*/
	function get_metadatos_simples( $componente, $tipo=null, $db = null )
	{
		$metadatos = array();	
		if ( !isset( $tipo ) ) {
			$tipo = self::get_tipo( $componente );	
		}
		if (!isset($db)) {
			//Estoy entrando por el nucleo
			$db = toba::instancia()->get_db();	
		}
		$clase_def = $tipo . '_def';
		$estructura = call_user_func(array($clase_def,'get_estructura'));
		if ( isset($this->cache_metadatos_simples) ) {				// Con CACHE!
			//Saco el componente del CACHE
			foreach ($estructura as $seccion) {
				$tabla = $seccion['tabla'];
				$id = $componente['componente'];
				$datos = $this->cache_metadatos_simples->get_datos_tabla( $tabla , $id );
				if ( count( $datos ) > 1 ) { //SI los registros de la tabla son mas de 1, ordeno.
					$definicion = toba_db_tablas_componente::$tabla();
					$columnas_orden = array_map('trim', explode(',',$definicion['dump_order_by']) );
					$datos = rs_ordenar_por_columnas( $datos, $columnas_orden );
				}
				$metadatos[$tabla] = $datos;
			}
		} else {													// Sin CACHE!
			foreach ($estructura as $seccion) {
				$tabla = $seccion['tabla'];
				$id = $db->quote($componente['componente']);
				$proyecto = $db->quote($componente['proyecto']);
				$definicion = toba_db_tablas_componente::$tabla();
				//Genero el SQL
				$sql = 'SELECT ' . implode(', ', $definicion['columnas']) .
						" FROM $tabla " .
						" WHERE {$definicion['dump_clave_proyecto']} = $proyecto " .
						" AND {$definicion['dump_clave_componente']} = $id " .
						" ORDER BY {$definicion['dump_order_by']} ;\n";
				$metadatos[$tabla] = $db->consultar( $sql );
			}
		}
		return $metadatos;
	}

	/**
	*	Retorna los metadatos de un componente, tal cual existen en las tablas
	*	del mismo. Este metodo es utilizado por el exportador de personalizaciones.
	*	El parametro DB tiene como objetivo brindar este servicio a la consola
	*	La diferencia con get_metadatos_simples es que reordena los resultados
	*	de manera que sea más fácil computar la diferencia
	*/
	function get_metadatos_simples_diff( $componente, $tipo=null, $db = null )
	{
		$metadatos = array();
		if ( !isset( $tipo ) ) {
			$tipo = self::get_tipo( $componente );
		}
		if (!isset($db)) {
			//Estoy entrando por el nucleo
			$db = toba::instancia()->get_db();
		}
		$clase_def = $tipo . '_def';
		$estructura = call_user_func(array($clase_def,'get_estructura'));
		
		foreach ($estructura as $seccion) {
			$tabla = $seccion['tabla'];
			$id = $db->quote($componente['componente']);
			$proyecto = $db->quote($componente['proyecto']);
			$definicion = toba_db_tablas_componente::$tabla();
			//Genero el SQL
			$sql = 'SELECT ' . implode(', ', $definicion['columnas']) .
					" FROM $tabla " .
					" WHERE {$definicion['dump_clave_proyecto']} = $proyecto " .
					" AND {$definicion['dump_clave_componente']} = $id " .
					" ORDER BY {$definicion['dump_order_by']} ;\n";
			$data = $db->consultar( $sql );

			/*
			 * Todo lo que sigue es la reorganización de tablas para facilitar
			 * la comparación de registros. $clave es la secuencia de la tabla,
			 * $clave_insercion es la clave que se utilizará para chequear conflictos
			 * de unique key
			 */

			$clave = $this->armar_clave($db, $tabla, $definicion, $seccion['registros']);
			$reorganizado = $this->reorganizar_tablas($tabla, $data, $clave);
			$metadatos[$tabla] = $reorganizado;
		}

		return $metadatos;
	}

	protected function armar_clave($db, $tabla, &$definicion, $cant_registros)
	{		
		$clausulas_clave = array();
		if (isset($definicion['dump_clave_proyecto'])) {			//Si el proyecto es parte de la clave
			$clausulas_clave[] = '%'. $definicion['dump_clave_proyecto'] .'%';
		}
		if (isset($definicion['dump_clave_componente'])) {		//Si existe un id de componente en la clave
			$clausulas_clave[] = '%'.$definicion['dump_clave_componente'].'%';
		}
		
		//Busco una posible columna secuencia en la tabla.
		$clave_db = $db->get_secuencia_tabla($tabla);		
		if (! is_null($clave_db)) {
			$clausulas_clave[] = "%$clave_db%";
		} else {
			//No existe columna secuencia pero la tabla permite N registros
			if ($cant_registros != 1) {
				if (isset($definicion['clave_elemento'])) {			// Si está definido clave_elemento la armamos de ahí
					$clausulas_clave = array();				//Reinicializo para armar con este conjunto de columnas
					$exp_clave_elem = explode(',', $definicion['clave_elemento']);
					foreach (array_keys($exp_clave_elem) as $key) {						
						$clausulas_clave[] = '%'.trim($exp_clave_elem[$key]).'%';
					}
				} else {
					print_r($definicion);
					throw new toba_error("TOBA CARGADOR: Falta definir la clave de la tabla $tabla");
				}
			} 
		}
		
		$clausulas_clave = array_unique($clausulas_clave);	//Quito posibles columnas repetidas
		$clave = implode(';', $clausulas_clave);
		return $clave;
	}

	/**
	 *
	 * @param array $registros
	 * @param string $claves es un string separado por ; con las claves del registro.
	 * @return <type>
	 */
	protected function reorganizar_tablas($tabla, &$registros, $claves)
	{
		$reorganizado = array();
		
		foreach (array_keys($registros) as $registro_key) {
			$exp_claves = explode(';', $claves);
			$clave_reg = $claves;
			foreach ($exp_claves as $clave) {
				$clave_limpia = substr($clave, 1, -1);	//removemos los %% 
				$clave_reg = str_replace("$clave",
										$clave_limpia.':'.$registros[$registro_key][$clave_limpia],
										$clave_reg);
			}
			
			$reorganizado[$clave_reg] = array();
			
			foreach ($registros[$registro_key] as $columna => $valor) {
				$reorganizado[$clave_reg][$columna] = $valor;
			}
		}

		return $reorganizado;
	}

	/**
	*	Retorna los metadatos extendidos de un componente
	*	Este metodo es utilizado por el constructor de runtimes e infos
	*/
	function get_metadatos_extendidos( $componente, $tipo=null, $db=null, $resumidos=false)
	{
		if ( !isset( $tipo ) ) {
			$tipo = self::get_tipo( $componente );	
		}
		if (!isset($db)) {
			//Estoy entrando por el nucleo
			$db = toba::instancia()->get_db();	
		}
		$proyecto = $componente['proyecto'];
		$id = $componente['componente'];		
		$clave_ser = $proyecto.'||'.$id;
		//--- Los metadatos fueron definidos en runtime?
		if (isset($this->redefinidos[$clave_ser])) {
			return $this->redefinidos[$clave_ser];
		}
		if ( isset($this->cache_metadatos_extendidos) ) {			// CACHE de EXTENDIDOS no implementado!
			throw new toba_error_def('No implementado');
		} else {													// Sin CACHE!
			$clase_def = $tipo . '_def';
			$metodo_def = $resumidos ? 'get_vista_extendida_resumida' : 'get_vista_extendida';
			//----TODO: HACK para evitar bug en php 5.2.1 y 5.2.3
			new $clase_def;
			//----
			call_user_func_array(array($clase_def, 'set_db' ), array($db));			
			$estructura = call_user_func_array( array(	$clase_def,
														$metodo_def ),
														array( $proyecto, $id ) );
			foreach ( $estructura as $seccion => $contenido ) {
				$temp = $db->consultar( $contenido['sql'] );
				if ( $contenido['obligatorio'] && count($temp) == 0 ) {
					if ($tipo == 'toba_item' && $seccion == 'basica') {
						throw new toba_error_seguridad("La operación '$id' no existe");						
					} else {
						throw new toba_error_seguridad("Error en la carga del componente '$id' (TIPO '$tipo'). No existe el la seccion de datos '$seccion'");
					}
				}
				if ($contenido['registros']!=='1') {
					$metadatos[$seccion] = $temp;
				} else {
					$metadatos[$seccion] = current($temp);
				}
			}			
		}
		return $metadatos;
	}
	
	/**
	 * Permite definir los metadatos de un componente existente o no en la instancia actual
	 */
	function set_metadatos_extendidos($metadatos, $componente) 
	{
		$clave_ser = $componente['proyecto'].'||'.$componente['componente'];
		$this->redefinidos[$clave_ser] = $metadatos;
	}

	/**
	 * Si el componente posee metadatos redefinidos en runtime los retorna, sino retorna null
	 */
	function get_metadatos_redefinidos($componente)
	{
		$proyecto = $componente['proyecto'];
		$id = $componente['componente'];
		$clave_ser = $proyecto.'||'.$id;
		if (isset($this->redefinidos[$clave_ser])) {
			return $this->redefinidos[$clave_ser];
		} else {
			return null;
		}
	}

	/**
	 * Retorna el tipo al que pertenece un componente
	 * @param array $componente array('componente'=>id, 'proyecto'=>proyecto)
	 * @return string
	 */
	static function get_tipo( $componente )
	{
		$db = toba::instancia()->get_db();
		$componente = $db->quote($componente);
		$sql = " 	SELECT clase
					FROM apex_objeto
					WHERE (objeto = {$componente['componente']})
					AND (proyecto = {$componente['proyecto']})";
		$datos = $db->consultar($sql);
		return $datos[0]['clase'];
	}
	
	static function cargar_clase_archivo($pm, $clase_archivo, $id_proyecto)
	{
		$path = '';
		if (! is_null($pm)) {
			$punto = toba::puntos_montaje()->get_instancia_pm_proyecto($id_proyecto, $pm);
			$path  = $punto->get_path_absoluto().'/';
			toba::logger()->info("PUNTO MONTAJE: se cargó la clase $clase_archivo del punto de montaje {$punto->get_etiqueta()}. El path del mismo es {$punto->get_path_absoluto()}");			
		}

		require_once($path.$clase_archivo);
	}
	
	//----------------------------------------------------------------
	// CACHES
	//----------------------------------------------------------------

	/**
	 * @ignore 
	 */
	function crear_cache_simple( $proyecto, $db = null )
	{
		$this->cache_metadatos_simples_proyecto = $proyecto;
		if ( isset ( $db ) ) {
			$this->cache_metadatos_simples = new toba_cache_db( $db );
		} else {
			//Acceso por el nucleo
			$this->cache_metadatos_simples = new toba_cache_db( toba::instancia()->get_db() );
		}
		foreach ( toba_db_tablas_componente::get_lista() as $tabla ) {
			$definicion = toba_db_tablas_componente::$tabla();
			//Genero el SQL
			$sql = 'SELECT ' . implode(', ', $definicion['columnas']) .
					" FROM $tabla " .
					" WHERE {$definicion['dump_clave_proyecto']} = '$proyecto' " .
					" ORDER BY {$definicion['dump_order_by']} ;\n";
			//Agrego la tabla
			$this->cache_metadatos_simples->agregar_tabla( $tabla, 
															$sql,
															$definicion['dump_clave_componente'] );
		}
		//print_r( $this->cache_metadatos_simples->info() );
	}

	/**
	 * @ignore 
	 */
	function generar_cache_extendido( $proyecto, $db =null )
	{
	}
}
?>
