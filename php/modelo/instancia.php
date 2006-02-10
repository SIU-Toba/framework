<?
require_once('modelo/lib/elemento_modelo.php');
require_once('modelo/instalacion.php');
require_once('modelo/estructura_db/tablas_instancia.php');
require_once('modelo/estructura_db/catalogo_general.php');
require_once('modelo/estructura_db/secuencias.php');
require_once('nucleo/lib/manejador_archivos.php');
/**
	FALTA:
		- Falta un parametrizar en la instalacion si la base toba es independiente o adosada al negocio
			( se eliminan las tablas o la base en la regeneracion? )
*/
class instancia extends elemento_modelo
{
	const dir_datos_globales = 'global';
	const prefijo_dir_proyecto = 'p__';
	const archivo_datos = 'datos.sql';
	const archivo_usuarios = 'usuarios.sql';
	const archivo_logs = 'logs.sql';
	const cantidad_seq_grupo = 1000000;
	private $identificador;					// Identificador de la instancia
	private $dir;							// Directorio raiz de la instancia
	private $db;							// Referencia a la conexion con la DB de la instancia
	
	public function __construct( $identificador )
	{
		parent::__construct();
		$this->identificador = $identificador;
		define('apex_pa_instancia', $this->identificador);
		$this->dir = $this->dir_raiz . '/instalacion/' . instalacion::instancia_prefijo . $this->identificador;
		if( ! is_dir( $this->dir ) ) {
			throw new excepcion_toba("INSTANCIA: La instancia '{$this->identificador}' es invalida. (la carpeta '{$this->dir}' no existe)");
		} else {
			//Incluyo el archivo de parametros de la instancia
			require_once( $this->dir . '/info_instancia.php' );
		}
	}

