<?php
/**
*	@todo
*		- Falta un parametrizar en el ini de la instancia si la base toba es independiente o adosada al negocio
*			( se eliminan las tablas o la base en la regeneracion? )
*/
class toba_modelo_instancia extends toba_modelo_elemento
{
	const dir_prefijo = 'i__';
	const toba_instancia = 'instancia.ini';
	const toba_instancia_titulo = 'Configuracion de la INSTANCIA';
	const prefijo_dir_proyecto = 'p__';
	const dir_datos_globales = 'global';
	const archivo_datos = 'datos.sql';
	const archivo_usuarios = 'usuarios.sql';
	const archivo_logs = 'logs.sql';
	const cantidad_seq_grupo = 1000000;
	protected $proyectos_ya_migrados = array('toba_testing', 'toba_referencia', 'toba_editor');	
	private $instalacion;					// Referencia a la instalacion en la que esta metida la instancia
	private $identificador;					// Identificador de la instancia
	private $dir;							// Directorio raiz de la instancia
	private $ini_base;						// ID de la BASE donde reside la instancia
	private $ini_proyectos_vinculados;		// IDs de proyectos vinculados (a nivel FS)
	private $db = null;						// Referencia a la CONEXION con la DB de la instancia
	private $sincro_archivos;				// Sincronizador de archivos.
	private $nombre_log;					// Nombre que llevan los archivos de LOG
	private $datos_ini;
	
	function __construct( toba_modelo_instalacion $instalacion, $identificador )
	{
		$this->identificador = $identificador;
		$this->instalacion = $instalacion;
		$this->dir = $this->instalacion->get_dir() . '/' . self::dir_prefijo . $this->identificador;
		if( ! is_dir( $this->dir ) ) {
			throw new toba_error("INSTANCIA: La instancia '{$this->identificador}' es invalida. (la carpeta '{$this->dir}' no existe)");
		}
		//Solo se sincronizan los SQLs
		$this->cargar_info_ini();
		$this->nombre_log = "grupo_" . $this->instalacion->get_id_grupo_desarrollo() . ".". self::archivo_logs;
		toba_logger::instancia()->debug('INSTANCIA "'.$this->identificador.'"');		
	}

	function get_sincronizador()
	{
		if ( ! isset( $this->sincro_archivos ) ) {
			$regex = "#datos.sql|$this->nombre_log|usuarios.sql#"; // No hay que interferir con archivos de otras celulas
			$this->sincro_archivos = new toba_sincronizador_archivos( $this->dir, $regex );
		}
		return $this->sincro_archivos;
	}
	
	/**
	 * @returns toba_modelo_instalacion
	 */
	function get_instalacion()
	{
		return $this->instalacion;
	}

	function cargar_info_ini()
	{
		$archivo_ini = $this->dir . '/' . self::toba_instancia;
		if ( ! is_file( $archivo_ini ) ) {
			throw new toba_error("INSTANCIA: La instancia '{$this->identificador}' es invalida. (El archivo de configuracion '$archivo_ini' no existe)");
		} else {
			//--- Levanto la CONFIGURACION de la instancia
			//  BASE
			$this->datos_ini = parse_ini_file( $archivo_ini, true );
			toba_logger::instancia()->debug("Parametros instancia {$this->identificador}: ".var_export($this->datos_ini, true));
			if ( ! isset( $this->datos_ini['base'] ) ) {
				throw new toba_error("INSTANCIA: La instancia '{$this->identificador}' es invalida. (El archivo de configuracion '$archivo_ini' no posee una entrada 'base')");
			}
			$this->ini_base = $this->datos_ini['base'];
			// PROYECTOS
			if ( ! isset( $this->datos_ini['proyectos'] ) ) {
				throw new toba_error("INSTANCIA: La instancia '{$this->identificador}' es invalida. (El archivo de configuracion '$archivo_ini' no posee una entrada 'proyectos')");
			}
			$lista_proyectos = array();
			if (trim($this->datos_ini['proyectos']) != '') {
				$lista_proyectos = explode(',', $this->datos_ini['proyectos'] );
				$lista_proyectos = array_map('trim',$lista_proyectos);
				if ( count( $lista_proyectos ) == 0 ) {
					throw new toba_error("INSTANCIA: La instancia '{$this->identificador}' es invalida. (El archivo de configuracion '$archivo_ini' no posee proyectos asociados. La entrada 'proyectos' debe estar constituida por una lista de proyectos separados por comas)");
				}
			}
			$this->ini_proyectos_vinculados = $lista_proyectos;
		}
	}
	
	//-----------------------------------------------------------
	//	Manejo de subcomponentes
	//-----------------------------------------------------------

	/**
	 * @return toba_modelo_proyecto
	 */
	function get_proyecto($id)
	{
		return toba_modelo_catalogo::instanciacion()->get_proyecto( $this->get_id(), 
										$id, $this->manejador_interface);
				
	}

