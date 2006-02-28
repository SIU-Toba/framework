<?php
require_once('lib/elemento_modelo.php');
require_once('modelo/instancia.php');
require_once('nucleo/componentes/catalogo_toba.php');
require_once('nucleo/componentes/cargador_toba.php');
require_once('nucleo/lib/manejador_archivos.php');
require_once('nucleo/lib/sincronizador_archivos.php');
require_once('nucleo/lib/editor_archivos.php');
require_once('nucleo/lib/reflexion/clase_datos.php');
require_once('modelo/estructura_db/tablas_proyecto.php');
require_once('modelo/estructura_db/tablas_instancia.php');
require_once('modelo/estructura_db/tablas_componente.php');

/*
*	Administrador de metadatos de PROYECTOS

	Atencion: en el caso de la importacion de proyectos directa (sin instancia)
	hay que actualizar las secuencias despues.
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
		parent::__construct();
		$this->instancia = $instancia;
		$this->identificador = $identificador;
		if ( $this->identificador == 'toba' ) {
			$this->dir = $this->dir_raiz . '/php/admin';	
		} else {
			$this->dir = $this->dir_raiz . '/proyectos/' . $this->identificador;	
		}
		if( ! is_dir( $this->dir ) ) {
			throw new excepcion_toba("PROYECTO: El proyecto '{$this->identificador}' es invalido. (la carpeta '{$this->dir}' no existe)");
		}
		$this->db = $this->instancia->get_db();
		$this->sincro_archivos = new sincronizador_archivos( $this->get_dir_dump() );
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

	function info()
	{
		$sql = "	SELECT clase, COUNT(*) as cantidad
					FROM apex_objeto
					WHERE proyecto = '{$this->identificador}'
					GROUP BY 1
					ORDER BY 2 DESC";
		$datos = consultar_fuente($sql, 'instancia' );
		return $datos;
	}

	function get_instancia()
	{
		return $this->instancia;
	}

	//-----------------------------------------------------------
	//	EXPORTAR
	//-----------------------------------------------------------

	function exportar( $control_vinculo = true )
	{
		$existen_metadatos = $this->instancia->existen_metadatos_proyecto( $this->identificador );
		// El el caso de la creacion de un proyecto, el vinculo a las intancia no puede reconocerse
		// porque la clase informativa se incluyo antes de la creacion del vinculo. Esto hace que sea
		// necesario desactivar el control.
		$existe_vinculo = $control_vinculo && $this->instancia->existe_proyecto_vinculado( $this->identificador );
		if( !( $existen_metadatos || $existe_vinculo ) ) {
			throw new excepcion_toba("PROYECTO: El proyecto '{$this->identificador}' no esta asociado a la instancia actual");
		}
		try {
			$this->exportar_tablas();
			$this->exportar_componentes();
			$this->sincronizar_archivos();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante la exportacion.' );
			$this->manejador_interface->mensaje( $e->getMessage() );
		}
	}
	
	private function sincronizar_archivos()
	{
		$this->manejador_interface->titulo( "SINCRONIZAR ARCHIVOS" );
		$obs = $this->sincro_archivos->sincronizar();
		$this->manejador_interface->lista( $obs, 'Observaciones' );
	}

	private function exportar_tablas()
	{
		$this->manejador_interface->titulo( "TABLAS" );
		manejador_archivos::crear_arbol_directorios( $this->get_dir_tablas() );
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
			$datos = consultar_fuente($sql, 'instancia' );
			$regs = count( $datos );
			if ( $regs > 1 ) {
				$columnas_orden = array_map('trim', explode(',',$definicion['dump_order_by']) );
				$datos = rs_ordenar_por_columnas( $datos, $columnas_orden );
			}
			$this->manejador_interface->mensaje( "TABLA  $tabla  --  $regs" );
			for ( $a = 0; $a < $regs ; $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
			}
			if ( trim( $contenido ) != '' ) {
				$this->guardar_archivo( $this->get_dir_tablas() .'/'. $tabla . '.sql', $contenido );			
			}
		}
	}

	/*
	*	Exporta los componentes
	*/
	private function exportar_componentes()
	{
		$this->manejador_interface->titulo( "COMPONENTES" );
		cargador_toba::instancia()->crear_cache_simple( $this->get_id() );
		foreach (catalogo_toba::get_lista_tipo_componentes_dump() as $tipo) {
			$lista_componentes = catalogo_toba::get_lista_componentes( $tipo, $this->get_id() );
			foreach ( $lista_componentes as $id_componente) {
				$this->exportar_componente( $tipo, $id_componente );
			}
		}
	}
	
	/*
	*	Exporta un componente
	*/
	private function exportar_componente( $tipo, $id )
	{
		$this->manejador_interface->mensaje("COMPONENTE $tipo  --  " . $id['componente']);
		$directorio = $this->get_dir_componentes() . '/' . $tipo;
		manejador_archivos::crear_arbol_directorios( $directorio );
		$archivo = manejador_archivos::nombre_valido( self::dump_prefijo_componentes . $id['componente'] );
		$contenido =&  $this->get_contenido_componente( $tipo, $id );
		$this->guardar_archivo( $directorio .'/'. $archivo . '.sql', $contenido ); 
	}
	
	/*
	*	Genera el contenido de la exportacion de un componente
	*/
	private function & get_contenido_componente( $tipo, $id )
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

	//-----------------------------------------------------------
	//	CARGAR
	//-----------------------------------------------------------
	
	/*
	*	Importacion de un PROYECTO dentro del proceso de CARGA de una INSTANCIA
	*/
	function cargar()
	{
		if( ! $this->instancia->existe_proyecto_vinculado( $this->identificador ) ) {
			throw new excepcion_toba("PROYECTO: El proyecto '{$this->identificador}' no esta vinculado a la instancia actual.");
		}
		$this->cargar_tablas();
		$this->cargar_componentes();
	}

	/*
	*	cargar un PROYECTO en una instancia ya creada
	*/
	function cargar_autonomo()
	{
		if( ! $this->instancia->existe_proyecto_vinculado( $this->identificador ) ) {
			throw new excepcion_toba("PROYECTO: El proyecto '{$this->identificador}' no esta vinculado a la instancia actual.");
		}
		try {
			$this->db->abrir_transaccion();
			$this->db->retrazar_constraints();
			$this->cargar();
			$this->db->cerrar_transaccion();
		} catch ( excepcion_toba $e ) {
			$this->db->abortar_transaccion();
			$this->manejador_interface->error( 'PROYECTO: Ha ocurrido un error durante la IMPORTACION.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}
	
	private function cargar_tablas()
	{
		$archivos = manejador_archivos::get_archivos_directorio( $this->get_dir_tablas(), '|.*\.sql|' );
		foreach( $archivos as $archivo ) {
			$this->manejador_interface->mensaje( $archivo );
			$this->db->ejecutar_archivo( $archivo );
		}
	}
	
	private function cargar_componentes()
	{
		$subdirs = manejador_archivos::get_subdirectorios( $this->get_dir_componentes() );
		foreach ( $subdirs as $dir ) {
			$this->manejador_interface->mensaje( $dir );
			$archivos = manejador_archivos::get_archivos_directorio( $dir , '|.*\.sql|' );
			foreach( $archivos as $archivo ) {
				$this->db->ejecutar_archivo( $archivo );
			}
		}
	}

	//-----------------------------------------------------------
	//	ELIMINAR
	//-----------------------------------------------------------

	function eliminar()
	{
		try {
			$this->db->abrir_transaccion();
			$this->db->retrazar_constraints();
			$sql = $this->get_sql_eliminacion();
			$this->db->ejecutar( $sql );
			$this->db->cerrar_transaccion();
			$this->manejador_interface->mensaje("El proyecto '{$this->identificador}' ha sido eliminado");
		} catch ( excepcion_toba $e ) {
			$this->db->abortar_transaccion();
			$this->manejador_interface->error( 'Ha ocurrido un error durante la eliminacion de TABLAS de la instancia.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}
	
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
	//	COMPILAR
	//-----------------------------------------------------------

	function compilar()
	{
		try {
			$this->compilar_componentes();
			$this->crear_compilar_archivo_referencia();
		} catch ( excepcion_toba $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante la compilacion.' );
			$this->manejador_interface->mensaje( $e->getMessage() );
		}
	}

	/*
	*	Ciclo de compilacion de componentes
	*/
	function compilar_componentes()
	{
		foreach (catalogo_toba::get_lista_tipo_componentes() as $tipo) {
			$this->manejador_interface->titulo( $tipo );
			$path = $this->get_dir_componentes_compilados() . '/' . $tipo;
			manejador_archivos::crear_arbol_directorios( $path );
			foreach (catalogo_toba::get_lista_componentes( $tipo, $this->get_id() ) as $id_componente) {
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
		$metadatos = cargador_toba::instancia()->get_metadatos_extendidos( $id, $tipo );
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
	//	Manejo de USUARIOS
	//-----------------------------------------------------------

	function vincular_usuario( $usuario, $perfil_acceso='admin', $perfil_datos='no' )
	{
		$sql = "INSERT INTO apex_usuario_proyecto (proyecto, usuario, usuario_grupo_acc, usuario_perfil_datos)
				VALUES ('". $this->get_id()."','$usuario','$perfil_acceso','$perfil_datos');";
		$this->instancia->get_db()->ejecutar( $sql );
	}

	function desvincular_usuario( $usuario )
	{
		$sql = "DELETE FROM apex_usuario_proyecto 
				WHERE usuario = '$usuario'
				AND proyecto = '".$this->get_id()."'";
		$this->instancia->get_db()->ejecutar( $sql );
	}

	//-----------------------------------------------------------
	//	Primitivas basicas
	//-----------------------------------------------------------

	private function guardar_archivo( $archivo, $contenido )
	{
		file_put_contents( $archivo, $contenido );
		$this->sincro_archivos->agregar_archivo( $archivo );
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
						&& ($archivo != '.' ) && ($archivo != '..' ) ){
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
	static function crear( instancia $instancia, $nombre )
	{
		$dir_template = toba_dir() . self::template_proyecto;
		if ( $nombre == 'toba' ) {
			throw new excepcion_toba("INSTALACION: No es posible crear un proyecto con el nombre 'toba'");	
		}
		if ( self::existe( $nombre ) ) {
			throw new excepcion_toba("INSTALACION: Ya existe una carpeta con el nombre '$nombre' en la carpeta 'proyectos'");	
		}
		$dir_proyecto = toba_dir() . '/proyectos/' . $nombre;
		//- 1 - Creo la CARPETA del PROYECTO
		manejador_archivos::copiar_directorio( $dir_template, $dir_proyecto );
		// Modifico los archivos
		$editor = new editor_archivos();
		$editor->agregar_sustitucion( '|__proyecto__|', $nombre );
		$editor->agregar_sustitucion( '|__instancia__|', $instancia->get_id() );
		$editor->agregar_sustitucion( '|__toba_dir__|', toba_dir() );
		$editor->procesar_archivo( $dir_proyecto . '/www/admin.php' );
		$editor->procesar_archivo( $dir_proyecto . '/www/aplicacion.php' );
		$editor->procesar_archivo( $dir_proyecto . '/toba.conf' );
		//- 2 - Asocio el proyecto a la instancia
		$instancia->vincular_proyecto( $nombre );
		//- 3 - Creo los metadatos basicos del proyecto
		$sql = self::get_sql_metadatos_basicos( $nombre );
		$instancia->get_db()->ejecutar( $sql );
	}
	
	/**
	*	Sentencias de creacion de los metadatos BASICOS
	*/
	static function get_sql_metadatos_basicos( $id_proyecto )
	{
		// Creo el proyecto
		$sql[] = "INSERT INTO apex_proyecto (proyecto, estilo,descripcion,descripcion_corta,listar_multiproyecto) VALUES ('$id_proyecto','toba','$id_proyecto','$id_proyecto',1);";
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
}
?>