<?php
	
use SIUToba\rest\rest;	

require_once('toba_modelo_mocks_rest.php');

/**
 * Clase que representa el proyecto
 * @package Centrales
 * @subpackage Modelo
 */
class toba_modelo_proyecto extends toba_modelo_elemento
{
	private static $lista_proyectos;	
	private $instancia;
	private $identificador;
	private $dir;
	private $sincro_archivos;
	private $db;
	private $aplicacion_comando;
	private $aplicacion_modelo;
	private $prefijo_dir_metadatos = 'metadatos';	
	private $ini_proyecto;
	const dump_prefijo_componentes = 'dump_';
	const dump_prefijo_permisos = 'grupo_acceso__';
	const compilar_archivo_referencia = 'tabla_tipos';
	const template_proyecto = '/php/modelo/template_proyecto';
	const patron_nombre_autoload = '%id_proyecto%_autoload';
	const patron_nombre_autoload_pers = '%id_proyecto%_pers_autoload';
	private static $clases_exc_autoload = array();
	const tipo_paquete_produccion = 'p';
	const tipo_paquete_desarrollo = 'd';

	static function set_clases_excluidas_autoload($clases)
	{
		self::$clases_exc_autoload = $clases;
	}

	static function get_clases_excluidas_autoload()
	{
		return self::$clases_exc_autoload;
	}

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