	//-----------------------------------------------------------
	//	Relacion con la base de datos donde reside la instancia
	//-----------------------------------------------------------

	/**
	*	Creacion de la conexion con la DB donde reside la instancia
	* @return toba_db
	*/
	function get_db($forzar_recarga=false)
	{
		if ($forzar_recarga || ! isset( $this->db ) ) {
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
	
	/**
	 * Retorna el id de la base que representa la instancia
	 */
	function get_ini_base()
	{
 		return $this->ini_base;
	}
	
	function get_path_proyecto($proyecto)
	{
		if (isset($this->datos_ini[$proyecto]['path'])) {
			$path = $this->datos_ini[$proyecto]['path'];
			if (substr($path, 0, 1) == '.') { 
				return realpath(toba_dir().'/'.$path); 
			} else { 
				return $path; 
			}		
		} else {
			$listado = toba_modelo_proyecto::get_lista();
			foreach ($listado as $path => $id) {
				if ($proyecto == $id) {
					return toba_dir() . "/proyectos/" . $path;
				}
			}
			return toba_dir() . "/proyectos/" . $proyecto;
		}
	}
	
	function get_url_proyecto($proyecto)
	{
		if (isset($this->datos_ini[$proyecto]['url'])) {
			return $this->datos_ini[$proyecto]['url'];
		}		
	}
	
	function set_url_proyecto($id_proyecto, $url)
	{
		$ini = $this->get_ini();
		if ($ini->existe_entrada($id_proyecto)) {
			$conf_proy = $ini->get_datos_entrada($id_proyecto);
		} else {
			$conf_proy = array();
		}
		$conf_proy['url'] = $url;
		$ini->agregar_entrada($id_proyecto, $conf_proy);
		$ini->guardar();
		toba_logger::instancia()->debug("Cambiando la url del proyecto '$id_proyecto' a '$url'");
		// Recargo la inicializacion de la instancia
		$this->cargar_info_ini();
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
			$sql = "SELECT 1 FROM apex_usuario;";
			$db = $this->get_db();
			@$db->consultar( $sql );
			return true;
		} catch ( toba_error_db $e ) {
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

	function vincular_proyecto($proyecto, $path=null, $url=null)
	{
		if ( isset($path) || toba_modelo_proyecto::existe($proyecto) ) {
			$ini = $this->get_ini();
			$datos = explode(',',$ini->get_datos_entrada( 'proyectos'));
			$datos = array_map('trim',$datos);
			$datos_ini = array();
			if ( ! in_array( $proyecto, $datos ) ) {
				$datos[] = $proyecto;
				$ini->set_datos_entrada( 'proyectos', implode(', ', $datos) );
				if (isset($path)) {
					 $datos_ini['path'] = $path;
				}
				if (isset($url)) {
					 $datos_ini['url'] = $url;
				}				
				if (! empty($datos_ini)) {
					$ini->agregar_entrada($proyecto, $datos_ini);
				}
				$ini->guardar();
			}
			toba_logger::instancia()->debug("Vinculado el proyecto '$proyecto' a la instancia");
			// Recargo la inicializacion de la instancia
			$this->cargar_info_ini();
		} else {
			throw new toba_error("El proyecto '$proyecto' no existe.");
		}
	}

	/**
	 * Brinda una nueva lista de proyectos vinculados a la instancia
	 * @param array $id_proyectos
	 */
	function set_proyectos_vinculados($id_proyectos)
	{
		$ini = $this->get_ini();
		$ini->set_datos_entrada('proyectos', implode(', ', $id_proyectos));		
		$ini->guardar();		
		// Recargo la inicializacion de la instancia
		$this->cargar_info_ini();
	}
	
	function desvincular_proyecto( $proyecto )
	{
		$ini = $this->get_ini();
		$datos =  explode(',',$ini->get_datos_entrada( 'proyectos'));
		$datos = array_map('trim',$datos);
		if ( in_array( $proyecto, $datos ) ) {
			$datos = array_diff( $datos, array( $proyecto ) );
			$ini->set_datos_entrada( 'proyectos', implode(', ', $datos) );
			// Elimino la carpeta de METADATOS de la instancia especificos del PROYECTO
			$dir_proyecto = $this->get_dir() . '/' . self::prefijo_dir_proyecto . $proyecto;
			if ( is_dir( $dir_proyecto ) ) {
				toba_manejador_archivos::eliminar_directorio( $dir_proyecto );			
			}
			toba_logger::instancia()->debug("Desvinculado el proyecto '$proyecto' de la instancia");
		}
		if ($ini->existe_entrada($proyecto)) {
			$ini->eliminar_entrada($proyecto);;
		}
		$ini->guardar();		
		// Recargo la inicializacion de la instancia
		$this->cargar_info_ini();
	}

	/**
	 * Elimina toda relacion del proyecto con la instancia (lo desvicula, quita la config, metadatos, alias)
	 * @param string $proy_id
	 * @param boolean $desinstalar Ejecuta el proyecto de desintalacion propio del proyecto (ej. eliminar base de negocios)
	 */
	function eliminar_proyecto( $proy_id, $desinstalar=false)
	{
		$proyecto = $this->get_proyecto($proy_id);
		$proyecto->despublicar();
		if ($desinstalar) {
			//--- Opcionalmente borra los datos propios
			$proyecto->desinstalar();
		}
		$proyecto->eliminar_autonomo();
		$this->desvincular_proyecto($proy_id);				
	}
	
	/**
	 * @return toba_ini
	 */
	function get_ini()
	{
		$ini = new toba_ini( $this->dir . '/' . self::toba_instancia );
		$ini->agregar_titulo( self::toba_instancia_titulo );
		return $ini;
	}

	//-----------------------------------------------------------
	//	EXPORTAR datos de la DB
	//-----------------------------------------------------------

	/**
	* Exportacion de TODO lo que hay en una instancia
	*/
	function exportar($excluir=array())
	{
		foreach( $this->get_lista_proyectos_vinculados() as $id_proyecto ) {
			if ($id_proyecto != 'toba') {
				if (! in_array($id_proyecto, $excluir)) {
					$proyecto = $this->get_proyecto($id_proyecto);
					$proyecto->exportar();
				}
			}
		}	
		$this->exportar_local();
	}	

	/**
	* Exportacion de la informacion correspondiente a la instancia (no proyectos)
	*/
	function exportar_local()
	{
		$this->manejador_interface->titulo( "Exportación local de la instancia '{$this->get_id()}'" );
		$this->exportar_global();
		$this->exportar_proyectos();
		$this->sincronizar_archivos();
	}

	private function sincronizar_archivos()
	{
		//$this->manejador_interface->titulo( "SINCRONIZAR ARCHIVOS" );
		$obs = $this->get_sincronizador()->sincronizar();
		toba_logger::instancia()->debug("Observaciones de sincronizacion: ".implode(', ', $obs));
		$this->manejador_interface->lista( $obs, 'Observaciones' );
	}	
	
	/*
	*	Exportar informacion GLOBAL de la instancia
	*/
	private function exportar_global()
	{
		$this->manejador_interface->mensaje("Exportando datos globales", false);		
		$dir_global = $this->get_dir() . '/' . self::dir_datos_globales;
		toba_manejador_archivos::crear_arbol_directorios( $dir_global );
		$this->exportar_tablas_global( 'get_lista_global', $dir_global .'/' . self::archivo_datos, 'GLOBAL' );	
		$this->exportar_tablas_global( 'get_lista_global_usuario', $dir_global .'/' . self::archivo_usuarios, 'USUARIOS' );	
		$this->exportar_tablas_global( 'get_lista_global_log', $dir_global .'/'. $this->nombre_log, 'LOGS' );
		$this->manejador_interface->progreso_fin();
	}

	private function exportar_tablas_global( $metodo_lista_tablas, $path, $texto )
	{
		$contenido = "";
		foreach ( toba_db_tablas_instancia::$metodo_lista_tablas() as $tabla ) {
			$definicion = toba_db_tablas_instancia::$tabla();
			//Genero el SQL
			$sql = "SELECT " . implode(', ', $definicion['columnas']) .
					" FROM $tabla " .
					" ORDER BY {$definicion['dump_order_by']} ;\n";
			//$this->manejador_interface->mensaje( $sql );
			$datos = $this->get_db()->consultar($sql);
			toba_logger::instancia()->debug("Tabla $texto  --  $tabla (".count($datos).' reg.)');
			if ( count( $datos ) > 1 ) { //SI los registros de la tabla son mas de 1, ordeno.
				$columnas_orden = array_map('trim', explode(',',$definicion['dump_order_by']) );
				$datos = rs_ordenar_por_columnas( $datos, $columnas_orden );
			}			
			for ( $a = 0; $a < count( $datos ) ; $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
			$this->manejador_interface->progreso_avanzar();
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
			$this->manejador_interface->mensaje("Exportando proyecto $proyecto", false);
			toba_logger::instancia()->debug("Exportando local PROYECTO $proyecto");						
			$dir_proyecto = $this->get_dir() . '/' . self::prefijo_dir_proyecto . $proyecto;
			toba_manejador_archivos::crear_arbol_directorios( $dir_proyecto );
			$this->exportar_tablas_proyecto( 'get_lista_proyecto', $dir_proyecto .'/' . self::archivo_datos, $proyecto, 'GLOBAL' );	
			$this->exportar_tablas_proyecto( 'get_lista_proyecto_usuario', $dir_proyecto .'/' . self::archivo_usuarios, $proyecto, 'USUARIO' );	
			$this->exportar_tablas_proyecto( 'get_lista_proyecto_log', $dir_proyecto .'/' . $this->nombre_log, $proyecto, 'LOG' );	
			$this->manejador_interface->progreso_fin();
		}
	}

	private function exportar_tablas_proyecto( $metodo_lista_tablas, $nombre_archivo, $proyecto, $texto )
	{
		$contenido = "";
		foreach ( toba_db_tablas_instancia::$metodo_lista_tablas() as $tabla ) {
			$definicion = toba_db_tablas_instancia::$tabla();
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
			toba_logger::instancia()->debug("Tabla $texto  --  $tabla (".count($datos).' reg.)');
			if ( count( $datos ) > 1 ) { //SI los registros de la tabla son mas de 1, ordeno.
				$columnas_orden = array_map('trim', explode(',',$definicion['dump_order_by']) );
				$datos = rs_ordenar_por_columnas( $datos, $columnas_orden );
			}			
			for ( $a = 0; $a < count( $datos ) ; $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
			$this->manejador_interface->progreso_avanzar();
		}
		if ( trim( $contenido ) != '' ) {
			$this->guardar_archivo( $nombre_archivo, $contenido );			
		}
	}

	private function guardar_archivo( $archivo, $contenido )
	{
		file_put_contents( $archivo, $contenido );
		$this->get_sincronizador()->agregar_archivo( $archivo );
	}
	
	//-----------------------------------------------------------
	//	CARGAR modelo en la DB
	//-----------------------------------------------------------

	/**
	* Importacion completa de una instancia
	*/
	function cargar( $forzar_carga = false )
	{
		$this->manejador_interface->titulo('Creación de la instancia');		
		// Existe la base?
		if ( ! $this->instalacion->existe_base_datos( $this->ini_base ) ) {
			$this->manejador_interface->mensaje("Creando base '{$this->ini_base}'...", false);
			$this->instalacion->crear_base_datos( $this->ini_base );
			$this->manejador_interface->progreso_fin();
		}
		// Esta el modelo cargado
		if ( $this->existe_modelo() ) {
			if ( $forzar_carga ) {
				$this->eliminar();
				$this->instalacion->crear_base_datos( $this->ini_base );
			} else {
				throw new toba_error_modelo_preexiste("INSTANCIA: Ya existe un modelo cargado en la base de datos.");
			}
		}
		//Inicio el proceso de carga
		try {
			$this->get_db()->abrir_transaccion();
			$this->get_db()->retrazar_constraints();
			$this->crear_modelo_datos_toba();
			$this->cargar_proyectos();
			$this->manejador_interface->enter();
			$this->cargar_informacion_instancia();
			$this->generar_info_carga();
			$this->actualizar_secuencias();
			$this->set_version(toba_modelo_instalacion::get_version_actual());
			$this->get_db()->cerrar_transaccion();
		} catch ( toba_error $e ) {
			$this->get_db()->abortar_transaccion();
			throw $e;
		}
	}

	function cargar_tablas_minimas($forzar_carga = false)
	{
		$this->manejador_interface->titulo('Creación de una instancia MINIMA');		
		// Existe la base?
		if ( ! $this->instalacion->existe_base_datos( $this->ini_base ) ) {
			$this->manejador_interface->mensaje("Creando base '{$this->ini_base}'...", false);
			$this->instalacion->crear_base_datos( $this->ini_base );
			$this->manejador_interface->progreso_fin();
		}
		// Esta el modelo cargado
		if ( $this->existe_modelo() ) {
			if ( $forzar_carga ) {
				$this->eliminar_tablas_minimas();
			} else {
				throw new toba_error_modelo_preexiste("INSTANCIA: Ya existe un modelo cargado en la base de datos.");
			}
		}
		try {
			$this->get_db()->abrir_transaccion();
			$this->get_db()->retrazar_constraints();
			// Creo las tablas basicas
			$this->crear_tablas_minimas();
			// Cargo informacion del proyecto
			$this->cargar_proyectos(true);
			// Cargo la informacion de la instancia
			$this->cargar_informacion_instancia();
			$this->get_db()->cerrar_transaccion();
		} catch ( toba_error $e ) {
			$this->get_db()->abortar_transaccion();
			throw $e;
		}		
	}

	/**
	* Inicializacion de instancias
	*/
	function crear_modelo_datos_toba()
	{	
		$this->crear_tablas();
		$this->cargar_datos_nucleo();
		toba_logger::instancia()->debug("Modelo creado");		
	}
	
	private function crear_tablas()
	{
		$this->manejador_interface->mensaje('Creando las tablas del sistema', false);
		$directorio = toba_modelo_nucleo::get_dir_ddl();
		$archivos = toba_manejador_archivos::get_archivos_directorio( $directorio, '|.*\.sql|' );
		sort($archivos);
		foreach( $archivos as $archivo ) {
			$cant = $this->get_db()->ejecutar_archivo( $archivo );
			toba_logger::instancia()->debug($archivo . ". ($cant)");			
			$this->manejador_interface->progreso_avanzar();

		}
		$this->manejador_interface->progreso_fin();
	}
	
	private function crear_tablas_minimas()
	{
		$this->manejador_interface->mensaje('Creando las tablas del sistema (version reducida)', false);
		$directorio = toba_modelo_nucleo::get_dir_ddl();
		$archivo = $directorio . "/pgsql_a00_tablas_instancia.sql";
		$cant = $this->get_db()->ejecutar_archivo( $archivo );
		toba_logger::instancia()->debug($archivo . ". ($cant)");			
		$this->manejador_interface->progreso_avanzar();
		$archivo = $directorio . "/pgsql_a02_tablas_usuario.sql";
		$cant = $this->get_db()->ejecutar_archivo( $archivo );
		toba_logger::instancia()->debug($archivo . ". ($cant)");			
		$this->manejador_interface->progreso_avanzar();
		$archivo = $directorio . "/pgsql_a04_tablas_solicitudes.sql" ;
		$cant = $this->get_db()->ejecutar_archivo( $archivo );
		toba_logger::instancia()->debug($archivo . ". ($cant)");			
		$this->manejador_interface->progreso_avanzar();
		$this->manejador_interface->progreso_fin();		
	}

	function eliminar_tablas_minimas()
	{
		$sql[] = 'DROP TABLE apex_permiso_grupo_acc';
		$sql[] = 'DROP TABLE apex_usuario_grupo_acc_item';
		$sql[] = 'DROP TABLE apex_usuario_proyecto';
		$sql[] = 'DROP TABLE apex_usuario_grupo_acc';
		$sql[] = 'DROP TABLE apex_usuario_perfil_datos';
		$sql[] = 'DROP TABLE apex_usuario';
		$sql[] = 'DROP TABLE apex_usuario_tipodoc';
		$sql[] = 'DROP TABLE apex_log_ip_rechazada';
		$sql[] = 'DROP TABLE apex_log_error_login';
		$sql[] = 'DROP TABLE apex_log_sistema';
		$sql[] = 'DROP TABLE apex_solicitud_observacion';
		$sql[] = 'DROP TABLE apex_solicitud_cronometro';
		$sql[] = 'DROP TABLE apex_solicitud_consola';
		$sql[] = 'DROP TABLE apex_solicitud_browser';
		$sql[] = 'DROP TABLE apex_sesion_browser';
		$sql[] = 'DROP TABLE apex_solicitud';
		$sql[] = 'DROP TABLE apex_revision';
		$sql[] = 'DROP TABLE apex_instancia';
		$sql[] = 'DROP TABLE apex_proyecto';
		$sql[] = 'DROP SEQUENCE apex_solicitud_seq';
		$sql[] = 'DROP SEQUENCE apex_sesion_browser_seq';
		$sql[] = 'DROP SEQUENCE apex_solicitud_observacion_seq';
		$sql[] = 'DROP SEQUENCE apex_log_sistema_seq';
		$sql[] = 'DROP SEQUENCE apex_log_error_login_seq';
		$this->get_db()->ejecutar($sql);
	}
		
	private function cargar_datos_nucleo()
	{
		$this->manejador_interface->mensaje('Cargando datos del nucleo', false);
		$directorio = toba_modelo_nucleo::get_dir_metadatos();
		$archivos = toba_manejador_archivos::get_archivos_directorio( $directorio, '|.*\.sql|' );
		foreach( $archivos as $archivo ) {
			$cant = $this->get_db()->ejecutar_archivo( $archivo );
			toba_logger::instancia()->debug($archivo . ". ($cant)");
			$this->manejador_interface->progreso_avanzar();			
		}
		$this->manejador_interface->progreso_fin();		
	}

	/*
	*	Importa los proyectos asociados
	*/
	private function cargar_proyectos($informacion_reducida=false)
	{
		foreach( $this->get_lista_proyectos_vinculados() as $id_proyecto ) {
			if ($id_proyecto != 'toba') {
				$this->manejador_interface->enter();
				$this->manejador_interface->subtitulo("$id_proyecto:");
				$proyecto = $this->get_proyecto($id_proyecto);
				if(!$informacion_reducida) {
					$proyecto->cargar();										
				}else{
					$proyecto->cargar_informacion_reducida();										
				}
			}
		}	
	}
	
	
	/*
	* 	Importa la informacion perteneciente a la instancia
	*/
	private function cargar_informacion_instancia()
	{
		$this->manejador_interface->mensaje('Cargando datos de la instancia', false);
		$subdirs = toba_manejador_archivos::get_subdirectorios( $this->get_dir() );
		$proyectos = $this->get_lista_proyectos_vinculados();
		$nombres_carp = array('global');
		foreach ($proyectos as $proy) {
			$nombres_carp[] = self::prefijo_dir_proyecto.$proy;
		}
		foreach ( $nombres_carp as $carp ) {
			$dir = $this->get_dir()."/".$carp;
			if (file_exists($dir)) {
				$archivos = toba_manejador_archivos::get_archivos_directorio( $dir , '|.*\.sql|' );
				foreach( $archivos as $archivo ) {
					$cant = $this->get_db()->ejecutar_archivo( $archivo );
					toba_logger::instancia()->debug($archivo . ". ($cant)");
					$this->manejador_interface->progreso_avanzar();
				}
			}
		}
		$this->manejador_interface->progreso_avanzar();		
		$this->manejador_interface->progreso_fin();		
	}

	function cargar_informacion_instancia_proyecto( $proyecto )
	{
		$this->manejador_interface->mensaje("Cargando datos locales de la instancia", false);
		toba_logger::instancia()->debug("Cargando datos de la instancia del proyecto '{$proyecto}'");
		$directorio = $this->get_dir() . '/' . self::prefijo_dir_proyecto . $proyecto;
		if (file_exists($directorio)) {
			$archivos = toba_manejador_archivos::get_archivos_directorio( $directorio , '|.*\.sql|' );
			foreach ( $archivos as $archivo ) {
				$cant = $this->get_db()->ejecutar_archivo( $archivo );
				toba_logger::instancia()->debug($archivo . ". ($cant)");
				$this->manejador_interface->progreso_avanzar();
			}
			$this->manejador_interface->progreso_fin();
		}
	}
	
	/**
	 * Importa la información perteneciente a la instancia desde otra instalacion/instancia
	 *
	 */
	function importar_informacion_instancia($instancia_origen, $path_origen, $reemplazar_actuales)
	{
		if (! isset($path_origen)) {
			$path_origen = toba_dir();
		}
		$path = $path_origen.'/instalacion/'.self::dir_prefijo.$instancia_origen;
		if (! file_exists($path)) {
			throw new toba_error("No existe la carpeta $path");
		}
		$subdirs = toba_manejador_archivos::get_subdirectorios($path);
		$proyectos = $this->get_lista_proyectos_vinculados();
		$nombres_carp = array('global');
		foreach ($proyectos as $proy) {
			$nombres_carp[] = self::prefijo_dir_proyecto.$proy;
		}
		$this->get_db()->abrir_transaccion();
		$this->get_db()->retrazar_constraints();
		if ($reemplazar_actuales) {
			$this->eliminar_informacion_instancia();
		}
		foreach ( $nombres_carp as $carp ) {
			$dir = $path."/".$carp;
			if (file_exists($dir)) {
				$archivos = toba_manejador_archivos::get_archivos_directorio( $dir , '|.*\.sql|' );
				foreach( $archivos as $archivo ) {
					$cant = $this->get_db()->ejecutar_archivo( $archivo );
					toba_logger::instancia()->debug($archivo . ". ($cant)");
					$this->manejador_interface->progreso_avanzar();
				}
			}
		}
		$this->manejador_interface->progreso_avanzar();		
		$this->get_db()->abortar_transaccion();
		$this->manejador_interface->progreso_fin();		
	}

	/**
	 * Elimina todos los datos locales de la instancia actual
	 */
	private function eliminar_informacion_instancia()
	{
		$sql = array();
		$metodos = get_class_methods('toba_db_tablas_instancia');
		foreach ($metodos as $metodo) {
			if (substr($metodo, 0, 10) === 'get_lista_') {
				foreach ( toba_db_tablas_instancia::$metodo() as $tabla ) {
					$sql[] = 'DELETE FROM '.$tabla;
				}
			}
		}
		if (! empty($sql)) {
			$this->get_db()->ejecutar($sql);
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
		toba_logger::instancia()->debug("Actualizada la revision svn de la instancia a $revision");
	}
	
	/*
	*	Reestablece las secuencias del sistema
	*/
	function actualizar_secuencias()
	{
		toba_logger::instancia()->debug('Actualizando SECUENCIAS');
		$this->manejador_interface->mensaje("Actualizando secuencias", false);		
		$id_grupo_de_desarrollo = $this->instalacion->get_id_grupo_desarrollo();
		foreach ( toba_db_secuencias::get_lista() as $seq => $datos ) {
			if ( is_null( $id_grupo_de_desarrollo ) ) {
				//Si no hay definido un grupo la secuencia se toma en forma normal
				$sql = "SELECT setval('$seq', max({$datos['campo']})) as nuevo FROM {$datos['tabla']}"; 
				$res = $this->get_db()->consultar($sql, null, true);
				$nuevo = $res[0]['nuevo'];
			} else {
				//Sino se toma utilizando los límites según el ID del grupo
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
				$sql = "SELECT setval('$seq', $nuevo)";
				$this->get_db()->consultar( $sql );	
			}
			toba_logger::instancia()->debug("SECUENCIA $seq: $nuevo");
			$this->manejador_interface->progreso_avanzar();			
		}
		$this->manejador_interface->progreso_fin();
	}
	
	/**
	 * Dado el valor de un campo generado por una secuencia determina el grupo de desarrollo que lo genero
	 */
	function get_grupo_desarrollo_de_valor($valor)
	{
		if (! is_numeric($valor)) {
			return null;
		}
		return floor($valor / self::cantidad_seq_grupo);
	}
	
	/**
	 * Retorna el campo que es una secuencia en una tabla de la instancia
	 */
	function get_campo_secuencia_de_tabla($tabla)
	{
		if (! isset($this->lista_secuencias)) {
			$this->lista_secuencias = toba_db_secuencias::get_lista();
		}
		foreach ($this->lista_secuencias as $datos) {
			if ($datos['tabla'] == $tabla) {
				return $datos['campo'];				
			}
		}
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
			$this->manejador_interface->mensaje("Eliminando base '{$this->ini_base}'...", false);
			$this->instalacion->borrar_base_datos( $this->ini_base );
			$this->manejador_interface->progreso_fin();
		} catch ( toba_error $e ) {
			$this->manejador_interface->error( "Ha ocurrido un error durante la eliminacion de la BASE:\n".
												$e->getMessage());
			
		}
	}

	/**
	* Eliminacion de las TABLAS de la instancia
	*/
	function eliminar_modelo()
	{
		try {
			$this->manejador_interface->mensaje("Eliminando el modelo...",false);			
			$this->get_db()->abrir_transaccion();
			// Tablas
			$sql = sql_array_tablas_drop( catalogo_general::get_tablas() );
			$this->get_db()->ejecutar( $sql );
			// Secuencias
			$secuencias = array_keys( secuencias::get_lista() );
			$sql = sql_array_secuencias_drop( $secuencias );
			$this->get_db()->ejecutar( $sql );
			$this->get_db()->cerrar_transaccion();
			$this->manejador_interface->progreso_fin();
			toba_logger::instancia()->debug("Modelo de la instancia {$this->identificador} creado");
		} catch ( toba_error $e ) {
			$this->get_db()->abortar_transaccion();
			throw $e;
		}
	}

	/**
	 * Elimina los archivos de configuracion y datos propios de la instancia
	 */
	function eliminar_archivos()
	{
		toba_manejador_archivos::eliminar_directorio($this->dir);		
	}
	
	//-----------------------------------------------------------
	//	Informacion sobre METADATOS
	//-----------------------------------------------------------

	function get_lista_usuarios($proyecto=null)
	{
		if(isset($proyecto)) {
			$sql = "SELECT u.usuario as usuario, u.nombre as nombre
					FROM apex_usuario u, apex_usuario_proyecto up
					WHERE u.usuario = up.usuario
					AND up.proyecto = '$proyecto';";
		} else {
			$sql = "SELECT usuario, nombre FROM apex_usuario";	
		}
		return $this->get_db()->consultar( $sql );
	}
	
	function get_usuarios_administradores($proyecto, $grupo = 'admin')
	{
		$sql = "
			SELECT	usuario
			FROM	apex_usuario_proyecto
			WHERE	
					proyecto='$proyecto'
				AND	usuario_grupo_acc = '$grupo'
		";
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
		$algoritmo = 'sha256';
		$clave = encriptar_con_sal($clave, $algoritmo);
		toba_logger::instancia()->debug("Agregando el usuario '$usuario' a la instancia {$this->identificador}");
		$sql = "INSERT INTO apex_usuario ( usuario, nombre, autentificacion, clave )
				VALUES ('$usuario', '$nombre', '$algoritmo', '$clave')";
		return $this->get_db()->ejecutar( $sql );
	}
	
	function eliminar_usuario( $usuario )
	{
		toba_logger::instancia()->debug("Borrando el usuario '$usuario' de la instancia {$this->identificador}");		
		$sql = "DELETE FROM apex_usuario WHERE usuario = '$usuario'";	
		return $this->get_db()->ejecutar( $sql );
	}
	
	function desbloquear_ips()
	{
		$sql = "DELETE FROM apex_log_ip_rechazada";
		$cant = $this->get_db()->ejecutar($sql);
		$this->manejador_interface->mensaje("Ips liberadas: $cant");
	}
	
	/**
	 * Cambia los grupos de acceso de un usuario en los distintos proyectos de la instancia
	 *
	 * @param string $usuario
	 * @param array $accesos Arreglo asociativo proyecto=>array(grupos)
	 */
	function cambiar_acceso_usuario($usuario, $accesos)
	{
		$this->db->abrir_transaccion();
		foreach( $this->get_lista_proyectos_vinculados() as $id_proyecto ) {
			if (isset($accesos[$id_proyecto])) {
				$proyecto = $this->get_proyecto($id_proyecto);
				$proyecto->desvincular_usuario($usuario);
				$proyecto->vincular_usuario($usuario, $accesos[$id_proyecto], 'no', false);
			}
		}
		$this->db->cerrar_transaccion();
	}

	//-------------------------------------------------------------
	//-- CREACION de INSTANCIAS
	//-------------------------------------------------------------

	/**
	* Agrega una instancia
	*/
	static function crear_instancia( $nombre, $base, $lista_proyectos, $tipo='normal' )
	{
		//Creo la carpeta
		if( ! self::existe_carpeta_instancia( $nombre ) ) {
			$dir = self::dir_instancia( $nombre );
			mkdir( $dir );
			toba_logger::instancia()->debug("Creado directorio $dir");
		}
		//Creo la clase que proporciona informacion sobre la instancia
		$ini = new toba_ini();
		$ini->agregar_titulo( self::toba_instancia_titulo );
		$ini->agregar_entrada( 'base', $base );
		$ini->agregar_entrada( 'proyectos', implode(', ', array_keys($lista_proyectos)) );
		$ini->agregar_entrada( 'tipo', $tipo );
		
		//--- Se revisa la lista de proyectos para ver si algun id_proyecto != dir_proyecto
		foreach ($lista_proyectos as $id_pro => $path_pro) {
			//$datos_ini = array('url' => '/'.$id_pro);
			$datos_ini = array();
			if ($path_pro != $id_pro) {
				//--- Se agrega una seccion para el proyecto
				$datos_ini['path'] = toba_dir().'/proyectos/'.$path_pro;
			}
			$ini->agregar_entrada($id_pro, $datos_ini);			
		}
		
		$archivo = self::dir_instancia( $nombre ) . '/' . toba_modelo_instancia::toba_instancia ;
		$ini->guardar( $archivo );
		toba_logger::instancia()->debug("Creado archivo $archivo");
	}

	static function dir_instancia( $nombre )
	{
		return toba_modelo_instalacion::dir_base() . '/' . self::dir_prefijo . $nombre;
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
			$temp = toba_manejador_archivos::get_subdirectorios( toba_modelo_instalacion::dir_base() , '|^'.self::dir_prefijo.'|' );
			foreach ( $temp as $dir ) {
				$temp_dir = explode( self::dir_prefijo, $dir );
				if (count($temp_dir) > 1) {
					$dirs[] = $temp_dir[1];
				}
			}
		} catch ( toba_error $e ) {
			// No existe la instalacion
		}
		return $dirs;
	}


	function crear_alias_proyectos()
	{
		foreach( $this->get_lista_proyectos_vinculados() as $id_proyecto ) {
			if ($id_proyecto != 'toba') {
				$proyecto = $this->get_proyecto($id_proyecto);										
				$proyecto->publicar();	
			}
		}			
	}
	
	//------------------------------------------------------------------------
	//--------------------------  Manejo de Versiones ------------------------
	//------------------------------------------------------------------------

	function migrar_version($version, $recursivo, $con_transaccion=true)
	{
		if ($version->es_mayor($this->get_version_actual())) {
			$this->manejador_interface->enter();		
			$this->manejador_interface->subtitulo("Migrando instancia '{$this->identificador}'");
			toba_logger::instancia()->debug("Migrando instancia {$this->identificador} a la versión ".$version->__toString());
			if ($con_transaccion) $this->get_db()->abrir_transaccion();
			$version->ejecutar_migracion('instancia', $this, null, $this->manejador_interface);
			
			//-- Se migran los proyectos incluidos
			if ($recursivo) {
				foreach( $this->get_lista_proyectos_vinculados() as $id_proyecto ) {
					if ($id_proyecto != 'toba') {
						$proyecto = $this->get_proyecto($id_proyecto);						
						
						//-- Se evitan los proyectos propios, ya que ya estan migrados pero recien se va a notar cuando se regenere
						if (! in_array($proyecto->get_id(), $this->proyectos_ya_migrados)) {
							$proyecto->migrar_version($version);
						}
					}
				}
			}
			$this->set_version($version);			
			if ($con_transaccion) $this->get_db()->cerrar_transaccion();
		} else {
			toba_logger::instancia()->debug("La instancia {$this->identificador} no necesita migrar a la versión ".$version->__toString());
		}
	}
	
	function set_version($version)
	{
		$nueva = $version->__toString();		
		if ($this->get_version_actual()->es_igual(toba_version::inicial())) {
			$sql = "INSERT INTO apex_instancia (instancia, version) VALUES ('".
					$this->get_id(). "', '$nueva')";			
		} else {
			$sql = "UPDATE apex_instancia SET version='$nueva' WHERE instancia='{$this->identificador}'";
		}
		toba_logger::instancia()->debug("Actualizando la instancia {$this->identificador} a versión $nueva");		
		$this->get_db()->ejecutar($sql);
	}
	
	function get_version_actual()
	{
		$sql = "SELECT version FROM apex_instancia";
		$rs = $this->get_db()->consultar($sql);
		if (empty($rs)) {
			return toba_version::inicial(); //Es la version anterior al cambio de la migracion
		} else {
			return new toba_version($rs[0]['version']);	
		}
	}
	
}
?>
