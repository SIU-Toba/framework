<?
require_once('modelo/lib/elemento_modelo.php');
require_once('modelo/instalacion.php');
require_once('modelo/estructura_db/tablas_instancia.php');
require_once('modelo/estructura_db/catalogo_general.php');
require_once('modelo/estructura_db/secuencias.php');
require_once('nucleo/lib/manejador_archivos.php');
require_once('nucleo/lib/sincronizador_archivos.php');
require_once('nucleo/lib/reflexion/clase_datos.php');
/**
*	@todo
*		- Manipulacion del INI
*		- Falta un parametrizar en la instalacion si la base toba es independiente o adosada al negocio
*			( se eliminan las tablas o la base en la regeneracion? )
*/
class instancia extends elemento_modelo
{
	const dir_prefijo = 'i__';
	const info_instancia = 'instancia.ini';
	const prefijo_dir_proyecto = 'p__';
	const dir_datos_globales = 'global';
	const archivo_datos = 'datos.sql';
	const archivo_usuarios = 'usuarios.sql';
	const archivo_logs = 'logs.sql';
	const cantidad_seq_grupo = 1000000;
	private $instalacion;					// Referencia a la instalacion en la que esta metida la instancia
	private $identificador;					// Identificador de la instancia
	private $dir;							// Directorio raiz de la instancia
	private $ini_base;						// ID de la BASE donde reside la instancia
	private $ini_proyectos_vinculados;		// IDs de proyectos vinculados (a nivel FS)
	private $db = null;						// Referencia a la CONEXION con la DB de la instancia
	private $sincro_archivos;				// Sincronizador de archivos.
	
	function __construct( instalacion $instalacion, $identificador )
	{
		$this->identificador = $identificador;
		$this->instalacion = $instalacion;
		$this->dir = $this->instalacion->get_dir() . '/' . self::dir_prefijo . $this->identificador;
		if( ! is_dir( $this->dir ) ) {
			throw new excepcion_toba("INSTANCIA: La instancia '{$this->identificador}' es invalida. (la carpeta '{$this->dir}' no existe)");
		}
		//Solo se sincronizan los SQLs
		$this->cargar_info_ini();
		$this->sincro_archivos = new sincronizador_archivos( $this->dir, '|.*\.sql|' );
	}

	function cargar_info_ini()
	{
		$archivo_ini = $this->dir . '/' . self::info_instancia;
		if ( ! is_file( $archivo_ini ) ) {
			throw new excepcion_toba("INSTANCIA: La instancia '{$this->identificador}' es invalida. (El archivo de configuracion '$archivo_ini' no existe)");
		} else {
			//--- Levanto la CONFIGURACION de la instancia
			//  BASE
			$ini = parse_ini_file( $archivo_ini );
			if ( ! isset( $ini['base'] ) ) {
				throw new excepcion_toba("INSTANCIA: La instancia '{$this->identificador}' es invalida. (El archivo de configuracion '$archivo_ini' no posee una entrada 'base')");
			}
			$this->ini_base = $ini['base'];
			// PROYECTOS
			if ( ! isset( $ini['proyectos'] ) ) {
				throw new excepcion_toba("INSTANCIA: La instancia '{$this->identificador}' es invalida. (El archivo de configuracion '$archivo_ini' no posee una entrada 'proyectos')");
			}
			$lista_proyectos = explode(',', $ini['proyectos'] );
			$lista_proyectos = array_map('trim',$lista_proyectos);
			if ( count( $lista_proyectos ) == 0 ) {
				throw new excepcion_toba("INSTANCIA: La instancia '{$this->identificador}' es invalida. (El archivo de configuracion '$archivo_ini' no posee proyectos asociados. La entrada 'proyectos' debe estar constituida por una lista de proyectos separados por comas)");
			}
			if ( ! in_array( 'toba', $lista_proyectos ) ) {
				throw new excepcion_toba("INSTANCIA: La instancia '{$this->identificador}' es invalida. (El archivo de configuracion '$archivo_ini' no posee asociado el proyecto 'toba'.)");
			}
			$this->ini_proyectos_vinculados = $lista_proyectos;
		}
	}

