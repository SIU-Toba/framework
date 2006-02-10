<?php
require_once('lib/elemento_modelo.php');
require_once('modelo/estructura_db/tablas_nucleo.php');
require_once('nucleo/lib/manejador_archivos.php');
require_once('nucleo/lib/reflexion/clase_datos.php');

class nucleo extends elemento_modelo
{
	// Directorios de trabajo
	protected $dir_sql;
	protected $dir_ddl;
	protected $ba_instancia = 'tablas_instancia';	// ba = base archivo
	protected $ba_nucleo = 'tablas_nucleo';
	protected $ba_proyecto = 'tablas_proyecto';
	protected $ba_componente = 'tablas_componente';
	protected $ba_no_clasificadas = 'tablas_no_clasificadas';
	// Parseo
	protected $secuencias = array();
	protected $tablas = array();
	protected $archivos_procesados = 0;
	protected $cantidad_tablas_total = 0;	
	protected $cantidad_secuencias_total = 0;	
	protected $archivos;
	// Plan de generacion de PHP
	protected $plan;
	protected $catalogo;
	
	//------------------------------------------------
	// Informacion
	//------------------------------------------------
	
	static function get_dir_ddl()
	{
		return toba_dir() . '/php/modelo/ddl';
	}
	
	static function get_dir_estructura_db()
	{
		return toba_dir() . '/php/modelo/estructura_db';		
	}

	static function get_dir_metadatos()
	{
		return toba_dir() . '/php/modelo/metadatos';		
	}

	//-------------------------------------------------------------------
	// PARSEO de TABLAS toba para generar planes de export / import
	//-------------------------------------------------------------------

