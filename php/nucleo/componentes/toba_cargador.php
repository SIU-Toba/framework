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
		if (class_exists('toba')) {
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
				$id = $componente['componente'];
				$proyecto = $componente['proyecto'];
				$definicion = toba_db_tablas_componente::$tabla();
				//Genero el SQL
				$sql = "SELECT " . implode(', ', $definicion['columnas']) .
						" FROM $tabla " .
						" WHERE {$definicion['dump_clave_proyecto']} = '$proyecto' " .
						" AND {$definicion['dump_clave_componente']} = '$id' " .
						" ORDER BY {$definicion['dump_order_by']} ;\n";
				$metadatos[$tabla] = $db->consultar( $sql );
			}
		}
		return $metadatos;
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
			throw new toba_error('No implementado');
		} else {													// Sin CACHE!
			$clase_def = $tipo . '_def';
			$metodo_def = $resumidos ? 'get_vista_extendida_resumida' : 'get_vista_extendida';
			//----TODO: HACK para evitar bug en php 5.2.1 y 5.2.3
			new $clase_def;
			//----
			$estructura = call_user_func_array( array(	$clase_def,
														$metodo_def ),
														array( $proyecto, $id ) );
			foreach ( $estructura as $seccion => $contenido ) {
				$temp = $db->consultar( $contenido['sql'] );
				if ( $contenido['obligatorio'] && count($temp) == 0 ) {
					throw new toba_error("Error en la carga del componente '$id' (TIPO '$tipo'). No existe el la seccion de datos '$seccion'");
				}
				if ($contenido['registros']!=='1') {
					$metadatos[$seccion] = $temp;
				} else {
					$metadatos[$seccion] = $temp[0];
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

	static function get_tipo( $componente )
	{
		$sql = " 	SELECT clase
					FROM apex_objeto
					WHERE (objeto = '{$componente['componente']}')
					AND (proyecto = '{$componente['proyecto']}')";
		$datos = toba::instancia()->get_db()->consultar($sql);
		return $datos[0]['clase'];
	}
	
	//----------------------------------------------------------------
	// CACHES
	//----------------------------------------------------------------

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
			$sql = "SELECT " . implode(', ', $definicion['columnas']) .
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

	function generar_cache_extendido( $proyecto, $db =null )
	{
	}
}
?>
