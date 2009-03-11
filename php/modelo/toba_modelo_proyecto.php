<?php
/**
*	Administrador de metadatos de PROYECTOS
*/
class toba_modelo_proyecto extends toba_modelo_elemento
{
	private $instancia;
	private $identificador;
	private $dir;
	private $sincro_archivos;
	private $db;
	private $aplicacion_comando;
	private $aplicacion_modelo;
	const dump_prefijo_componentes = 'dump_';
	const dump_prefijo_permisos = 'grupo_acceso__';
	const compilar_archivo_referencia = 'tabla_tipos';
	const template_proyecto = '/php/modelo/template_proyecto';

	function __construct( toba_modelo_instancia $instancia, $identificador )
	{
		$this->instancia = $instancia;
		$this->identificador = $identificador;
		$this->dir = $instancia->get_path_proyecto($identificador);
		if( ! is_dir( $this->dir ) ) {
			throw new toba_error("PROYECTO: El proyecto '{$this->identificador}' es invalido. (la carpeta '{$this->dir}' no existe)");
		}
		$this->db = $this->instancia->get_db();
		toba_contexto_info::set_db($this->get_db());
		toba_contexto_info::set_proyecto($this->identificador);
		toba_logger::instancia()->debug('PROYECTO "'.$this->identificador.'"');
	}

	function get_sincronizador()
	{
		if ( ! isset( $this->sincro_archivos ) ) {
			//-- Si estamos en produccion, evitar sincronizar los perfiles de acceso
			if ($this->maneja_perfiles_produccion()) {
				$patron = "#componentes|tablas#"; //Los permisos los exporta localmente
			} else {
				$patron = null;						
			}
			$this->sincro_archivos = new toba_sincronizador_archivos( $this->get_dir_dump(), $patron);
		}
		return $this->sincro_archivos;
	}
	
	/**
	 * @return toba_aplicacion_comando
	 */
	function get_aplicacion_comando()
	{
		if (! isset($this->aplicacion_comando)) {
			$id_proyecto = $this->get_id();
			$archivo_proy = $this->instancia->get_path_proyecto($id_proyecto)."/php/extension_toba/".$id_proyecto."_comando.php";			
			if (file_exists($archivo_proy)) {
				require_once($archivo_proy);
				$clase = $id_proyecto.'_comando';
				$this->aplicacion_comando = new $clase();
				$modelo = $this->get_aplicacion_modelo();
				$this->aplicacion_comando->set_entorno($this->manejador_interface, $modelo);
			}
		}
		if (isset($this->aplicacion_comando)) {
			return $this->aplicacion_comando;
		}
	}
	
	/**
	 * @return toba_aplicacion_modelo
	 */
	function get_aplicacion_modelo()
	{
		if (! isset($this->aplicacion_modelo)) {
			$id_proyecto = $this->get_id();
			$archivo_proy = $this->instancia->get_path_proyecto($id_proyecto)."/php/extension_toba/".$id_proyecto."_modelo.php";			
			if (file_exists($archivo_proy)) {
				require_once($archivo_proy);
				$clase = $id_proyecto.'_modelo';
				$this->aplicacion_modelo = new $clase();
				$this->aplicacion_modelo->set_entorno($this->manejador_interface, $this->get_instalacion(), $this->get_instancia(), $this);
			}
		}
		if (isset($this->aplicacion_modelo)) {
			return $this->aplicacion_modelo;
		}		
	}

	//-----------------------------------------------------------
	//	Informacion BASICA
	//-----------------------------------------------------------

