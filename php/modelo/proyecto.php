<?php
require_once('lib/elemento_modelo.php');
require_once('modelo/instancia.php');
require_once('nucleo/componentes/catalogo_toba.php');
require_once('nucleo/componentes/cargador_toba.php');
require_once('lib/manejador_archivos.php');
require_once('lib/sincronizador_archivos.php');
require_once('lib/editor_archivos.php');
require_once('lib/reflexion/clase_datos.php');
require_once('modelo/estructura_db/tablas_proyecto.php');
require_once('modelo/estructura_db/tablas_instancia.php');
require_once('modelo/estructura_db/tablas_componente.php');

/**
*	Administrador de metadatos de PROYECTOS
*/
class proyecto extends elemento_modelo
{
	private $instancia;				
	private $identificador;
	private $dir;
	private $sincro_archivos;
	private $db;
	const dump_prefijo_componentes = 'dump_';
	const compilar_archivo_referencia = 'tabla_tipos';
	const compilar_prefijo_componentes = 'php_';	
	const template_proyecto = '/php/modelo/template_proyecto';
	private $compilacion_tabla_tipos;

	public function __construct( instancia $instancia, $identificador )
	{
		$this->instancia = $instancia;
		$this->identificador = $identificador;
		$this->dir = toba_dir() . '/proyectos/' . $this->identificador;	
		if( ! is_dir( $this->dir ) ) {
			throw new excepcion_toba("PROYECTO: El proyecto '{$this->identificador}' es invalido. (la carpeta '{$this->dir}' no existe)");
		}
		$this->db = $this->instancia->get_db();
		logger::instancia()->debug('PROYECTO "'.$this->identificador.'"');				
	}