	//-----------------------------------------------------------
	//	Manejo de subcomponentes
	//-----------------------------------------------------------

	/**
	*	Devuelve un array con los objetos PROYECTO cargados
	*/
	function get_proyectos()
	{
		$proyectos = array();
		foreach( $this->get_lista_proyectos_vinculados() as $proyecto ) {
			$proyectos[$proyecto] = new proyecto( $this, $proyecto );
			$proyectos[$proyecto]->set_manejador_interface( $this->manejador_interface );			
		}
		return $proyectos;
	}
	
	//-----------------------------------------------------------
	//	Relacion con la base de datos donde reside la instancia
	//-----------------------------------------------------------

	/**
	*	Creacion de la conexion con la DB donde reside la instancia
	*/
	function get_db()
	{
		if ( ! isset( $this->db ) ) {
			$this->db = $this->instalacion->conectar_base( $this->ini_base );
		}
		return $this->db;
	}

	/**
	*	Eliminaciond e la conexion con la instancia
	*/
	function desconectar_db()
	{
		$this->get_db()->destruir();
		unset( $this->db );
	}

	/**
	*	Recuperacion de los parametros de la DB donde reside la instancia
	*/
	function get_parametros_db()
	{
		return $this->instalacion->get_parametros_base( $this->ini_base );
	}

	//-----------------------------------------------------------
	//	Informacion BASICA
	//-----------------------------------------------------------

	function get_id()
	{
		return $this->identificador;
	}

	function get_dir()
	{
		return $this->dir;		
	}
	
	function get_version_actual()
	{
		$sql = "SELECT version FROM apex_instancia WHERE instancia='".$this->get_id()."'";
		$rs = $this->get_db()->consultar($sql);
		if (empty($rs)) {
			return null;	
		} else {
			return new version_toba($rs[0]['version']);	
		}
	}

	function get_lista_proyectos_vinculados()
	{
		return $this->ini_proyectos_vinculados;
	}
	