	function get_id()
	{
		return $this->identificador;
	}
	
	
	function get_url()
	{
		$id = $this->get_id();
		$url = $this->instancia->get_url_proyecto($id);
		if ($url == '') {
			$version = $this->get_version_proyecto();
			$url = '/'.$id.'/'.$version->get_release();
		}
		return $url;
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

	function get_dir_permisos()
	{
		if ($this->maneja_perfiles_produccion()) {
			return $this->get_instancia()->get_dir_instalacion_proyecto($this->identificador).'/perfiles';
		} else {		
			return $this->get_dir_dump() . '/permisos';
		}
	}
	
	function get_dir_componentes_compilados()
	{
		return $this->dir . '/metadatos_compilados';
	}

	function get_dir_generales_compilados()
	{
		return $this->dir . '/metadatos_compilados/gene';
	}

	/**
	 * @return toba_modelo_instancia
	 */	
	function get_instancia()
	{
		return $this->instancia;
	}
	
	/**
	 * @return toba_modelo_instalacion
	 */
	function get_instalacion()
	{	
		return $this->instancia->get_instalacion();
	}
	
	function get_db($refrescar = false)
	{
		if (! isset($this->db) || $refrescar) {
			$this->db = $this->instancia->get_db();	
		}
		return $this->db;
	}
	
	
	/**
	 * Retorna una referencia a la fuente de datos predeterminada del proyecto
	 * @return toba_db
	 */
	function get_db_negocio()
	{
		$fuentes = $this->get_indice_fuentes();
		if (empty($fuentes)) {
			return;
		}
		$fuente_defecto = toba_info_editores::get_fuente_datos_defecto($this->identificador);
		if (! isset($fuente_defecto)) {
			$fuente_defecto = current($fuentes);		
		}
		$id_def_base = $this->construir_id_def_base($fuente_defecto);
		return $this->get_instalacion()->conectar_base($id_def_base);		
	}
	
	/**
	 * Retorna arreglo asociativo con parametros de la conexion a la fuente de de datos predeterminada
	 * @return array
	 */
	function get_parametros_db_negocio()
	{
		$fuentes = $this->get_indice_fuentes();
		if (empty($fuentes)) {
			return;
		}
		$fuente_defecto = toba_info_editores::get_fuente_datos_defecto($this->identificador);
		if (! isset($fuente_defecto)) {
			$fuente_defecto = current($fuentes);		
		}
		$id_def_base = $this->construir_id_def_base($fuente_defecto);
		return $this->get_instalacion()->get_parametros_base($id_def_base);		
	}
	
	
	/**
	 * Determina si el proyecto debe guardar/cargar sus perfiles desde la instalacion (produccion) o el proyecto (desarrollo)
	 *
	 */
	function maneja_perfiles_produccion()
	{
		return $this->get_instalacion()->es_produccion() && $this->instancia->get_proyecto_usar_perfiles_propios($this->identificador);	
	}
	
	/**
	 * Dado el nombre de una fuente construye el id a utilizar en bases.ini unido a la instancia actual
	 */
	function construir_id_def_base($nombre_fuente)
	{
		return $this->get_instancia()->get_id().' '.$this->get_id().' '.$nombre_fuente;
	}

	//-----------------------------------------------------------
	//	ACTUALIZAR SVN
	//-----------------------------------------------------------

	function actualizar()
	{
		$dir = $this->get_dir();
		system("svn update --non-interactive $dir");		
	}
	
	//-----------------------------------------------------------
	//	EXPORTAR
	//-----------------------------------------------------------

	/**
	 * Exporta la información exclusiva de la implementación, es decir perfiles, usuarios, logs ,etc. 
	 */
	function exportar_implementacion()
	{
		$this->get_instancia()->exportar_local();
		
	}
	
	function exportar()
	{
		toba_logger::instancia()->debug( "Exportando PROYECTO {$this->identificador}");
		$this->manejador_interface->titulo( "Exportación PROYECTO {$this->identificador}" );		
		$existe_vinculo = $this->instancia->existe_proyecto_vinculado( $this->identificador );
		$existen_metadatos = $this->instancia->existen_metadatos_proyecto( $this->identificador );
		if( !( $existen_metadatos || $existe_vinculo ) ) {
			throw new toba_error("PROYECTO: El proyecto '{$this->identificador}' no esta asociado a la instancia actual");
		}
		try {
			$this->exportar_tablas();
			$this->exportar_componentes();
			$this->exportar_perfiles();
			$this->sincronizar_archivos();
		} catch ( toba_error $e ) {
			throw new toba_error( "Proyecto {$this->identificador}: Ha ocurrido un error durante la exportacion:\n".
												$e->getMessage() );
		}
	}
	
	private function sincronizar_archivos()
	{
		$obs = $this->get_sincronizador()->sincronizar();
		$this->manejador_interface->lista( $obs, 'Observaciones' );
	}

	//-- TABLAS -------------------------------------------------------------

	private function exportar_tablas()
	{
		$this->manejador_interface->mensaje("Exportando datos generales", false);
		toba_manejador_archivos::crear_arbol_directorios( $this->get_dir_tablas() );
		foreach ( toba_db_tablas_proyecto::get_lista() as $tabla ) {
			$contenido = $this->get_contenido_tabla($tabla);			
			if ( trim( $contenido ) != '' ) {
				$this->guardar_archivo( $this->get_dir_tablas() .'/'. $tabla . '.sql', $contenido );			
			}
			$this->manejador_interface->progreso_avanzar();
		}
		$this->manejador_interface->progreso_fin();
	}

	private function get_contenido_tabla($tabla, $where_extra=null)
	{
		$datos = $this->get_contenido_tabla_datos($tabla, $where_extra);		
		$definicion = toba_db_tablas_proyecto::$tabla();
		$columna_grupo_desarrollo = null;
		if (isset($definicion['columna_grupo_desarrollo'])) {
			$columna_grupo_desarrollo = $definicion['columna_grupo_desarrollo'];
		}		
		return $this->get_contenido_exportacion_datos($tabla, $datos, $columna_grupo_desarrollo);
	}
	
	private function get_contenido_tabla_datos($tabla, $where_extra=null)
	{
		$definicion = toba_db_tablas_proyecto::$tabla();
		//Genero el SQL
		if( isset($definicion['dump_where']) && ( trim($definicion['dump_where']) != '') ) {
   			$w = stripslashes($definicion['dump_where']);
   			$where = ereg_replace("%%",$this->get_id(), $w);
        } else {
   			$where = " ( proyecto = '".$this->get_id()."')";
		}
		if(isset($where_extra)) $where = $where . ' AND ('. $where_extra .')';
		$sql = "SELECT " . implode(', ', $definicion['columnas']) .
				" FROM $tabla " .
				" WHERE $where " .
				" ORDER BY {$definicion['dump_order_by']} ;\n";
		$datos = $this->db->consultar($sql);
		$regs = count( $datos );
		if ( $regs > 1 ) {
			$columnas_orden = array_map('trim', explode(',',$definicion['dump_order_by']) );
			$datos = rs_ordenar_por_columnas( $datos, $columnas_orden );
		}
		toba_logger::instancia()->debug("TABLA  $tabla  ($regs reg.)");		
		return $datos;		
	}

	
	//-- COMPONENTES -------------------------------------------------------------

	/*
	*	Exporta los componentes
	*/
	private function exportar_componentes()
	{
		$this->manejador_interface->mensaje("Exportando componentes", false);
		toba_cargador::instancia()->crear_cache_simple( $this->get_id(), $this->db );
		foreach ($this->get_lista_tipo_componentes() as $tipo) {
			foreach ( $this->get_lista_componentes( $tipo ) as $id_componente) {
				$this->exportar_componente( $tipo, $id_componente );
			}
			$this->manejador_interface->progreso_avanzar();
		}
		$this->manejador_interface->progreso_fin();		
	}
	
	/*
	*	Exporta un componente
	*/
	private function exportar_componente( $tipo, $id )
	{
		$directorio = $this->get_dir_componentes() . '/' . $tipo;
		toba_manejador_archivos::crear_arbol_directorios( $directorio );
		$archivo = toba_manejador_archivos::nombre_valido( self::dump_prefijo_componentes . $id['componente'] );
		$contenido =&  $this->get_contenido_componente( $tipo, $id );
		$this->guardar_archivo( $directorio .'/'. $archivo . '.sql', $contenido ); 
		toba_logger::instancia()->debug("COMPONENTE $tipo  --  " . $id['componente'] . 
									' ('.$this->cant_reg_exp.' reg.)');
	}
	
	/*
	*	Genera el contenido de la exportacion de un componente
	*/
	private function & get_contenido_componente( $tipo, $id )
	{
		$this->cant_reg_exp = 0;
		//Recupero metadatos
		$metadatos = toba_cargador::instancia()->get_metadatos_simples( $id, $tipo, $this->db );
		//Obtengo el nombre del componente
		if ( isset($metadatos['apex_objeto']) ) {
			$nombre_componente = $metadatos['apex_objeto'][0]['nombre'];		
		} elseif (isset($metadatos['apex_molde_operacion'])) {
			$nombre_componente = $metadatos['apex_molde_operacion'][0]['nombre'];	
		} else {
			$nombre_componente = $metadatos['apex_item'][0]['nombre'];		
		}
		//Genero el CONTENIDO
		$contenido = "------------------------------------------------------------\n";
		$contenido .= "--[{$id['componente']}]--  $nombre_componente \n";
		$contenido .= "------------------------------------------------------------\n";
		foreach ( $metadatos as $tabla => $datos) {
			$definicion = toba_db_tablas_componente::$tabla();
			$columna_grupo_desarrollo = null;
			if (isset($definicion['columna_grupo_desarrollo'])) {
				$columna_grupo_desarrollo = $definicion['columna_grupo_desarrollo'];
			}
			$contenido .= $this->get_contenido_exportacion_datos($tabla, $datos, $columna_grupo_desarrollo);
		}
		return $contenido;		
	}
	
	private function get_contenido_exportacion_datos($tabla, $datos, $columna_grupo_desarrollo)
	{
		//--- Si no se especifico porque columna filtrar el grupo de desarrollo buscar en las secuencias
		if (! isset($columna_grupo_desarrollo)) {
			$columna_grupo_desarrollo = $this->instancia->get_campo_secuencia_de_tabla($tabla);
		}
		$contenido = '';
		$grupo_actual = -1;		
		for ( $a = 0; $a < count($datos) ; $a++ ) {
			if (isset($columna_grupo_desarrollo)) {
				$valor = $datos[$a][$columna_grupo_desarrollo];
				$grupo = $this->instancia->get_grupo_desarrollo_de_valor($valor);
				if ($grupo !== $grupo_actual) {
					if ($grupo_actual !== -1) {
						$contenido .= "--- FIN Grupo de desarrollo $grupo_actual\n";
					}
					$contenido .= "\n--- INICIO Grupo de desarrollo $grupo\n";
					$grupo_actual = $grupo;
				}
			}
			$contenido .= sql_array_a_insert_formateado( $tabla, $datos[$a] );
		}
		if ($grupo_actual !== -1) {
			$contenido .= "--- FIN Grupo de desarrollo $grupo_actual\n";
		}
		if ($contenido != '') {
			$extra = "\n------------------------------------------------------------\n";
			$extra .= "-- $tabla\n";
			$extra .= "------------------------------------------------------------\n";
			$contenido = $extra.$contenido;				
		}
		return $contenido;		
	}

	//-- PERFILES -------------------------------------------------------------

	function exportar_perfiles()
	{
		$this->manejador_interface->mensaje("Exportando perfiles", false);
		if ($this->maneja_perfiles_produccion()) {
			$this->exportar_perfiles_produccion();
		} else {
			$this->exportar_perfiles_proyecto();
		}
		$this->manejador_interface->progreso_fin();				
	}
	
	private function exportar_perfiles_proyecto()
	{
		//-- Perfiles Funcionales
		toba_manejador_archivos::crear_arbol_directorios( $this->get_dir_permisos() );
		$tablas = array('apex_usuario_grupo_acc', 'apex_usuario_grupo_acc_item', 'apex_permiso_grupo_acc', 'apex_grupo_acc_restriccion_funcional');
		foreach( $this->get_indice_grupos_acceso() as $permiso ) {
			toba_logger::instancia()->debug("PERMISO  $permiso");
			$contenido = '';		
			$where = "usuario_grupo_acc = '$permiso'";
			foreach($tablas as $tabla) {
				$contenido .= $this->get_contenido_tabla($tabla, $where);
			}
			if ( $contenido ) {
				$this->guardar_archivo( $this->get_dir_permisos() .'/'. self::dump_prefijo_permisos . $permiso . '.sql', $contenido );			
				$this->manejador_interface->progreso_avanzar();
			}			
		}
		
		//-- Perfiles de datos
		$tablas = array('apex_usuario_perfil_datos', 'apex_usuario_perfil_datos_dims');
		foreach ($tablas as $tabla ) {
			$contenido = $this->get_contenido_tabla($tabla);	
			if ( trim( $contenido ) != '' ) {
				$this->guardar_archivo( $this->get_dir_permisos() .'/'. $tabla . '.sql', $contenido );			
			}
			$this->manejador_interface->progreso_avanzar();
		}
	}

	function exportar_perfiles_produccion()
	{
		$dir_perfiles = $this->get_dir_permisos();
		//-- Borra los perfiles anteriormente guardados, si existen
		if (file_exists($dir_perfiles)) {
			toba_manejador_archivos::eliminar_directorio($dir_perfiles);
		}
		//-- Perfiles Funcionales
		toba_manejador_archivos::crear_arbol_directorios($dir_perfiles);
		$tablas = array('apex_usuario_grupo_acc', 'apex_usuario_grupo_acc_item', 'apex_permiso_grupo_acc', 'apex_grupo_acc_restriccion_funcional');
		foreach( $this->get_indice_grupos_acceso() as $permiso ) {
			toba_logger::instancia()->debug("PERFIL  $permiso");
			$contenido = '';		
			$where = "usuario_grupo_acc = '$permiso'";
			$datos = array();
			foreach($tablas as $tabla) {
				$datos[$tabla] = $this->get_contenido_tabla_datos($tabla, $where);
			}
			$archivo = $dir_perfiles."/perfil_$permiso.xml";
			$xml = new toba_xml_tablas();
			$xml->set_tablas($datos);
			$xml->guardar($archivo);
			$this->manejador_interface->progreso_avanzar();
		}
		//--- Perfiles de datos
		$tablas = array('apex_usuario_perfil_datos', 'apex_usuario_perfil_datos_dims');
		$datos = array();
		foreach ($tablas as $tabla) {
			$datos[$tabla] = $this->get_contenido_tabla_datos($tabla);	
		}
		$archivo = $dir_perfiles."/perfiles_datos.xml";
		$xml = new toba_xml_tablas();
		$xml->set_tablas($datos);
		$xml->guardar($archivo);
		$this->manejador_interface->progreso_avanzar();				
	}	
	
	//-----------------------------------------------------------
	//	CARGAR
	//-----------------------------------------------------------
	
	/*
	*	Carga en proyecto en una transaccion
	*/
	function cargar_autonomo()
	{
		toba_logger::instancia()->debug( "Cargando PROYECTO {$this->identificador}");
		$errores = array();					
		try {
			$this->db->abrir_transaccion();
			$this->db->retrazar_constraints();
			$errores = $this->cargar();
			$this->instancia->actualizar_secuencias();
			$this->db->cerrar_transaccion();
		} catch ( toba_error $e ) {
			$this->db->abortar_transaccion();
			throw $e;
		}
		return $errores;
	}

	/*
	*	Carga un proyecto
	*/
	function cargar()
	{
		toba_logger::instancia()->debug("Cargando proyecto '{$this->identificador}'");
		if( ! ( $this->instancia->existe_proyecto_vinculado( $this->identificador ) ) ) {
			throw new toba_error("PROYECTO: El proyecto '{$this->identificador}' no esta asociado a la instancia actual");
		}
		$this->cargar_tablas();
		$this->cargar_componentes();
		$errores = $this->cargar_perfiles();
		return $errores;
	}

	private function cargar_tablas()
	{
		$this->manejador_interface->mensaje('Cargando datos globales', false);
		$archivos = toba_manejador_archivos::get_archivos_directorio( $this->get_dir_tablas(), '|.*\.sql|' );
		$cant_total = 0;
		foreach( $archivos as $archivo ) {
			$cant = $this->db->ejecutar_archivo( $archivo );
			toba_logger::instancia()->debug($archivo . ". ($cant)");
			$this->manejador_interface->progreso_avanzar();
			$cant_total++;
		}
		$this->manejador_interface->progreso_fin();
	}
	
	function get_sql_cargar_tablas()
	{
		$salida = '';
		$archivos = toba_manejador_archivos::get_archivos_directorio( $this->get_dir_tablas(), '|.*\.sql|' );		
		foreach( $archivos as $archivo ) {
			$salida .= file_get_contents($archivo)."\n\n";
		}		
		return $salida;
	}

	
	private function cargar_componentes()
	{
		$this->manejador_interface->mensaje('Cargando componentes', false);		
		$subdirs = toba_manejador_archivos::get_subdirectorios( $this->get_dir_componentes() );
		foreach ( $subdirs as $dir ) {
			$archivos = toba_manejador_archivos::get_archivos_directorio( $dir , '|.*\.sql|' );
			foreach( $archivos as $archivo ) {
				$cant = $this->db->ejecutar_archivo( $archivo );
				toba_logger::instancia()->debug($archivo . " ($cant)");
			}
			$this->manejador_interface->progreso_avanzar();			
		}
		$this->manejador_interface->progreso_fin();
	}
	
	function get_sql_cargar_componentes()
	{
		$salida = '';
		$subdirs = toba_manejador_archivos::get_subdirectorios( $this->get_dir_componentes() );
		foreach ( $subdirs as $dir ) {
			$archivos = toba_manejador_archivos::get_archivos_directorio( $dir , '|.*\.sql|' );
			foreach( $archivos as $archivo ) {
				$salida .= file_get_contents($archivo)."\n\n";
			}
		}
		return $salida;
	}		

	function get_sql_carga_reducida()
	{
		$this->get_dir_tablas() . '/apex_proyecto.sql';
	}
	
	/*
	*	Carga el conjunto de metadatos minimo
	*/
	function cargar_informacion_reducida()
	{
		// Cabecera del proyecto
		$this->manejador_interface->mensaje('Cargando datos globales', false);
		$archivo = $this->get_dir_tablas() . '/apex_proyecto.sql';
		$this->db->ejecutar_archivo( $archivo );
		$this->manejador_interface->mensaje('.OK');	
		// Grupos de acceso y permisos
		$this->cargar_perfiles();
	}
	
	
	//------------------------------------------------------------------
	//		CARGA DE PERFILES
	//------------------------------------------------------------------
	
	
	private function cargar_perfiles()
	{
		$this->manejador_interface->mensaje('Cargando permisos', false);
		$errores = array();
		if ($this->maneja_perfiles_produccion()) {
			$errores = $this->cargar_perfiles_produccion();		
		} else {
			$this->cargar_perfiles_proyecto();
		}
		return $errores;
	}
	
	private function cargar_perfiles_proyecto()
	{
		$archivos = toba_manejador_archivos::get_archivos_directorio( $this->get_dir_permisos(), '|.*\.sql|' );
		$cant_total = 0;
		foreach( $archivos as $archivo ) {
			$cant = $this->db->ejecutar_archivo( $archivo );
			toba_logger::instancia()->debug($archivo . ". ($cant)");
			$this->manejador_interface->progreso_avanzar();
			$cant_total++;
		}
		$this->manejador_interface->progreso_fin();
	}
	
	private function cargar_perfiles_produccion()
	{
		$todos_errores = array();
		$archivos = toba_manejador_archivos::get_archivos_directorio( $this->get_dir_permisos(), '|.*\.xml$|' );
		foreach( $archivos as $archivo ) {
			$perfil = basename($archivo, 'xml');
			$xml = new toba_xml_tablas($archivo);
			$errores = $xml->insertar_db($this->db);
			if (! empty($errores)) {
				$msg = "ATENCION! No fue posible cargar por completo el '$perfil', posiblemente a causa de que al menos una operación, restricción o derecho ha dejado de existir en '{$this->identificador}'.";
				$msg .= " A continuación el detalle:";
				$this->manejador_interface->separador();
				$this->manejador_interface->error($msg);
				foreach ($errores as $error) {
					$this->manejador_interface->error($error['msg_motor']);
				}
				$this->manejador_interface->error('De todas formas se continúa la carga, se recomienda revisar la definición de este perfil.');
				$this->manejador_interface->separador();
				$todos_errores = array_merge($todos_errores, $errores);
			}
			toba_logger::instancia()->debug($archivo);
			$this->manejador_interface->progreso_avanzar();
		}
		$this->manejador_interface->progreso_fin();
		return $todos_errores;
	}

	function get_sql_carga_perfiles()
	{
		$salida = '';
		$archivos = toba_manejador_archivos::get_archivos_directorio( $this->get_dir_permisos(), '|.*\.sql|' );		
		foreach( $archivos as $archivo ) {
			$salida .= file_get_contents($archivo)."\n\n";
		}		
		return $salida;
	}		

	
	//------------------------------------------------------------------
	//		PUBLICACION
	//------------------------------------------------------------------	
	
	function publicar($url=null)
	{
		if (! $this->esta_publicado()) {
			if ($url == '') {
				$url = $this->get_url();
			}
			$this->instancia->set_url_proyecto($this->get_id(), $url);
			toba_modelo_instalacion::agregar_alias_apache($url,
															$this->get_dir(),
															$this->get_instancia()->get_id(),
															$this->get_id());
			$this->actualizar_punto_acceso($url, $this->get_id());
		}
	}
	
	function despublicar()
	{
		if ($this->esta_publicado()) {
			toba_modelo_instalacion::quitar_alias_apache($this->get_id());
		}
	}
	
	function esta_publicado()
	{
		return toba_modelo_instalacion::existe_alias_apache($this->get_id());
	}
	
	private function actualizar_punto_acceso($url, $proyecto)
	{				
		$punto = $this->db->quote($url);
		$proyecto = $this->db->quote($proyecto);
		
		$sql = "UPDATE apex_admin_param_previsualizazion SET punto_acceso = $punto
				WHERE 	proyecto = $proyecto";					
		$this->db->ejecutar($sql);
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
		} catch ( toba_error $e ) {
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
		toba_logger::instancia()->debug("Eliminacion. Registros borrados: $cant");
		$this->manejador_interface->progreso_fin();				
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
		$catalogos['toba_db_tablas_componente'][] = 'get_lista';
		$catalogos['toba_db_tablas_proyecto'][] = 'get_lista';
		$catalogos['toba_db_tablas_proyecto'][] = 'get_lista_permisos';
		$catalogos['toba_db_tablas_instancia'][] = 'get_lista_proyecto';
		$catalogos['toba_db_tablas_instancia'][] = 'get_lista_proyecto_log';
		$catalogos['toba_db_tablas_instancia'][] = 'get_lista_proyecto_usuario';
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
		$this->manejador_interface->titulo( "Regenerando PROYECTO {$this->identificador}" );		
		toba_logger::instancia()->debug( "Regenerando PROYECTO {$this->identificador}");
		try {
			$this->db->abrir_transaccion();
			$this->db->retrazar_constraints();
			$this->instancia->exportar_local_proyecto($this->identificador);
			$this->eliminar();
			$this->cargar();
			$this->instancia->cargar_informacion_instancia_proyecto( $this->identificador );
			$this->instancia->actualizar_secuencias();			
			$this->db->cerrar_transaccion();
		} catch ( toba_error $e ) {
			$this->db->abortar_transaccion();
			throw $e;
		}
	}

	//-----------------------------------------------------------
	//	COMPILAR
	//-----------------------------------------------------------

	function compilar()
	{
		try {
			$this->compilar_componentes();
			$this->compilar_metadatos_generales();
			$this->compilar_operaciones();
		} catch ( toba_error $e ) {
			$this->manejador_interface->error( "PROYECTO {$this->identificador}: Ha ocurrido un error durante la compilacion:\n".$e->getMessage());
		}
	}

	/*
	*	Ciclo de compilacion de componentes

	*/
	function compilar_componentes()
	{
		$this->manejador_interface->titulo("Compilando componentes");
		foreach ($this->get_lista_tipo_componentes() as $tipo) {
			$c = 0;
			$this->manejador_interface->mensaje( $tipo, false );
			if ( $tipo == 'toba_item' ) {
				$directorio = $this->get_dir_componentes_compilados() . '/item';
			} else {
				$directorio = $this->get_dir_componentes_compilados() . '/comp';
			}
			toba_manejador_archivos::crear_arbol_directorios( $directorio );
			foreach ( $this->get_lista_componentes( $tipo ) as $id_componente) {
				$this->compilar_componente( $tipo, $id_componente, $directorio );
				$c++;
			}
			$this->manejador_interface->mensaje("($c) OK");
		}
	}
	
	/*
	*	Compila un componente
	*/
	function compilar_componente( $tipo, $id, $directorio )
	{
		//Armo la clase compilada
		$this->manejador_interface->progreso_avanzar();
		toba_logger::instancia()->debug("COMPONENTE --  " . $id['componente']);
		$prefijo = ($tipo == 'toba_item') ? 'toba_mc_item__' : 'toba_mc_comp__';
		$nombre = toba_manejador_archivos::nombre_valido( $prefijo . $id['componente'] );
		$clase = new toba_clase_datos( $nombre );		
		$metadatos = toba_cargador::instancia()->get_metadatos_extendidos( $id, $tipo, $this->db );
		$clase->agregar_metodo_datos('get_metadatos',$metadatos);
		//Creo el archivo
		$path = $directorio .'/'. $nombre . '.php';
		$clase->guardar( $path );
	}

	/*
	*	Compila los metadatos que no son componentes
	*/
	function compilar_metadatos_generales()
	{
		$this->manejador_interface->titulo("Compilando datos generales");
		toba_proyecto_db::set_db( $this->db );
		$path = $this->get_dir_generales_compilados();
		toba_manejador_archivos::crear_arbol_directorios( $path );
		$this->compilar_metadatos_generales_basicos();
		$this->compilar_metadatos_generales_grupos_acceso();
		$this->compilar_metadatos_generales_puntos_control();
		$this->compilar_metadatos_generales_mensajes();
		$this->compilar_metadatos_generales_dimensiones();
		$this->compilar_metadatos_generales_consultas_php();
	}

	/**
	*	Compilacion de DATOS BASICOS
	*/	
	private function compilar_metadatos_generales_basicos()
	{
		//-- Datos basicos --
		$this->manejador_interface->mensaje('Info basica', false);
		$nombre_clase = 'toba_mc_gene__basicos';
		$archivo = $this->get_dir_generales_compilados() . '/' . $nombre_clase . '.php';
		$clase = new toba_clase_datos( $nombre_clase );
		$datos = toba_proyecto_db::cargar_info_basica( $this->get_id() );
		$clase->agregar_metodo_datos('info_basica', $datos);
		$this->manejador_interface->progreso_avanzar();
		//-- Fuentes --
		foreach( $this->get_indice_fuentes() as $fuente ) {		
			$datos = toba_proyecto_db::get_info_fuente_datos( $this->get_id(), $fuente );
			//-- Se busca la relacion entre nombre_tabla y dt
			$mapeo = toba_proyecto_db::get_mapeo_tabla_dt($this->get_id(), $fuente);
			$datos['mapeo_tablas_dt'] = $mapeo;
			$clase->agregar_metodo_datos('info_fuente__'.$fuente, $datos );
		}
		$this->manejador_interface->progreso_avanzar();
		//-- Permisos --
		foreach( $this->get_indice_permisos() as $permiso ) {		
			$datos = toba_proyecto_db::get_descripcion_permiso( $this->get_id(), $permiso );
			$clase->agregar_metodo_datos('info_permiso__'.$permiso, $datos );
		}
		$this->manejador_interface->progreso_avanzar();
		//-- Indice de componentes --
		$datos = toba_proyecto_db::get_mapeo_componentes_indice( $this->get_id() );
		$clase->agregar_metodo_datos('info_indices_componentes', $datos );
		$this->manejador_interface->progreso_avanzar();
		//Creo el archivo
		$clase->guardar( $archivo );
		$this->manejador_interface->progreso_fin();
	}

	/**
	*	Compilacion de GRUPOS de ACCESO
	*/	
	function compilar_metadatos_generales_grupos_acceso($limpiar_existentes=false)
	{
		if ($limpiar_existentes) {
			$archivos = toba_manejador_archivos::get_archivos_directorio($this->get_dir_generales_compilados(), '/toba_mc_gene__grupo/');
			foreach($archivos as $archivo) {
				unlink($archivo);
			}
		}
		$this->manejador_interface->mensaje('Grupos de acceso', false);
		foreach( $this->get_indice_grupos_acceso() as $grupo_acceso ) {
			$nombre_clase = 'toba_mc_gene__grupo_' . $grupo_acceso;
			$archivo = $this->get_dir_generales_compilados() . '/' . $nombre_clase . '.php';
			$clase = new toba_clase_datos( $nombre_clase );
			//-- Menu -------------------------------
			$datos = toba_proyecto_db::get_items_menu( $this->get_id(), array($grupo_acceso) );
			$temp = array();
			foreach($datos as $dato) {
				$temp[$dato['proyecto'].'-'.$dato['item']] = $dato;
			}
			$clase->agregar_metodo_datos('get_items_menu', $temp );
			//-- Control acceso ---------------------
			$datos = toba_proyecto_db::get_items_accesibles( $this->get_id(), array($grupo_acceso) );
			$temp = array();
			foreach($datos as $dato) {
				$temp[$dato['proyecto'].'-'.$dato['item']] = $dato;
			}
			$clase->agregar_metodo_datos('get_items_accesibles', $temp );
			//-- Permisos ---------------------------
			$datos = toba_proyecto_db::get_lista_permisos( $this->get_id(), array($grupo_acceso) );
			$temp = array();
			foreach($datos as $dato) {
				$temp[$dato['nombre'].'-'] = $dato;//Se concatena un string porque asi el merge unifica si o si aunque el nombre sea un numero
			}
			$clase->agregar_metodo_datos('get_lista_permisos', $temp );
			//-- Acceso items zonas -----------------
			foreach( $this->get_indice_zonas() as $zona ) {
				$datos = toba_proyecto_db::get_items_zona( $this->get_id(), array($grupo_acceso), $zona );
				$temp = array();
				foreach($datos as $dato) {
					$temp[$dato['item_proyecto'].'-'.$dato['item']] = $dato;
				}
				$clase->agregar_metodo_datos('get_items_zona__'.$zona, $temp );
			}
			//Guardo el archivo
			$clase->guardar( $archivo );
			$this->manejador_interface->progreso_avanzar();
		}		
		
		//-- Agrego los items publicos en un archivo aparte, para que el usuario no autentificado pueda navegarlos
		$nombre_clase = 'toba_mc_gene__items_publicos';
		$archivo = $this->get_dir_generales_compilados() . '/' . $nombre_clase . '.php';	
		$clase = new toba_clase_datos( $nombre_clase );	
		$datos = toba_proyecto_db::get_items_accesibles($this->get_id(), array());
		$temp = array();
		foreach($datos as $dato) {
			$temp[$dato['proyecto'].'-'.$dato['item']] = $dato;
		}
		$clase->agregar_metodo_datos('get_items_accesibles', $temp );		
		$clase->guardar( $archivo );
		$this->manejador_interface->progreso_avanzar();
		
		$this->manejador_interface->progreso_fin();
	}

	/**
	*	Compilacion de PUNTOS de CONTROL
	*/	
	private function compilar_metadatos_generales_puntos_control()
	{
		$this->manejador_interface->mensaje('Puntos de control', false);
		foreach( toba_info_editores::get_puntos_control() as $punto_control ) {
			$nombre_clase = 'toba_mc_gene__pcontrol_' . $punto_control['pto_control'];
			$archivo = $this->get_dir_generales_compilados() . '/'. $nombre_clase .'.php';
			$clase = new toba_clase_datos( $nombre_clase );
			//Cabecera
			$datos['cabecera'] = $punto_control;
			$datos['parametros'] = toba_proyecto_db::punto_control_parametros( $this->get_id(), $punto_control['pto_control'] );
			$datos['controles'] = toba_proyecto_db::punto_control_controles( $this->get_id(), $punto_control['pto_control'] );
			//Guardo el archivo
			$clase->agregar_metodo_datos('get_info', $datos );
			$clase->guardar( $archivo );
			$this->manejador_interface->progreso_avanzar();
		}		
		$this->manejador_interface->progreso_fin();
	}
	
	/**
	*	Compilacion de MENSAJES
	*/	
	function compilar_metadatos_generales_mensajes()
	{
		$this->manejador_interface->mensaje('Mensajes', false);
		//---- Mensajes TOBA ------
		$nombre_clase = 'toba_mc_gene__msj_toba';
		$archivo = $this->get_dir_generales_compilados() . '/' . $nombre_clase . '.php';
		$clase = new toba_clase_datos( $nombre_clase );
		foreach( $this->get_indice_mensajes('toba') as $mensaje ) {
			$datos = toba_proyecto_db::get_mensaje_toba( $mensaje );
			$clase->agregar_metodo_datos('get__'.$mensaje, $datos );
		}		
		$clase->guardar( $archivo );
		$this->manejador_interface->progreso_avanzar();
		//---- Mensajes PROYECTO ------
		$nombre_clase = 'toba_mc_gene__msj_proyecto';
		$archivo = $this->get_dir_generales_compilados() . '/' . $nombre_clase . '.php';
		$clase = new toba_clase_datos( $nombre_clase );
		foreach( $this->get_indice_mensajes() as $mensaje ) {
			$datos = toba_proyecto_db::get_mensaje_proyecto( $this->get_id(), $mensaje );
			$clase->agregar_metodo_datos('get__'.$mensaje, $datos );
		}		
		$clase->guardar( $archivo );
		$this->manejador_interface->progreso_avanzar();
		//---- Mensajes OBJETOS ------
		$nombre_clase = 'toba_mc_gene__msj_proyecto_objeto';
		$archivo = $this->get_dir_generales_compilados() . '/' . $nombre_clase . '.php';
		$clase = new toba_clase_datos( $nombre_clase );
		foreach ($this->get_lista_tipo_componentes() as $tipo) {
			if ($tipo != 'toba_item') {	//Los items no tienen mensajes
				foreach ( $this->get_lista_componentes( $tipo ) as $id_componente) {
					$objeto = $id_componente['componente'];
					foreach( $this->get_indice_mensajes_objeto($objeto) as $mensaje ) {
						$datos = toba_proyecto_db::get_mensaje_objeto( $this->get_id(), $objeto, $mensaje );
						$clase->agregar_metodo_datos('get__'.$objeto.'__'.$mensaje, $datos );
					}		
				}
			}
		}
		$clase->guardar( $archivo );
		$this->manejador_interface->progreso_avanzar();	
		//---------------------------
		$this->manejador_interface->progreso_fin();
	}
	
	/**
	*	Compilacion de DIMENSIONES
	*/	
	private function compilar_metadatos_generales_dimensiones()
	{
		//-- Dimensiones --
		$this->manejador_interface->mensaje('Dimensiones', false);
		$nombre_clase_base = 'toba_mc_gene__dim';
		foreach( $this->get_indice_dimensiones() as $dimension ) {
			$nombre_clase = $nombre_clase_base . '_' . $dimension;
			$archivo = $this->get_dir_generales_compilados() . '/' . $nombre_clase . '.php';
			$clase = new toba_clase_datos( $nombre_clase );
			$datos = toba_proyecto_db::get_info_dimension( $this->get_id(), $dimension );
			$clase->agregar_metodo_datos('get_info', $datos);
			$clase->guardar( $archivo );
			$this->manejador_interface->progreso_avanzar();
		}
		//-- Relaciones entre tablas --
		$nombre_clase_base = 'toba_mc_gene__relacion_tablas';
		foreach( $this->get_indice_fuentes() as $fuente ) {		
			$nombre_clase = $nombre_clase_base . '_' . $fuente;
			$archivo = $this->get_dir_generales_compilados() . '/' . $nombre_clase . '.php';
			$clase = new toba_clase_datos( $nombre_clase );
			$datos = toba_proyecto_db::get_info_relacion_entre_tablas( $this->get_id(), $fuente );
			$clase->agregar_metodo_datos('get_info', $datos );
			$clase->guardar( $archivo );
			$this->manejador_interface->progreso_avanzar();
		}
		$this->manejador_interface->progreso_fin();
	}

	/**
	*	Compilacion de CONSULTAS PHP
	*/	
	private function compilar_metadatos_generales_consultas_php()
	{
		//-- Datos basicos --
		$this->manejador_interface->mensaje('Consultas PHP', false);
		$nombre_clase = 'toba_mc_gene__consultas_php';
		$archivo = $this->get_dir_generales_compilados() . '/' . $nombre_clase . '.php';
		$clase = new toba_clase_datos( $nombre_clase );
		foreach( $this->get_indice_consultas_php() as $clase_consultas ) {		
			$datos = toba_proyecto_db::get_consulta_php( $this->get_id(), $clase_consultas );
			$clase->agregar_metodo_datos('info_consulta_php__'.$clase_consultas, $datos );
			$this->manejador_interface->progreso_avanzar();
		}
		//Creo el archivo
		$clase->guardar( $archivo );
		$this->manejador_interface->progreso_fin();
	}

	private function compilar_operaciones()
	{
		$this->manejador_interface->mensaje('Operaciones resumidas', false);
		foreach( toba_info_editores::get_lista_items() as $item) {
			$clases_creadas = array();	//Indice para proteger no crear una dos veces
			$php = "<?php\n";
			$directorio = $this->get_dir_componentes_compilados() . '/oper';
			toba_manejador_archivos::crear_arbol_directorios( $directorio );
			$nombre_archivo = toba_manejador_archivos::nombre_valido( 'toba_mc_oper__' . $item['id'] );
			$arbol = toba_info_editores::get_arbol_componentes_item($item['proyecto'], $item['id']);
			foreach( $arbol as $componente) {
				$tipo = $componente['tipo'];
				$prefijo_clase = ( $tipo == 'toba_item') ? 'toba_mc_item__' : 'toba_mc_comp__';
				$nombre_clase = toba_manejador_archivos::nombre_valido($prefijo_clase . $componente['componente']);
				if (! in_array($nombre_clase, $clases_creadas)) {
					$clase = new toba_clase_datos( $nombre_clase );		
					$metadatos = toba_cargador::instancia()->get_metadatos_extendidos( 	$componente, 
																						$tipo,
																						$this->db );
					$clase->agregar_metodo_datos('get_metadatos',$metadatos);
					$php .= $clase->get_contenido();
					$clases_creadas[] = $nombre_clase;
				}
			}
			$php .= "\n?>";
			file_put_contents($directorio .'/'. $nombre_archivo . '.php', $php);
			$this->manejador_interface->progreso_avanzar();	
		}
		$this->manejador_interface->progreso_fin();
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

	function get_resumen_componentes_utilizados()
	{
		$sql = "	SELECT clase, COUNT(*) as cantidad
					FROM apex_objeto
					WHERE proyecto = '{$this->identificador}'
					GROUP BY 1
					ORDER BY 2 DESC";
		return $this->instancia->get_db()->consultar( $sql );
	}

	function get_indice_grupos_acceso()
	{
		$rs = toba_info_permisos::get_grupos_acceso();
		$datos = array();
		foreach($rs as $dato) {
			$datos[] = $dato['usuario_grupo_acc'];	
		}
		return $datos;
	}

	function get_indice_fuentes()
	{
		$rs = toba_info_editores::get_fuentes_datos();
		$datos = array();
		foreach($rs as $dato) {
			$datos[] = $dato['fuente_datos'];	
		}
		return $datos;
	}

	function get_indice_permisos()
	{
		$rs = toba_info_permisos::get_lista_permisos();
		$datos = array();
		foreach($rs as $dato) {
			$datos[] = $dato['nombre'];	
		}
		return $datos;
	}

	function get_indice_zonas()
	{
		$rs = toba_info_editores::get_zonas();
		$datos = array();
		foreach($rs as $dato) {
			$datos[] = $dato['zona'];	
		}
		return $datos;
	}					

	function get_indice_mensajes($proyecto=null)
	{
		$rs = toba_info_editores::get_mensajes($proyecto);
		$datos = array();
		foreach($rs as $dato) {
			$datos[] = $dato['indice'];	
		}
		return $datos;
	}

	function get_indice_mensajes_objeto($objeto)
	{
		$rs = toba_info_editores::get_mensajes_objeto($objeto);
		$datos = array();
		foreach($rs as $dato) {
			$datos[] = $dato['indice'];	
		}
		return $datos;
	}

	function get_indice_dimensiones()
	{
		$rs = toba_info_editores::get_dimensiones();
		$datos = array();
		foreach($rs as $dato) {
			$datos[] = $dato['dimension'];	
		}
		return $datos;		
	}

	function get_indice_consultas_php()
	{
		$rs = toba_info_editores::get_consultas_php();
		$datos = array();
		foreach($rs as $dato) {
			$datos[] = $dato['clase'];	
		}
		return $datos;		
	}
	
	//-------------------------------------------------------------------------------------
	//-- Info interna sobre componentes para los procesos
	//-------------------------------------------------------------------------------------

	/**
	*	Devuelve la lista de componentes para los procesos de exportacion y compilacion
	*/
	function get_lista_tipo_componentes()
	{
		$datos = toba_info_editores::get_lista_tipo_componentes(false);
		$datos[] = 'toba_item';
		return $datos;
	}
	
	/**
	*	Lista de componentes del proyecto
	*/
	function get_lista_componentes( $tipo_componente )
	{
		$proyecto = $this->get_id();
		if ($tipo_componente == 'toba_item' ) {
			$sql = "SELECT 	proyecto as 		proyecto,
							item as 			componente
					FROM apex_item 
					WHERE proyecto = '$proyecto'
					ORDER BY 1;";
			$datos = $this->db->consultar( $sql );
		} elseif(strpos($tipo_componente,'toba_asistente')!== false) {
			$sql = "SELECT 	o.proyecto as 		proyecto,
							o.molde as 			componente,
							t.clase
					FROM 	apex_molde_operacion o,
							apex_molde_operacion_tipo t
					WHERE 	o.operacion_tipo = t.operacion_tipo
					AND		t.clase = '$tipo_componente'
					AND		proyecto = '$proyecto'
					ORDER BY 1;";
			$datos = $this->db->consultar( $sql );
		} else {
			$sql = "SELECT 	proyecto as 		proyecto,
							objeto as 			componente
					FROM apex_objeto 
					WHERE proyecto = '$proyecto'
					AND clase = '$tipo_componente'
					ORDER BY 1;";
			$datos = $this->db->consultar( $sql );
		}
		return $datos;
	}

	/**
	*  Retorna el grupo de acceso que será el predeterminado del usuario administrador en la instalación 
	*/
	function get_grupo_acceso_admin()
	{
		$ga = $this->get_lista_grupos_acceso();
		if ( count( $ga ) == 1 ) {
			return $ga[0]['id'];
		} else {
			//--- Si hay un grupo llamado 'admin' lo prefiere, sino toma el primero que encuentra
			foreach ($ga as $grupo) {
				if ($grupo['id'] == 'admin') {
					return 'admin';
				}
			}
			return $ga[0]['id'];
		}
	}
		
	//-----------------------------------------------------------
	//	Manipulacion de METADATOS
	//-----------------------------------------------------------

	function vincular_usuario($usuario, $perfil_acceso, $perfil_datos=array(), $set_previsualizacion=true )
	{
		//-- Perfiles Funcionales
		if (! is_array($perfil_acceso)) {
			$perfil_acceso = array($perfil_acceso);
		}
		//-- Perfiles de datos		
		if (! is_array($perfil_datos)) {
			if (is_null($perfil_datos)) {
				$perfil_datos = array();
			} else {
				$perfil_datos = array($perfil_datos);
			}
		}	
		self::do_vincular_usuario($this->get_db(), $this->get_id(), $usuario, $perfil_acceso, $perfil_datos, $set_previsualizacion, $this->get_url());
	}		

	function desvincular_usuario( $usuario )
	{
		$sql = "DELETE FROM apex_usuario_proyecto 
				WHERE usuario = '$usuario'
				AND proyecto = '".$this->get_id()."'";
		$this->instancia->get_db()->ejecutar( $sql );
	}
	
	function get_item_login()
	{
		$sql = "SELECT item_pre_sesion FROM apex_proyecto WHERE proyecto='{$this->identificador}'";
		$rs = $this->get_db()->consultar($sql);
		return $rs[0]['item_pre_sesion'];
	}
	
	/**
	 * @todo Cuando los toba_info_editores se puedan usar desde consola, cambiar la consulta manual 
	 */
	function actualizar_login($pisar_anterior = false)
	{
		//--- ¿Existe el proyecto editor?
		if (! $this->instancia->existen_metadatos_proyecto( toba_editor::get_id() )) {
			$msg = "No se crea la operación de login porque el proyecto editor no está cargado en la instancia";
			toba_logger::instancia()->info($msg);
			$this->manejador_interface->error($msg);
			return;
		}
		// Contextualizo al nucleo en el proyecto "toba_editor"
		toba_nucleo::instancia()->iniciar_contexto_desde_consola(	$this->instancia->get_id(), 
																	toba_editor::get_id() );
		//--- Averiguo la fuente destino
		$sql = "SELECT proyecto, fuente_datos, descripcion_corta  
				FROM apex_fuente_datos
				WHERE ( proyecto = '{$this->identificador}' )
				ORDER BY 2";
		$fuentes = $this->get_db()->consultar($sql);
		if (empty($fuentes)) {
			throw new toba_error("El proyecto no tiene definida una fuente de datos.");
		} else {
			$fuente = current($fuentes);
		}
		
		//--- Clonando
		$id = array(	'proyecto' => toba_editor::get_id(),
					 	'componente' =>  1000042 );
		$info_item = toba_constructor::get_info($id, 'toba_item');
		$nuevos_datos = array();
		$nuevos_datos['proyecto'] = $this->identificador;
		$nuevos_datos['padre_proyecto'] = $this->identificador;
		$nuevos_datos['padre'] = toba_info_editores::get_item_raiz($this->identificador);
		$nuevos_datos['fuente_datos'] = $fuente['fuente_datos'];
		$nuevos_datos['fuente_datos_proyecto'] = $nuevos_datos['proyecto'];
		$directorio = 'login';
		$clave = $info_item->clonar($nuevos_datos, $directorio);
		
		$this->manejador_interface->progreso_fin();
		
		//--- Actualizar el item de login
		$this->manejador_interface->mensaje("Actualizando el proyecto...", false);
		$sql = "UPDATE apex_proyecto SET item_pre_sesion='{$clave['componente']}'
				WHERE proyecto='{$this->identificador}'";
		$this->get_db()->ejecutar($sql);
		$this->manejador_interface->progreso_fin();
		
		//--- Borrar el item viejo
		if ($pisar_anterior) {
			$this->manejador_interface->mensaje( "Aun no está implementada la eliminación desde consola");
		}		
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
			toba_logger::instancia()->debug("Migrando proyecto {$this->identificador} a la versión ".$version->__toString());
			$this->manejador_interface->mensaje("Migrando proyecto '{$this->identificador}'");
			$version->ejecutar_migracion('proyecto', $this, null, $this->manejador_interface);
			$this->actualizar_campo_version($version);
		} else {
			toba_logger::instancia()->debug("El proyecto {$this->identificador} no necesita migrar a la versión ".$version->__toString());
		}
	}
	
	function ejecutar_migracion_particular($version, $metodo)
	{
		$this->get_db()->abrir_transaccion();		
		$version->ejecutar_migracion('proyecto', $this, $metodo, $this->manejador_interface);
		$this->get_db()->cerrar_transaccion();		
	}

	/**
	 * Retorna el número de versión propio del proyecto
	 * @return toba_version
	 */
	function get_version_proyecto()
	{
		$modelo = $this->get_aplicacion_modelo();
		if (! isset($modelo)) {
			if (file_exists($this->get_dir().'/VERSION')) {
				return new toba_version(file_get_contents($this->get_dir().'/VERSION'));
			} else {
				return toba_modelo_instalacion::get_version_actual();
			}
		} else {
			return $modelo->get_version_actual();
		}
	}
	
	/**
	 * Retorna la versión de TOBA con la cual fue cargado el proyecto en la instancia  
	 * @return toba_version
	 */
	function get_version_actual()
	{
		$sql = "SELECT version_toba FROM apex_proyecto WHERE proyecto='{$this->identificador}'";
		$rs = $this->db->consultar($sql);		
		if (! empty($rs)) { 
			if (! isset($rs[0]['version_toba'])) {
				return toba_version::inicial();
			}
			return new toba_version($rs[0]['version_toba']);
		}
		return toba_version::inicial();
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

	//------------------------------------------------------
	//------- Empaquetar -----------------------------------
	//------------------------------------------------------
	
	/**
	 * Genera y copia los archivos necesarios para el instalador
	 */
	function empaquetar()
	{
		$nombre_ini = 'proyecto.ini';
		$path_ini = $this->get_dir().'/'.$nombre_ini;
		if (! file_exists($path_ini)) {
			throw new toba_error("Para crear el paquete de instalación debe existir el archivo '$nombre_ini' en la raiz del proyecto");
		}
		chdir(toba_dir());
		$ini = new toba_ini($path_ini);
		
		//--- Crea la carpeta destino
		$crear_carpeta = false;
		$empaquetado = $ini->get_datos_entrada('empaquetado');
		if (! isset($empaquetado['path_destino'])) {
			throw new toba_error("'$nombre_ini': Debe indicar 'path_destino' en seccion [empaquetado]");
		}
		$empaquetado['path_destino'].= '/'.$this->get_version_proyecto()->__toString();
		if (file_exists($empaquetado['path_destino'])) {
			if (! is_dir($empaquetado['path_destino'])) {
				throw new toba_error("'$nombre_ini': La ruta '{$empaquetado['path_destino']}' no es un directorio valido");
			}
			if (! toba_manejador_archivos::es_directorio_vacio($empaquetado['path_destino'])) {
				//-- Existe la carpeta y no está vacia, se borra?
				if ($this->manejador_interface->dialogo_simple("La carpeta destino '{$empaquetado['path_destino']}' no esta vacia. Desea borrarla?", 's')) {
					toba_manejador_archivos::eliminar_directorio($empaquetado['path_destino']);
					$crear_carpeta = true;
				}
			}
		} else {
			$crear_carpeta = true;
		}
		if ($crear_carpeta) {
			toba_manejador_archivos::crear_arbol_directorios($empaquetado['path_destino']);
		}
		$empaquetado['path_destino'] = realpath($empaquetado['path_destino']);
		
		//-- Borra de la instancia todo proyecto ajeno a la exportacion
		
		//--- Compila metadatos del proyecto
		$this->compilar();
		
		//--- Incluye el instalador base
		$this->manejador_interface->titulo("Copiando archivos");		
		if (! isset($empaquetado['path_instalador'])) {
			throw new toba_error("'$nombre_ini': Debe indicar 'path_instalador' en seccion [empaquetado]");
		}
		$empaquetado['path_instalador'] = realpath($empaquetado['path_instalador']);
		if (!file_exists($empaquetado['path_instalador']) || !is_dir($empaquetado['path_instalador'])) {
			throw new toba_error("'$nombre_ini': La ruta '{$empaquetado['path_instalador']}' no es un directorio valido");
		}
		$this->manejador_interface->mensaje("Copiando instalador..", false);
		$excepciones = array($empaquetado['path_instalador'].'/ejemplo.proyecto.ini');		
		toba_manejador_archivos::copiar_directorio($empaquetado['path_instalador'], $empaquetado['path_destino'], 
														$excepciones, $this->manejador_interface, false);
		$this->manejador_interface->progreso_fin();
		
		//--- Empaqueta el núcleo de toba y lo deja en destino
		$this->manejador_interface->mensaje("Copiando framework", false);	
		$librerias = array();
		$proyectos = array();
		if (isset($empaquetado['librerias'])) {
			$librerias = explode(',', $empaquetado['librerias']);
			foreach (array_keys($librerias) as $i) {
				$librerias[$i] = trim($librerias[$i]);
			}
		}
		if (isset($empaquetado['proyectos_extra'])) {
			$proyectos = explode(',', $empaquetado['proyectos_extra']);
			foreach (array_keys($proyectos) as $i) {
				$proyectos[$i] = trim($proyectos[$i]);
			}
		}		
		$instalacion = $this->instancia->get_instalacion();
		$destino_instalacion = $empaquetado['path_destino'].'/proyectos/'.$this->get_id().'/toba';
		$instalacion->empaquetar_en_carpeta($destino_instalacion, $librerias, $proyectos);
		$this->manejador_interface->progreso_fin();
		
		//--- Empaqueta el proyecto actual
		$this->manejador_interface->mensaje("Copiando aplicacion", false);		
		$destino_aplicacion = $empaquetado['path_destino'].'/proyectos/'.$this->get_id().'/aplicacion';		
		$excepciones = array();
		if (isset($empaquetado['excepciones_proyecto'])) {
			$excepciones = explode(',', $empaquetado['proyectos_extra']);
			$origen = $this->get_dir();
			foreach (array_keys($excepciones) as $i) {
				$excepciones[$i] = $origen.'/'.trim($excepciones[$i]);
			}			
		}

		$this->empaquetar_proyecto($destino_aplicacion, $excepciones);
		$this->manejador_interface->progreso_fin();

	}
	
	protected function empaquetar_proyecto($destino, $excepciones)
	{
		$origen = $this->get_dir();		
		//-- Los metadatos no se envian ya que son incluidos en la distribución del framework
		//$excepciones[] = $origen.'/metadatos';
		
		toba_manejador_archivos::crear_arbol_directorios($destino);
		toba_manejador_archivos::copiar_directorio($origen, $destino, 
													$excepciones, $this->manejador_interface, false);

		//-- Crea un archivo revision con la actual de toba
		file_put_contents($destino.'/REVISION', revision_svn($origen, true));		
		
		//-- Modifica aplicacion.php
		$dir_template = toba_dir().self::template_proyecto;
		copy( $dir_template.'/www/aplicacion.produccion.php', $destino.'/www/aplicacion.php');
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion( '|__proyecto__|', $this->get_id() );
		$editor->procesar_archivo( $destino . '/www/aplicacion.php' );
	}

	//-----------------------------------------------------------
	//	VENTANAS
	//-----------------------------------------------------------	

	/**
	 * Ejecuta un script de instalación propio del proyecto
	 */
	function instalar()
	{
		$aplicacion = $this->get_aplicacion_modelo();
		if (isset($aplicacion)) {
			$parametros = $this->instancia->get_parametros_db();
			if (isset($parametros['base'])) {
				unset($parametros['base']);
			}
			$aplicacion->instalar($parametros);
		}
	}

	/**
	 * Ejecuta un script de desinstalación propio del proyecto
	 */	
	function desinstalar()
	{
		$aplicacion = $this->get_aplicacion_modelo();
		if (isset($aplicacion)) {
			$aplicacion->desinstalar();
		}		
	}
	
	/**
	 * Ejecuta un script de migracion de datos de negocio entre la version actual y la dada
	 */		
	function migrar_datos_negocio(toba_version $desde, toba_version $hasta)
	{
		$aplicacion = $this->get_aplicacion_modelo();
		if (isset($aplicacion)) {
			$parametros = $this->instancia->get_parametros_db();
			if (isset($parametros['base'])) {
				unset($parametros['base']);
			}
			$aplicacion->migrar($desde, $hasta);
		}
	}
	
	
	//-----------------------------------------------------------
	//	Funcionalidad ESTATICA
	//-----------------------------------------------------------
	
	/**
	*	Devuelve la lista de proyectos existentes en la carpeta por defecto de la instalación
	* 	Es posible que existan proyectos en otros lugares del sistema de archivos y no se listen con este método
	* 	@return array Arreglo asociativo path relativo => id proyecto
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
						$arch_nombre = $directorio_proyectos . '/' . $archivo.'/PROYECTO';
						$id = $archivo;
						//--- Si no se encuentra el archivo PROYECTO, se asume que dir=id
						if (file_exists($arch_nombre)) {
							$id = file_get_contents($arch_nombre);
						}
						$proyectos[$archivo] = $id;													
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
	static function crear( toba_modelo_instancia $instancia, $nombre, $usuarios_a_vincular )
	{
		//- 1 - Controles
		$dir_template = toba_dir() . self::template_proyecto;
		if ( $nombre == 'toba' ) {
			throw new toba_error("INSTALACION: No es posible crear un proyecto con el nombre 'toba'");	
		}
		if ( self::existe( $nombre ) ) {
			throw new toba_error("INSTALACION: Ya existe una carpeta con el nombre '$nombre' en la carpeta 'proyectos'");	
		}
		try {
			//- 2 - Modificaciones en el sistema de archivos
			$dir_proyecto = $instancia->get_path_proyecto($nombre);
			$url_proyecto = $instancia->get_url_proyecto($nombre);
			
			// Creo la CARPETA del PROYECTO
			$excepciones = array();
			$excepciones[] = $dir_template.'/www/aplicacion.produccion.php';
			toba_manejador_archivos::copiar_directorio( $dir_template, $dir_proyecto, $excepciones);
			
			//--- Creo el archivo PROYECTO
			file_put_contents($dir_proyecto.'/PROYECTO', $nombre);
			file_put_contents($dir_proyecto.'/VERSION', '1.0.0');
			
			// Modifico los archivos
			$editor = new toba_editor_archivos();
			$editor->agregar_sustitucion( '|__proyecto__|', $nombre );
			$editor->agregar_sustitucion( '|__instancia__|', $instancia->get_id() );
			$editor->agregar_sustitucion( '|__toba_dir__|', toba_manejador_archivos::path_a_unix( toba_dir() ) );
			$editor->procesar_archivo( $dir_proyecto . '/www/aplicacion.php' );
			$modelo = $dir_proyecto . '/php/extension_toba/modelo.php';
			$comando = $dir_proyecto . '/php/extension_toba/comando.php';
			$editor->procesar_archivo($comando);
			$editor->procesar_archivo($modelo);
			rename($modelo, str_replace('modelo.php', $nombre.'_modelo.php', $modelo));			
			rename($comando, str_replace('comando.php', $nombre.'_comando.php', $comando));

			
			// Asocio el proyecto a la instancia
			$instancia->vincular_proyecto( $nombre, null, $url_proyecto);

			//- 3 - Modificaciones en la BASE de datos
			$db = $instancia->get_db();
			try {
				$db->abrir_transaccion();
				$db->retrazar_constraints();
				$db->ejecutar( self::get_sql_metadatos_basicos( $nombre ) );
				$sql_version = self::get_sql_actualizar_version( toba_modelo_instalacion::get_version_actual(),
																$nombre);
				$db->ejecutar($sql_version);
				foreach( $usuarios_a_vincular as $usuario ) {
					self::do_vincular_usuario($db, $nombre, $usuario, array('admin'));
				}
				$db->cerrar_transaccion();
			} catch ( toba_error $e ) {
				$db->abortar_transaccion();
				$txt = 'PROYECTO : Ha ocurrido un error durante la carga de METADATOS del PROYECTO. DETALLE: ' . $e->getMessage();
				throw new toba_error( $txt );
			}
		} catch ( toba_error $e ) {
			// Borro la carpeta creada
			if ( is_dir( $dir_proyecto ) ) {
				$instancia->desvincular_proyecto( $nombre );
				toba_manejador_archivos::eliminar_directorio( $dir_proyecto );
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
		$sql[] = "INSERT INTO apex_proyecto (proyecto, estilo,descripcion,descripcion_corta,listar_multiproyecto, item_inicio_sesion, menu, requiere_validacion, log_archivo, log_archivo_nivel, sesion_tiempo_no_interac_min, registrar_solicitud) 
									VALUES ('$id_proyecto','plastik','".strtoupper($id_proyecto)."','".ucwords($id_proyecto)."',1, '2','css', 1, 1, 7, 30, 1);";
		//Le agrego los items basicos
		$sql[] = "INSERT INTO apex_item (proyecto, item, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, nombre, descripcion, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron) VALUES ('$id_proyecto','1','$id_proyecto','1','1','0',NULL,'toba','NO','Raiz PROYECTO','','toba','0','toba','especifico');";
		$sql[] = "INSERT INTO apex_item (proyecto, item, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, nombre, descripcion, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron,actividad_accion,menu,orden) VALUES ('$id_proyecto','2','$id_proyecto','1','0','0','web','toba','normal','Inicio','','toba','0','toba','especifico','item_inicial.php',1,'0');";
		// Creo un grupo de acceso
		$sql[] = "INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion) VALUES ('$id_proyecto','admin','Administrador','0','Accede a toda la funcionalidad');";
		$sql[] = "INSERT INTO apex_usuario_grupo_acc_item ( proyecto, usuario_grupo_acc, item ) VALUES ('$id_proyecto', 'admin', '2');";
		// Crea una fuente de datos 
		$sql[] = "INSERT INTO apex_fuente_datos (proyecto, fuente_datos, fuente_datos_motor, descripcion, descripcion_corta, link_instancia, instancia_id) VALUES ('$id_proyecto','$id_proyecto', 'postgres7', 'Fuente $id_proyecto', '$id_proyecto', 1, '$id_proyecto');";
		// Pone la fuente de datos como predeterminada
		$sql[] = "UPDATE apex_proyecto SET fuente_datos='$id_proyecto' WHERE proyecto='$id_proyecto';";
		return $sql;
	}

	static function do_vincular_usuario($db, $proyecto, $usuario, $perfiles_acceso=array(), $perfiles_datos=array(), $set_previsualizacion=true, $url=null )
	{
		$proyecto = $db->quote($proyecto);
		$usuario = $db->quote($usuario);
		foreach ($perfiles_acceso as $perfil) {
			$perfil = $db->quote($perfil);
			$sql[] = "INSERT INTO apex_usuario_proyecto (proyecto, usuario, usuario_grupo_acc)
						VALUES ($proyecto, $usuario, $perfil);";
		}
		foreach ($perfiles_datos as $perfil) {
			$perfil = $db->quote($perfil);
			$sql[] = "INSERT INTO apex_usuario_proyecto_perfil_datos (proyecto, usuario, usuario_perfil_datos)
						VALUES ($proyecto, $usuario, $perfil);";
		}
		
		// Decide un PA por defecto para el proyecto
		if(!empty($perfil_acceso) && $set_previsualizacion && isset($url)) {
			$funcional = $db->quote(implode(',', $perfil_acceso));
			if (empty($perfil_datos)) {
				$datos = 'NULL';
			} else {
				$datos = $db->quote(implode(',', $perfil_datos));
			}
			$url = $db->quote($url);
			$sql[] = "INSERT INTO apex_admin_param_previsualizazion (proyecto, usuario, grupo_acceso, perfil_datos, punto_acceso) 
						VALUES ($proyecto, $usuario, $funcional, $datos, $url);";
		}
		$db->ejecutar($sql);
	}
}
?>