<?
require_once('modelo/lib/proceso.php');
require_once('modelo/instalacion.php');
require_once('modelo/instancia.php');
require_once('modelo/estructura_db/tablas_instancia.php');
require_once('nucleo/lib/manejador_archivos.php');

class instancia_exportador extends proceso
{
	function procesar()
	{
		$this->exportar_global();
		$this->exportar_proyectos();
	}
	
	//-------------------------------------------------------------------
	// TABLAS
	//-------------------------------------------------------------------

	function exportar_global()
	{
		$dir_global = $this->elemento->get_dir() . '/' . instancia::dir_datos_globales;
		manejador_archivos::crear_arbol_directorios( $dir_global );
		$this->interface->titulo( "Exportar informacion global" );
		$this->exportar_tablas_global( 'get_lista_global', $dir_global .'/' . instancia::archivo_datos );	
		$this->interface->titulo( "Exportar logs globales" );
		$this->exportar_tablas_global( 'get_lista_global_log', $dir_global .'/' . instancia::archivo_logs );	
	}

	function exportar_tablas_global( $metodo_lista_tablas, $path )
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
			file_put_contents( $path  , $contenido );			
		}
	}

	function exportar_proyectos()
	{
		foreach( $this->elemento->get_lista_proyectos() as $proyecto ) {
			$this->interface->titulo( "Exportar proyecto: $proyecto" );
			$dir_proyecto = $this->elemento->get_dir() . '/' . instancia::prefijo_dir_proyecto . $proyecto;
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