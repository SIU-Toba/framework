<?php
require_once('lib/elemento_modelo.php');
require_once('modelo/instancia.php');
require_once('nucleo/componentes/catalogo_toba.php');
require_once('nucleo/componentes/cargador_toba.php');
require_once('nucleo/lib/manejador_archivos.php');
require_once('nucleo/lib/reflexion/clase_datos.php');
require_once('modelo/estructura_db/tablas_proyecto.php');
require_once('modelo/estructura_db/tablas_instancia.php');
require_once('modelo/estructura_db/tablas_componente.php');

/*
*	Administrador de metadatos de PROYECTOS

	Atencion: en el caso de la importacion de proyectos directa (sin instancia)
	hay que actualizar las secuencias despues.
*/
class proyecto extends elemento_modelo
{
	private $instancia;				
	private $identificador;
	private $dir;
	const dump_prefijo_componentes = 'dump_';
	const compilar_archivo_referencia = 'tabla_tipos';
	const compilar_prefijo_componentes = 'php_';	
	private $compilacion_tabla_tipos;

	public function __construct( instancia $instancia, $identificador )
	{
		parent::__construct();
		$this->instancia = $instancia;
		$this->identificador = $identificador;
		if ( $this->identificador == 'toba' ) {
			$this->dir = $this->dir_raiz . '/php/admin';	
		} else {
			$this->dir = $this->dir_raiz . '/proyectos/' . $this->identificador;	
		}
		if( ! is_dir( $this->dir ) ) {
			throw new excepcion_toba("PROYECTO: El proyecto '{$this->identificador}' es invalido. (la carpeta '{$this->dir}' no existe)");
		} 
	}

	//-----------------------------------------------------------
	//	Informacion
	//-----------------------------------------------------------

	function get_id()
	{
		return $this->identificador;
	}
	
	function get_dir()
	{
		return $this->dir;	
	}

	function get_dir_componentes()
	{
		return $this->dir . '/metadatos/componentes';
	}
	
	function get_dir_tablas()
	{
		return $this->dir . '/metadatos/tablas';
	}

	function get_dir_componentes_compilados()
	{
		return $this->dir . '/metadatos_compilados/componentes';
	}

	function info()
	{
		/*
			Cuantas objetos hay, etc.
		*/	
	}

	//-----------------------------------------------------------
	//	EXPORTAR
	//-----------------------------------------------------------

	function exportar()
	{
		if( ! $this->instancia->existe_proyecto( $this->identificador ) ) {
			throw new excepcion_toba("PROYECTO: El proyecto '{$this->identificador}' no esta asociado a la instancia actual");
		}
		try {
			$this->exportar_tablas();
			$this->exportar_componentes();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante la exportacion.' );
			$this->manejador_interface->mensaje( $e->getMessage() );
		}
	}