	function get_db()
	{
		return dba::get_db('instancia');
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

	function get_parametros_db()
	{
		return dba::get_info_db_instancia();
	}
		
	function get_lista_proyectos()
	{
		$lista_proyectos = info_instancia::get_lista_proyectos();
		//ATENCION: temporal, hasta que el administrador se oficialice como proyecto
		if ( ! in_array( 'toba', $lista_proyectos ) ) {
			$lista_proyectos[] = 'toba';	
		}
		return $lista_proyectos;
	}
	
	function existe_proyecto( $proyecto )
	{
		$proyectos = $this->get_lista_proyectos();
		if ( in_array( $proyecto, $proyectos ) ) {
			return true;	
		}
		return false;
	}
	
	function existe_modelo()
	{
		try {
			$sql = "SELECT 1 FROM apex_proyecto;";
			$this->get_db()->consultar( $sql );
			return true;
		} catch ( excepcion_toba $e ) {
			return false;
		}
	}
	
	function get_registros_tabla()
	{
		$registros = array();
		$tablas = catalogo_general::get_tablas();
		foreach ( $tablas as $tabla ) {
			$sql = "SELECT COUNT(*) as registros FROM $tabla;";
			$temp = $this->get_db()->consultar( $sql );
			$registros[ $tabla ] = $temp[0]['registros'];
		}
		return $registros;
	}

	//-----------------------------------------------------------
	//	EXPORTAR
	//-----------------------------------------------------------

	/**
	* Exportacion de TODO lo que hay en una instancia
	*/
	function exportar_full()
	{
		$this->exportar();
		foreach( $this->get_lista_proyectos() as $proyecto ) {
			$this->manejador_interface->titulo( "PROYECTO: $proyecto" );
			$proyecto = new proyecto( $this, $proyecto );
			$proyecto->set_manejador_interface( $this->manejador_interface );			
			$proyecto->exportar();
		}	
	}	

	/**
	* Exportacion de la informacion correspondiente a la instancia (no proyectos)
	*/
	function exportar()
	{
		try {
			$this->manejador_interface->titulo( "Exportando informacion de la INSTANCIA" );
			$this->exportar_global();
			$this->exportar_proyectos();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante la exportacion.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}
	
	/*
	*	Exportar informacion GLOBAL de la instancia
	*/
	private function exportar_global()
	{
		$dir_global = $this->get_dir() . '/' . self::dir_datos_globales;
		manejador_archivos::crear_arbol_directorios( $dir_global );
		$this->manejador_interface->titulo( "Exportar informacion global" );
		$this->exportar_tablas_global( 'get_lista_global', $dir_global .'/' . self::archivo_datos );	
		$this->manejador_interface->titulo( "Exportar informacion de usuarios" );
		$this->exportar_tablas_global( 'get_lista_global_usuario', $dir_global .'/' . self::archivo_usuarios );	
		$this->manejador_interface->titulo( "Exportar logs globales" );
		$this->exportar_tablas_global( 'get_lista_global_log', $dir_global .'/' . self::archivo_logs );	
	}

	private function exportar_tablas_global( $metodo_lista_tablas, $path )
	{
		$contenido = "";
		foreach ( tablas_instancia::$metodo_lista_tablas() as $tabla ) {
			$this->manejador_interface->mensaje( "Tabla: $tabla." );
			$definicion = tablas_instancia::$tabla();
			//Genero el SQL
			$sql = "SELECT " . implode(', ', $definicion['columnas']) .
					" FROM $tabla " .
					" ORDER BY {$definicion['dump_order_by']} ;\n";
			//$this->manejador_interface->mensaje( $sql );
			$datos = consultar_fuente($sql, 'instancia' );
			for ( $a = 0; $a < count( $datos ) ; $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
		}
		if ( trim( $contenido ) != '' ) {
			file_put_contents( $path  , $contenido );			
		}
	}

	/*
	*	Exportar informacion de PROYECTOS de la instancia
	*/
	private function exportar_proyectos()
	{
		foreach( $this->get_lista_proyectos() as $proyecto ) {
			$this->manejador_interface->titulo( "Exportar proyecto: $proyecto" );
			$dir_proyecto = $this->get_dir() . '/' . self::prefijo_dir_proyecto . $proyecto;
			manejador_archivos::crear_arbol_directorios( $dir_proyecto );
			$this->exportar_tablas_proyecto( 'get_lista_proyecto', $dir_proyecto .'/' . self::archivo_datos, $proyecto );	
			$this->exportar_tablas_proyecto( 'get_lista_proyecto_log', $dir_proyecto .'/' . self::archivo_logs, $proyecto );	
			$this->exportar_tablas_proyecto( 'get_lista_proyecto_usuario', $dir_proyecto .'/' . self::archivo_usuarios, $proyecto );	
		}
	}

	private function exportar_tablas_proyecto( $metodo_lista_tablas, $nombre_archivo, $proyecto )
	{
		$contenido = "";
		foreach ( tablas_instancia::$metodo_lista_tablas() as $tabla ) {
			$this->manejador_interface->mensaje( "Exportando tabla: $tabla." );
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
			//$this->manejador_interface->mensaje( $sql );
			$datos = consultar_fuente($sql, 'instancia' );
			for ( $a = 0; $a < count( $datos ) ; $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
		}
		if ( trim( $contenido ) != '' ) {
			file_put_contents( $nombre_archivo, $contenido );			
		}
	}

	//-----------------------------------------------------------
	//	IMPORTAR
	//-----------------------------------------------------------

	/**
	* Importacion completa de una instancia
	*/
	function importar( $forzar_carga = false )
	{
		// Existe la base?
		$base = info_instancia::get_base();
		if ( ! dba::existe_base_datos( $base ) ) {
			dba::crear_base_datos( $base );
		}
		// Esta el modelo cargado
		if ( $this->existe_modelo() ) {
			if ( $forzar_carga ) {
				$this->eliminar();
				dba::crear_base_datos( $base );
			} else {
				throw new excepcion_toba_modelo_preexiste("INSTANCIA: Ya existe un modelo cargado en la base de datos.");
			}
		}
		//Inicio el proceso de carga
		try {
			$this->get_db()->abrir_transaccion();
			$this->get_db()->retrazar_constraints();
			$this->crear_modelo_datos_toba();
			$this->importar_proyectos();
			$this->importar_informacion_instancia();
			$this->generar_info_importacion();
			$this->actualizar_secuencias();
			$this->get_db()->cerrar_transaccion();
		} catch ( excepcion_toba $e ) {
			$this->get_db()->abortar_transaccion();
			$this->manejador_interface->error( 'Ha ocurrido un error durante la inicializacion de la instancia.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}

	/**
	* Inicializacion de instancias
	*/
	function crear_modelo_datos_toba()
	{	
		$this->crear_tablas();
		$this->cargar_datos_nucleo();
	}
	
	private function crear_tablas()
	{
		$this->manejador_interface->titulo('Creando tablas del sistema.');
		$directorio = nucleo::get_dir_ddl();
		$archivos = manejador_archivos::get_archivos_directorio( $directorio, '|.*\.sql|' );
		foreach( $archivos as $archivo ) {
			$this->manejador_interface->mensaje( $archivo );
			$this->get_db()->ejecutar_archivo( $archivo );
		}
	}
	
	private function cargar_datos_nucleo()
	{
		$this->manejador_interface->titulo('Cargando datos del nucleo.');
		$directorio = nucleo::get_dir_metadatos();
		$archivos = manejador_archivos::get_archivos_directorio( $directorio, '|.*\.sql|' );
		foreach( $archivos as $archivo ) {
			$this->manejador_interface->mensaje( $archivo );
			$this->get_db()->ejecutar_archivo( $archivo );
		}
	}

	/*
	*	Importa los proyectos asociados
	*/
	private function importar_proyectos()
	{
		foreach( $this->get_lista_proyectos() as $proyecto ) {
			$this->manejador_interface->titulo( "PROYECTO: $proyecto" );
			$proyecto = new proyecto( $this, $proyecto );
			$proyecto->set_manejador_interface( $this->manejador_interface );			
			$proyecto->importar();
		}	
	}
	
	/*
	* 	Importa la informacion perteneciente a la instancia
	*/
	private function importar_informacion_instancia()
	{
		$this->manejador_interface->titulo('Cargando datos de la instancia');
		$subdirs = manejador_archivos::get_subdirectorios( $this->get_dir() );
		foreach ( $subdirs as $dir ) {
			$this->manejador_interface->mensaje( $dir );
			$archivos = manejador_archivos::get_archivos_directorio( $dir , '|.*\.sql|' );
			foreach( $archivos as $archivo ) {
				$this->manejador_interface->mensaje( $archivo );
				$this->get_db()->ejecutar_archivo( $archivo );
			}
		}
	}
	
	/*
	*	Genera informacion descriptiva sobre la instancia creada
	*/
	private function generar_info_importacion()
	{
		$revision = revision_svn( toba_dir() );
		$sql = "INSERT INTO apex_revision ( revision ) VALUES ('$revision')";
		$this->get_db()->ejecutar( $sql );
	}
	
	/*
	*	Reestablece las secuencias del sistema
	*/
	private function actualizar_secuencias()
	{
		$this->manejador_interface->titulo('Importando SECUENCIAS');
		$id_grupo_de_desarrollo = instalacion::get_id_grupo_desarrollo();
		foreach ( secuencias::get_lista() as $seq => $datos ) {
			if ( is_null( $id_grupo_de_desarrollo ) ) {
				//Si no hay definido un grupo la secuencia se toma en forma normal
				$sql = "SELECT setval('$seq', max({$datos['campo']})) as nuevo FROM {$datos['tabla']}"; 
				$res = consultar_fuente($sql, 'instancia', null, true);
				$nuevo = $res[0]['nuevo'];
			} else {
				//Sino se toma utilizando los límites según el ID del grupo
				$lim_inf = self::cantidad_seq_grupo * $id_grupo_de_desarrollo;
				$lim_sup = self::cantidad_seq_grupo * ( $id_grupo_de_desarrollo + 1 );
				$sql_nuevo = "SELECT max({$datos['campo']}) as nuevo
							  FROM {$datos['tabla']}
							  WHERE	{$datos['campo']} BETWEEN $lim_inf AND $lim_sup";
				$res = consultar_fuente($sql_nuevo, 'instancia', null, true);
				$nuevo = $res[0]['nuevo'];
				//Si no hay un maximo, es el primero del grupo
				if ($nuevo == NULL) {
					$nuevo = $lim_inf;
				}
				$sql = "SELECT setval('$seq', $nuevo)
							FROM {$datos['tabla']}";
				consultar_fuente($sql, 'instancia');		
			}
			$this->manejador_interface->mensaje("SECUENCIA $seq: $nuevo");
		}	
	}

	//-----------------------------------------------------------
	//	ELIMINAR
	//-----------------------------------------------------------

	/*
	*	Elimina la instancia de la forma predefinida
	*/
	function eliminar()
	{
		//Por defecto se elimina la base.
		$this->eliminar_base();
	}

	/**
	* Eliminacion de la BASE de la instancia
	*/
	function eliminar_base()
	{
		try {
			$base = info_instancia::get_base();
			dba::desconectar('instancia');
			dba::borrar_base_datos( $base );
			$this->manejador_interface->mensaje("La base ha sido eliminada.");
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante la eliminacion de la BASE' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}

	/**
	* Eliminacion de las TABLAS de la instancia
	*/
	function eliminar_modelo()
	{
		try {
			$this->get_db()->abrir_transaccion();
			// Tablas
			$sql = sql_array_tablas_drop( catalogo_general::get_tablas() );
			$this->get_db()->ejecutar( $sql );
			// Secuencias
			$secuencias = array_keys( secuencias::get_lista() );
			$sql = sql_array_secuencias_drop( $secuencias );
			$this->get_db()->ejecutar( $sql );
			$this->get_db()->cerrar_transaccion();
			$this->manejador_interface->mensaje("El modelo ha sido eliminado.");
		} catch ( excepcion_toba $e ) {
			$this->get_db()->abortar_transaccion();
			$this->manejador_interface->error( 'Ha ocurrido un error durante la eliminacion de TABLAS de la instancia.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}

	//-----------------------------------------------------------
	//	Control de INSTANCIAS
	//-----------------------------------------------------------

	function dump_info_tablas()
	{
		// Esta el modelo cargado
		if ( ( ! dba::existe_base_datos( info_instancia::get_base() ) ) || ( ! $this->existe_modelo() ) ) {
			throw new excepcion_toba_modelo_preexiste("La instancia no esta inicializada");
		}
		$archivo = 'registros_' . $this->identificador;
		$clase = new clase_datos( $archivo );		
		$clase->agregar_metodo_datos('get_datos',$this->get_registros_tabla() );
		$path = toba_dir() .'/temp/'. $archivo . '.php';
		$clase->guardar( $path );
		//$this->manejador_interface->mensaje("Se creo el archivo '$path'");
	}
}
?>