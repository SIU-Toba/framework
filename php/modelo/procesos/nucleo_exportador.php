<?
require_once('modelo/lib/proceso.php');
require_once('modelo/estructura_db/tablas_nucleo.php');
require_once('nucleo/lib/manejador_archivos.php');

class nucleo_exportador extends proceso
{
	function procesar( $db )
	{
		$this->exportar_tablas( $db );
	}
	
	//-------------------------------------------------------------------
	// TABLAS
	//-------------------------------------------------------------------

	function exportar_tablas( $db )
	{
		$this->interface->titulo( "Exportacion de tablas del NUCLEO" );
		manejador_archivos::crear_arbol_directorios( $this->elemento->get_dir_metadatos() );
		foreach ( tablas_nucleo::get_lista() as $tabla ) {
			$this->interface->mensaje( "Tabla: $tabla." );
			$definicion = tablas_nucleo::$tabla();
			//Genero el SQL
			$sql = "SELECT " . implode(', ', $definicion['columnas']) .
					" FROM $tabla " .
					" ORDER BY {$definicion['dump_order_by']} ;\n";
			//$this->interface->mensaje( $sql );
			$contenido = "";
			$datos = $db->consultar( $sql );
			for ( $a = 0; $a < count( $datos ) ; $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
			if ( trim( $contenido ) != '' ) {
				file_put_contents( $this->elemento->get_dir_metadatos() .'/'. $tabla . '.sql', $contenido );			
			}
		}
	}
}
?>