	/**
	*	Genera la informacion que describe el modelo de datos para todos los procesos toba
	*/
	function parsear_ddl()
	{
		try {
			$this->get_archivos_ddl();
			$this->parsear_archivos();
			$this->analizar_tablas();
			$this->generar_archivos_estructura();
			$this->generar_archivos_catalogo();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante el parseo.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}

	/**
	*	Crea la lista de archivos SQL del toba
	*/
	private function get_archivos_ddl()
	{
		$directorio = $this->get_dir_ddl();
		$patron = '|^pgsql_a.*\.sql|';
		$this->archivos = manejador_archivos::get_archivos_directorio( $directorio, $patron );
	}

	/**
	*	Parsea los archivos. ( Genera las estructuras $this->tablas y $this->secuencias )
	*/
	private function parsear_archivos()
	{
		foreach($this->archivos as $archivo)
		{
			//Intento abrir	el archivo
			$fd	= @fopen ($archivo,	"r");
			if(!is_resource($fd)){
				throw new excepcion_toba("ERROR: '$archivo' no es un archivo valido\n");
			}
			//Recorro el archivo
			$table = null; //Referencia a una tabla.
			if(isset($table)) unset($table);
			while (!feof ($fd))	
			//while((!feof	($fd)) && $tabla_actual	< 3	)
			{
				$buffer	= fgets($fd, 4096);	
				//------- Entro	en una tabla
				if(preg_match("/^create\ssequence/i",$buffer))
				{
					$temp =	preg_split("/\s+/",$buffer);
					$this->secuencias[] = $temp[2];
					$this->cantidad_secuencias_total++;
				}
				//------- Entro	en una tabla
				if(preg_match("/^create\stable/i",$buffer))
				{
					$temp =	preg_split("/\s+/",$buffer);
					$nombre_tabla = $temp[2];
					if(!isset($this->tablas[$nombre_tabla])){
						$this->tablas[$nombre_tabla] = array();
					}
					//Apunto la referencia a la tabla
					$tabla =& $this->tablas[$nombre_tabla];
					$tabla['archivo'] = basename($archivo);
					$this->cantidad_tablas_total++;
				}
				//------- Entre	en una propiedad
				if(preg_match("/^--:/",$buffer))
				{
					$temp =	preg_split("/(\s*):(\s*)/",$buffer);
					if(!isset($temp[1])||!isset($temp[2])){	
						throw new excepcion_toba("Error parseando la linea: $temp\n (archivo: $archivo)");
					}
					$tabla[trim($temp[1])]=addslashes(trim($temp[2]));
				}
				//------- Entre	en una columna
				if(preg_match("/^\s*?\w+\s*?.*NULL/",$buffer))
				{
					$temp =	preg_split("/\s+|\t/",$buffer);	
					//print_r($temp);
					$columna = $temp[1];
					//$columna_definicion	= addslashes(trim(preg_replace("/^\s*?\w+\s*?/","",$buffer)));
					//$tabla['columnas'][$columna] = $columna_definicion;
					$tabla['columnas'][] = $columna;	
				}
			}
			$this->archivos_procesados++;	
			fclose ($fd);
		}
	}
	
	/**
	*	Analiza las tablas creando planes de dumpeo. ( Genera la estructura $this->plan,
	*		que posee la lista de tablas por dominio y sus respectivos catalogos.
	*/
	private function analizar_tablas()
	{
		foreach($this->tablas as $id => $tabla) {
			//-- Clasificacion de tablas --
			$es_instancia = ( isset( $tabla['instancia'] ) && ( $tabla['instancia'] == '1' ) );
			$es_log = ( isset( $tabla['historica'] ) && ( $tabla['historica'] == '1' ) );
			$es_usuario = ( isset( $tabla['usuario'] ) && ( $tabla['usuario'] == '1' ) );
			$dump_componente = ( $tabla['dump'] == 'componente' );
			$dump_proyecto = ( $tabla['dump'] == 'multiproyecto' ); 
			$dump_nucleo = ( $tabla['dump'] == 'nucleo' );
			//-- Controles de integridad de la DEFINICION del plan --
			if ( $dump_componente && ( $es_instancia || $es_log ) ) {
				throw new excepcion_toba("La tabla '$id' posee un error en el plan de dumpeo: componente + (historica || instancia).");
			}
			if( $es_instancia && $es_log ) {
				throw new excepcion_toba("La tabla '$id' posee un error en el plan de dumpeo: historica + instancia.");
			}
			if( !( $dump_componente || $dump_proyecto || $dump_nucleo ) ) {
				throw new excepcion_toba("La tabla '$id' no posee una modalidad de dumpeo definida.");
			}
			//-- Armo el PLAN --
			if ( $es_instancia ) {
				$this->plan[ $this->ba_instancia ]['tablas'][] = $id;
				if ( $dump_proyecto ) {
					if ( $es_usuario ) {
						$this->plan[ $this->ba_instancia ]['indices']['get_lista_proyecto_usuario'][] = $id;
					} else {
						$this->plan[ $this->ba_instancia ]['indices']['get_lista_proyecto'][] = $id;
					}
				} elseif ( $dump_nucleo ) {
					if ( $es_usuario ) {
						$this->plan[ $this->ba_instancia ]['indices']['get_lista_global_usuario'][] = $id;
					} else {
						$this->plan[ $this->ba_instancia ]['indices']['get_lista_global'][] = $id;
					}
				}
			} elseif ( $es_log ) {
				$this->plan[ $this->ba_instancia ]['tablas'][] = $id;
				if ( $dump_proyecto ) {
					$this->plan[ $this->ba_instancia ]['indices']['get_lista_proyecto_log'][] = $id;
				} elseif ( $dump_nucleo ) {
					$this->plan[ $this->ba_instancia ]['indices']['get_lista_global_log'][] = $id;
				}
			} else {
				if ( $dump_componente ) {
					$this->plan[ $this->ba_componente ]['tablas'][] = $id;
					$this->plan[ $this->ba_componente ]['indices']['get_lista'][] = $id;
				} elseif ( $dump_proyecto ) {
					$this->plan[ $this->ba_proyecto ]['tablas'][] = $id;
					$this->plan[ $this->ba_proyecto ]['indices']['get_lista'][] = $id;
				} elseif ( $dump_nucleo ) {
					$this->plan[ $this->ba_nucleo ]['tablas'][] = $id;
					$this->plan[ $this->ba_nucleo ]['indices']['get_lista'][] = $id;
				} else {
					//Las tablas que entran aca no son catalogadas en ningun lado
					$this->plan[ $this->ba_no_clasificadas ]['tablas'][] = $id;
					$this->plan[ $this->ba_no_clasificadas ]['indices']['get_lista'][] = $id;
				}
			}
			//Armo el catalogo GENERAL
			$this->catalogo['catalogo_general'][] = $id;
		}
	}

	/**
	*	Crea los archivos PHP que describen el modelo. Utiliza $this->plan,
	*		por cada entrada crea una clase con N metodos catalogo y un metodo
	*		informativo por tabla
	*/
	private function generar_archivos_estructura()
	{
		foreach(array_keys($this->plan) as $nombre ) {
			$this->manejador_interface->titulo( $nombre );
			$clase = new clase_datos( $nombre, basename(__FILE__));
			//Creo los indices
			foreach ( $this->plan[$nombre]['indices'] as $id => $indice) {
				$clase->agregar_metodo_datos( $id, $indice );
			}
			//Informacion de cada tabla
			foreach($this->plan[$nombre]['tablas'] as $tabla) {
				$this->manejador_interface->mensaje("Tabla: $tabla");
				$clase->agregar_metodo_datos( $tabla, $this->tablas[$tabla] );
			}
			$clase->guardar( $this->get_dir_estructura_db() .'/'.$nombre.'.php' );
		}
	}

	private function generar_archivos_catalogo()
	{
		$this->manejador_interface->titulo("Creacion de catalogos");
		foreach( array_keys( $this->catalogo ) as $nombre ) {
			$this->manejador_interface->mensaje( "Catalogo: $nombre" );
			$clase = new clase_datos( $nombre, basename(__FILE__) );
			//Informacion de cada tabla
			$clase->agregar_metodo_datos( 'get_tablas' , $this->catalogo[ $nombre ] );
			$clase->guardar( $this->get_dir_estructura_db() .'/'.$nombre.'.php' );
		}
	}

	//-------------------------------------------------------------------
	// EXPORTACION de TABLAS MAESTRAS
	//-------------------------------------------------------------------

	/*
	*	Exporta los metadatos correspondientes a las tablas maestras del sistema
	*/
	function exportar( instancia $instancia )
	{
		try {
			$this->manejador_interface->titulo( "Exportacion de tablas del NUCLEO" );
			manejador_archivos::crear_arbol_directorios( $this->get_dir_metadatos() );
			foreach ( tablas_nucleo::get_lista() as $tabla ) {
				$this->manejador_interface->mensaje( "Tabla: $tabla." );
				$definicion = tablas_nucleo::$tabla();
				//Genero el SQL
				$sql = "SELECT " . implode(', ', $definicion['columnas']) .
						" FROM $tabla " .
						" ORDER BY {$definicion['dump_order_by']} ;\n";
				$contenido = "";
				$datos = $instancia->get_db()->consultar( $sql );
				for ( $a = 0; $a < count( $datos ) ; $a++ ) {
					$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
				}
				if ( trim( $contenido ) != '' ) {
					file_put_contents( $this->get_dir_metadatos() .'/'. $tabla . '.sql', $contenido );			
				}
			}
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante la exportacion.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}
}
?>