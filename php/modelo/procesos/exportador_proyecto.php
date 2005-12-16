<?
require_once('modelo/proceso_toba.php');
require_once('modelo/estructura_db/tablas_proyecto.php');
require_once('nucleo/componentes/catalogo_toba.php');
require_once('nucleo/componentes/cargador_toba.php');
require_once('nucleo/lib/manejador_archivos.php');

class exportador_proyecto extends proceso_toba
{
	const subdir_componentes = 'metadatos/componentes';
	const subdir_tablas = 'metadatos/tablas';
	const prefijo_componentes = 'dump_';

	protected $dir_componentes;
	protected $dir_tablas;
	
	function __construct( $raiz, $instancia, $proyecto )
	{
		parent::__construct( $raiz, $instancia, $proyecto );
		$this->dir_componentes = $this->dir_proyecto . '/' . self::subdir_componentes;
		$this->dir_tablas = $this->dir_proyecto . '/' . self::subdir_tablas;
	}

	function procesar( $argumentos )
	{
		parent::procesar( $argumentos );
		$this->exportar_tablas();
		$this->exportar_componentes();
	}
	
	//-------------------------------------------------------------------
	// TABLAS
	//-------------------------------------------------------------------

	function exportar_tablas()
	{
		$this->interface->titulo( "Exportacion de tablas" );
		manejador_archivos::crear_arbol_directorios( $this->dir_tablas );
		foreach ( tablas_proyecto::get_lista() as $tabla ) {
			$this->interface->mensaje( "Exportando tabla: $tabla." );
			$definicion = tablas_proyecto::$tabla();
			//Genero el SQL
			if( isset($definicion['dump_where']) && ( trim($definicion['dump_where']) != '') ) {
       			$w = stripslashes($definicion['dump_where']);
       			$where = ereg_replace("%%",$this->proyecto, $w);
            }else{
       			$where = " ( proyecto = '{$this->proyecto}')";
			}
			$sql = "SELECT " . implode(', ', $definicion['columnas']) .
					" FROM $tabla " .
					" WHERE $where " .
					//" WHERE {$definicion['dump_clave_proyecto']} = '{$this->proyecto}' " .
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

	//-------------------------------------------------------------------
	// COMPONENTES
	//-------------------------------------------------------------------
	/*
	*	Exporta los componentes
	*/
	function exportar_componentes()
	{
		cargador_toba::instancia()->crear_cache_simple( $this->proyecto );
		foreach (catalogo_toba::get_lista_tipo_componentes() as $tipo) {
			$this->interface->titulo( $tipo );
			$path = $this->dir_componentes . '/' . $tipo;
			manejador_archivos::crear_arbol_directorios( $path );
			foreach (catalogo_toba::get_lista_componentes( $tipo, $this->proyecto ) as $id_componente) {
				$this->exportar_componente( $tipo, $id_componente );
			}
		}
	}
	
	/*
	*	Exporta un componente
	*/
	function exportar_componente( $tipo, $id )
	{
		$this->interface->mensaje("Exportando: " . $id['componente']);
		$directorio = $this->dir_componentes . '/' . $tipo;
		$archivo = manejador_archivos::nombre_valido( self::prefijo_componentes . $id['componente'] );
		$contenido =&  $this->get_contenido_componente( $tipo, $id );
		file_put_contents( $directorio .'/'. $archivo . '.sql', $contenido );
		//Log
	}
	
	/*
	*	Genera el contenido de la exportacion de un componente
	*/
	function & get_contenido_componente( $tipo, $id )
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
}
?>