	/**
	 * Permite setear el directorio de metadatos a utilizar para realizar la carga
	 * @param type $dir
	 */
	function set_dir_metadatos($dir=null)
	{
		$test = realpath($this->get_dir(). '/' .$dir);
		if (! is_null($dir) && $test !== false && is_dir($test)) {
			$this->prefijo_dir_metadatos = $dir;
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

	function get_url_pers()
	{
		$id = $this->get_id();
		$url = $this->instancia->get_url_proyecto_pers($id);
		if ($url == '') {
			$version = $this->get_version_proyecto();
			$url = '/'.$id.'_pers/'.$version->get_release();
		}
		return $url;
	}

	function get_dir()
	{
		return $this->dir;	
	}

	function get_dir_pers()
	{
		return $this->dir.'/'.toba_personalizacion::dir_personalizacion;
	}

	function get_dir_dump()
	{
		return $this->dir . '/'. $this->prefijo_dir_metadatos;
	}

	function get_dir_componentes()
	{
		return $this->get_dir_dump() . '/componentes';
	}
	
	function get_dir_tablas()
	{
		return $this->get_dir_dump() . '/tablas';
	}
		
	function get_dir_permisos_produccion()
	{
		return $this->get_instancia()->get_dir_instalacion_proyecto($this->identificador).'/perfiles';
	}
	
	function get_dir_permisos_proyecto()
	{
		return $this->get_dir_dump() . '/permisos';
	}
	
	function get_dir_instalacion_proyecto()
	{
		return $this->get_instancia()->get_dir_instalacion_proyecto($this->identificador);
	}

	function get_dir_componentes_compilados()
	{
		return $this->dir . '/metadatos_compilados';
	}

	function get_dir_generales_compilados()
	{
		return $this->dir . '/metadatos_compilados/gene';
	}
	
	function get_lista_tablas_perfil_funcional()
	{
		return array('apex_usuario_grupo_acc', 
					 'apex_usuario_grupo_acc_miembros', 
					 'apex_usuario_grupo_acc_item', 
					 'apex_permiso_grupo_acc', 
					 'apex_grupo_acc_restriccion_funcional');
	}
	
	function get_lista_tablas_perfil_datos()
	{
		return array('apex_usuario_perfil_datos', 
					 'apex_usuario_perfil_datos_dims');
	}
	
	function get_lista_tablas_restricciones()
	{
		return array('apex_restriccion_funcional', 
					 'apex_restriccion_funcional_cols', 
					 'apex_restriccion_funcional_ef', 
					 'apex_restriccion_funcional_ei', 
					 'apex_restriccion_funcional_evt', 
					 'apex_restriccion_funcional_filtro_cols', 
					 'apex_restriccion_funcional_pantalla');
	}

	function get_lista_tablas_menu()
	{
		return array( 'apex_menu', 
				 'apex_menu_operaciones');
	}
	
	
	/**
	 * @return toba_estandar_convenciones
	 */
	function get_estandar_convenciones()
	{
		return new toba_estandar_convenciones();
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

	/**
	 * @return toba_db_postgres7
	 */
	function get_db($refrescar = false)
	{
		if (! isset($this->db) || $refrescar) {
			$this->db = $this->instancia->get_db($refrescar);	
		}
		return $this->db;
	}	
	
	/**
	 * Retorna una referencia a la fuente de datos predeterminada del proyecto
	 * @return toba_db
	 */
	function get_db_negocio($fuente=null)
	{
		if (! isset($fuente)) {
			$fuentes = $this->get_indice_fuentes();
			if (empty($fuentes)) {
				return;
			}
			$fuente = toba_info_editores::get_fuente_datos_defecto($this->identificador);
			if (! isset($fuente)) {
				$fuente = current($fuentes);		
			}
		}
		$id_def_base = $this->construir_id_def_base($fuente);
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
	 * Devuelve el manejador de puntos de montaje para este proyecto
	 * @return toba_modelo_pms
	 */
	function get_pms()
	{
		return toba_modelo_catalogo::instanciacion()->get_pms($this);
	}

	/**
	 * 
	 * @return type
	 */
	function get_lista_pms()
	{
		$proyecto = $this->db->quote($this->identificador);
		$sql = "SELECT * FROM apex_puntos_montaje WHERE proyecto= $proyecto";	
		return $this->db->consultar($sql);
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

	function get_parametro($seccion, $parametro=null, $obligatorio=true)
	{
		if (! isset($this->ini_proyecto)) {
			$nombre_ini = 'proyecto.ini';
			$path_ini = $this->get_dir().'/'.$nombre_ini;
			if (! file_exists($path_ini)) {
				throw new toba_error("No existe el archivo '$nombre_ini' en la raiz del proyecto");
			}
			$this->ini_proyecto = new toba_ini($path_ini);			
		}
		
		if ($this->ini_proyecto->existe_entrada($seccion, $parametro)) {
			return $this->ini_proyecto->get($seccion, $parametro);
		} elseif ($obligatorio) {
			throw new toba_error("INFO_PROYECTO: El parametro '$id' no se encuentra definido.");
		}	
		return null;
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
		//Verifico que no hubo actualizacion precoz,
		//no recalculo version aca ya que hasta que no hace commit no cambia.
		if ($this->get_instalacion()->chequea_sincro_svn()) {
			$this->chequear_actualizacion_prematura();
		}
		try {
			$this->exportar_tablas();
			$this->exportar_componentes();
			$this->exportar_perfiles();
			$this->sincronizar_archivos();
			$this->generar_checksum(); //Regenero el checksum
		} catch ( toba_error $e ) {
			throw new toba_error( "Proyecto {$this->identificador}: Ha ocurrido un error durante la exportacion:\n".
												$e->getMessage() );
		}
	}

	private function sincronizar_archivos()
	{
		if (!$this->maneja_perfiles_produccion()) {
			$obs = $this->get_sincronizador()->sincronizar();
			$this->manejador_interface->lista( $obs, 'Observaciones' );	
		}
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
   			$where = str_replace("%%",$this->get_id(), $w);
		} else {
   			$where = " ( proyecto = '".$this->get_id()."')";
		}
		if(isset($where_extra)) $where = $where . ' AND ('. $where_extra .')';
		$sql = 'SELECT ' . implode(', ', $definicion['columnas']) .
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
	
	function get_descripcion_items($datos)
	{
		$items = toba_info_editores::get_lista_items($this->get_id(), false);
		$items = rs_convertir_asociativo($items, array('id'), 'descripcion');
		$items_desc = array();
		$duplicados = array();
		foreach (array_keys($datos) as $key)
		{
			if (!in_array($datos[$key]['item'], $duplicados)) {
				$items_desc[] = array('item' => $datos[$key]['item'], 'nombre' => $items[$datos[$key]['item']]);
				$duplicados[] = $datos[$key]['item'];
			}
		}
		
		return $items_desc;
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
		} elseif (isset($metadatos['apex_item'][0]['nombre'])) {
			$nombre_componente = $metadatos['apex_item'][0]['nombre'];		
		} else {
			throw new toba_error("Los metadatos para el componente con id {$id['componente']} no existen");
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
			$contenido .= sql_array_a_insert_formateado( $tabla, $datos[$a] , $this->db);
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

	//-- ITEMS ------------------------------------------------------------------
	
	private function get_descripciones_items($datos)
	{
		$desc = array();
		if (! empty($datos)) {
			foreach (array_keys($datos['items']) as $fila) {
				$desc[$fila['item']] = $fila['nombre'];
			}
		}
		return $desc;
	}
	
	function exportar_item($item)
	{
		toba_logger::instancia()->debug( "Exportando ITEM $item en PROYECTO {$this->get_id()}");
		$this->manejador_interface->titulo( "Exportación ITEM $item en PROYECTO {$this->get_id()}" );
		$existe_vinculo = $this->instancia->existe_proyecto_vinculado( $this->get_id());
		$existen_metadatos = $this->instancia->existen_metadatos_proyecto( $this->get_id() );
		if( !( $existen_metadatos || $existe_vinculo ) ) {
			throw new toba_error("PROYECTO: El proyecto '{$this->get_id()}' no esta asociado a la instancia actual");
		}
		//Verifico que no hubo actualizacion precoz,
		//no recalculo version aca ya que hasta que no hace commit no cambia.
		if ($this->get_instalacion()->chequea_sincro_svn()) {
			$this->chequear_actualizacion_prematura();
		}
		try {
			$this->exportar_componentes_item($item);
			$this->get_sincronizador()->sincronizar_agregados();	//Sincronizo los archivos
			$this->generar_checksum();													//Regenero el checksum
		} catch ( toba_error $e ) {
			throw new toba_error( "Proyecto {$this->identificador}: Ha ocurrido un error durante la exportacion del item $item:\n".
												$e->getMessage() );
		}

		$this->manejador_interface->titulo( "Recuerde que esta operación actualmente no exporta los permisos ni los perfiles para el item." );
	}

	function exportar_componentes_item($item)
	{
		//Primero verifico que el item existe
		if (toba_info_editores::existe_item($item, $this->get_id())) {
			$arbol = toba_info_editores::get_arbol_componentes_item($this->get_id(), $item);
			$this->manejador_interface->mensaje("Exportando componentes", false);
			foreach($arbol as $componente) {
				$this->exportar_componente($componente['tipo'], $componente);
				$this->manejador_interface->progreso_avanzar();
			}
			$this->manejador_interface->progreso_fin();
		} else {
			throw new toba_error_def( "No existe el item $item \n");
		}
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
		toba_manejador_archivos::crear_arbol_directorios( $this->get_dir_permisos_proyecto() );
		$tablas = $this->get_lista_tablas_perfil_funcional();
		foreach( $this->get_indice_grupos_acceso() as $permiso ) {
			toba_logger::instancia()->debug("PERMISO  $permiso");
			$contenido = '';		
			$where = "usuario_grupo_acc = '$permiso'";
			foreach($tablas as $tabla) {
				$contenido .= $this->get_contenido_tabla($tabla, $where);
			}
			if ( $contenido ) {
				$this->guardar_archivo( $this->get_dir_permisos_proyecto() .'/'. self::dump_prefijo_permisos . $permiso . '.sql', $contenido );			
				$this->manejador_interface->progreso_avanzar();
			}			
		}
		
		//-- Perfiles de datos
		$tablas = $this->get_lista_tablas_perfil_datos();
		foreach ($tablas as $tabla ) {
			$contenido = $this->get_contenido_tabla($tabla);	
			if ( trim( $contenido ) != '' ) {
				$this->guardar_archivo( $this->get_dir_permisos_proyecto() .'/'. $tabla . '.sql', $contenido );			
			}
			$this->manejador_interface->progreso_avanzar();
		}
		
		//-- Restricciones Funcionales
		$tablas = $this->get_lista_tablas_restricciones();
		foreach ($tablas as $tabla ) {
			$contenido = $this->get_contenido_tabla($tabla);
			if ( trim( $contenido ) != '' ) {
				$this->guardar_archivo( $this->get_dir_permisos_proyecto() .'/'. $tabla . '.sql', $contenido );			
			}
			$this->manejador_interface->progreso_avanzar();
		}
		
		//-- Configuración de Menues
		$tablas = $this->get_lista_tablas_menu();
		foreach($tablas as $tabla) {
			$contenido = $this->get_contenido_tabla($tabla);
			if (trim($contenido) != '') {
				$this->guardar_archivo($this->get_dir_permisos_proyecto() . '/' .$tabla. '.sql', $contenido);
			}
			$this->manejador_interface->progreso_avanzar();
		}		
	}

	function exportar_perfiles_produccion()
	{
		$dir_perfiles = $this->get_dir_permisos_produccion();
		//-- Borra los perfiles anteriormente guardados, si existen
		if (file_exists($dir_perfiles)) {
			toba_manejador_archivos::eliminar_directorio($dir_perfiles);
		}
		
		//-- Perfiles Funcionales
		toba_logger::instancia()->debug("Exportación a xml de perfiles funcionales '{$this->identificador}'");
		toba_manejador_archivos::crear_arbol_directorios($dir_perfiles);
		$items = array(); //-- Util para recuperar la descripción asociada al item (si es que la tiene) en las siguientes etapas
		$tablas = $this->get_lista_tablas_perfil_funcional();
		$proyecto = $this->db->quote($this->identificador);
		foreach( $this->get_indice_grupos_acceso() as $permiso ) 
		{
			toba_logger::instancia()->debug("PERFIL  $permiso");
			$where = "usuario_grupo_acc IN (SELECT gc.usuario_grupo_acc FROM apex_usuario_grupo_acc AS gc WHERE gc.proyecto = $proyecto AND gc.usuario_grupo_acc = '$permiso' AND gc.permite_edicion = 1)";
			$datos = array();
			foreach($tablas as $tabla) 
			{
				$contenido = $this->get_contenido_tabla_datos($tabla, $where);
				if (!empty($contenido)) {
					$datos[$tabla] = $contenido;
				}
			}
			
			if (!empty($datos)) {
				$archivo = $dir_perfiles."/perfil_$permiso.xml";
				$xml = new toba_xml_tablas();
				$xml->set_tablas($datos);
				$xml->guardar($archivo);
				if (isset($datos['apex_usuario_grupo_acc_item']) && ! is_null($datos['apex_usuario_grupo_acc_item'])) {
					$items = array_merge($items, $datos['apex_usuario_grupo_acc_item']);
				}
			}
			unset($datos);
			$this->manejador_interface->progreso_avanzar();
		}

		//-- Se guarda la descripción de las operaciones del proyecto
		$items = $this->get_descripcion_items($items);
		$archivo = $this->get_dir_instalacion_proyecto() . "/items.xml";
		$xml = new toba_xml_tablas();
		$xml->set_tablas(array('items' => $items));
		$xml->guardar($archivo);
		
		//--- Perfiles de datos
		toba_logger::instancia()->debug("Exportación a xml de perfiles de datos '{$this->identificador}'");
		$tablas = $this->get_lista_tablas_perfil_datos();
		$datos = array();
		foreach ($tablas as $tabla) 
		{
			$contenido = $this->get_contenido_tabla_datos($tabla);
			if (!empty($contenido)) {
				$datos[$tabla] = $contenido;
			}
		}
		
		if (!empty($datos)) {
			$archivo = $dir_perfiles."/perfiles_datos.xml";
			$xml = new toba_xml_tablas();
			$xml->set_tablas($datos);
			$xml->guardar($archivo);
		}
		unset($datos);
		$this->manejador_interface->progreso_avanzar();
		
		//--- Restricciones Funcionales
		toba_logger::instancia()->debug("Exportación a xml de restricciones funcionales '{$this->identificador}'");
		$tablas = $this->get_lista_tablas_restricciones();
		$datos = array();
		$where = "restriccion_funcional IN (SELECT res.restriccion_funcional FROM apex_restriccion_funcional AS res WHERE res.permite_edicion = 1)";
		foreach ($tablas as $tabla)
		{
			$contenido = $this->get_contenido_tabla_datos($tabla, $where);
			if (!empty($contenido)) {
				$datos[$tabla] = $contenido;
			}
		}
		
		if (!empty($datos)) {
			$archivo = $dir_perfiles."/restricciones_funcionales.xml";
			$xml = new toba_xml_tablas();
			$xml->set_tablas($datos);
			$xml->guardar($archivo);	
		}
		unset($datos);
		$this->manejador_interface->progreso_avanzar();
		
		//-- Configuración de Menues
		toba_logger::instancia()->debug("Exportación a xml de menues de aplicacion '{$this->identificador}'");
		$tablas = $this->get_lista_tablas_menu();
		$datos = array();
		foreach($tablas as $tabla) {
			$contenido = $this->get_contenido_tabla_datos($tabla);
			if (! empty($contenido)) {
				$datos[$tabla] = $contenido;
			}			
		}
		if (! empty($datos)) {
			$archivo = $dir_perfiles.'/menues_aplicacion.xml';
			$xml = new toba_xml_tablas();
			$xml->set_tablas($datos);
			$xml->guardar($archivo);			
		}
		unset($datos);
		$this->manejador_interface->progreso_avanzar();
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------------------//
	//------------------------------------------------------------------------------------------------------------------------------------------------//
	//	PERMISOS SOBRE TABLAS EN LA BASE
	//------------------------------------------------------------------------------------------------------------------------------------------------//			
	//------------------------------------------------------------------------------------------------------------------------------------------------//
	static function get_usuario_prueba_db($fuente)
	{
		return "temp_".$fuente;
	}
	
	static function get_rol_prueba_db($fuente, $id_operacion)
	{
		return "temp_".$fuente.'_'.$id_operacion;		
	}
	
	static function get_rol_prueba_db_basico($fuente)
	{
		return "temp_".$fuente.'_basico';		
	}				
	
	/**
	 * Devuelve un nombre estandar de rol 
	 * @param string $perfil
	 * @return string 
	 */
	protected function get_nombre_rol($perfil) 
	{
		return strtolower($this->identificador . '_' . $perfil);		//Analizar si no conviene generar un ID por fuente 
	}
	
	/**
	 *  Devuelve una lista con los roles actuales del motor
	 * @return array
	 */
	protected function get_roles_disponibles()
	{
		$roles_existentes = array();		
		$datos = $this->db->listar_roles();
		foreach($datos as $valor) {
			$roles_existentes[] = strtolower($valor['rolname']);
		}
		return $roles_existentes;		
	}
	
	/**
	 *  Verifica la existencia de todos los esquemas necesarios dentro de la bd
	 * @param toba_db $conexion
	 * @param array $necesarios
	 * @return boolean
	 * @ignore
	 */
	protected function existen_todos_los_schemas($conexion, $necesarios)
	{
		$schemas_disp = array();
		$aux = $conexion->get_lista_schemas_disponibles();
		foreach($aux as $disp) {
			$schemas_disp[] = $disp['esquema'];
		}
	
		$resultado = array_diff($necesarios, $schemas_disp);
		return (empty($resultado)); 
	}	
	
	/**
	 * Devuelve una lista con todos los esquemas que se necesitan para la operacion
	 * @param string $fuente
	 * @param string $id_operacion
	 * @return array
	 * @ignore
	 */
	protected function get_schemas_necesarios($fuente, $id_operacion)
	{
		$necesarios = array();
		$disponibles = $this->get_lista_tablas_con_permisos($fuente, $id_operacion);
		foreach($disponibles as $valor) {
			$necesarios[] = $valor['esquema'];
		}
		return array_unique($necesarios);
	}	
	
	/**
	 * Arma los roles de prueba del proyecto
	 * @param int $id_operacion Si no se especifica actualiza todas las operaciones
	 */
	function generar_roles_db($id_operacion = null)
	{
		if (! $this->get_instalacion()->es_produccion()) {
			foreach (toba_info_editores::get_fuentes_datos($this->identificador) as $fuente) {	
				if ($fuente['permisos_por_tabla'] == 0) {
					continue;
				}
				$this->manejador_interface->mensaje('Actualizando roles fuente '.$fuente['fuente_datos'], false);						
				//Primero verificar si todos los schemas usados por la operacion estan disponibles
				try {
					$conexion = $this->get_db_negocio($fuente['fuente_datos']);
					$datos_schemas = $this->get_schemas_necesarios($fuente['fuente_datos'], $id_operacion);
					if (! $this->existen_todos_los_schemas($conexion, $datos_schemas)) {
						$this->manejador_interface->progreso_fin();						
						continue;
					}
				} catch (toba_error $e) {
					//No es posible hacer la conexion, seguir con otra fuente
					$this->manejador_interface->progreso_fin();
					continue;
				}
				
				$usuario = $this->get_usuario_prueba_db($fuente['fuente_datos']);	
				try {
					$conexion->abrir_transaccion();			
					$rol_select = $this->get_rol_prueba_db_basico($fuente['fuente_datos']);
					//-- Si no existe el usuario lo crea y le asigna permisos
					if (! $conexion->existe_rol($usuario)) {
						$conexion->crear_usuario($usuario, $usuario);
					}		
					if (! $conexion->existe_rol($rol_select)) {
						$conexion->crear_rol($rol_select);
					}				
					//Asigno los permisos para cada esquema necesario por la operacion
					foreach($datos_schemas as $schema) {
						$conexion->grant_schema($rol_select, $schema);
					}
					
					$schema = (isset($fuente['schema']))? $fuente['schema']: 'public';		//Retomo el schema por defecto para pasarlo al otro metodo			
					$conexion->grant_rol($usuario, $rol_select);					
					if (! isset($id_operacion)) {
						foreach(toba_info_editores::get_lista_items($this->identificador) as $operacion) {
							$this->generar_roles_db_pruebas_operacion($fuente['fuente_datos'], $schema, $conexion, $operacion['id']);
							$this->manejador_interface->progreso_avanzar();
						}
					} else {
						$this->generar_roles_db_pruebas_operacion($fuente['fuente_datos'], $schema, $conexion, $id_operacion);
					}
					$this->generar_roles_db_auditoria($conexion, $fuente, $schema, $rol_select);
					$conexion->cerrar_transaccion();					
				} catch (toba_error_db $e) {
					//Error al generar permisos
					$conexion->abortar_transaccion();
					throw $e;						
				}
				$this->manejador_interface->progreso_fin();
			}
		}
	}
	
	/**
	 * Arma los roles de prueba en base a los permisos de tablas de una operación
	 */
	protected function generar_roles_db_pruebas_operacion($fuente, $esquema, $conexion, $id_operacion)
	{
		$rol = $this->get_rol_prueba_db($fuente, $id_operacion);
		$usuario = $this->get_usuario_prueba_db($fuente);
		$fuente_info = toba_info_editores::get_info_fuente_datos($fuente);
		
		//-- Determina tablas y schema
		$tablas = $this->get_lista_tablas_con_permisos($fuente, $id_operacion);
		$existe_rol = $conexion->existe_rol($rol);
		
		//Busco los schemas necesarios por la/s operacion/es
		$schemas = $this->get_schemas_necesarios($fuente, $id_operacion);
		if (empty($schemas)) {				//Esto pasa si se quitaron los permisos a la operacion, defaulteo en el principal
			$schemas[] = $esquema;
		}
		//--Revocar permisos actuales
		if ($existe_rol) {
			foreach($schemas as $schema) {
				$conexion->revoke_schema($rol, $schema, 'ALL PRIVILEGES');
			}
		} 
		if (!empty($tablas)) {
			//-- Crea el nuevo rol
			if (! $existe_rol) {
				$conexion->crear_rol($rol);
			}
			//-- Asigna el usuario de prueba al rol
			$conexion->grant_rol($usuario, $rol);			
			//-- Asignar nuevos permisos a los schemas
			foreach($schemas as $schema) {
				$conexion->grant_schema($rol, $schema);
			}			
			//Asignar permisos a las tablas involucradas
			foreach($tablas as $tabla) {
				$conexion->grant_tablas($rol, $tabla['esquema'], array($tabla['tabla']), $tabla['permisos']);
			}
			
			//-- Da permisos a las secuencias de la tabla
			foreach($schemas as $schema) {
				$secuencias = $conexion->get_lista_secuencias($schema);
				$secuencias_grant = array();
				foreach ($secuencias as $secuencia) {
					if (in_array($secuencia['tabla'], $tablas)) {
						$pos_schema = strpos($secuencia['nombre'], '.');	//Busco un punto por si tiene acoplado el schema
						if ($pos_schema !== false) {
							$secuencia['nombre'] = substr($secuencia['nombre'], $pos_schema + 1);	//Le sumo 1 para evitar el punto separador.
						}
						$secuencias_grant[] = $secuencia['nombre'];
					}
				}
				$conexion->grant_tablas($rol, $schema, $secuencias_grant, 'UPDATE');			
				$this->generar_roles_db_auditoria($conexion, $fuente_info, $schema, $rol);			//Le asigno los roles de las operaciones a la parte de auditoria				
			}
		} else {
			//-- Borrar el rol, ya no es necesario
			if ($existe_rol) {
				$this->revocar_rol_db_auditoria($conexion, $fuente_info, $schema, $rol);
				$conexion->revoke_rol($usuario, $rol);
				$conexion->borrar_rol($rol);
			}
		}
	}

	function generar_roles_db_auditoria($conexion, $fuente, $schema, $rol)
	{
		$schema_auditoria = $schema . '_auditoria';
		if ($fuente['tiene_auditoria'] == '1'  && $conexion->existe_schema($schema_auditoria)) {		//Le doy permisos al esquema de auditoria, sino no se puede usar en el desarrollo
			$conexion->grant_schema($rol, $schema_auditoria);
			$conexion->grant_tablas_schema($rol, $schema_auditoria, 'INSERT');
			$conexion->grant_sp_schema($rol, $schema_auditoria, 'EXECUTE');
		}
	}

	function revocar_rol_db_auditoria($conexion, $fuente, $schema, $rol)
	{
		$schema_auditoria = $schema . '_auditoria';
		if ($fuente['tiene_auditoria'] == '1' && $conexion->existe_schema($schema_auditoria)) {		//Le doy permisos al esquema de auditoria, sino no se puede usar en el desarrollo
			$conexion->revoke_sp_schema($rol, $schema_auditoria, 'EXECUTE');
			$conexion->revoke_schema($rol, $schema_auditoria);
		}
	}
	
	protected function get_lista_tablas_con_permisos($fuente, $id_operacion = null)
	{
		$sql = "SELECT 
					tabla,
					esquema, 
					permisos
				FROM
					apex_item_permisos_tablas
				WHERE 
						proyecto = '{$this->identificador}'
					AND	fuente_datos = '$fuente' ";
		if (! is_null($id_operacion)) {
			$sql .= " AND item = ".$this->db->quote($id_operacion);
		}
		
		$sql .= ' GROUP BY permisos, tabla, esquema;' ;
		$datos = $this->db->consultar($sql);
		return $datos;
	}
	
	//----------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 *  Genera un script por fuente de datos para crear los roles y darles permisos
	 */
	function crear_script_generacion_roles_db($dir = '', $perfiles_eliminados=array())
	{
		$sentencias = array(); $fuentes = array(); $sql = array();
		$prefijo_archivo = $this->identificador.'_roles_';
		toba_proyecto_db::set_db( $this->db );
		
		//------------------------------------------------------------------------------------------//
		//	Obtengo estado actual de perfiles funcionales
		//------------------------------------------------------------------------------------------//
		$grupos = $this->get_indice_grupos_acceso();		
		//Ahora busco las fuentes del mismo, quitando aquellas que no usen permisos por tablas o no esten configuradas para que no tire error
		$fuentes_disponibles = toba_info_editores::get_fuentes_datos($this->identificador);	
		foreach ($fuentes_disponibles as $fuente) {
			try {
				$this->construir_id_def_base($fuente['fuente_datos']);			
				if ($fuente['permisos_por_tabla'] == '1') { 
					$fuentes[] = $fuente['fuente_datos'];
				}
			} catch (toba_error $e) {
				continue;
			}
		}
		
		$roles_activos_db = $this->get_roles_disponibles();		
			
		//------------------------------------------------------------------------------------------//
		//	Genero el nuevo estado para los perfiles 
		//------------------------------------------------------------------------------------------//		
		foreach($grupos as $perfil) {
			$nombre_final = $this->get_nombre_rol($perfil);
			$rol_existente = (in_array($nombre_final, $roles_activos_db));								//Miro si es un rol existente o eliminado.
			$rol_eliminado = (in_array($perfil, $perfiles_eliminados));
			$drop_generado = false;
				
			if (! $rol_existente) {																	//Si es un perfil nuevo, creo el rol correspondiente
				$sql[] = $this->get_db()->crear_rol($nombre_final, false);
			}
			
			$operaciones_disponibles = toba_proyecto_db::get_items_accesibles($this->identificador, array($perfil));							//Obtengo las operaciones para el perfil
			foreach ($fuentes as $fuente) {
				$permisos_tablas = $this->get_tablas_permitidas_x_fuente($fuente, $operaciones_disponibles);							//Obtengo las tablas que usan las operaciones en esta fuente
				$sql_rvk_rol =  (! $rol_existente) ? array():  $this->get_sql_revocacion_permisos_rol($nombre_final, $fuente, $permisos_tablas);	//Genero las SQLs para los REVOKE si existe el rol
				
				if (! $rol_eliminado) {
					$sql_rol = $this->get_sql_generacion_permisos_rol($nombre_final, $fuente, $permisos_tablas);						//Genero las SQLs para los GRANT	si es nuevo o existe												
				} elseif (! $drop_generado) {
					$sql[] = $this->get_db()->borrar_rol($nombre_final, false);														//Genero el DROP si el rol no existe mas.	
					$drop_generado = true;					
				}
				
				if (! empty($sql_rol) || ! empty($sql_rvk_rol)) {																		//Agrego las consultas al pool para la fuente
					$sentencias[$fuente] = (! isset($sentencias[$fuente])) ? array_merge($sql_rvk_rol, $sql, $sql_rol): array_merge($sentencias[$fuente], $sql_rvk_rol, $sql, $sql_rol);					
					$sql = array();																							//Reinicializo para evitar que el rol se cree nuevamente.
					$sql_rol = array();					
				}
			}
		}		
		
		//------------------------------------------------------------------------------------------//
		//		Grabo todo en los archivos correspondientes
		//------------------------------------------------------------------------------------------//		
		foreach ($fuentes as $fuente) {
			$nombre_archivo = $dir . $prefijo_archivo . '_' . $fuente. '.sql';
			if (! empty ($sentencias[$fuente])) {
				if (! file_put_contents($nombre_archivo, $sentencias[$fuente])) {
					throw new toba_error('PROYECTO: Se produjo un error en la generación del script, verifique los logs', 'Se produjo un error al guardar los datos para la fuente '. $fuente);
				}
			}
		}
	}
	
	/**
	 * Devuelve que tablas son utilizadas en la fuente por las operaciones indicadas
	 * @param string $fuente
	 * @param array $operaciones
	 * @return array 
	 */
	function get_tablas_permitidas_x_fuente($fuente, $operaciones)
	{
		$conjunto = array();									//Para cada operacion obtengo las tablas involucradas
		$resultado = array();		
		foreach($operaciones as $item) {
			$tmp_datos = $this->get_lista_tablas_con_permisos($fuente, $item['item']);
			$conjunto = array_merge($conjunto, $tmp_datos);
		}		
		foreach($conjunto as $rs) {
			$indx = $rs['tabla'];
			$resultado[$indx] = $rs;
		}
		
		return $resultado;
	}
	
	protected function get_sql_revocacion_permisos_rol($rol, $fuente, $tablas) 
	{
		//Voy pasandole los permisos x cada tabla.
		$sqls = array();
		foreach($tablas as $tabla) {
			$permisos = 'ALL PRIVILEGES';
			/*if (isset($tabla['permisos'])) {
				$permisos = $tabla['permisos'];
			}*/
			
			$tmp_sql = $this->get_db()->revoke_tablas($rol, $tabla['esquema'], array($tabla['tabla']), $permisos, false);			
			$sqls = array_merge($sqls, $tmp_sql);
		}		
		return $sqls;
	}
	
	/**
	 * Devuelve un arreglo de sentencias SQL que realizan el GRANT de los permisos
	 * @param string $rol
	 * @param string $fuente
	 * @param array $tablas
	 * @return array 
	 */
	protected function get_sql_generacion_permisos_rol($rol, $fuente, $tablas)
	{	
		//Voy pasandole los permisos x cada tabla.
		$sqls = array();
		foreach($tablas as $tabla) {
			$permisos = 'ALL PRIVILEGES';
			if (isset($tabla['permisos'])) {
				$permisos = $tabla['permisos'];
			}
			
			$tmp_sql = $this->get_db()->grant_tablas($rol, $tabla['esquema'], array($tabla['tabla']), $permisos, false);
			$sqls = array_merge($sqls, $tmp_sql);
		}		
		return $sqls;
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
			$this->db->retrasar_constraints();
			$errores = $this->cargar();
			$this->instancia->actualizar_secuencias();
			$this->generar_roles_db();			
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
		$this->generar_roles_db();
		//Regenero el checksum para el proyecto
		if ($this->get_instalacion()->chequea_sincro_svn()) {
			$this->generar_estado_codigo();
		}
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
		
		$this->cargar_perfiles_proyecto();
		
		if ($this->maneja_perfiles_produccion()) {
			$this->eliminar_permisos_editables();
			$errores = $this->cargar_perfiles_produccion();		
		}
		
		return $errores;
	}
	
	private function cargar_perfiles_proyecto()
	{
		$archivos = toba_manejador_archivos::get_archivos_directorio( $this->get_dir_permisos_proyecto(), '|.*\.sql|' );
		$es_produccion = $this->maneja_perfiles_produccion();
		//-- En producción no se cargan los perfiles de datos?
		$keys_a = array_keys($archivos);
		foreach ($keys_a as $clave) {
			if ($es_produccion && in_array(basename($archivos[$clave], '.sql'), $this->get_lista_tablas_perfil_datos())) {
				unset($archivos[$clave]);
			}
		}
		$cant_total = 0;
		foreach ($archivos as $archivo) {
			$cant = $this->db->ejecutar_archivo( $archivo );
			toba_logger::instancia()->debug($archivo . ". ($cant)");
			$this->manejador_interface->progreso_avanzar();
			$cant_total++;
		}
		$this->manejador_interface->progreso_fin();
	}
	
	private function cargar_perfiles_produccion()
	{
		$this->manejador_interface->mensaje("Cargando perfiles propios", false);
		$todos_errores = $lista_perfiles = array();
				
		$archivos = array(); 
		$dir_base = $this->get_dir_permisos_produccion();
						
		 //-- Si hay perfil de datos lo agrego
		$archivo_perfil_datos = $dir_base . '/perfiles_datos.xml';
		if (file_exists($archivo_perfil_datos)) {
			$archivos = array($archivo_perfil_datos);
		}		

		//-- Agrego los perfiles en orden alfabetico para respetar las membresias
		$lista_perfiles = toba_manejador_archivos::get_archivos_directorio($dir_base, '|perfil_.*\.xml$|' );		
		if (! empty($lista_perfiles)) {
			sort($lista_perfiles, SORT_LOCALE_STRING);
			$archivos = array_merge($lista_perfiles, $archivos);
		}
		 		
		 //-- Si hay restricciones las agrego al ppio.
		$archivo_restricciones = $dir_base . '/restricciones_funcionales.xml';
		if (file_exists($archivo_restricciones)) {
			$archivos = array_merge(array($archivo_restricciones), $archivos);
		}				
		
		 //-- Si hay menues los agrego al ppio.
		$archivo_menues = $dir_base . '/menues_aplicacion.xml';
		if (file_exists($archivo_menues)) {
			$archivos = array_merge(array($archivo_menues), $archivos);
		}
				 	
		//-- Trata de encontrar los nombres de las operaciones que no se le pudieron asignar a los perfiles
		$dir_items = $this->get_dir_instalacion_proyecto() . '/items.xml';
		if (file_exists($dir_items)) {
			$xml = new toba_xml_tablas($dir_items);
			$items = $this->get_descripciones_items($xml->get_tablas());
		}
		
		//-- Intenta cargar los archivos xml
		foreach( $archivos as $archivo ) {
			$perfil = basename($archivo, '.xml');
			$xml = new toba_xml_tablas($archivo);
			$errores = $xml->insertar_db($this->db, $this->get_dir_instalacion_proyecto());
			if (! empty($errores)) {
				foreach (array_keys($errores) as $clave) {
					$id_item = (isset($errores[$clave]['datos']['item']))?  $errores[$clave]['datos']['item'] : null;
					if ($errores[$clave]['tabla'] == 'apex_usuario_grupo_acc_item' && array_key_exists($id_item, $items)) {
						if (! is_null($id_item) && isset($items[$id_item])) {
							$errores[$clave]['extras'] = $items[$id_item];
						} else {
							$errores[$clave]['extras'] = '';
						}
					}
				}
				
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
	
	//------------------------------------------------------------------
	//		PUBLICACION
	//------------------------------------------------------------------	
	
	function publicar($url=null, $full_url=null)
	{
		if (! $this->esta_publicado()) {
			$url_pers = (trim($url) != '') ? $url . '_pers/' : null;
			if ($url == '' || is_null($url)) {
				$url = $this->get_url();
			}
			$this->instancia->set_url_proyecto($this->get_id(), $url, $full_url);
			toba_modelo_instalacion::agregar_alias_apache(	$url,
													$this->get_dir(),
													$this->get_instancia()->get_id(),
													$this->get_id());
			$this->actualizar_previsualizacion($url, $this->get_id());
			if ($this->es_personalizable()) {				
				$this->publicar_pers($url_pers);
			}
		}
	}

	function publicar_pers($url=null)
	{
		if (! $this->esta_publicado_pers()) {
			if ($url == '' || is_null($url)) {
				$url = $this->get_url_pers();
			}
			$this->instancia->set_url_proyecto_pers($this->get_id(), $url);
			toba_modelo_instalacion::agregar_alias_apache(	$url,
													$this->get_dir_pers(),
													$this->get_instancia()->get_id(),
													$this->get_id(),
													true);
		}
	}

	function despublicar()
	{
		if ($this->esta_publicado()) {
			toba_modelo_instalacion::quitar_alias_apache($this->get_id());
			if ($this->es_personalizable()) {
				toba_modelo_instalacion::quitar_alias_apache($this->get_id(), true);
			}
		}
	}

	function esta_publicado()
	{
		return toba_modelo_instalacion::existe_alias_apache($this->get_id());
	}

	function esta_publicado_pers()
	{
		return toba_modelo_instalacion::existe_alias_apache($this->get_id(), true);
	}

	private function actualizar_previsualizacion($url, $proyecto)
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
			$this->db->retrasar_constraints();
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
		$catalogos['toba_db_tablas_instancia'][] = 'get_lista_proyecto_usuario';
		foreach( $catalogos as $catalogo => $indices ) {
			foreach( $indices as $indice ) {
				$lista_tablas = call_user_func( array( $catalogo, $indice ) );
				foreach ( $lista_tablas as $t ) {
					$info_tabla = call_user_func( array( $catalogo, $t ) );
					if( isset( $info_tabla['dump_where'] ) ) {
						$where = " WHERE " . str_replace('%%', $this->identificador, stripslashes($info_tabla['dump_where']) );
						$where = str_replace(" dd", $t, $where);
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
			$this->db->retrasar_constraints();
			$this->instancia->exportar_local_proyecto($this->identificador);
			$this->eliminar();
			$this->cargar();
			$this->instancia->cargar_informacion_instancia_proyecto( $this->identificador );
			$this->instancia->actualizar_secuencias();
			$this->generar_roles_db();
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
		$this->compilar_metadatos_generales_servicios_web();
		$this->compilar_metadatos_generales_pms();

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
		toba_proyecto_db::set_db( $this->db );
		if ($limpiar_existentes) {
			$archivos = toba_manejador_archivos::get_archivos_directorio($this->get_dir_generales_compilados(), '/toba_mc_gene__grupo/');
			foreach($archivos as $archivo) {
				unlink($archivo);
			}
		}
		$this->manejador_interface->mensaje('Perfiles funcionales', false);
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
			//-- Membresía -------------------
			$miembros = toba_proyecto_db::get_perfiles_funcionales_asociados($this->get_id(), $grupo_acceso);
			$clase->agregar_metodo_datos('get_membresia', $miembros);			
			
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
	
	/**
	*	Compilacion acceso SERVICIOS WEB
	*/	
	private function compilar_metadatos_generales_servicios_web()
	{
		//-- Datos basicos --
		$this->manejador_interface->mensaje('Servicios Web', false);
		$nombre_clase = 'toba_mc_gene__servicios_web';
		$archivo = $this->get_dir_generales_compilados() . '/' . $nombre_clase . '.php';
		$clase = new toba_clase_datos( $nombre_clase );
		foreach(toba_info_editores::get_servicios_web_acc() as $serv_web) {		
			$datos = toba_proyecto_db::get_info_servicio_web($this->get_id(), $serv_web['servicio_web']);
			$clase->agregar_metodo_datos('servicio__'.$serv_web['servicio_web'], $datos );
			$this->manejador_interface->progreso_avanzar();
		}
		//Creo el archivo
		$clase->guardar( $archivo );
		$this->manejador_interface->progreso_fin();
	}	

	private function compilar_metadatos_generales_pms()
	{
		$this->manejador_interface->mensaje('Puntos de Montaje', false);
		$nombre_clase = 'toba_mc_gene__pms';
		$archivo = $this->get_dir_generales_compilados().'/'.$nombre_clase.'.php';
		$clase = new toba_clase_datos($nombre_clase);

		$datos = toba_proyecto_db::get_pms($this->get_id());
		$clase->agregar_metodo_datos('get_pms', $datos);

		$clase->guardar($archivo);
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
	//	Autoload
	//-----------------------------------------------------------
	
	/**
	 * @ignore
	 */
	private function get_dirs_no_autoload($path) 
	{
		$dirs = array();
		$archivo = $path . '/noautoload.ini';
		if (is_dir($path) && file_exists($archivo)) {
			$ini = new toba_ini($archivo);
			$dato = $ini->get('excluidos', 'directorios');	
			if (trim($dato) != '')  {
				$dirs = explode (',' , $dato);
				$dirs = array_map('trim', $dirs);
			}
		}
		return $dirs;
	}
	
	/**
	 * Genera el archivo de autoload de un proyecto
	 * @param consola $consola la consola desde que se invocó el comando
	 * @param boolean $generar_vacio si se desea generar el archivo vacío
	 * @param boolean $ret_obj_autoload si se retorna el obj de autoload
	 * @param boolean $generar_solo_pers si genera solamente el autooad de la personalizacion si existe
	 */
	function generar_autoload(consola $consola, $generar_vacio=false, $ret_obj_autoload = false, $generar_solo_pers=false)
	{
		$montaje_proyecto = $this->get_dir().'/php';
		$id_proyecto = $this->get_id();
		
		if (!$generar_solo_pers) {
			$excluidos = $this->get_dirs_no_autoload($montaje_proyecto);
			$param = array(
					$montaje_proyecto => array(
							'archivo_salida' => $id_proyecto.'_autoload.php',
							'dirs_excluidos' => $excluidos,
							'extras' => array(),
					),
			);
		}

		if ($this->es_personalizable()) {
			$montaje_personalizacion = $this->get_dir().'/'.toba_personalizacion::dir_personalizacion.'/php';

			$excluidos = $this->get_dirs_no_autoload($montaje_personalizacion);
			// Los parámetros deberían ser cargados del proyecto.ini
			$param[$montaje_personalizacion] = array(
				'archivo_salida' => $id_proyecto.'_pers_autoload.php',
				'dirs_excluidos' => $excluidos,
				'extras' => array(),
			);
		}

		// Union de los 2 arreglos
//		$clases = array_unique(array_merge($this->get_clases_extendidas(), self::get_clases_excluidas_autoload()));
//		$clases[] = 'ci_editores_toba';
		$extractor = new toba_extractor_clases($param);
//		$extractor->set_extends_excluidos($clases);
		if (! $generar_vacio) {
			$extractor->generar();
		} else {
			$extractor->generar_vacio();
		}

		if ($ret_obj_autoload) {
			return $extractor;
		}
	}

	//-----------------------------------------------------------
	//	Personalización
	//-----------------------------------------------------------

	/**
	 * Devuelve true si el proyecto es personalizable
	 * @return boolean
	 */
	function es_personalizable()
	{
		return $this->tiene_clases_proyecto_extendidas();
	}

	/**
	 * Identifica si las clases de $de están extendidas
	 * @param string $de valores posibles: toba | proyecto
	 * @return boolean
	 */
	function tiene_clases_extendidas($de)
	{
		if ($de == 'toba') {
			return $this->tiene_clases_toba_extendidas();
		} else {
			return $this->tiene_clases_proyecto_extendidas();
		}
	}

	function tiene_clases_toba_extendidas()
	{
		$db	= $this->get_db();
		$id_proyecto = $db->quote($this->get_id());
		$select = 'extension_toba';
		$sql = "SELECT $select FROM apex_proyecto WHERE proyecto=$id_proyecto";
		$fila = $db->consultar_fila($sql);
		return $fila[$select];
	}

	function tiene_clases_proyecto_extendidas()
	{
		$db	= $this->get_db();
		$id_proyecto = $db->quote($this->get_id());
		$select = 'extension_proyecto';
		$sql = "SELECT $select FROM apex_proyecto WHERE proyecto=$id_proyecto";
		$fila = $db->consultar_fila($sql);
		return $fila[$select];
	}

	/**
	 * @return array arreglo de strings representando los nombres de las clases extendidas
	 */
	protected function get_clases_extendidas()
	{
		if ($this->tiene_clases_proyecto_extendidas()) {
			// Si las clases extendidas son las del proyecto entonces se devuelven
			// las clases de la personalización
			return $this->get_clases_componentes_personalizacion();
		} elseif ($this->tiene_clases_toba_extendidas()) {
			// Si las clases extendidas son las de toba pero no las del proyecto
			// entonces se devuelven las clases del proyecto
			return $this->get_clases_componentes_proyecto();
		} else {	// Si no se hizo ninguna de las 2 extensiones se devuelven las clases de toba
			return $this->get_clases_componentes_toba();
		}
	}

	protected function get_componentes_toba()
	{
		return util_modelo_proyecto::get_componentes_toba($this);
	}

	protected function get_clases_componentes_toba()
	{
		return util_modelo_proyecto::get_clases_componentes_toba($this);
	}

	function get_clases_componentes_proyecto()
	{
		$comp_de_toba = $this->get_componentes_toba();
		$id_proyecto = $this->get_id();
		$clases = array();
		foreach($comp_de_toba as $componente) {
			$clases[] = $id_proyecto.'_'.$componente;
		}
		return $clases;
	}

	function get_clases_componentes_personalizacion()
	{
		$comp_de_toba = $this->get_componentes_toba();
		$id_proyecto = $this->get_id();
		$clases = array();
		foreach($comp_de_toba as $componente) {
			$clases[] = $id_proyecto.'_pers_'.$componente;
		}
		return $clases; 
	}

	//-----------------------------------------------------------
	//	Puntos de montaje y dependencias entre proyectos
	//-----------------------------------------------------------

	function set_pm_defecto($punto) 
	{
		$db = $this->get_db();
		$id_punto = $db->quote($punto->get_id());
		$sql = "UPDATE apex_proyecto 
				SET pm_impresion = $id_punto,
					pm_sesion = $id_punto,
					pm_contexto = $id_punto,
					pm_usuario = $id_punto
				WHERE proyecto = ".$db->quote($this->identificador);
		$db->ejecutar($sql);
	}
	
	function agregar_dependencia($id_proyecto)
	{
		$deps = $this->get_dependencias();
		if (!in_array($id_proyecto, $deps)) {
			$deps[] = $id_proyecto;
		}
		$this->set_dependencias($deps);
	}

	function quitar_dependencia($id_proyecto)
	{
		$deps  = $this->get_dependencias();
		$index = array_search($id_proyecto, $deps);
		if ($index !== false) { // existe la dependencia
			unset($deps[$index]);
		}
		$this->set_dependencias($deps);
	}

	/**
	 * Devuelve verdadero en caso de que el proyecto cumpla con todas sus dependencias
	 * sino devuelve falso
	 * @return boolean
	 */
	function cumple_dependencias()
	{
		return count($this->get_dependencias_faltantes()) > 0;
	}

	function get_dependencias_faltantes()
	{
		$deps = $this->get_dependencias();
		$proyectos_instancia = toba::instancia()->get_proyectos_accesibles();
		$proyectos_faltantes = array();

		foreach($deps as $dep) {
			if (!in_array($dep, $proyectos_instancia)) { // Si no está en la instancia
				$proyectos_faltantes[] = $dep;
			}
		}

		return $proyectos_faltantes;
	}

	// metodo dependencias no cumplidas

	function get_dependencias()
	{
		$path_ini = $this->get_dir().'/proyecto.ini';
		$contenido = file_get_contents($path_ini);
		$matches = array();
		preg_match_all('#^dependencias\s*=\s*(.*)$#im', $contenido, $matches);
		if (empty($matches[1])) {
			return array();
		} else {
			return explode(',', $matches[1][0]);
		}
	}

	protected function set_dependencias($deps)
	{
		$path_ini = $this->get_dir().'/proyecto.ini';
		$contenido = file_get_contents($path_ini);
		$deps_lista = implode(',', $deps);
		if (preg_match('#^dependencias(?:\s)*=(?:\s)*(.*)$#im', $contenido)) {
			$replacement = (empty($deps)) ? '' : "\ndependencias = $deps_lista";
			$contenido = preg_replace('#\ndependencias(?:\s)=(?:\s)(.*)$#im', $replacement, $contenido);
		} else {
			$contenido = preg_replace('#\[proyecto\]#im', "[proyecto]\ndependencias = $deps_lista", $contenido);
		}
		
		file_put_contents($path_ini, $contenido);
	}

	//-----------------------------------------------------------
	//	Informacion sobre METADATOS
	//-----------------------------------------------------------
	
	function get_lista_grupos_acceso()
	{
		$proyecto = $this->instancia->get_db()->quote($this->get_id());
		$sql = "SELECT usuario_grupo_acc as id, nombre
				FROM apex_usuario_grupo_acc
				WHERE proyecto = $proyecto;";
		return $this->instancia->get_db()->consultar( $sql );
	}

	function get_resumen_componentes_utilizados()
	{
		$proyecto = $this->instancia->get_db()->quote($this->identificador);
		$sql = "	SELECT clase, COUNT(*) as cantidad
					FROM apex_objeto
					WHERE proyecto = $proyecto
					GROUP BY 1
					ORDER BY 2 DESC";
		return $this->instancia->get_db()->consultar( $sql );
	}

	function get_indice_grupos_acceso()
	{
		$rs = toba_info_permisos::get_perfiles_funcionales();
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
		$proyecto = $this->db->quote($this->get_id());
		$comp_sano = $this->db->quote($tipo_componente);
		if ($tipo_componente == 'toba_item' ) {
			$sql = "SELECT 	proyecto as 		proyecto,
							item as 			componente
					FROM apex_item 
					WHERE proyecto = $proyecto
					ORDER BY 2 ASC;";
		} elseif(strpos($tipo_componente,'toba_asistente') !== false) {
			$sql = "SELECT 	o.proyecto as 		proyecto,
							o.molde as 			componente,
							t.clase
					FROM 	apex_molde_operacion o,
							apex_molde_operacion_tipo t
					WHERE 	o.operacion_tipo = t.operacion_tipo
					AND		t.clase = $comp_sano
					AND		proyecto = $proyecto
					ORDER BY 2 ASC;";
		} else {
			$sql = "SELECT 	proyecto as 		proyecto,
							objeto as 			componente
					FROM apex_objeto 
					WHERE proyecto = $proyecto
					AND clase = $comp_sano
					ORDER BY 2 ASC;";		
		}
		$datos = $this->db->consultar( $sql );
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
			} elseif ($perfil_datos == 'NULL') {	//Compatibilidad hacia atras, así se enviaba antes
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
		$proyecto = $this->db->quote($this->identificador);
		$sql = "SELECT item_pre_sesion FROM apex_proyecto WHERE proyecto= $proyecto";
		$rs = $this->get_db()->consultar($sql);
		return $rs[0]['item_pre_sesion'];
	}
	
	/**
	 * @todo Cuando los toba_info_editores se puedan usar desde consola, cambiar la consulta manual 
	 */
	function actualizar_login($pisar_anterior = false, $pm_destino=null)
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
		$pm_safe = (is_null($pm_destino)) ? 'NULL' : quote($pm_destino);
		$sql = "SELECT fuente_datos, COALESCE($pm_safe,  pm_contexto) as pm_contexto FROM apex_proyecto WHERE proyecto = ".quote($this->identificador);
		$defecto = toba::db()->consultar_fila($sql);
		if (empty($defecto['fuente_datos'])) {
			throw new toba_error("El proyecto no tiene definida una fuente de datos.");
		}		
		if (empty($defecto['pm_contexto'])) {
			throw new toba_error("El proyecto no tiene definida un punto de montaje para el contexto.");
		}			
		
		//--- Clonando
		$id = array(	'proyecto' => toba_editor::get_id(),
					 	'componente' =>  1000042 );
		$info_item = toba_constructor::get_info($id, 'toba_item');
		$nuevos_datos = array();
		$nuevos_datos['proyecto'] = $this->identificador;
		$nuevos_datos['padre_proyecto'] = $this->identificador;
		$nuevos_datos['padre'] = toba_info_editores::get_item_raiz($this->identificador);
		$nuevos_datos['fuente_datos'] = $defecto['fuente_datos'];
		$nuevos_datos['fuente_datos_proyecto'] = $this->identificador;
		$nuevos_datos['punto_montaje'] = $defecto['pm_contexto'];
		$directorio = 'login';
		$clave = $info_item->clonar($nuevos_datos, $directorio);
		
		$this->manejador_interface->progreso_fin();
		
		//--- Actualizar el item de login
		$this->manejador_interface->mensaje("Actualizando el proyecto...", false);
		$sql = "UPDATE apex_proyecto SET item_pre_sesion='{$clave['componente']}'
				WHERE proyecto='{$this->identificador}'";
		$this->get_db()->ejecutar($sql);
		$this->manejador_interface->progreso_fin();		
	}

	//------------------------------------------------------------------------
	//-------------------------- Manejo de Versiones --------------------------
	//------------------------------------------------------------------------
	function migrar_rango_versiones($desde, $hasta, $recursivo, $con_transaccion=true)
	{
		$this->get_db()->abrir_transaccion();
		parent::migrar_rango_versiones($desde, $hasta, $recursivo, $con_transaccion);
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
	
	function ejecutar_migracion_particular(toba_version $version, $metodo)
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
			$path_ini = $this->get_dir().'/proyecto.ini';
			if (file_exists($path_ini)) {
				$ini = new toba_ini($path_ini);
				if ($ini->existe_entrada('proyecto', 'version')) {
					$version = $ini->get('proyecto', 'version', null, true);
					return new toba_version($version);
				}
			}
			return toba_modelo_instalacion::get_version_actual();
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
		$proyecto = $this->db->quote($this->identificador);
		$sql = "SELECT version_toba FROM apex_proyecto WHERE proyecto= $proyecto";
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
	
	private static function get_sql_actualizar_version($version, $id_proyecto)
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
	function empaquetar($tipo_paquete, $es_legacy=false)
	{
		$nombre_ini = 'proyecto.ini';
		$path_ini = $this->get_dir().'/'.$nombre_ini;
		if (! file_exists($path_ini)) {
			throw new toba_error("Para crear el paquete de instalación debe existir el archivo '$nombre_ini' en la raiz del proyecto");
		}
		$ini = new toba_ini($path_ini);
		
		//--- Crea la carpeta destino
		$empaquetado = $ini->get_datos_entrada('empaquetado');
		if (! isset($empaquetado['path_destino'])) {
			throw new toba_error("'$nombre_ini': Debe indicar 'path_destino' en seccion [empaquetado]");
		}
		if (! isset($empaquetado['path_instalador'])) {
			$empaquetado['path_instalador'] = toba_dir().'/instalador'; 
		}
		$empaquetado['path_instalador'] = realpath($empaquetado['path_instalador']);
		$empaquetado['path_destino'].= '/'.$this->get_version_proyecto()->__toString();
		
		//-- Invoco el empaquetador
		$legacy = (! file_exists($this->get_dir() . '/composer.json') || $es_legacy);
		$packager = new toba_empaquetador($this->manejador_interface, $this);
		$packager->inicializar($this->get_dir(), $empaquetado['path_destino'], $empaquetado['path_instalador'], $legacy);
		$packager->empaquetar();
				
		$this->manejador_interface->mensaje("", true);
		$this->manejador_interface->mensaje("Proyecto empaquetado en: {$empaquetado['path_destino']}", true);		
	}
	
	/*protected function empaquetar_desarrollo($empaquetado)
	{
		$this->manejador_interface->mensaje("Copiando framework", false);	
		$excepciones = toba_modelo_instalacion::dir_base();
		$destino_instalacion = $empaquetado['path_destino'].'/proyectos/'.$this->get_id().'/toba';
		toba_manejador_archivos::crear_arbol_directorios($destino_instalacion);
		toba_manejador_archivos::copiar_directorio(toba_dir(), $destino_instalacion, 
														$excepciones, $this->manejador_interface, false);
		file_put_contents($destino_instalacion.'/REVISION', revision_svn(toba_dir(), true));
		$this->manejador_interface->progreso_fin();
		
		//--- Empaqueta el proyecto actual
		$this->manejador_interface->mensaje("Copiando aplicacion", false);		
		$destino_aplicacion = $empaquetado['path_destino'].'/proyectos/'.$this->get_id().'/aplicacion';		
		$excepciones = array(toba_dir(), toba_modelo_instalacion::dir_base());									//Para cuando es una instalacion via composer
		if (isset($empaquetado['excepciones_proyecto'])) {
			$excepciones_extras = explode(',', $empaquetado['excepciones_proyecto']);
			$origen = $this->get_dir();
			foreach (array_keys($excepciones_extras) as $i) {
				$excepciones[] = $origen.'/'.trim($excepciones_extras[$i]);
			}			
		}
		$this->empaquetar_proyecto($destino_aplicacion, $excepciones);	
		$path_copia_metadatos = $destino_aplicacion . '/metadatos_originales/';
		$path_metadatos_ap = $destino_aplicacion. '/metadatos/';
		if (file_exists($path_copia_metadatos) && !toba_manejador_archivos::es_directorio_vacio($path_copia_metadatos)) {
			toba_manejador_archivos::eliminar_directorio($path_copia_metadatos);
		}
		toba_manejador_archivos::crear_arbol_directorios($path_copia_metadatos);
		toba_manejador_archivos::copiar_directorio($path_metadatos_ap, $path_copia_metadatos);
	}*/
	
	/*protected function empaquetar_proyecto($destino, $excepciones)
	{
		$origen = $this->get_dir();	
		toba_manejador_archivos::crear_arbol_directorios($destino);
		toba_manejador_archivos::copiar_directorio($origen, $destino, 
													$excepciones, $this->manejador_interface, false);

		//-- Crea un archivo revision con la actual de toba
		file_put_contents($destino.'/REVISION', revision_svn($origen, true));		
	}*/
	
	protected function actualizar_punto_acceso($destino)
	{
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
			if (isset($parametros['schema'])) {
				unset($parametros['schema']);
			}
			$aplicacion->instalar($parametros);
			$this->generar_roles_db();
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
			$aplicacion->migrar($desde, $hasta);
			$this->generar_roles_db();			
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
	static function get_lista($usar_cache=true)
	{
		if (! isset(self::$lista_proyectos) || ! $usar_cache) {
			$proyectos = array();
			$directorio_proyectos = toba_dir() . '/proyectos';
			if( is_dir( $directorio_proyectos ) ) {
				if ($dir = opendir($directorio_proyectos)) {
				   while (false	!==	($archivo = readdir($dir)))	{ 
						if( is_dir($directorio_proyectos . '/' . $archivo) 
								&& ($archivo != '.' ) && ($archivo != '..' ) && ($archivo != '.svn' ) ) {
							$arch_nombre = $directorio_proyectos . '/' . $archivo.'/proyecto.ini';
							$id = $archivo;
							//--- Si no se encuentra el archivo PROYECTO, se asume que dir=id
							if (file_exists($arch_nombre)) {
								$ini = new toba_ini($arch_nombre);
								$id = $ini->get('proyecto', 'id', null, true);
							}
							$proyectos[$archivo] = $id;													
						}
				   } 
				   closedir($dir);
				}
			}
			self::$lista_proyectos = $proyectos;
		}
		return self::$lista_proyectos;
	}
	
	
	
	/**
	*	Indica si un proyecto existe en el sistema de archivos
	*/
	static function existe($nombre, $cache = true )
	{
		$proyectos = self::get_lista($cache);
		if (in_array($nombre, $proyectos)) {
			return true;	
		} else {
			return false;	
		}
	}
	
	/**
	*	Crea un proyecto NUEVO
	*/
	static function crear( toba_modelo_instancia $instancia, $nombre, $usuarios_a_vincular , $dir_inst_proyecto=null)
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
			$dir_proyecto = (is_null($dir_inst_proyecto)) ? $instancia->get_path_proyecto($nombre): $dir_inst_proyecto;
			$url_proyecto = $instancia->get_url_proyecto($nombre);
			
			// Creo la CARPETA del PROYECTO
			$excepciones = array();
			$excepciones[] = $dir_template.'/www/aplicacion.produccion.php';
			toba_manejador_archivos::copiar_directorio( $dir_template, $dir_proyecto, $excepciones);
			
			// Modifico los archivos
			$editor = new toba_editor_archivos();
			$editor->agregar_sustitucion( '|__proyecto__|', $nombre );
			$editor->agregar_sustitucion( '|__instancia__|', $instancia->get_id() );
			$editor->agregar_sustitucion( '|__toba_dir__|', toba_manejador_archivos::path_a_unix( toba_dir() ) );
			$editor->agregar_sustitucion( '|__version__|', '1.0.0');
			$editor->procesar_archivo( $dir_proyecto . '/www/aplicacion.php' );
			
			$modelo = $dir_proyecto . '/php/extension_toba/modelo.php';
			$comando = $dir_proyecto . '/php/extension_toba/comando.php';
			$editor->procesar_archivo($comando);
			$editor->procesar_archivo($modelo);
			$editor->procesar_archivo($dir_proyecto.'/www/rest.php');
			$editor->procesar_archivo($dir_proyecto.'/www/servicios.php');
			
			rename($modelo, str_replace('modelo.php', $nombre.'_modelo.php', $modelo));			
			rename($comando, str_replace('comando.php', $nombre.'_comando.php', $comando));
			$ini = $dir_proyecto.'/proyecto.ini';
			$editor->procesar_archivo($ini);

			// Asocio el proyecto a la instancia
			$instancia->vincular_proyecto( $nombre, $dir_inst_proyecto, $url_proyecto);

			//- 3 - Modificaciones en la BASE de datos
			$db = $instancia->get_db();
			try {
				$db->abrir_transaccion();
				$db->retrasar_constraints();
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
		$sql = array();
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
		if(!empty($perfiles_acceso) && $set_previsualizacion && isset($url)) {
			$funcional = $db->quote(implode(',', $perfiles_acceso));
			if (empty($perfil_datos)) {
				$datos = 'NULL';
			} else {
				$datos = $db->quote(implode(',', $perfil_datos));
			}
			$url = $db->quote($url);
			$sql[] = "INSERT INTO apex_admin_param_previsualizazion (proyecto, usuario, grupo_acceso, perfil_datos, punto_acceso) 
						VALUES ($proyecto, $usuario, $funcional, $datos, $url);";
		}
		if (! empty($sql)) {
			$db->ejecutar($sql);
		}
	}

	function chequear_actualizacion_prematura()
	{
		$this->manejador_interface->mensaje("Calculando revisiones {$this->identificador} ", false);
		//Necesito recuperar el checksum de la base de datos.
		$checksum_anterior = $this->instancia->get_checksum_proyecto($this->identificador);
		if (! is_null($checksum_anterior)) {
			//Ahora calculo el checksum del directorio
			$checksum_actual = toba_manejador_archivos::get_checksum_directorio($this->get_dir_dump());
			if ($checksum_anterior != $checksum_actual) {
				//Estoy en problemas
				$msg = "PROYECTO {$this->identificador}: \n Algún archivo de metadatos ".
				"tiene revision mayor a la existente en la " .
				"instancia.\n\n Si desea preservar las modificaciones locales se recomiendan los siguientes pasos: \n \n";
				
				$svn = new toba_svn();
				if ($svn->hay_cliente_svn()) { //Si hay cliente probablemente calcule la revision
					$max_rev = $this->instancia->get_revision_proyecto($this->identificador);
					$msg .= " * Update a revisión '$max_rev' (svn update -r $max_rev)\n".
									" * Exportación de proyecto (toba proyecto exportar)\n";
				}else{
					$msg .= " * Update a alguna revisión anterior (svn update -r (revision_actual - 1)) \n".
									" * Exportación de proyecto (toba proyecto exportar)\n".
									"	Si falla: \n".
									//"			* Ejecutar un revert (svn revert -R directorio_metadatos)\n".
									"			* Ejecutar los dos pasos anteriores nuevamente \n";
									//"	Si no falla continuar ejecutando: \n";
				}
				$msg .=	
				" * Actualización SVN (svn update)\n".
				" * Regeneración de proyecto (toba proyecto regenerar)\n\n".
				"Si en cambio quiere descartar los posibles cambios locales simplemente regenere el proyecto (toba proyecto regenerar)\n\n".
				"Este mensaje tiene como objetivo prevenir que se edite el proyecto sin antes haber sincronizado el trabajo del resto del equipo.";
				throw new toba_error_def($msg);
				$this->manejador_interface->progreso_avanzar();
			}
		}
		$this->manejador_interface->progreso_fin();
	}
	

	function generar_estado_codigo()
	{
		//Se cambia el chequeo de propiedades svn a checksum de archivos
		$this->manejador_interface->mensaje("Calculando revisiones {$this->identificador} " , false);
		$this->generar_checksum();

		//Esto simplemente se calcula para darle una idea al pobre chango de cual
		//fue la ultima revision que cargo en la base,util para el revert
		$svn = new toba_svn();
		if ($svn->hay_cliente_svn()) {
			$max_rev = 0;
			$revisiones = $svn->get_revisiones_dir_recursivos($this->get_dir_dump());
			$max_rev = 0;
			if (! empty($revisiones)) {
				foreach($revisiones as $revision) {
					if (isset($revision['error'])) {
						throw new toba_error_def($revision['error']);
					}
					if ($max_rev < intval($revision['revision'])) {
						$max_rev = intval($revision['revision']);
					}
				}
			}
			$this->manejador_interface->progreso_avanzar();		
			$this->instancia->set_revision_proyecto($this->identificador, $max_rev);
		}
		$this->manejador_interface->progreso_fin();
	}

	function generar_checksum()
	{
		$checksum_actual = toba_manejador_archivos::get_checksum_directorio($this->get_dir_dump());
		$this->instancia->set_checksum_proyecto($this->identificador, $checksum_actual);
	}
	
	function eliminar_permisos_editables()
	{
		$this->manejador_interface->mensaje("Eliminando perfiles editables", false);
		toba_logger::instancia()->debug("Eliminando perfiles editables '{$this->identificador}'");
		$proyecto = $this->db->quote($this->identificador);
		$where = "proyecto = $proyecto AND usuario_grupo_acc IN (SELECT gc.usuario_grupo_acc FROM apex_usuario_grupo_acc AS gc WHERE gc.proyecto = $proyecto AND gc.permite_edicion = 1)";
		foreach(array_reverse($this->get_lista_tablas_perfil_funcional()) as $tabla) {
			$sql = "DELETE FROM $tabla WHERE $where";
			$cant = $this->db->ejecutar($sql);
			toba_logger::instancia()->debug("$tabla ($cant)");
			$this->manejador_interface->progreso_avanzar();
		}
		
		toba_logger::instancia()->debug("Eliminando restricciones editables '{$this->identificador}'");
		$where = "proyecto = $proyecto AND restriccion_funcional IN (SELECT res.restriccion_funcional FROM apex_restriccion_funcional AS res WHERE res.proyecto = $proyecto AND res.permite_edicion = 1)";
		foreach(array_reverse($this->get_lista_tablas_restricciones()) as $tabla) {
			$sql = "DELETE FROM $tabla WHERE $where";
			$this->db->ejecutar($sql);
			$cant = $this->db->ejecutar($sql);
			toba_logger::instancia()->debug("$tabla ($cant)");
			$this->manejador_interface->progreso_avanzar();
		}
		$this->manejador_interface->progreso_fin();
	}
	
	
	
	//------------------------------------------------------------------------------------------//
	//				SERVICIOS WEB						//
	//-----------------------------------------------------------------------------------------//
	
	function get_servicios_web_ofrecidos()
	{		
		$sql = "
			SELECT 
				item as servicio_web,
				nombre
			FROM apex_item 
			WHERE 
				proyecto = '{$this->get_id()}'
				AND solicitud_tipo = 'servicio_web'
			ORDER BY item;
		";
		return  $this->get_db(true)->consultar($sql);
	}
	
	function desactivar_servicios_web()
	{
		$desactivados = array();
		$servicios = $this->get_servicios_web_ofrecidos();
		foreach($servicios as $serv) {
			if (! toba_modelo_servicio_web::esta_activo($this, $serv['servicio_web'])) {				//Esto en realidad verifica si existe o no la configuracion
				toba_modelo_servicio_web::set_estado_activacion( $this, $serv['servicio_web'], 0);	//Explicito la desactivacion
				$desactivados[] = $serv['servicio_web'];
			}
		}
		return $desactivados;
	}
	
	/**
	 * Devuelve un array con los paths donde deberia estar la api rest del proyecto
	 * @return array
	 */
	public function get_path_api_rest()
	{
		$api_base = toba_modelo_rest::get_dir_api_base($this);
		$api_pers = toba_modelo_rest::get_dir_api_personalizacion($this);
		$path_controladores = array($api_base, $api_pers);
		return $path_controladores;
	}
	
	/**
	 * Devuelve un string en formato Json con la documentacion de la API REST
	 * @return string
	 */
	public function get_documentacion_rest()
	{
		$doc_rest = '';
		
		//Armo la info de inicializacion de libreria REST
		$ini =  toba_modelo_rest::get_ini_server($this, '');		
		$es_produccion = (boolean) $this->get_instalacion()->es_produccion();		
		$path_controladores = $this->get_path_api_rest();		
		$url_base = toba_modelo_rest::get_url_base($this);
		$settings = array(
			'path_controladores' => $path_controladores,
			'url_api' => $url_base,
			'prefijo_api_docs' => 'api-docs',
			'debug' => !$es_produccion,
			'encoding' => 'latin1'
		);
		//Agrego el nro de version del proyecto a la API
		$datos_ini_proyecto = $this->get_parametro('proyecto','version', false);
		if (isset($datos_ini_proyecto)) {
			$settings['api_version'] = $datos_ini_proyecto;
		}
		$settings = array_merge($settings, $ini->get('settings', null, array(), false));
		
		//Hay que definir esto porque la libreria REST las asume siempre presentes y saca notice rompiendo el json.
		$_SERVER['REQUEST_METHOD'] = '';									
		$_SERVER['REQUEST_URI'] = '';							
		
		//Instancio la libreria, agrego los mocks necesarios para poder generar sin problemas la doc
		$app = new SIUToba\rest\rest($settings);
		$app->set_request(new mock_request($url_base. '/api-docs'));
		$app->set_autenticador(new mock_autenticador());
		
		//Capturo el procesamiento del pedido donde se genera la documentacion
		ob_start();
		$app->procesar();
		$doc_rest = ob_get_clean();
		return $doc_rest;		
	}
	
}
?>