	function existe_proyecto_vinculado( $proyecto )
	{
		return in_array( $proyecto, $this->ini_proyectos_vinculados );
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

	function existen_metadatos_proyecto( $proyecto )
	{
		$sql = "SELECT 1 FROM apex_proyecto WHERE proyecto = '$proyecto';";
		$datos = $this->get_db()->consultar( $sql );
		if ( count( $datos ) > 0 ) {
			return true;
		} else {
			return false;
		}
	}
		
	//-----------------------------------------------------------
	//	Manipulacion de la DEFINICION
	//-----------------------------------------------------------

	function vincular_proyecto( $proyecto )
	{
		if ( proyecto::existe( $proyecto ) ) {
			$clase = $this->get_clase_datos();
			$datos = $clase->get_datos_metodo( 'get_lista_proyectos' );
			if ( ! in_array( $proyecto, $datos ) ) {
				$datos[] = $proyecto;
				$clase->set_datos_metodo( 'get_lista_proyectos', $datos );
				$clase->guardar();
			}
		} else {
			throw new excepcion_toba("El proyecto '$proyecto' no existe.");
		}
	}
	
	function desvincular_proyecto( $proyecto )
	{
		$clase = $this->get_clase_datos();
		$datos = $clase->get_datos_metodo( 'get_lista_proyectos' );
		if ( in_array( $proyecto, $datos ) ) {
			// EL proyecto recien creado no aparece en 'get_lista_proyectos' hasta el final de request
			// Elimino el LINK
			$datos = array_diff( $datos, array( $proyecto ) );
			$clase->set_datos_metodo( 'get_lista_proyectos', $datos );
			$clase->guardar();
			// Elimino la carpeta de METADATOS de la instancia especificos del PROYECTO
			$dir_proyecto = $this->get_dir() . '/' . self::prefijo_dir_proyecto . $proyecto;
			if ( is_dir( $dir_proyecto ) ) { //Fue exportado?
				manejador_archivos::eliminar_directorio( $dir_proyecto );			
			}
		}
	}

	private function get_clase_datos()
	{
		$clase = instalacion::instancia_info;
		$path = $this->dir . '/' . instalacion::instancia_info . '.php';
		return new clase_datos( $clase, $path );
	}

	//-----------------------------------------------------------
	//	EXPORTAR datos de la DB
	//-----------------------------------------------------------

	/**
	* Exportacion de TODO lo que hay en una instancia
	*/
	function exportar()
	{
		foreach( $this->get_lista_proyectos_vinculados() as $proyecto ) {
			$proyecto = new proyecto( $this, $proyecto );
			$proyecto->set_manejador_interface( $this->manejador_interface );			
			$proyecto->exportar();
		}	
		$this->exportar_local();
	}	

	/**
	* Exportacion de la informacion correspondiente a la instancia (no proyectos)
	*/
	function exportar_local()
	{
		try {
			$this->manejador_interface->titulo( "INSTANCIA" );
			$this->exportar_global();
			$this->exportar_proyectos();
			$this->sincronizar_archivos();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante la exportacion.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}

	private function sincronizar_archivos()
	{
		$this->manejador_interface->titulo( "SINCRONIZAR ARCHIVOS" );
		$obs = $this->sincro_archivos->sincronizar();
		$this->manejador_interface->lista( $obs, 'Observaciones' );
	}	
	/*
	*	Exportar informacion GLOBAL de la instancia
	*/
	private function exportar_global()
	{
		$dir_global = $this->get_dir() . '/' . self::dir_datos_globales;
		manejador_archivos::crear_arbol_directorios( $dir_global );
		$this->exportar_tablas_global( 'get_lista_global', $dir_global .'/' . self::archivo_datos, 'GLOBAL' );	
		$this->exportar_tablas_global( 'get_lista_global_usuario', $dir_global .'/' . self::archivo_usuarios, 'USUARIOS' );	
		$this->exportar_tablas_global( 'get_lista_global_log', $dir_global .'/' . self::archivo_logs, 'LOGS' );	
	}

	private function exportar_tablas_global( $metodo_lista_tablas, $path, $texto )
	{
		$contenido = "";
		foreach ( tablas_instancia::$metodo_lista_tablas() as $tabla ) {
			$this->manejador_interface->mensaje( "tabla $texto  --  $tabla" );
			$definicion = tablas_instancia::$tabla();
			//Genero el SQL
			$sql = "SELECT " . implode(', ', $definicion['columnas']) .
					" FROM $tabla " .
					" ORDER BY {$definicion['dump_order_by']} ;\n";
			//$this->manejador_interface->mensaje( $sql );
			$datos = $this->get_db()->consultar($sql);
			if ( count( $datos ) > 1 ) { //SI los registros de la tabla son mas de 1, ordeno.
				$columnas_orden = array_map('trim', explode(',',$definicion['dump_order_by']) );
				$datos = rs_ordenar_por_columnas( $datos, $columnas_orden );
			}			
			for ( $a = 0; $a < count( $datos ) ; $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
		}
		if ( trim( $contenido ) != '' ) {
			$this->guardar_archivo( $path  , $contenido );			
		}
	}

	/*
	*	Exportar informacion de PROYECTOS de la instancia
	*/
	private function exportar_proyectos()
	{
		foreach( $this->get_lista_proyectos_vinculados() as $proyecto ) {
			$this->manejador_interface->titulo( "PROYECTO $proyecto" );
			$dir_proyecto = $this->get_dir() . '/' . self::prefijo_dir_proyecto . $proyecto;
			manejador_archivos::crear_arbol_directorios( $dir_proyecto );
			$this->exportar_tablas_proyecto( 'get_lista_proyecto', $dir_proyecto .'/' . self::archivo_datos, $proyecto, 'GLOBAL' );	
			$this->exportar_tablas_proyecto( 'get_lista_proyecto_log', $dir_proyecto .'/' . self::archivo_logs, $proyecto, 'LOG' );	
			$this->exportar_tablas_proyecto( 'get_lista_proyecto_usuario', $dir_proyecto .'/' . self::archivo_usuarios, $proyecto, 'USUARIO' );	
		}
	}

	private function exportar_tablas_proyecto( $metodo_lista_tablas, $nombre_archivo, $proyecto, $texto )
	{
		$contenido = "";
		foreach ( tablas_instancia::$metodo_lista_tablas() as $tabla ) {
			$this->manejador_interface->mensaje( "tabla $texto  --  $tabla" );
			$definicion = tablas_instancia::$tabla();
			//Genero el SQL
			if( isset($definicion['dump_where']) && ( trim($definicion['dump_where']) != '') ) {
       			$w = stripslashes($definicion['dump_where']);
       			$where = ereg_replace("%%",$proyecto, $w);
            }else{
       			$where = " ( proyecto = '$proyecto')";
			}
			$from = "$tabla dd";
			if( isset($definicion['dump_from']) && ( trim($definicion['dump_from']) != '') ) {
       			$from .= ", ".stripslashes($definicion['dump_from']);
            }
            $columnas = array();
            foreach ($definicion['columnas'] as $columna ) {
            	$columnas[] = "dd.$columna";
            }
			$sql = "SELECT " . implode(', ',$columnas) .
					" FROM $from " .
					" WHERE $where " .
					" ORDER BY {$definicion['dump_order_by']} ;\n";
			//$this->manejador_interface->mensaje( $sql );
			$datos = $this->get_db()->consultar($sql);
			if ( count( $datos ) > 1 ) { //SI los registros de la tabla son mas de 1, ordeno.
				$columnas_orden = array_map('trim', explode(',',$definicion['dump_order_by']) );
				$datos = rs_ordenar_por_columnas( $datos, $columnas_orden );
			}			
			for ( $a = 0; $a < count( $datos ) ; $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
		}
		if ( trim( $contenido ) != '' ) {
			$this->guardar_archivo( $nombre_archivo, $contenido );			
		}
	}

	private function guardar_archivo( $archivo, $contenido )
	{
		file_put_contents( $archivo, $contenido );
		$this->sincro_archivos->agregar_archivo( $archivo );
	}
	
	//-----------------------------------------------------------
	//	CARGAR modelo en la DB
	//-----------------------------------------------------------

	/**
	* Importacion completa de una instancia
	*/
	function cargar( $forzar_carga = false )
	{
		// Existe la base?
		if ( ! $this->instalacion->existe_base_datos( $this->ini_base ) ) {
			$this->instalacion->crear_base_datos( $this->ini_base );
		}
		// Esta el modelo cargado
		if ( $this->existe_modelo() ) {
			if ( $forzar_carga ) {
				$this->eliminar();
				$this->instalacion->crear_base_datos( $this->ini_base );
			} else {
				throw new excepcion_toba_modelo_preexiste("INSTANCIA: Ya existe un modelo cargado en la base de datos.");
			}
		}
		//Inicio el proceso de carga
		try {
			$this->get_db()->abrir_transaccion();
			$this->get_db()->retrazar_constraints();
			$this->crear_modelo_datos_toba();
			$this->cargar_proyectos();
			$this->cargar_informacion_instancia();
			$this->generar_info_carga();
			$this->actualizar_secuencias();
			$this->actualizar_version();
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
		sort($archivos);
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
	private function cargar_proyectos()
	{
		foreach( $this->get_lista_proyectos_vinculados() as $proyecto ) {
			$this->manejador_interface->titulo( "PROYECTO: $proyecto" );
			$proyecto = new proyecto( $this, $proyecto );
			$proyecto->set_manejador_interface( $this->manejador_interface );			
			$proyecto->cargar();
		}	
	}
	
	/*
	* 	Importa la informacion perteneciente a la instancia
	*/
	private function cargar_informacion_instancia()
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

	function cargar_informacion_instancia_proyecto( $proyecto )
	{
		$directorio = $this->get_dir() . '/' . self::prefijo_dir_proyecto . $proyecto;
		$archivos = manejador_archivos::get_archivos_directorio( $directorio , '|.*\.sql|' );
		foreach( $archivos as $archivo ) {
			$this->get_db()->ejecutar_archivo( $archivo );
		}
	}
		
	/*
	*	Genera informacion descriptiva sobre la instancia creada
	*/
	private function generar_info_carga()
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
		$id_grupo_de_desarrollo = $this->instalacion->get_id_grupo_desarrollo();
		foreach ( secuencias::get_lista() as $seq => $datos ) {
			if ( is_null( $id_grupo_de_desarrollo ) ) {
				//Si no hay definido un grupo la secuencia se toma en forma normal
				$sql = "SELECT setval('$seq', max({$datos['campo']})) as nuevo FROM {$datos['tabla']}"; 
				$res = $this->get_db()->consultar($sql, null, true);
				$nuevo = $res[0]['nuevo'];
			} else {
				//Sino se toma utilizando los lï¿½ites segn el ID del grupo
				$lim_inf = self::cantidad_seq_grupo * $id_grupo_de_desarrollo;
				$lim_sup = self::cantidad_seq_grupo * ( $id_grupo_de_desarrollo + 1 );
				$sql_nuevo = "SELECT max({$datos['campo']}) as nuevo
							  FROM {$datos['tabla']}
							  WHERE	{$datos['campo']} BETWEEN $lim_inf AND $lim_sup";
				$res = $this->get_db()->consultar($sql_nuevo, null, true);
				$nuevo = $res[0]['nuevo'];
				//Si no hay un maximo, es el primero del grupo
				if ($nuevo == NULL) {
					$nuevo = $lim_inf;
				}
				$sql = "SELECT setval('$seq', $nuevo)
							FROM {$datos['tabla']}";
				$this->get_db()->consultar( $sql, null );	
			}
			$this->manejador_interface->mensaje("SECUENCIA $seq: $nuevo");
		}	
	}
	
	private function actualizar_version()
	{
		$nueva = instalacion::get_version_actual()->__toString();
		$vieja = $this->get_version_actual();
		if (!isset($vieja)) {
			//Caso especial cuando es la primera vez que se carga la instancia con el esquema nuevo
			$sql = "INSERT INTO apex_instancia (instancia, version) 
					VALUES ('{$this->identificador}', '$nueva')";
		} else {
			$sql = "UPDATE apex_instancia SET version='$nueva' WHERE instancia='{$this->identificador}'";
		}
		$this->manejador_interface->titulo("Actualizando número de versión: $nueva");
		$this->get_db()->ejecutar($sql);
	}

	//-----------------------------------------------------------
	//	ELIMINAR una DB
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
			$this->desconectar_db();
			$this->instalacion->borrar_base_datos( $this->ini_base );
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
	//	Informacion sobre METADATOS
	//-----------------------------------------------------------

	function get_lista_usuarios()
	{
		$sql = "SELECT usuario, nombre FROM apex_usuario";	
		return $this->get_db()->consultar( $sql );
	}

	function get_registros_tablas()
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
	//	Manipulacion de METADATOS
	//-----------------------------------------------------------

	function agregar_usuario( $usuario, $nombre, $clave )
	{
		$sql = "INSERT INTO apex_usuario ( usuario, nombre, autentificacion, clave )
				VALUES ('$usuario', '$nombre', 'md5', '. md5($clave) .')";
		return $this->get_db()->ejecutar( $sql );
	}
	
	function eliminar_usuario( $usuario )
	{
		$sql = "DELETE FROM apex_usuario WHERE usuario = '$usuario'";	
		return $this->get_db()->ejecutar( $sql );
	}

	//-------------------------------------------------------------
	//-- CREACION de INSTANCIAS
	//-------------------------------------------------------------

	/**
	* Agrega una instancia
	*/
	static function crear_instancia( $nombre, $base, $lista_proyectos )
	{
		//Creo la carpeta
		if( ! self::existe_carpeta_instancia( $nombre ) ) {
			mkdir( self::dir_instancia( $nombre ) );
		}
		//Creo la clase que proporciona informacion sobre la instancia
		$ini = new ini();
		$ini->agregar_titulo("Configuracion de la INSTANCIA");
		$ini->agregar_directiva( 'base', $base );
		$ini->agregar_directiva( 'proyectos', implode(', ', $lista_proyectos) );
		$ini->guardar( self::dir_instancia( $nombre ) . '/' . instancia::info_instancia );
	}

	static function dir_instancia( $nombre )
	{
		return instalacion::dir_base() . '/' . self::dir_prefijo . $nombre;
	}

	static function existe_carpeta_instancia( $nombre )
	{
		return is_dir( self::dir_instancia( $nombre) );
	}
	
	/**
	* Devuelve la lista de las INSTANCIAS
	*/
	static function get_lista()
	{
		$dirs = array();
		try {
			$temp = manejador_archivos::get_subdirectorios( instalacion::dir_base() , '|^'.self::dir_prefijo.'|' );
			foreach ( $temp as $dir ) {
				$temp_dir = explode( self::dir_prefijo, $dir );
				$dirs[] = $temp_dir[1];
			}
		} catch ( excepcion_toba $e ) {
			// No existe la instalacion
		}
		return $dirs;
	}

	//-----------------------------------------------------------
	//	Conversion desde 0.8.3
	//-----------------------------------------------------------
	
	/**
	*	Modificaciones en el modelo de datos introducidos en la version 0.9.0
	*		No puede ir en la conversion comun, las conversiones supenen el nuevo modelo.
	*		No puede ir en la instalacion porque se puede ejecutar mas de una vez.
	*/
	function cambio_modelo_datos_090()
	{
		try {
			$this->get_db()->abrir_transaccion();
			// Modificaciones en TABLAS
			$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN salida_impr_html_c varchar(1);";
			$sql[] = "ALTER TABLE apex_proyecto ADD COLUMN salida_impr_html_a varchar(1);";
			$sql[] = "ALTER TABLE apex_objeto_dependencias ADD COLUMN orden smallint;";
			$sql[] = "ALTER TABLE apex_usuario ADD COLUMN autentificacion varchar(10);";
			$sql[] = "ALTER TABLE apex_usuario ADD COLUMN clave2 varchar(128);";
			$sql[] = "UPDATE apex_usuario SET clave2 = clave;";
			$sql[] = "ALTER TABLE apex_usuario DROP COLUMN clave;";
			$sql[] = "ALTER TABLE apex_usuario ADD COLUMN clave varchar(128);";
			$sql[] = "UPDATE apex_usuario SET clave = clave2;";
			$sql[] = "ALTER TABLE apex_usuario DROP COLUMN clave2;";
			$sql[] = "INSERT INTO apex_solicitud_tipo (solicitud_tipo, descripcion, descripcion_corta, icono) VALUES ('web', 'Solicitud WEB', 'Solicitud WEB', 'solic_browser.gif');";
			$this->get_db()->ejecutar( $sql );	
			// Secciones nuevas del MODELO
			$this->get_db()->ejecutar_archivo( toba_dir() . '/php/modelo/ddl/pgsql_a22_permisos.sql' );
			$this->get_db()->cerrar_transaccion();
		} catch ( excepcion_toba $e ) {
			$this->get_db()->abortar_transaccion();
			$this->manejador_interface->error( 'Ha ocurrido un error durante la inicializacion de la instancia.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}	
}
?>
