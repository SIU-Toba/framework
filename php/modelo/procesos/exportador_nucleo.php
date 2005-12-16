<?
require_once('modelo/proceso_toba.php');
require_once('modelo/estructura_db/tablas_nucleo.php');
require_once('nucleo/lib/manejador_archivos.php');

class exportador_nucleo extends proceso_toba
{
	const subdir_tablas = 'php/modelo/metadatos';

	protected $dir_tablas;
	
	function __construct( $raiz, $instancia, $proyecto )
	{
		parent::__construct( $raiz, $instancia, $proyecto );
		$this->dir_tablas = $this->dir_raiz . '/' . self::subdir_tablas;
	}

	function procesar( $argumentos )
	{
		parent::procesar( $argumentos );
		$this->exportar_tablas();
	}
	
	//-------------------------------------------------------------------
	// TABLAS
	//-------------------------------------------------------------------

	function exportar_tablas()
	{
		$this->interface->titulo( "Exportacion de tablas del NUCLEO" );
		manejador_archivos::crear_arbol_directorios( $this->dir_tablas );
		foreach ( tablas_nucleo::get_lista() as $tabla ) {
			$this->interface->mensaje( "Tabla: $tabla." );
			$definicion = tablas_nucleo::$tabla();
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
			file_put_contents( $this->dir_tablas .'/'. $tabla . '.sql', $contenido );			
		}
	}
}
?>