	function get_sincronizador()
	{
		if ( ! isset( $this->sincro_archivos ) ) {
			$this->sincro_archivos = new sincronizador_archivos( $this->get_dir_dump() );
		}
		return $this->sincro_archivos;
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

	function get_dir_dump()
	{
		return $this->dir . '/metadatos';	
	}

	function get_dir_componentes()
	{
		return $this->get_dir_dump() . '/componentes';
	}
	
	function get_dir_tablas()
	{
		return $this->get_dir_dump() . '/tablas';
	}

	function get_dir_componentes_compilados()
	{
		return $this->dir . '/metadatos_compilados/componentes';
	}

	function get_instancia()
	{
		return $this->instancia;
	}
	
	function get_db()
	{
		return $this->instancia->get_db();	
	}

	//-----------------------------------------------------------
	//	ACTUALIZAR SVN
	//-----------------------------------------------------------

	function actualizar()
	{
		$dir = $this->get_dir();
		system("svn update $dir");		
	}
	
	//-----------------------------------------------------------
	//	EXPORTAR
	//-----------------------------------------------------------

	function exportar()
	{
		logger::instancia()->debug( "Exportando PROYECTO {$this->identificador}");
		$this->manejador_interface->titulo( "Exportación PROYECTO {$this->identificador}" );		
		$existe_vinculo = $this->instancia->existe_proyecto_vinculado( $this->identificador );
		$existen_metadatos = $this->instancia->existen_metadatos_proyecto( $this->identificador );
		if( !( $existen_metadatos || $existe_vinculo ) ) {
			throw new excepcion_toba("PROYECTO: El proyecto '{$this->identificador}' no esta asociado a la instancia actual");
		}
		try {
			$this->exportar_tablas();
			$this->exportar_componentes();
			$this->sincronizar_archivos();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( "Proyecto {$this->identificador}: Ha ocurrido un error durante la exportacion:\n".
												$e->getMessage() );
		}
	}
	
	private function sincronizar_archivos()
	{
		$obs = $this->get_sincronizador()->sincronizar();
		$this->manejador_interface->lista( $obs, 'Observaciones' );
	}

	private function exportar_tablas()
	{
		$this->manejador_interface->mensaje("Exportando datos generales...", false);
		manejador_archivos::crear_arbol_directorios( $this->get_dir_tablas() );
		$cant = 0;
		foreach ( tablas_proyecto::get_lista() as $tabla ) {
			$definicion = tablas_proyecto::$tabla();
			//Genero el SQL
			if( isset($definicion['dump_where']) && ( trim($definicion['dump_where']) != '') ) {
       			$w = stripslashes($definicion['dump_where']);
       			$where = ereg_replace("%%",$this->get_id(), $w);
            } else {
       			$where = " ( proyecto = '".$this->get_id()."')";
			}
			$sql = "SELECT " . implode(', ', $definicion['columnas']) .
					" FROM $tabla " .
					" WHERE $where " .
					//" WHERE {$definicion['dump_clave_proyecto']} = '".$this->get_id()."}' " .
					" ORDER BY {$definicion['dump_order_by']} ;\n";
			//$this->manejador_interface->mensaje( $sql );
			$contenido = "";
			$datos = $this->db->consultar($sql);
			$regs = count( $datos );
			if ( $regs > 1 ) {
				$columnas_orden = array_map('trim', explode(',',$definicion['dump_order_by']) );
				$datos = rs_ordenar_por_columnas( $datos, $columnas_orden );
			
			}
			logger::instancia()->debug("TABLA  $tabla  ($regs reg.)");
			for ( $a = 0; $a < $regs ; $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
			if ( trim( $contenido ) != '' ) {
				$this->guardar_archivo( $this->get_dir_tablas() .'/'. $tabla . '.sql', $contenido );			
			}
			$cant++;
		}
		$this->manejador_interface->mensaje("$cant tablas.");
	}

	/*
	*	Exporta los componentes
	*/
	private function exportar_componentes()
	{
		$this->manejador_interface->mensaje("Exportando componentes...", false);
		$cant = 0;
		cargador_toba::instancia()->crear_cache_simple( $this->get_id(), $this->db );
		foreach (catalogo_toba::get_lista_tipo_componentes_dump() as $tipo) {
			$lista_componentes = catalogo_toba::get_lista_componentes( $tipo, $this->get_id(), $this->db );
			foreach ( $lista_componentes as $id_componente) {
				$this->exportar_componente( $tipo, $id_componente );
				$cant++;
			}
		}
		$this->manejador_interface->mensaje("$cant tablas.");		
	}
	
	/*
	*	Exporta un componente
	*/
	private function exportar_componente( $tipo, $id )
	{
		$directorio = $this->get_dir_componentes() . '/' . $tipo;
		manejador_archivos::crear_arbol_directorios( $directorio );
		$archivo = manejador_archivos::nombre_valido( self::dump_prefijo_componentes . $id['componente'] );
		$contenido =&  $this->get_contenido_componente( $tipo, $id );
		$this->guardar_archivo( $directorio .'/'. $archivo . '.sql', $contenido ); 
		logger::instancia()->debug("COMPONENTE $tipo  --  " . $id['componente'] . 
									' ('.$this->cant_reg_exp.' reg.)');
	}
	
	/*
	*	Genera el contenido de la exportacion de un componente
	*/
	private function & get_contenido_componente( $tipo, $id )
	{
		$this->cant_reg_exp = 0;
		//Recupero metadatos
		$metadatos = cargador_toba::instancia()->get_metadatos_simples( $id, $tipo, $this->db );
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
				$this->cant_reg_exp++;				
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
		}
		return $contenido;		
	}

	//-----------------------------------------------------------
	//	CARGAR
	//-----------------------------------------------------------
	
	/*
	*	Carga en proyecto en una transaccion
	*/
	function cargar_autonomo()
	{
		logger::instancia()->debug( "Cargando PROYECTO {$this->identificador}");					
		try {
			$this->db->abrir_transaccion();
			$this->db->retrazar_constraints();
			$this->cargar();
			$this->instancia->actualizar_secuencias();
			$this->db->cerrar_transaccion();
		} catch ( excepcion_toba $e ) {
			$this->db->abortar_transaccion();
			$this->manejador_interface->error( "PROYECTO: Ha ocurrido un error durante la IMPORTACION:\n".
												$e->getMessage() );
		}
	}

	/*
	*	Carga un proyecto
	*/
	function cargar()
	{
		logger::instancia()->debug("Cargando proyecto '{$this->identificador}'");
		if( ! ( $this->instancia->existe_proyecto_vinculado( $this->identificador ) ) ) {
			throw new excepcion_toba("PROYECTO: El proyecto '{$this->identificador}' no esta asociado a la instancia actual");
		}
		$this->cargar_tablas();
		$this->cargar_componentes();
	}

	private function cargar_tablas()
	{
		$this->manejador_interface->mensaje('Cargando datos globales...', false);
		$archivos = manejador_archivos::get_archivos_directorio( $this->get_dir_tablas(), '|.*\.sql|' );
		$cant_total = 0;
		foreach( $archivos as $archivo ) {
			$cant = $this->db->ejecutar_archivo( $archivo );
			logger::instancia()->debug($archivo . ". ($cant)");
			$cant_total++;
		}
		$this->manejador_interface->mensaje($cant_total." arch.");
	}
	
	private function cargar_componentes()
	{
		$this->manejador_interface->mensaje('Cargando componentes...', false);		
		$subdirs = manejador_archivos::get_subdirectorios( $this->get_dir_componentes() );
		$cant_total = 0;
		foreach ( $subdirs as $dir ) {
			$archivos = manejador_archivos::get_archivos_directorio( $dir , '|.*\.sql|' );
			foreach( $archivos as $archivo ) {
				$cant = $this->db->ejecutar_archivo( $archivo );
				logger::instancia()->debug($archivo . " ($cant)");
				$cant_total++;
			}
		}
		$this->manejador_interface->mensaje($cant_total.' arch.');
	}

	//-----------------------------------------------------------
	//	ELIMINAR
	//-----------------------------------------------------------

	/*
	*	Eliminacion dentro de una transaccion
	*/
	function eliminar_autonomo()
	{
		try {
			$this->db->abrir_transaccion();
			$this->db->retrazar_constraints();
			$this->eliminar();
			$this->db->cerrar_transaccion();
			$this->manejador_interface->mensaje("El proyecto '{$this->identificador}' ha sido eliminado");
		} catch ( excepcion_toba $e ) {
			$this->db->abortar_transaccion();
			$this->manejador_interface->error( "Ha ocurrido un error durante la eliminacion de TABLAS de la instancia:\n".
												$e->getMessage() );
		}
	}

	function eliminar()
	{
		$this->manejador_interface->mensaje( "Borrando metadatos...", false);
		$sql = $this->get_sql_eliminacion();
		$cant = count($sql);		
		$cant = $this->db->ejecutar( $sql );
		logger::instancia()->debug("Eliminacion. Registros borrados: $cant");
		$this->manejador_interface->mensaje( "$cant reg." );				
	}

	/*
	*	Genera el SQL de eliminacion del proyecto
	*/	
	private function get_sql_eliminacion()
	{
		// Tablas
		$tablas = array();
		//Busco las TABLAS y sus WHERES
		$catalogos = array();
		$catalogos['tablas_componente'][] = 'get_lista';
		$catalogos['tablas_proyecto'][] = 'get_lista';
		$catalogos['tablas_instancia'][] = 'get_lista_proyecto';
		$catalogos['tablas_instancia'][] = 'get_lista_proyecto_log';
		$catalogos['tablas_instancia'][] = 'get_lista_proyecto_usuario';
		foreach( $catalogos as $catalogo => $indices ) {
			foreach( $indices as $indice ) {
				$lista_tablas = call_user_func( array( $catalogo, $indice ) );
				foreach ( $lista_tablas as $t ) {
					$info_tabla = call_user_func( array( $catalogo, $t ) );
					if( isset( $info_tabla['dump_where'] ) ) {
						$where = " WHERE " . ereg_replace('%%', $this->identificador, stripslashes($info_tabla['dump_where']) );
						$where = ereg_replace( " dd", $t, $where );						
					} else {
						$where = " WHERE proyecto = '{$this->identificador}'";
					}
					$tablas[ $t ] = $where;
				}
			}
		}
		$sql = sql_array_tablas_delete( $tablas );
		return $sql;
	}

	//-----------------------------------------------------------
	//	REGENERAR
	//-----------------------------------------------------------

	/*
	*	cargar un PROYECTO en una instancia ya creada
	*/
	function regenerar()
	{
		logger::instancia()->debug( "Regenerando PROYECTO {$this->identificador}");
		$this->manejador_interface->titulo( "Regenerando PROYECTO {$this->identificador}" );		
		try {
			$this->db->abrir_transaccion();
			$this->db->retrazar_constraints();
			$this->eliminar();
			$this->cargar();
			$this->instancia->cargar_informacion_instancia_proyecto( $this->identificador );
			$this->instancia->actualizar_secuencias();			
			$this->db->cerrar_transaccion();
		} catch ( excepcion_toba $e ) {
			$this->db->abortar_transaccion();
			$this->manejador_interface->error("PROYECTO {$this->identificador}: Ha ocurrido un error durante la IMPORTACION:\n".
												$e->getMessage());
		}
	}

	//-----------------------------------------------------------
	//	COMPILAR
	//-----------------------------------------------------------

	function compilar()
	{
		try {
			$this->compilar_componentes();
			$this->crear_compilar_archivo_referencia();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( "PROYECTO {$this->identificador}: Ha ocurrido un error durante la compilacion:\n".
												$e->getMessage());
		}
	}

	/*
	*	Ciclo de compilacion de componentes
	*/
	function compilar_componentes()
	{
		foreach (catalogo_toba::get_lista_tipo_componentes_dump() as $tipo) {
			$this->manejador_interface->titulo( $tipo );
			$path = $this->get_dir_componentes_compilados() . '/' . $tipo;
			manejador_archivos::crear_arbol_directorios( $path );
			foreach (catalogo_toba::get_lista_componentes( $tipo, $this->get_id(), $this->db ) as $id_componente) {
				$this->compilar_componente( $tipo, $id_componente );
			}
		}
	}
	
	/*
	*	Compila un componente
	*/
	function compilar_componente( $tipo, $id )
	{
		//Armo la clase compilada
		$nombre = manejador_archivos::nombre_valido( self::compilar_prefijo_componentes . $id['componente'] );
		$this->manejador_interface->mensaje("Compilando: " . $id['componente']);
		$clase = new clase_datos( $nombre );		
		$metadatos = cargador_toba::instancia()->get_metadatos_extendidos( $id, $tipo, $this->db );
		$clase->agregar_metodo_datos('get_metadatos',$metadatos);
		//Creo el archivo
		$directorio = $this->get_dir_componentes_compilados() . '/' . $tipo;
		$path = $directorio .'/'. $nombre . '.php';
		$clase->guardar( $path );
		//Creo la tabla de referencia
		/*	ATENCION! excluyo los items porque pueden pisarse los IDs con los objetos	*/
		if ( $tipo != 'item' ) {
			$this->compilacion_tabla_tipos[$id['componente']] = $tipo;
		}
	}

	/*
	*	Creo la tabla de referencias
	*/
	function crear_compilar_archivo_referencia()
	{
		//Armo la clase compilada
		$this->manejador_interface->mensaje("Creando tabla de tipos.");
		$clase = new clase_datos( self::compilar_archivo_referencia );		
		$clase->agregar_metodo_datos('get_datos',$this->compilacion_tabla_tipos);
		//Creo el archivo
		$archivo = manejador_archivos::nombre_valido( self::compilar_archivo_referencia );
		$path = $this->get_dir_componentes_compilados() .'/'. $archivo . '.php';
		$clase->guardar( $path );
	}

	//-----------------------------------------------------------
	//	Primitivas basicas
	//-----------------------------------------------------------

	private function guardar_archivo( $archivo, $contenido )
	{
		file_put_contents( $archivo, $contenido );
		$this->get_sincronizador()->agregar_archivo( $archivo );
	}
	
	//-----------------------------------------------------------
	//	Informacion sobre METADATOS
	//-----------------------------------------------------------
	
	function get_lista_grupos_acceso()
	{
		$sql = "SELECT usuario_grupo_acc as id, nombre
				FROM apex_usuario_grupo_acc
				WHERE proyecto = '".$this->get_id()."';";
		return $this->instancia->get_db()->consultar( $sql );
	}

	function get_lista_componentes()
	{
		$sql = "	SELECT clase, COUNT(*) as cantidad
					FROM apex_objeto
					WHERE proyecto = '{$this->identificador}'
					GROUP BY 1
					ORDER BY 2 DESC";
		return $this->instancia->get_db()->consultar( $sql );
	}
		
	//-----------------------------------------------------------
	//	Manipulacion de METADATOS
	//-----------------------------------------------------------

	function vincular_usuario( $usuario, $perfil_acceso, $perfil_datos = 'no' )
	{
		$sql = self::get_sql_vincular_usuario( $this->get_id(), $usuario, $perfil_acceso, $perfil_datos );
		$this->instancia->get_db()->ejecutar( $sql );
	}

	function desvincular_usuario( $usuario )
	{
		$sql = "DELETE FROM apex_usuario_proyecto 
				WHERE usuario = '$usuario'
				AND proyecto = '".$this->get_id()."'";
		$this->instancia->get_db()->ejecutar( $sql );
	}

	//------------------------------------------------------------------------
	//-------------------------- Manejo de Versiones --------------------------
	//------------------------------------------------------------------------
	function migrar_rango_versiones($desde, $hasta, $recursivo)
	{
		$this->get_db()->abrir_transaccion();
		parent::migrar_rango_versiones($desde, $hasta, $recursivo);
		$this->get_db()->cerrar_transaccion();
	}
	
	function migrar_version($version)
	{
		if ($version->es_mayor($this->get_version_actual())) {
			$this->manejador_interface->mensaje("  Migrando proyecto '{$this->identificador}'");
			$version->ejecutar_migracion('proyecto', $this);
			$this->actualizar_campo_version($version);
		}
	}
	
	function ejecutar_migracion_particular($version, $metodo)
	{
		$this->get_db()->abrir_transaccion();		
		$version->ejecutar_migracion('proyecto', $this, $metodo);
		$this->get_db()->cerrar_transaccion();		
	}
	
	function get_version_actual()
	{
		$sql = "SELECT version_toba FROM apex_proyecto WHERE proyecto='{$this->identificador}'";
		$rs = $this->db->consultar($sql);
		$version = $rs[0]['version_toba'];
		if (! isset($version)) {
			return version_toba::inicial();
		}
		return new version_toba($version);
		
	}
	
	private function actualizar_campo_version($version)
	{
		$sql = $this->get_sql_actualizar_version($version, $this->identificador);
		$this->get_db()->ejecutar($sql);
	}
	
	private function get_sql_actualizar_version($version, $id_proyecto)
	{
		$nueva = $version->__toString();
		$sql = "UPDATE apex_proyecto SET version_toba='$nueva' WHERE proyecto='$id_proyecto'";
		return $sql;
	}	
	
	//-----------------------------------------------------------
	//	Funcionalidad ESTATICA
	//-----------------------------------------------------------
	
	/**
	*	Devuelve la lista de proyectos existentes en el sistema de archivos
	*/
	static function get_lista()
	{
		$proyectos = array();
		$directorio_proyectos = toba_dir() . '/proyectos';
		if( is_dir( $directorio_proyectos ) ) {
			if ($dir = opendir($directorio_proyectos)) {
			   while (false	!==	($archivo = readdir($dir)))	{ 
					if( is_dir($directorio_proyectos . '/' . $archivo) 
						&& ($archivo != '.' ) && ($archivo != '..' ) && ($archivo != '.svn' ) ) {
						$proyectos[] = $archivo;
					}
			   } 
			   closedir($dir); 
			}
		}
		return $proyectos;
	}
	
	/**
	*	Indica si un proyecto existe en el sistema de archivos
	*/
	static function existe( $nombre )
	{
		$proyectos = self::get_lista();
		if ( in_array( $nombre, $proyectos ) ) {
			return true;	
		} else {
			return false;	
		}
	}
	
	/**
	*	Crea un proyecto NUEVO
	*/
	static function crear( instancia $instancia, $nombre, $usuarios_a_vincular )
	{
		//- 1 - Controles
		$dir_template = toba_dir() . self::template_proyecto;
		if ( $nombre == 'toba' ) {
			throw new excepcion_toba("INSTALACION: No es posible crear un proyecto con el nombre 'toba'");	
		}
		if ( self::existe( $nombre ) ) {
			throw new excepcion_toba("INSTALACION: Ya existe una carpeta con el nombre '$nombre' en la carpeta 'proyectos'");	
		}
		try {
			//- 2 - Modificaciones en el sistema de archivos
			$dir_proyecto = toba_dir() . '/proyectos/' . $nombre;
			// Creo la CARPETA del PROYECTO
			manejador_archivos::copiar_directorio( $dir_template, $dir_proyecto );
			// Modifico los archivos
			$editor = new editor_archivos();
			$editor->agregar_sustitucion( '|__proyecto__|', $nombre );
			$editor->agregar_sustitucion( '|__instancia__|', $instancia->get_id() );
			$editor->agregar_sustitucion( '|__toba_dir__|', manejador_archivos::path_a_unix( toba_dir() ) );
			$editor->procesar_archivo( $dir_proyecto . '/www/admin.php' );
			$editor->procesar_archivo( $dir_proyecto . '/www/aplicacion.php' );
			$editor->procesar_archivo( $dir_proyecto . '/toba.conf' );
			// Asocio el proyecto a la instancia
			$instancia->vincular_proyecto( $nombre );

			//- 3 - Modificaciones en la BASE de datos
			$db = $instancia->get_db();
			try {
				$db->abrir_transaccion();
				$db->retrazar_constraints();
				$db->ejecutar( self::get_sql_metadatos_basicos( $nombre ) );
				$sql_version = self::get_sql_actualizar_version( instalacion::get_version_actual(),
																$nombre);
				$db->ejecutar($sql_version);
				foreach( $usuarios_a_vincular as $usuario ) {
					$db->ejecutar( self::get_sql_vincular_usuario( $nombre, $usuario, 'admin', 'no' ) );
				}
				$db->cerrar_transaccion();
			} catch ( excepcion_toba $e ) {
				$db->abortar_transaccion();
				$txt = 'PROYECTO : Ha ocurrido un error durante la carga de METADATOS del PROYECTO. DETALLE: ' . $e->getMessage();
				throw new excepcion_toba( $txt );
			}
		} catch ( excepcion_toba $e ) {
			// Borro la carpeta creada
			if ( is_dir( $dir_proyecto ) ) {
				$instancia->desvincular_proyecto( $nombre );
				manejador_archivos::eliminar_directorio( $dir_proyecto );
			}	
			throw $e;
		}
	}
	
	/**
	*	Sentencias de creacion de los metadatos BASICOS
	*/
	static function get_sql_metadatos_basicos( $id_proyecto )
	{
		// Creo el proyecto
		$sql[] = "INSERT INTO apex_proyecto (proyecto, estilo,descripcion,descripcion_corta,listar_multiproyecto) VALUES ('$id_proyecto','toba','".strtoupper($id_proyecto)."','".ucwords($id_proyecto)."',1);";
		//Le agrego los items basicos
		$sql[] = "INSERT INTO apex_item (proyecto, item, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, nombre, descripcion, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron) VALUES ('$id_proyecto','','$id_proyecto','','1','0','browser','toba','NO','Raiz PROYECTO','','toba','0','toba','especifico');";
		$sql[] = "INSERT INTO apex_item (proyecto, item, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, nombre, descripcion, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron) VALUES ('$id_proyecto','/autovinculo','$id_proyecto','','0','0','fantasma','toba','NO','Autovinculo','','toba','0','toba','especifico');";
		$sql[] = "INSERT INTO apex_item (proyecto, item, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, nombre, descripcion, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron) VALUES ('$id_proyecto','/vinculos','$id_proyecto','','0','0','fantasma','toba','NO','Vinculador','','toba','0','toba','especifico');";
		// Creo un grupo de acceso
		$sql[] = "INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion) VALUES ('$id_proyecto','admin','Administrador','0','Accede a toda la funcionalidad');";
		// Creo un perfil de datos
		$sql[] = "INSERT INTO apex_usuario_perfil_datos (proyecto, usuario_perfil_datos, nombre, descripcion) VALUES ('$id_proyecto','no','No posee','');";
		return $sql;		
	}

	static function get_sql_vincular_usuario( $proyecto, $usuario, $perfil_acceso, $perfil_datos )
	{
		return "INSERT INTO apex_usuario_proyecto (proyecto, usuario, usuario_grupo_acc, usuario_perfil_datos)
		VALUES ('$proyecto','$usuario','$perfil_acceso','$perfil_datos');";
	}
}
?>
