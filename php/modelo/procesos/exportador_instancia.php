<?
require_once('modelo/instancia.php');
require_once('modelo/proceso_toba.php');
require_once('modelo/estructura_db/tablas_instancia.php');
require_once('nucleo/lib/manejador_archivos.php');

/*
	Exporta la informacion local. Si se pasa un proyecto como parametro
	solo se exporta la informacion general y la del proyecto
*/
class exportador_instancia extends proceso_toba
{
	protected $dir_instancia;
	protected $lista_proyectos = array();
	
	function __construct( $raiz, $instancia )
	{
		parent::__construct( $raiz, $instancia );
		$this->dir_instancia = $this->dir_raiz . '/instalacion/' . instancia::prefijo_dir_instancia . $this->instancia;
		if( ! is_dir( $this->dir_instancia ) ) {
			throw new excepcion_toba("Exportador de Instancia: la carpeta '{$this->dir_instancia}' no existe");
		}
		//Recupero la lista de proyectos incluidos en la instancia
		require_once( $this->dir_instancia . '/info_instancia.php' );
		$this->lista_proyectos = info_instancia::get_lista_proyectos();
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
		$this->exportar_tablas( 'get_lista_global', instancia::archivo_datos );	
		$this->interface->titulo( "Exportar logs globales" );
		$this->exportar_tablas( 'get_lista_global_log', instancia::archivo_logs );	
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
			$dir_proyecto = $this->dir_instancia . '/' . instancia::prefijo_dir_proyecto . $proyecto;
			manejador_archivos::crear_arbol_directorios( $dir_proyecto );
			$this->exportar_tablas_proyecto( 	'get_lista_proyecto', 
												$dir_proyecto .'/' . instancia::archivo_datos, 
												$proyecto );	
			$this->exportar_tablas_proyecto( 	'get_lista_proyecto_log',
												$dir_proyecto .'/' . instancia::archivo_logs,
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
					" ORDER BY {$definicion['dump_order_by']} ;\n";
			$this->interface->mensaje( $sql );
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