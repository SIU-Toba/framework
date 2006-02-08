<?
require_once('modelo/lib/proceso.php');
require_once('modelo/estructura_db/tablas_proyecto.php');
require_once('nucleo/componentes/catalogo_toba.php');
require_once('nucleo/componentes/cargador_toba.php');
require_once('nucleo/lib/manejador_archivos.php');

class proyecto_exportador extends proceso
{

	const prefijo_componentes = 'dump_';

	function procesar()
	{
		$this->exportar_tablas();
		$this->exportar_componentes();
	}
	
	//-------------------------------------------------------------------
	// TABLAS
	//-------------------------------------------------------------------

	function exportar_tablas()
	{
		$this->interface->titulo( "Exportacion de tablas" );
		manejador_archivos::crear_arbol_directorios( $this->elemento->get_dir_tablas() );
		foreach ( tablas_proyecto::get_lista() as $tabla ) {
			$this->interface->mensaje( "Exportando tabla: $tabla." );
			$definicion = tablas_proyecto::$tabla();
			//Genero el SQL
			if( isset($definicion['dump_where']) && ( trim($definicion['dump_where']) != '') ) {
       			$w = stripslashes($definicion['dump_where']);
       			$where = ereg_replace("%%",$this->elemento->get_id(), $w);
            }else{
       			$where = " ( proyecto = '".$this->elemento->get_id()."')";
			}
			$sql = "SELECT " . implode(', ', $definicion['columnas']) .
					" FROM $tabla " .
					" WHERE $where " .
					//" WHERE {$definicion['dump_clave_proyecto']} = '".$this->elemento->get_id()."}' " .
					" ORDER BY {$definicion['dump_order_by']} ;\n";
			//$this->interface->mensaje( $sql );
			$contenido = "";
			$datos = consultar_fuente($sql, 'instancia' );
			for ( $a = 0; $a < count( $datos ) ; $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
			if ( trim( $contenido ) != '' ) {
				file_put_contents( $this->elemento->get_dir_tablas() .'/'. $tabla . '.sql', $contenido );			
			}
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
		cargador_toba::instancia()->crear_cache_simple( $this->elemento->get_id() );
		foreach (catalogo_toba::get_lista_tipo_componentes_dump() as $tipo) {
			$this->interface->titulo( $tipo );
			foreach (catalogo_toba::get_lista_componentes( $tipo, $this->elemento->get_id() ) as $id_componente) {
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
		$directorio = $this->elemento->get_dir_componentes() . '/' . $tipo;
		manejador_archivos::crear_arbol_directorios( $directorio );
		$archivo = manejador_archivos::nombre_valido( self::prefijo_componentes . $id['componente'] );
		$contenido =&  $this->get_contenido_componente( $tipo, $id );
		//if ( trim( $contenido ) != '' ) {
			file_put_contents( $directorio .'/'. $archivo . '.sql', $contenido );
		//}
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