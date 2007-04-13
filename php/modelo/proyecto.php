<?php
require_once('lib/elemento_modelo.php');
require_once('modelo/instancia.php');
require_once('modelo/consultas/dao_permisos.php');
require_once('modelo/consultas/dao_editores.php');
require_once('modelo/info/contexto_info.php');
require_once('nucleo/componentes/toba_catalogo.php');
require_once('nucleo/componentes/toba_cargador.php');
require_once('nucleo/componentes/toba_constructor.php');
require_once('lib/toba_manejador_archivos.php');
require_once('lib/toba_sincronizador_archivos.php');
require_once('lib/toba_editor_archivos.php');
require_once('lib/reflexion/toba_clase_datos.php');
require_once('modelo/estructura_db/tablas_proyecto.php');
require_once('modelo/estructura_db/tablas_instancia.php');
require_once('modelo/estructura_db/tablas_componente.php');
require_once('nucleo/lib/toba_editor.php'); //Se necesita para saber el ID del editor

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
	const dump_prefijo_permisos = 'grupo_acceso__';
	const compilar_archivo_referencia = 'tabla_tipos';
	const template_proyecto = '/php/modelo/template_proyecto';

	function __construct( instancia $instancia, $identificador )
	{
		$this->instancia = $instancia;
		$this->identificador = $identificador;
		$this->dir = $instancia->get_path_proyecto($identificador);
		if( ! is_dir( $this->dir ) ) {
			throw new toba_error("PROYECTO: El proyecto '{$this->identificador}' es invalido. (la carpeta '{$this->dir}' no existe)");
		}
		$this->db = $this->instancia->get_db();
		contexto_info::set_db($this->get_db());
		contexto_info::set_proyecto($this->identificador);
		toba_logger::instancia()->debug('PROYECTO "'.$this->identificador.'"');
	}

	function get_sincronizador()
	{
		if ( ! isset( $this->sincro_archivos ) ) {
			$this->sincro_archivos = new toba_sincronizador_archivos( $this->get_dir_dump() );
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
	
	
	function get_alias()
	{
		return $this->get_id();
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
	 * @return instancia
	 */	
	function get_instancia()
	{
		return $this->instancia;
	}
	
	/**
	 * @return instalacion
	 */
	function get_instalacion()
	{	return $this->instancia->get_instalacion();
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
		foreach ( tablas_proyecto::get_lista() as $tabla ) {
			$contenido = $this->get_contenido_tabla($tabla);			
			if ( trim( $contenido ) != '' ) {
				$this->guardar_archivo( $this->get_dir_tablas() .'/'. $tabla . '.sql', $contenido );			
			}
			$this->manejador_interface->mensaje_directo('.');
		}
		$this->manejador_interface->mensaje("OK");
	}

	private function get_contenido_tabla($tabla, $where_extra=null)
	{
		$definicion = tablas_proyecto::$tabla();
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
		toba_logger::instancia()->debug("TABLA  $tabla  ($regs reg.)");
		for ( $a = 0; $a < $regs ; $a++ ) {
			$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
		}
		return $contenido;
	}

	//-- COMPONENTES -------------------------------------------------------------

	/*
	*	Exporta los componentes
	*/
	private function exportar_componentes()
	{
		$this->manejador_interface->mensaje("Exportando componentes", false);
		toba_cargador::instancia()->crear_cache_simple( $this->get_id(), $this->db );
		foreach (toba_catalogo::get_lista_tipo_componentes_dump() as $tipo) {
			$lista_componentes = toba_catalogo::get_lista_componentes( $tipo, $this->get_id(), $this->db );
			foreach ( $lista_componentes as $id_componente) {
				$this->exportar_componente( $tipo, $id_componente );
			}
			$this->manejador_interface->mensaje_directo(".");
		}
		$this->manejador_interface->mensaje("OK");		
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
				$this->manejador_interface->mensaje_directo('.');
			}			
		}
		$this->manejador_interface->mensaje("OK");
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
			$this->manejador_interface->error( "PROYECTO: Ha ocurrido un error durante la IMPORTACION:\n".
												$e->getMessage() );
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
	
	/**
	 * Ejecuta un script de instalación propio del proyecto
	 * Redefinir para crear el propio entorno
	 * @ventana
	 */
	function instalar()
	{
				
	}
	
	private function cargar_tablas()
	{
		$this->manejador_interface->mensaje('Cargando datos globales', false);
		$archivos = toba_manejador_archivos::get_archivos_directorio( $this->get_dir_tablas(), '|.*\.sql|' );
		$cant_total = 0;
		foreach( $archivos as $archivo ) {
			$cant = $this->db->ejecutar_archivo( $archivo );
			toba_logger::instancia()->debug($archivo . ". ($cant)");
			$this->manejador_interface->mensaje_directo('.');
			$cant_total++;
		}
		$this->manejador_interface->mensaje("OK");
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
				$this->manejador_interface->mensaje_directo('.');
				$cant_total++;
			}
			$this->manejador_interface->mensaje("OK");
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
			$this->manejador_interface->mensaje_directo('.');			
		}
		$this->manejador_interface->mensaje('OK');
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
		$this->manejador_interface->mensaje( "OK" );				
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
		$catalogos['tablas_proyecto'][] = 'get_lista_permisos';
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
		foreach (toba_catalogo::get_lista_tipo_componentes_dump() as $tipo) {
			$c = 0;
			$this->manejador_interface->mensaje( $tipo, false );
			if ( $tipo == 'item' ) {
				$directorio = $this->get_dir_componentes_compilados() . '/item';
			} else {
				$directorio = $this->get_dir_componentes_compilados() . '/comp';
			}
			toba_manejador_archivos::crear_arbol_directorios( $directorio );
			foreach (toba_catalogo::get_lista_componentes( $tipo, $this->get_id(), $this->db ) as $id_componente) {
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
		$this->manejador_interface->mensaje_directo('.');
		toba_logger::instancia()->debug("COMPONENTE --  " . $id['componente']);
		$prefijo = ($tipo == 'item') ? 'toba_mc_item__' : 'toba_mc_comp__';
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
		require_once('nucleo/lib/toba_proyecto_db.php');
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
		$this->manejador_interface->mensaje_directo('.');
		//-- Fuentes --
		foreach( $this->get_indice_fuentes() as $fuente ) {		
			$datos = toba_proyecto_db::get_info_fuente_datos( $this->get_id(), $fuente );
			$clase->agregar_metodo_datos('info_fuente__'.$fuente, $datos );
		}
		$this->manejador_interface->mensaje_directo('.');
		//-- Permisos --
		foreach( $this->get_indice_permisos() as $permiso ) {		
			$datos = toba_proyecto_db::get_descripcion_permiso( $this->get_id(), $permiso );
			$clase->agregar_metodo_datos('info_permiso__'.$permiso, $datos );
		}
		$this->manejador_interface->mensaje_directo('.');
		//Creo el archivo
		$clase->guardar( $archivo );
		$this->manejador_interface->mensaje("OK");
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
			//Menu
			$datos = toba_proyecto_db::get_items_menu( $this->get_id(), $grupo_acceso );
			$clase->agregar_metodo_datos('get_items_menu', $datos );
			//Control acceso
			$datos = toba_proyecto_db::get_items_accesibles( $this->get_id(), $grupo_acceso );
			$clase->agregar_metodo_datos('get_items_accesibles', $datos );
			//Permisos
			$datos = toba_proyecto_db::get_lista_permisos( $this->get_id(), $grupo_acceso );
			$clase->agregar_metodo_datos('get_lista_permisos', $datos );
			//Acceso items zonas
			foreach( $this->get_indice_zonas() as $zona ) {
				$datos = toba_proyecto_db::get_items_zona( $this->get_id(), $grupo_acceso, $zona );
				$clase->agregar_metodo_datos('get_items_zona__'.$zona, $datos );
			}
			//Guardo el archivo
			$clase->guardar( $archivo );
			$this->manejador_interface->mensaje_directo('.');
		}		
		$this->manejador_interface->mensaje("OK");
	}

	/**
	*	Compilacion de PUNTOS de CONTROL
	*/	
	private function compilar_metadatos_generales_puntos_control()
	{
		$this->manejador_interface->mensaje('Puntos de control', false);
		foreach( dao_editores::get_puntos_control() as $punto_control ) {
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
			$this->manejador_interface->mensaje_directo('.');
		}		
		$this->manejador_interface->mensaje("OK");
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
		$this->manejador_interface->mensaje_directo('.');
		//---- Mensajes PROYECTO ------
		$nombre_clase = 'toba_mc_gene__msj_proyecto';
		$archivo = $this->get_dir_generales_compilados() . '/' . $nombre_clase . '.php';
		$clase = new toba_clase_datos( $nombre_clase );
		foreach( $this->get_indice_mensajes() as $mensaje ) {
			$datos = toba_proyecto_db::get_mensaje_proyecto( $this->get_id(), $mensaje );
			$clase->agregar_metodo_datos('get__'.$mensaje, $datos );
		}		
		$clase->guardar( $archivo );
		$this->manejador_interface->mensaje_directo('.');
		//---- Mensajes OBJETOS ------
		$nombre_clase = 'toba_mc_gene__msj_proyecto_objeto';
		$archivo = $this->get_dir_generales_compilados() . '/' . $nombre_clase . '.php';
		$clase = new toba_clase_datos( $nombre_clase );
		foreach (toba_catalogo::get_lista_tipo_componentes_dump() as $tipo) {
			foreach (toba_catalogo::get_lista_componentes( $tipo, $this->get_id(), $this->db ) as $id_componente) {
				$objeto = $id_componente['componente'];
				foreach( $this->get_indice_mensajes_objeto($objeto) as $mensaje ) {
					$datos = toba_proyecto_db::get_mensaje_objeto( $this->get_id(), $objeto, $mensaje );
					$clase->agregar_metodo_datos('get__'.$objeto.'__'.$mensaje, $datos );
				}		
			}
		}
		$clase->guardar( $archivo );
		$this->manejador_interface->mensaje_directo('.');	
		//---------------------------
		$this->manejador_interface->mensaje("OK");
	}
	

	private function compilar_operaciones()
	{
		$this->manejador_interface->mensaje('Operaciones resumidas', false);
		foreach( dao_editores::get_lista_items() as $item) {
			$php = "<?php\n";
			$directorio = $this->get_dir_componentes_compilados() . '/oper';
			toba_manejador_archivos::crear_arbol_directorios( $directorio );
			$nombre_archivo = toba_manejador_archivos::nombre_valido( 'toba_mc_oper__' . $item['id'] );
			$arbol = $this->get_arbol_componentes_item($item['proyecto'], $item['id']);
			foreach( $arbol as $componente) {
				$tipo = toba_catalogo::convertir_tipo($componente['tipo']);
				$prefijo_clase = ( $tipo == 'item') ? 'toba_mc_item__' : 'toba_mc_comp__';
				$nombre_clase = toba_manejador_archivos::nombre_valido($prefijo_clase . $componente['componente']);
				$clase = new toba_clase_datos( $nombre_clase );		
				$metadatos = toba_cargador::instancia()->get_metadatos_extendidos( 	$componente, 
																					$tipo,
																					$this->db );
				$clase->agregar_metodo_datos('get_metadatos',$metadatos);
				$php .= $clase->get_contenido();
			}
			$php .= "\n?>";
			file_put_contents($directorio .'/'. $nombre_archivo . '.php', $php);
			$this->manejador_interface->mensaje_directo('.');	
		}
		$this->manejador_interface->mensaje("OK");
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

	function get_indice_grupos_acceso()
	{
		$rs = dao_permisos::get_grupos_acceso();
		$datos = array();
		foreach($rs as $dato) {
			$datos[] = $dato['usuario_grupo_acc'];	
		}
		return $datos;
	}

	function get_indice_fuentes()
	{
		$rs = dao_editores::get_fuentes_datos();
		$datos = array();
		foreach($rs as $dato) {
			$datos[] = $dato['fuente_datos'];	
		}
		return $datos;
	}

	function get_indice_permisos()
	{
		$rs = dao_permisos::get_lista_permisos();
		$datos = array();
		foreach($rs as $dato) {
			$datos[] = $dato['nombre'];	
		}
		return $datos;
	}

	function get_indice_zonas()
	{
		$rs = dao_editores::get_zonas();
		$datos = array();
		foreach($rs as $dato) {
			$datos[] = $dato['zona'];	
		}
		return $datos;
	}					

	function get_indice_mensajes($proyecto=null)
	{
		$rs = dao_editores::get_mensajes($proyecto);
		$datos = array();
		foreach($rs as $dato) {
			$datos[] = $dato['indice'];	
		}
		return $datos;
	}

	function get_indice_mensajes_objeto($objeto)
	{
		$rs = dao_editores::get_mensajes_objeto($objeto);
		$datos = array();
		foreach($rs as $dato) {
			$datos[] = $dato['indice'];	
		}
		return $datos;
	}

	/**
	*	Devuelve la lista de dependencias de un ITEM
	*/
	private function get_arbol_componentes_item($proyecto, $item)
	{
		$resultado[0] = array( 'tipo' => 'item', 'componente'=> $item, 'proyecto' => $proyecto);
		$sql = "SELECT proyecto, objeto FROM apex_item_objeto WHERE item = '$item' AND proyecto = '$proyecto'";
		$datos = $this->db->consultar($sql);
		foreach($datos as $componente) {
			$resultado = array_merge($resultado, self::get_arbol_componentes($componente['proyecto'], $componente['objeto']));
		}
		return $resultado;
	}
	
	/*
	*	Devuelve la lista de dependencias de un ITEM
	*/
	private function get_arbol_componentes($proyecto, $componente)
	{
		static $id = 1;
		$sql = "SELECT 	o.proyecto as 			proyecto, 
						o.objeto as 			objeto,
						o.clase as 				clase,
						d.objeto_proveedor as 	dep
				FROM 	apex_objeto o LEFT OUTER JOIN apex_objeto_dependencias d
						ON o.objeto = d.objeto_consumidor AND o.proyecto = d.proyecto
				WHERE 	o.objeto = '$componente' 
				AND 	o.proyecto = '$proyecto'";
		$datos = $this->db->consultar($sql);
		$resultado[$id] = array( 'tipo' => $datos[0]['clase'], 'componente'=> $datos[0]['objeto'], 'proyecto' => $datos[0]['proyecto']);
		foreach($datos as $componente) {
			if(isset($componente['dep'])) {
				$id++;
				$resultado = array_merge($resultado, self::get_arbol_componentes($componente['proyecto'], $componente['dep']));
			}
		}
		return $resultado;
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
	
	function get_item_login()
	{
		$sql = "SELECT item_pre_sesion FROM apex_proyecto WHERE proyecto='{$this->identificador}'";
		$rs = $this->get_db()->consultar($sql);
		return $rs[0]['item_pre_sesion'];
	}
	
	/**
	 * @todo Cuando los dao_editores se puedan usar desde consola, cambiar la consulta manual 
	 */
	function actualizar_login($pisar_anterior = false)
	{
		//--- ¿Existe el proyecto editor?
		if (! $this->instancia->existen_metadatos_proyecto( toba_editor::get_id() )) {
			$msg = "No se crea el item de login porque el proyecto editor no está cargado en la instancia";
			toba_logger::instancia()->info($msg);
			$this->manejador_interface->mensaje($msg);
			return;
		}
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
		$comando = 'toba item ejecutar -p toba_editor -t 1000043 ';
		$comando .= ' -orig_proy toba_editor';
		$comando .= ' -orig_item 1000042';
		$comando .= ' -dest_proy '.$this->identificador;
		$comando .= ' -dest_padre "__raiz__"';
		$comando .= ' -dest_fuente '.$fuente['fuente_datos'];
		$comando .= ' -dest_dir login';	
		//---- Averiguo un usuario capaz de ejecutar en toba_editor
		$usuarios = $this->instancia->get_usuarios_administradores('toba_editor');
		if (! empty($usuarios)) {
			$comando .= ' -u '.$usuarios[0]['usuario'];	
		}		

		
		$this->manejador_interface->mensaje("Clonando item de login...", false);
		toba_logger::instancia()->debug("Ejecutando el comando: $comando");
		$id_item = trim(exec($comando));
		if (! is_numeric($id_item)) {
			throw new toba_error("($id_item). A ocurrido un error clonando el item de login. Ver el log del proyecto toba_editor");
		}
		$this->manejador_interface->mensaje("OK");
		
		//--- Actualizar el item de login
		$this->manejador_interface->mensaje("Actualizando el proyecto...", false);		
		$sql = "UPDATE apex_proyecto SET item_pre_sesion='$id_item'
				WHERE proyecto='{$this->identificador}'";
		$this->get_db()->ejecutar($sql);
		$this->manejador_interface->mensaje("OK");
		
		//--- Borrar el item viejo
		if ($pisar_anterior) {
			echo "Aun no está implementada la eliminación desde consola";
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
	static function crear( instancia $instancia, $nombre, $usuarios_a_vincular )
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
			// Creo la CARPETA del PROYECTO
			toba_manejador_archivos::copiar_directorio( $dir_template, $dir_proyecto );
			
			//--- Creo el archivo PROYECTO
			file_put_contents($dir_proyecto.'/PROYECTO', $nombre);
			// Modifico los archivos
			$editor = new toba_editor_archivos();
			$editor->agregar_sustitucion( '|__proyecto__|', $nombre );
			$editor->agregar_sustitucion( '|__instancia__|', $instancia->get_id() );
			$editor->agregar_sustitucion( '|__toba_dir__|', toba_manejador_archivos::path_a_unix( toba_dir() ) );
			$editor->procesar_archivo( $dir_proyecto . '/www/aplicacion.php' );
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
		$sql[] = "INSERT INTO apex_proyecto (proyecto, estilo,descripcion,descripcion_corta,listar_multiproyecto, item_inicio_sesion, menu) VALUES ('$id_proyecto','cubos','".strtoupper($id_proyecto)."','".ucwords($id_proyecto)."',1, '/inicio','css');";
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

	static function get_sql_vincular_usuario( $proyecto, $usuario, $perfil_acceso, $perfil_datos )
	{
		$sql = array();
		$sql[] = "INSERT INTO apex_usuario_proyecto (proyecto, usuario, usuario_grupo_acc, usuario_perfil_datos)
					VALUES ('$proyecto','$usuario','$perfil_acceso','$perfil_datos');";
				// Decide un PA por defecto para el proyecto
		$sql[] = "INSERT INTO apex_admin_param_previsualizazion (proyecto, usuario, grupo_acceso, punto_acceso) 
					VALUES ('$proyecto','$usuario','$perfil_acceso', '/$proyecto');";
		return $sql;
	}
}
?>