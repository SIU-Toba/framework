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
			$this->sincro_archivos = new toba_sincronizador_archivos( $this->get_dir_dump() );
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
			$url = '/'.$id.'/'.$this->get_version_proyecto();
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
		return $this->get_dir_dump() . '/permisos';
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
		system("svn update $dir");		
	}
	
	//-----------------------------------------------------------
	//	EXPORTAR
	//-----------------------------------------------------------

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
			$this->exportar_grupos_acceso();
			$this->sincronizar_archivos();
		} catch ( toba_error $e ) {
			$this->manejador_interface->error( "Proyecto {$this->identificador}: Ha ocurrido un error durante la exportacion:\n".
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
		$definicion = toba_db_tablas_proyecto::$tabla();
		$columna_grupo_desarrollo = null;
		if (isset($definicion['columna_grupo_desarrollo'])) {
			$columna_grupo_desarrollo = $definicion['columna_grupo_desarrollo'];
		}		
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
		return $this->get_contenido_exportacion_datos($tabla, $datos, $columna_grupo_desarrollo);
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

	//-- PERMISOS -------------------------------------------------------------

	private function exportar_grupos_acceso()
	{
		$this->manejador_interface->mensaje("Exportando permisos", false);
		toba_manejador_archivos::crear_arbol_directorios( $this->get_dir_permisos() );
		$tablas = array('apex_usuario_grupo_acc', 'apex_usuario_grupo_acc_item', 'apex_permiso_grupo_acc');
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
		$this->manejador_interface->progreso_fin();
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
		try {
			$this->db->abrir_transaccion();
			$this->db->retrazar_constraints();
			$this->cargar();
			$this->instancia->actualizar_secuencias();
			$this->db->cerrar_transaccion();
		} catch ( toba_error $e ) {
			$this->db->abortar_transaccion();
			throw $e;
		}
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
		$this->cargar_permisos();
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
	
	private function cargar_permisos()
	{
		$this->manejador_interface->mensaje('Cargando permisos', false);
		try {
			$archivos = toba_manejador_archivos::get_archivos_directorio( $this->get_dir_permisos(), '|.*\.sql|' );
			$cant_total = 0;
			foreach( $archivos as $archivo ) {
				$cant = $this->db->ejecutar_archivo( $archivo );
				toba_logger::instancia()->debug($archivo . ". ($cant)");
				$this->manejador_interface->progreso_avanzar();
				$cant_total++;
			}
			$this->manejador_interface->progreso_fin();
		} catch (toba_error $e) {
			$this->manejador_interface->mensaje($e->getMessage());
		}
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
		$this->cargar_permisos();
	}

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
		toba_logger::instancia()->debug( "Regenerando PROYECTO {$this->identificador}");
		$this->manejador_interface->titulo( "Regenerando PROYECTO {$this->identificador}" );		
		try {
			$this->db->abrir_transaccion();
			$this->db->retrazar_constraints();
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
		//Creo el archivo
		$clase->guardar( $archivo );
		$this->manejador_interface->progreso_fin();
	}

	/**
	*	Compilacion de GRUPOS de ACCESO
	*/	
	private function compilar_metadatos_generales_grupos_acceso()
	{
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
			foreach ( $this->get_lista_componentes( $tipo ) as $id_componente) {
				$objeto = $id_componente['componente'];
				foreach( $this->get_indice_mensajes_objeto($objeto) as $mensaje ) {
					$datos = toba_proyecto_db::get_mensaje_objeto( $this->get_id(), $objeto, $mensaje );
					$clase->agregar_metodo_datos('get__'.$objeto.'__'.$mensaje, $datos );
				}		
			}
		}
		$clase->guardar( $archivo );
		$this->manejador_interface->progreso_avanzar();	
		//---------------------------
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

	function vincular_usuario( $usuario, $perfil_acceso, $perfil_datos = 'no', $previsualizacion=true )
	{
		$url = $this->get_url();
		$sql = self::get_sql_vincular_usuario( $this->get_id(), $usuario, $perfil_acceso, $perfil_datos, $previsualizacion, $url);
		$this->instancia->get_db()->ejecutar( $sql );
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
		$nuevos_datos['padre'] = "__raiz__";
		$nuevos_datos['fuente_datos'] = $fuente['fuente_datos'];
		$nuevos_datos['fuente_datos_proyecto'] = $nuevos_datos['proyecto'];
		$directorio = 'login';
		$clave = $info_item->clonar($nuevos_datos, $directorio);
		
		$this->manejador_interface->progreso_fin();
		
		//--- Actualizar el item de login
		$this->manejador_interface->mensaje("Actualizando el proyecto...", false);		
		$sql = "UPDATE apex_proyecto SET item_pre_sesion='$clave'
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
			toba_manejador_archivos::copiar_directorio( $dir_template, $dir_proyecto );
			
			//--- Creo el archivo PROYECTO
			file_put_contents($dir_proyecto.'/PROYECTO', $nombre);
			file_put_contents($dir_proyecto.'/VERSION', '0.1.0');
			
			// Modifico los archivos
			$editor = new toba_editor_archivos();
			$editor->agregar_sustitucion( '|__proyecto__|', $nombre );
			$editor->agregar_sustitucion( '|__instancia__|', $instancia->get_id() );
			$editor->agregar_sustitucion( '|__toba_dir__|', toba_manejador_archivos::path_a_unix( toba_dir() ) );
			$editor->procesar_archivo( $dir_proyecto . '/www/aplicacion.php' );
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
					$db->ejecutar( self::get_sql_vincular_usuario( $nombre, $usuario, 'admin', 'no' ) );
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
		$sql[] = "INSERT INTO apex_proyecto (proyecto, estilo,descripcion,descripcion_corta,listar_multiproyecto, item_inicio_sesion, menu, requiere_validacion) 
									VALUES ('$id_proyecto','plastik','".strtoupper($id_proyecto)."','".ucwords($id_proyecto)."',1, '/inicio','css', 1);";
		//Le agrego los items basicos
		$sql[] = "INSERT INTO apex_item (proyecto, item, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, nombre, descripcion, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron) VALUES ('$id_proyecto','__raiz__','$id_proyecto','__raiz__','1','0',NULL,'toba','NO','Raiz PROYECTO','','toba','0','toba','especifico');";
		$sql[] = "INSERT INTO apex_item (proyecto, item, padre_proyecto, padre, carpeta, nivel_acceso, solicitud_tipo, pagina_tipo_proyecto, pagina_tipo, nombre, descripcion, actividad_buffer_proyecto, actividad_buffer, actividad_patron_proyecto, actividad_patron,actividad_accion,menu,orden) VALUES ('$id_proyecto','/inicio','$id_proyecto','__raiz__','0','0','web','toba','normal','Inicio','','toba','0','toba','especifico','item_inicial.php',1,'0');";
		// Creo un grupo de acceso
		$sql[] = "INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion) VALUES ('$id_proyecto','admin','Administrador','0','Accede a toda la funcionalidad');";
		$sql[] = "INSERT INTO apex_usuario_grupo_acc_item ( proyecto, usuario_grupo_acc, item ) VALUES ('$id_proyecto', 'admin', '/inicio');";
		// Creo un perfil de datos
		$sql[] = "INSERT INTO apex_usuario_perfil_datos (proyecto, usuario_perfil_datos, nombre, descripcion) VALUES ('$id_proyecto','no','No posee','');";
		// Crea una fuente de datos 
		$sql[] = "INSERT INTO apex_fuente_datos (proyecto, fuente_datos, fuente_datos_motor, descripcion, descripcion_corta, link_instancia, instancia_id) VALUES ('$id_proyecto','$id_proyecto', 'postgres7', 'Fuente $id_proyecto', '$id_proyecto', 1, '$id_proyecto');";
		// Pone la fuente de datos como predeterminada
		$sql[] = "UPDATE apex_proyecto SET fuente_datos='$id_proyecto' WHERE proyecto='$id_proyecto';";
		return $sql;
	}

	static function get_sql_vincular_usuario( $proyecto, $usuario, $perfil_acceso, $perfil_datos, $set_previsualizacion=true, $url=null )
	{
		if (! is_array($perfil_acceso)) {
			$perfil_acceso = array($perfil_acceso);
		}
		$sql = array();
		foreach ($perfil_acceso as $id_grupo) {
			$sql[] = "INSERT INTO apex_usuario_proyecto (proyecto, usuario, usuario_grupo_acc, usuario_perfil_datos)
						VALUES ('$proyecto','$usuario','$id_grupo','$perfil_datos');";
		}
				// Decide un PA por defecto para el proyecto
		if(isset($id_grupo) && $set_previsualizacion && isset($url)) {
			$sql[] = "INSERT INTO apex_admin_param_previsualizazion (proyecto, usuario, grupo_acceso, punto_acceso) 
						VALUES ('$proyecto','$usuario','$id_grupo', '$url');";
		}
		return $sql;
	}
}
?>