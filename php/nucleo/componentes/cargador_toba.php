<?php
require_once('nucleo/lib/cache_db.php');
require_once('modelo/estructura_db/tablas_componente.php');

class cargador_toba
{
	static private $instancia;
	protected $cache_metadatos_simples;
	protected $cache_metadatos_simples_proyecto;	// ATENCION, hay que controlar que coincidan en la solicitud
	protected $cache_metadatos_extendidos;
	protected $cache_metadatos_extendidos_proyecto;
	
	private function __construct(){}

	static function instancia() {
		if (!isset(self::$instancia)) {
			self::$instancia = new cargador_toba();	
		}
		return self::$instancia;	
	}

	//----------------------------------------------------------------
	// Servicios
	//----------------------------------------------------------------

	/**
	*	Retorna los metadatos de un componente, tal cual existen en las tablas
	*	del mismo. Este metodo es utilizado por el exportador de componentes
	*	El parametro DB tiene como objetivo brindar este servicio a la consola 
	*	(no existe 'dba' ni 'consultar_fuente')
	*/
	function get_metadatos_simples( $componente, $tipo=null, $db = null )
	{
		$metadatos = array();	
		if ( !isset( $tipo ) ) {
			$tipo = catalogo_toba::get_tipo( $componente );	
		}
		$clase_info = catalogo_toba::get_nombre_clase_definicion( $tipo );
		$estructura = call_user_func(array($clase_info,'get_estructura'));
		if ( isset($this->cache_metadatos_simples) ) {				// Con CACHE!
			//Saco el componente del CACHE
			foreach ($estructura as $seccion) {
				$tabla = $seccion['tabla'];
				$id = $componente['componente'];
				$datos = $this->cache_metadatos_simples->get_datos_tabla( $tabla , $id );
				if ( count( $datos ) > 1 ) { //SI los registros de la tabla son mas de 1, ordeno.
					$definicion = tablas_componente::$tabla();
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
				$definicion = tablas_componente::$tabla();
				//Genero el SQL
				$sql = "SELECT " . implode(', ', $definicion['columnas']) .
						" FROM $tabla " .
						" WHERE {$definicion['dump_clave_proyecto']} = '$proyecto' " .
						" AND {$definicion['dump_clave_componente']} = '$id' " .
						" ORDER BY {$definicion['dump_order_by']} ;\n";
				if ( isset( $db ) ) {
					$metadatos[$tabla] = $db->consultar( $sql );
				} else {
					$metadatos[$tabla] = consultar_fuente($sql, 'instancia' );
				}
			}
		}
		return $metadatos;
	}

	/**
	*	Retorna los metadatos extendidos de un componente
	*	Este metodo es utilizado por el constructor de runtimes e infos
	*/
	function get_metadatos_extendidos( $componente, $tipo=null, $db=null)
	{
		if ( !isset( $tipo ) ) {
			$tipo = catalogo_toba::get_tipo( $componente );	
		}
		$clase_info = catalogo_toba::get_nombre_clase_definicion( $tipo );
		if ( isset($this->cache_metadatos_extendidos) ) {			// CACHE no implementado!
			throw new excepcion_toba('No implementado');	
		} else {													// Sin CACHE!
			$proyecto = $componente['proyecto'];
			$id = $componente['componente'];
			$estructura = call_user_func_array( array(	$clase_info,
														'get_vista_extendida'),
														array( $proyecto, $id ) );
			foreach ( $estructura as $seccion => $contenido ) {
				if ( isset( $db ) ) {
					$temp = $db->consultar( $contenido['sql'] );
				} else {
					$temp = consultar_fuente($contenido['sql'], 'instancia' );
				}
				if ( $contenido['obligatorio'] && count($temp) == 0 ) {
					throw new excepcion_toba("El componente '$id' tiene una estructura incorrecta. No existe el segmento '$seccion'");
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

	//----------------------------------------------------------------
	// CACHES
	//----------------------------------------------------------------

	function crear_cache_simple( $proyecto, $db = null )
	{
		$this->cache_metadatos_simples_proyecto = $proyecto;
		if ( isset ( $db ) ) {
			$this->cache_metadatos_simples = new cache_db( $db );
		} else {
			$this->cache_metadatos_simples = new cache_db( dba::get_db('instancia') );
		}
		foreach ( tablas_componente::get_lista() as $tabla ) {
			$definicion = tablas_componente::$tabla();
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

	function generar_cache_extendido( $proyecto )
	{
	}
}
?>
