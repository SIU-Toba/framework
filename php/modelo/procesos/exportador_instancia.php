<?
require_once('modelo/proceso_toba.php');
require_once('modelo/estructura_db/tablas_instancia.php');
require_once('nucleo/lib/manejador_archivos.php');

/*
	Exporta la informacion local. Si se pasa un proyecto como parametro
	solo se exporta la informacion general y la del proyecto
*/
class exportador_instancia extends proceso_toba
{
	const archivo_datos_instancia = 'datos.sql';
	const archivo_logs_instancia = 'logs.sql';
	protected $dir_instancia;
	protected $lista_proyectos = array();
	
	function __construct( $raiz, $instancia )
	{
		parent::__construct( $raiz, $instancia );
		$this->dir_instancia = $this->dir_raiz . '/php/instalacion/i_' . $this->instancia;
		if( ! is_dir( $this->dir_instancia ) ) {
			throw new excepcion_toba("Exportador de Instancia: la carpeta '{$this->dir_instancia}' no existe");
		}
		//Recupero la lista de proyectos incluidos en la instancia
		require_once( $this->dir_instancia . '/info_proyectos.php' );
		$this->lista_proyectos = info_proyectos::get_lista();
	}

	function procesar( $argumentos )
	{
		parent::procesar( $argumentos );
		$this->exportar_global();
		$this->exportar_proyectos( $argumentos );
	}
	
	//-------------------------------------------------------------------
	// TABLAS
	//-------------------------------------------------------------------

	function exportar_global()
	{
		$this->interface->titulo( "Exportar informacion global" );
		$this->exportar_tablas( 'get_lista_global', exportador_instancia::archivo_datos_instancia );	
		$this->interface->titulo( "Exportar logs globales" );
		$this->exportar_tablas( 'get_lista_global_log', exportador_instancia::archivo_logs_instancia );	
	}

	function exportar_tablas( $metodo_lista_tablas, $nombre_archivo )
	{
		foreach ( tablas_instancia::$metodo_lista_tablas() as $tabla ) {
			$this->interface->mensaje( "Tabla: $tabla." );
			$definicion = tablas_instancia::$tabla();
			//Genero el SQL
			$sql = "SELECT " . implode(', ', $definicion['columnas']) .
					" FROM $tabla " .
					" ORDER BY {$definicion['dump_order_by']} ;\n";
			//$this->interface->mensaje( $sql );
			$contenido = "";
			$datos = consultar_fuente($sql, 'instancia' );
			for ( $a = 0; $a < count( $datos ) ; $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
			file_put_contents( $this->dir_instancia .'/'. $nombre_archivo , $contenido );			
		}
	}

	function exportar_proyectos( $argumentos )
	{
		if ( count( $argumentos ) > 0 ) {
			$proyectos = $argumentos;
		} else {
			$proyectos = $this->lista_proyectos;
		}
		foreach( $proyectos as $proyecto ) {
			$this->interface->titulo( "Exportar proyecto: $proyecto" );
			$dir_proyecto = $this->dir_instancia . '/' . $proyecto;
			manejador_archivos::crear_arbol_directorios( $dir_proyecto );
			$this->exportar_tablas_proyecto( 	'get_lista_proyecto', 
												$dir_proyecto .'/' . exportador_instancia::archivo_datos_instancia, 
												$proyecto );	
			$this->exportar_tablas_proyecto( 	'get_lista_proyecto_log',
												$dir_proyecto .'/' . exportador_instancia::archivo_logs_instancia,
												$proyecto );	
		}
	}

	function exportar_tablas_proyecto( $metodo_lista_tablas, $nombre_archivo, $proyecto )
	{
		foreach ( tablas_instancia::$metodo_lista_tablas() as $tabla ) {
			$this->interface->mensaje( "Exportando tabla: $tabla." );
			$definicion = tablas_instancia::$tabla();
			//Genero el SQL
			if( isset($definicion['dump_where']) && ( trim($definicion['dump_where']) != '') ) {
       			$w = stripslashes($definicion['dump_where']);
       			$where = ereg_replace("%%",$proyecto, $w);
            }else{
       			$where = " ( proyecto = '$proyecto')";
			}
			$sql = "SELECT " . implode(', ', $definicion['columnas']) .
					" FROM $tabla dd" .
					" WHERE $where " .
					//" WHERE {$definicion['dump_clave_proyecto']} = '{$this->proyecto}' " .
					" ORDER BY {$definicion['dump_order_by']} ;\n";
			//$this->interface->mensaje( $sql );
			$contenido = "";
			$datos = consultar_fuente($sql, 'instancia' );
			for ( $a = 0; $a < count( $datos ) ; $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
			file_put_contents( $nombre_archivo, $contenido );			
		}
	}
}
?>