	private function exportar_tablas()
	{
		$this->manejador_interface->titulo( "Exportacion de tablas" );
		manejador_archivos::crear_arbol_directorios( $this->get_dir_tablas() );
		foreach ( tablas_proyecto::get_lista() as $tabla ) {
			$this->manejador_interface->mensaje( "Exportando tabla: $tabla." );
			$definicion = tablas_proyecto::$tabla();
			//Genero el SQL
			if( isset($definicion['dump_where']) && ( trim($definicion['dump_where']) != '') ) {
       			$w = stripslashes($definicion['dump_where']);
       			$where = ereg_replace("%%",$this->get_id(), $w);
            }else{
       			$where = " ( proyecto = '".$this->get_id()."')";
			}
			$sql = "SELECT " . implode(', ', $definicion['columnas']) .
					" FROM $tabla " .
					" WHERE $where " .
					//" WHERE {$definicion['dump_clave_proyecto']} = '".$this->get_id()."}' " .
					" ORDER BY {$definicion['dump_order_by']} ;\n";
			//$this->manejador_interface->mensaje( $sql );
			$contenido = "";
			$datos = consultar_fuente($sql, 'instancia' );
			for ( $a = 0; $a < count( $datos ) ; $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
			if ( trim( $contenido ) != '' ) {
				file_put_contents( $this->get_dir_tablas() .'/'. $tabla . '.sql', $contenido );			
			}
		}
	}

	/*
	*	Exporta los componentes
	*/
	private function exportar_componentes()
	{
		cargador_toba::instancia()->crear_cache_simple( $this->get_id() );
		foreach (catalogo_toba::get_lista_tipo_componentes_dump() as $tipo) {
			$this->manejador_interface->titulo( $tipo );
			foreach (catalogo_toba::get_lista_componentes( $tipo, $this->get_id() ) as $id_componente) {
				$this->exportar_componente( $tipo, $id_componente );
			}
		}
	}
	
	/*
	*	Exporta un componente
	*/
	private function exportar_componente( $tipo, $id )
	{
		$this->manejador_interface->mensaje("Exportando: " . $id['componente']);
		$directorio = $this->get_dir_componentes() . '/' . $tipo;
		manejador_archivos::crear_arbol_directorios( $directorio );
		$archivo = manejador_archivos::nombre_valido( self::dump_prefijo_componentes . $id['componente'] );
		$contenido =&  $this->get_contenido_componente( $tipo, $id );
		//if ( trim( $contenido ) != '' ) {
			file_put_contents( $directorio .'/'. $archivo . '.sql', $contenido );
		//}
	}
	
	/*
	*	Genera el contenido de la exportacion de un componente
	*/
	private function & get_contenido_componente( $tipo, $id )
	{
		//Recupero metadatos
		$metadatos = cargador_toba::instancia()->get_metadatos_simples( $id, $tipo );
		//Obtengo el nombre del componente
		if ( isset($metadatos['apex_objeto']) ) {
			$nombre_componente = $metadatos['apex_objeto'][0]['nombre'];		
		} else {
			$nombre_componente = $metadatos['apex_item'][0]['nombre'];		
		}
		//Genero el CONTENIDO
		$contenido = "------------------------------------------------------------\n";
		$contenido .= "--[{$id['componente']}]--  $nombre_componente \n";
		$contenido .= "------------------------------------------------------------\n";
		foreach ( $metadatos as $tabla => $datos) {
			for ( $a=0; $a<count($datos); $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
		}
		return $contenido;		
	}

	//-----------------------------------------------------------
	//	IMPORTAR
	//-----------------------------------------------------------
	
	/*
	*	Importacion de un proyecto dentro de la inicializacion de una instancia
	*/
	function importar()
	{
		if( ! $this->instancia->existe_proyecto( $this->identificador ) ) {
			throw new excepcion_toba("PROYECTO: El proyecto '{$this->identificador}' no esta asociado a la instancia actual.");
		}
		$db = $this->instancia->get_db();
		$this->importar_tablas();
		$this->importar_componentes();
	}

	/*
	*	Importar un proyecto en una instancia creada
	*/
	function importar_autonomo()
	{
		if( ! $this->instancia->existe_proyecto( $this->identificador ) ) {
			throw new excepcion_toba("PROYECTO: El proyecto '{$this->identificador}' no esta asociado a la instancia actual.");
		}
		try {
			$db = $this->instancia->get_db();
			$db->abrir_transaccion();
			$db->retrazar_constraints();
			$this->importar_tablas();
			$this->importar_componentes();
			$db->cerrar_transaccion();
		} catch ( excepcion_toba $e ) {
			$db->abortar_transaccion();
			$this->manejador_interface->error( 'PROYECTO: Ha ocurrido un error durante la IMPORTACION.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}
	
	private function importar_tablas()
	{
		$archivos = manejador_archivos::get_archivos_directorio( $this->get_dir_tablas(), '|.*\.sql|' );
		foreach( $archivos as $archivo ) {
			$this->manejador_interface->mensaje( $archivo );
			$this->instancia->get_db()->ejecutar_archivo( $archivo );
		}
	}
	
	private function importar_componentes()
	{
		$subdirs = manejador_archivos::get_subdirectorios( $this->get_dir_componentes() );
		foreach ( $subdirs as $dir ) {
			$this->manejador_interface->mensaje( $dir );
			$archivos = manejador_archivos::get_archivos_directorio( $dir , '|.*\.sql|' );
			foreach( $archivos as $archivo ) {
				$this->instancia->get_db()->ejecutar_archivo( $archivo );
			}
		}
	}

	//-----------------------------------------------------------
	//	ELIMINAR
	//-----------------------------------------------------------

	function eliminar()
	{
		try {
			$db = $this->instancia->get_db();
			$db->abrir_transaccion();
			$db->retrazar_constraints();
			$sql = $this->get_sql_eliminacion();
			$db->ejecutar( $sql );
			$db->cerrar_transaccion();
			$this->manejador_interface->mensaje("El proyecto '{$this->identificador}' ha sido eliminado");
		} catch ( excepcion_toba $e ) {
			$db->abortar_transaccion();
			$this->manejador_interface->error( 'Ha ocurrido un error durante la eliminacion de TABLAS de la instancia.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}
	
	private function get_sql_eliminacion()
	{
		// Tablas
		$tablas = array();
		//Busco las TABLAS y sus WHERES
		$catalogos = array();
		$catalogos['tablas_componente'][] = 'get_lista';
		$catalogos['tablas_proyecto'][] = 'get_lista';
		$catalogos['tablas_instancia'][] = 'get_lista_proyecto';
		$catalogos['tablas_instancia'][] = 'get_lista_proyecto_log';
		$catalogos['tablas_instancia'][] = 'get_lista_proyecto_usuario';
		foreach( $catalogos as $catalogo => $indices ) {
			foreach( $indices as $indice ) {
				$lista_tablas = call_user_func( array( $catalogo, $indice ) );
				foreach ( $lista_tablas as $t ) {
					$info_tabla = call_user_func( array( $catalogo, $t ) );
					if( isset( $info_tabla['dump_where'] ) ) {
						$where = " WHERE " . ereg_replace('%%', $this->identificador, stripslashes($info_tabla['dump_where']) );
						$where = ereg_replace( " dd", $t, $where );						
					} else {
						$where = " WHERE proyecto = '{$this->identificador}'";
					}
					$tablas[ $t ] = $where;
				}
			}
		}
		$sql = sql_array_tablas_delete( $tablas );
		return $sql;
	}

	//-----------------------------------------------------------
	//	COMPILAR
	//-----------------------------------------------------------

	function compilar()
	{
		try {
			$this->compilar_componentes();
			$this->crear_compilar_archivo_referencia();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante la compilacion.' );
			$this->manejador_interface->mensaje( $e->getMessage() );
		}
	}

	/*
	*	Ciclo de compilacion de componentes
	*/
	function compilar_componentes()
	{
		foreach (catalogo_toba::get_lista_tipo_componentes() as $tipo) {
			$this->manejador_interface->titulo( $tipo );
			$path = $this->get_dir_componentes_compilados() . '/' . $tipo;
			manejador_archivos::crear_arbol_directorios( $path );
			foreach (catalogo_toba::get_lista_componentes( $tipo, $this->get_id() ) as $id_componente) {
				$this->compilar_componente( $tipo, $id_componente );
			}
		}
	}
	
	/*
	*	Compila un componente
	*/
	function compilar_componente( $tipo, $id )
	{
		//Armo la clase compilada
		$nombre = manejador_archivos::nombre_valido( self::compilar_prefijo_componentes . $id['componente'] );
		$this->manejador_interface->mensaje("Compilando: " . $id['componente']);
		$clase = new clase_datos( $nombre, basename(__FILE__) );		
		$metadatos = cargador_toba::instancia()->get_metadatos_extendidos( $id, $tipo );
		$clase->agregar_metodo_datos('get_metadatos',$metadatos);
		//Creo el archivo
		$directorio = $this->get_dir_componentes_compilados() . '/' . $tipo;
		$path = $directorio .'/'. $nombre . '.php';
		$clase->guardar( $path );
		//Creo la tabla de referencia
		/*	ATENCION! excluyo los items porque pueden pisarse los IDs con los objetos	*/
		if ( $tipo != 'item' ) {
			$this->compilacion_tabla_tipos[$id['componente']] = $tipo;
		}
	}

	/*
	*	Creo la tabla de referencias
	*/
	function crear_compilar_archivo_referencia()
	{
		//Armo la clase compilada
		$this->manejador_interface->mensaje("Creando tabla de tipos.");
		$clase = new clase_datos( self::compilar_archivo_referencia, basename(__FILE__) );		
		$clase->agregar_metodo_datos('get_datos',$this->compilacion_tabla_tipos);
		//Creo el archivo
		$archivo = manejador_archivos::nombre_valido( self::compilar_archivo_referencia );
		$path = $this->get_dir_componentes_compilados() .'/'. $archivo . '.php';
		$clase->guardar( $path );
	}
}
?>