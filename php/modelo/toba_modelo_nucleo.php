<?php

class toba_modelo_nucleo extends toba_modelo_elemento
{
	// Directorios de trabajo
	protected $dir_sql;
	protected $dir_ddl;
	protected $ba_instancia = 'toba_db_tablas_instancia';	// ba = base archivo
	protected $ba_nucleo = 'toba_db_tablas_nucleo';
	protected $ba_proyecto = 'toba_db_tablas_proyecto';
	protected $ba_componente = 'toba_db_tablas_componente';
	protected $ba_no_clasificadas = 'toba_db_tablas_no_clasificadas';
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
	// Sincro SVN	
	private $sincro_archivos;
	// Compilacion del nucleo
	private $comp_archivos_nucleo;
	private $comp_archivos_modelo;
	
	//------------------------------------------------
	// Informacion
	//------------------------------------------------

	function get_sincronizador()
	{
		if ( ! isset( $this->sincro_archivos ) ) {
			$this->sincro_archivos = new toba_sincronizador_archivos( $this->get_dir_metadatos(), '|apex_|' );
		}
		return $this->sincro_archivos;
	}

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
		} catch ( toba_error $e ) {
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
		$patron = '|pgsql_a.*\.sql|';
		$this->archivos = toba_manejador_archivos::get_archivos_directorio( $directorio, $patron );
		sort($this->archivos);
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
				throw new toba_error("ERROR: '$archivo' no es un archivo valido\n");
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
						throw new toba_error("Error parseando la linea: $temp\n (archivo: $archivo)");
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
			$dump_proyecto = ( ($tabla['dump'] == 'multiproyecto') || ($tabla['dump'] == 'nucleo_multiproyecto') );
			$dump_nucleo = ( $tabla['dump'] == 'nucleo' );
			$dump_nucleo_multiproyecto = ( $tabla['dump'] == 'nucleo_multiproyecto' );
			$dump_permisos = ( $tabla['dump'] == 'permisos' );
			$sin_dump = ( $tabla ['dump'] == 'no_requerido');
			
			//-- Controles de integridad de la DEFINICION del plan --
			if ( $dump_componente && ( $es_instancia || $es_log ) ) {
				throw new toba_error("La tabla '$id' posee un error en el plan de dumpeo: componente + (historica || instancia).");
			}
			if( $es_instancia && $es_log ) {
				throw new toba_error("La tabla '$id' posee un error en el plan de dumpeo: historica + instancia.");
			}
			if( !( $dump_componente || $dump_proyecto || $dump_nucleo || $dump_permisos || $sin_dump) ) {
				throw new toba_error("La tabla '$id' no posee una modalidad de dumpeo definida.");
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
				if (! $sin_dump){
				$this->plan[ $this->ba_instancia ]['tablas'][] = $id;
				}
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
					if ( $dump_nucleo_multiproyecto ) {
						$this->plan[ $this->ba_nucleo ]['tablas'][] = $id;
						$this->plan[ $this->ba_nucleo ]['indices']['get_lista_nucleo_multiproyecto'][] = $id;
					}
				}  elseif ( $dump_permisos ) {
					$this->plan[ $this->ba_proyecto ]['tablas'][] = $id;
					$this->plan[ $this->ba_proyecto ]['indices']['get_lista_permisos'][] = $id;
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
			$this->catalogo['toba_db_catalogo_general'][] = $id;
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
			$clase = new toba_clase_datos( $nombre );
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
			$clase = new toba_clase_datos( $nombre );
			//Informacion de cada tabla
			$clase->agregar_metodo_datos( 'get_tablas' , $this->catalogo[ $nombre ] );
			$clase->guardar( $this->get_dir_estructura_db() .'/'.$nombre.'.php' );
		}
	}

	//-------------------------------------------------------------------
	// Conversion de archivos DDL a mysql
	//-------------------------------------------------------------------

	function migrar_ddl()
	{
		$this->manejador_interface->mensaje('Migrar DDL postgresql a la sintaxis de otros motores');
		$directorio = toba_modelo_nucleo::get_dir_ddl();
		$archivos = toba_manejador_archivos::get_archivos_directorio( $directorio, '|.*\.sql|' );
		sort($archivos);
		//Migracion a MYSQL
		$dir = toba_modelo_nucleo::get_dir_ddl() . '/mysql/';
		foreach( $archivos as $archivo ) {
			$sql = $this->get_ddl_mysql($archivo);
			toba_manejador_archivos::crear_archivo_con_datos($dir . basename($archivo), $sql);
		}
	}
	
	/**
	*	Convierte de la sintaxis de postgres a la de mysql
	
			Problemas
				- No se puede llamar a un campo SQL
				- El campo item es un varchar de 60 con autoincrement!!
				- PUede haber solo una auto_increment (apex_item tiene varias)

			Actualizacion inicial de secuencias con INSERT
			recuperacion de secuencias de tablas con mas de una PK multiple son distintas
	*/
	private function get_ddl_mysql($archivo)
	{
		$this->manejador_interface->mensaje('Procesando: ' . $archivo);
		$renglones = file($archivo);
		$sql = '';
		foreach($renglones as $renglon) {
			//Las secuencias
			if(preg_match("/^create\ssequence/i",$renglon)){
				continue;
			}
			//Elimino los comentarios generales
			if(preg_match("/^\s*--/",$renglon)){
				continue;
			}
			//Saco comentarios lineas
			$renglon = preg_replace("/--.*$/",'',$renglon);
			//Saco comillas dobles
			$renglon = preg_replace("/\"/",' ',$renglon);
			//Cambio la declaracion del TIMESTAMP
			$renglon = preg_replace("/(?<!default)\stimestamp.*zone/i",' timestamp ',$renglon);
			//Cambio la declaracion del TIME
			$renglon = preg_replace("/(?<!default)\stime.*zone/i",' time ',$renglon);
			//Pongo los VARCHAR que no tienen largo definido como text
			$renglon = preg_replace("/varchar(?!\\s?\()/i",'text',$renglon);
			//Marco el tipo de tabla como InnoDB
			$renglon = preg_replace("/^\s*\);\s*$/",") ENGINE=InnoDB;\n",$renglon);
			//Saco la declaracion de constraints especifica de postgres
			$renglon = preg_replace("/DEFERRABLE|INITIALLY|IMMEDIATE/","",$renglon);
			//Tipos de datos
			$renglon = preg_replace("/\Wint4\W/","integer",$renglon);
			//Cambio las secuencias
			$renglon = preg_replace("/default\s*nextval\s*\(.*\)/i"," auto_increment ",$renglon);


			//fatan las enumeraciones

			//--- Temporales ---	 SHOW INNODB STATUS

			//No se puede llamar "sql" a una columna
			$renglon = preg_replace("/\Wsql\W/","nosql",$renglon);
			$renglon = preg_replace("/\Witem\s*varchar\(60\).*NOT NULL/"," item varchar(60) NOT NULL ",$renglon);
			$renglon = preg_replace("/\Witem_id\s*integer.*NULL/"," item_id integer NULL",$renglon);
			$sql .= $renglon;
		}
		return $sql;
	}

	//-------------------------------------------------------------------
	// EXPORTACION de TABLAS con METADATOS del NUCLEO
	//-------------------------------------------------------------------

	/*
	*	Exporta los metadatos correspondientes a las tablas maestras del sistema
	*/
	function exportar(toba_modelo_instancia $instancia)
	{
		$this->exportar_tablas_nucleo($instancia);
		$this->exportar_tablas_nucleo_multiproyecto($instancia);
		$this->get_sincronizador()->sincronizar();
	}

	function exportar_tablas_nucleo(toba_modelo_instancia $instancia)
	{
		try {
			$this->manejador_interface->titulo( "Tablas NUCLEO" );
			toba_manejador_archivos::crear_arbol_directorios( $this->get_dir_metadatos() );
			foreach ( toba_db_tablas_nucleo::get_lista() as $tabla ) {
				$this->manejador_interface->mensaje( "tabla  --  $tabla" );
				$definicion = toba_db_tablas_nucleo::$tabla();
				//Genero el SQL
				$sql = 'SELECT ' . implode(', ', $definicion['columnas']) .
						" FROM $tabla " .
						" ORDER BY {$definicion['dump_order_by']} ;\n";
				$contenido = "";
				$datos = $instancia->get_db()->consultar( $sql );
				$regs = count( $datos );
				if ( $regs > 1 ) {
					$columnas_orden = array_map('trim', explode(',',$definicion['dump_order_by']) );
					$datos = rs_ordenar_por_columnas( $datos, $columnas_orden );
				}
				for ( $a = 0; $a < $regs ; $a++ ) {
					$contenido .= sql_array_a_insert( $tabla, $datos[$a], $instancia->get_db() )."\n";
				}
				if ( trim( $contenido ) != '' ) {
					$this->guardar_tabla_archivo($tabla, $contenido);
				}
			}
		} catch ( toba_error $e ) {
			$this->manejador_interface->error( 'Ha ocurrido un error durante la exportacion.' );
			$this->manejador_interface->error( $e->getMessage() );
		}
	}

	private function exportar_tablas_nucleo_multiproyecto(toba_modelo_instancia $instancia)
	{
		$this->manejador_interface->titulo( "Tablas NUCLEO - PROYECTO" );
		foreach ( toba_db_tablas_nucleo::get_lista_nucleo_multiproyecto() as $tabla ) {
			$definicion = toba_db_tablas_nucleo::$tabla();
			//Genero el SQL
			if( isset($definicion['dump_where']) && ( trim($definicion['dump_where']) != '') ) {
					$w = stripslashes($definicion['dump_where']);
					$where = str_replace("%%",'toba', $w);
			} else {
					$where = " ( proyecto = 'toba')";
			}
			$sql = 'SELECT ' . implode(', ', $definicion['columnas']) .
					" FROM $tabla " .
					" WHERE $where " .
					//" WHERE {$definicion['dump_clave_proyecto']} = '".$this->get_id()."}' " .
					" ORDER BY {$definicion['dump_order_by']} ;\n";
			//$this->manejador_interface->mensaje( $sql );
			$contenido = "";
			$datos = $instancia->get_db()->consultar($sql);
			$regs = count( $datos );
			if ( $regs > 1 ) {
				$columnas_orden = array_map('trim', explode(',',$definicion['dump_order_by']) );
				$datos = rs_ordenar_por_columnas( $datos, $columnas_orden );
			}
			$this->manejador_interface->mensaje( "TABLA  $tabla  --  $regs" );
			for ( $a = 0; $a < $regs ; $a++ ) {
				$contenido .= sql_array_a_insert( $tabla, $datos[$a], $instancia->get_db() )."\n";
			}
			if ( trim( $contenido ) != '' ) {
				$this->guardar_tabla_archivo($tabla, $contenido);
			}
		}
	}

	private function guardar_tabla_archivo( $tabla, $contenido )
	{
		$archivo = $this->get_dir_metadatos() .'/'. $tabla . '.sql';
		file_put_contents( $archivo, $contenido );
		$this->get_sincronizador()->agregar_archivo( $archivo );
	}
	
	//------------------------------------------------------------------------
	//-- PARSEO de los EDITORES ----------------------------------------------
	//------------------------------------------------------------------------

	function parsear_editores(toba_modelo_instancia $instancia)
	{
		toba_contexto_info::set_db($instancia->get_db());
		//--- Se busca el CI asociado a cada clase
		$sql = "SELECT
					c.clase,
				 	o.proyecto,
					o.objeto
				FROM
					apex_clase c,
					apex_item_objeto io,
					apex_objeto o
				WHERE
					c.clase IN ('". implode("','", toba_info_editores::get_lista_tipo_componentes() ) ."')	AND
					c.proyecto = 'toba' AND
					c.editor_item = io.item AND				-- Se busca el item editor
					c.editor_proyecto = io.proyecto AND
					io.objeto = o.objeto AND				-- Se busca el CI del item
					io.proyecto = o.proyecto AND
					o.clase = 'toba_ci'";
		$rs = $instancia->get_db()->consultar($sql);
		
		$clase_php = new toba_clase_datos( "toba_datos_editores" );		
		foreach ($rs as $datos) {
			//--- Se buscan las pantallas asociadas a un CI especifico
			$this->manejador_interface->mensaje("Procesando " . $datos['clase'] . "...");
			$proyecto = $instancia->get_db()->quote($datos['proyecto']);
			$objeto = $instancia->get_db()->quote($datos['objeto']);
			$sql = "
				SELECT
					pant.identificador,
					pant.etiqueta,
					pant.imagen,
					pant.imagen_recurso_origen
				FROM
					apex_objeto_ci_pantalla pant
				WHERE
						pant.objeto_ci_proyecto = $proyecto
					AND pant.objeto_ci = $objeto
				ORDER BY pant.orden
			";
			$pantallas = $instancia->get_db()->consultar($sql);
			$clase_php->agregar_metodo_datos( 'get_pantallas_'.$datos['clase'] , $pantallas );
		}
		$dir = toba_dir()."/php/modelo/info";
		$clase_php->guardar( $dir.'/toba_datos_editores.php' );
	}
	
	function get_archivos_js_propios($patron_incl=null, $patron_excl=null)
	{
		$dir_js = toba_dir().'/www/js';		
		$archivos = array();		
		if (! isset($patron_incl)) {
			//--- Algunos archivos se ponen por adelantado porque requieren un orden de inclusión
			$archivos[] = $dir_js."/basicos/basico.js";		
			$archivos[] = $dir_js."/basicos/toba.js";
			$archivos[] = $dir_js."/componentes/ei.js";
			$archivos[] = $dir_js."/componentes/ei_formulario.js";
			$archivos[] = $dir_js."/componentes/ei_formulario_ml.js";
			$archivos[] = $dir_js."/efs/ef.js";
			$patron = '/.\.js/';	
		}
		
		$dirs = array($dir_js.'/basicos', $dir_js.'/componentes', $dir_js.'/efs');
		foreach ($dirs as $directorio) {
			$nuevos = toba_manejador_archivos::get_archivos_directorio($directorio, $patron_incl);
			$archivos = array_merge($archivos, $nuevos);
		}
		if (isset($patron_excl)) {
			$nuevos = array();
			foreach( $archivos as $archivo) {
				if(! preg_match( $patron_excl, $archivo )){
					$nuevos[] = $archivo;
				}
			}
			$archivos = $nuevos;
		}
		$archivos = array_unique($archivos);
		return $archivos;
	}
	
	function comprimir_js()
	{
		$archivos = $this->get_archivos_js_propios();
		$total = 0;
		require_once(toba_dir() . '/php/3ros/jscomp/JavaScriptCompressor.class.php');
		require_once(toba_dir() . '/php/3ros/jscomp/BaseConvert.class.php');
		$comp = new JavaScriptCompressor(false);
		$salida = array();
		$this->manejador_interface->mensaje('Comprimiendo '.count($archivos).' archivo/s', false);				
		foreach ($archivos as $archivo) {
			if (strpos($archivo, "www/js/toba_") !== false) {
				//--- Evita comprimir dos veces
				continue;	
			}
			$atr = stat($archivo);
			$total += $atr['size'];
			$nuevo = $comp->getClean(array('code' =>file_get_contents($archivo), 'name' => basename($archivo)));
			$salida[] = $nuevo;
			$this->manejador_interface->progreso_avanzar();			
		}
		$this->manejador_interface->progreso_fin();		
		$todo = implode("\n", $salida);
		$version = toba_modelo_instalacion::get_version_actual();
		$version = $version->__toString();
		$archivo = toba_dir()."/www/js/toba_$version.js";
		file_put_contents($archivo, $todo);
		$atr = stat($archivo);
		$nuevo_total = $atr['size'];		
		$this->manejador_interface->mensaje("Antes: $total bytes");
		$this->manejador_interface->mensaje("Despues: ".$nuevo_total." bytes");
		$this->manejador_interface->mensaje("Radio: ".number_format($nuevo_total/$total, 2));
		toba_modelo_instalacion::cambiar_info_basica(array('js_comprimido' => 1));
	}
	
	function validar_js($patron_incl=null, $patron_excl=null)
	{
		$archivos = $this->get_archivos_js_propios($patron_incl, $patron_excl);
		$this->manejador_interface->mensaje('Validando '.count($archivos).' archivo/s', false);		
		$validador = toba_dir().'/bin/herramientas/jslint.js';
		$ok = true;
		foreach ($archivos as $archivo) {
			if (strpos($archivo, "www/js/toba_") !== false) {
				//--- Evita chequear el comprimido
				continue;	
			}			
			$cmd = "rhino -opt 9 $validador $archivo";
			$otro = null;
			exec($cmd, $salida);
			if (! empty($salida)) {
				$this->manejador_interface->enter();
				$relativo = str_replace(toba_dir(), '', $archivo);
				$this->manejador_interface->subtitulo("$relativo :");			
				$this->manejador_interface->mensaje(implode("\n", $salida));
				$ok = false;
				break;
			}
			$this->manejador_interface->progreso_avanzar();
		}
		if ($ok) {
			$this->manejador_interface->progreso_fin();
		}
	}

	//------------------------------------------------------------------------
	//-- Compilacion del nucleo ----------------------------------------------
	//------------------------------------------------------------------------

	function compilar()
	{
		$this->manejador_interface->titulo('Compilacion del nucleo');
		$this->resumir_definicion_componentes();
		//$this->resumir_nucleo();
	}
	
	function resumir_nucleo()
	{
		$destino = toba_dir() . '/php/nucleo/toba_motor.php';
		$this->manejador_interface->mensaje('Clases del nucleo', false);			
		$this->cargar_lista_archivos();
		$resumen = '';
		$archivos = array_merge($this->comp_archivos_nucleo, $this->comp_archivos_modelo);
		$buscar = array(	'|<\?php|',
							'|\?>|',
						//	'|/\*\*.*?\*/|s',
						//	'|/\*.*?\*/|s',
						//	'|\s*//.*|',
							'|^\s*$|m'
						);
		foreach($archivos as $archivo) {
			$php = file_get_contents(toba_dir(). '/php/' . $archivo);
			$php = preg_replace($buscar,'',$php);
			$resumen .= $php;
			$this->manejador_interface->progreso_avanzar();			
		}
		$resumen = "<?php\n" . $resumen . "\n?>";
		file_put_contents($destino, $resumen);
		$this->manejador_interface->progreso_fin();			
	}
	
	function cargar_lista_archivos()
	{
		$this->comp_archivos_nucleo = array(
			'lib/toba_parseo.php',
			'lib/toba_sql.php',
			'lib/toba_varios.php',
			'lib/toba_asercion.php',
			'lib/toba_cache_db.php',
			'lib/db/toba_db.php',
			'lib/toba_encriptador.php',
			'lib/toba_manejador_archivos.php',
			'nucleo/lib/interface/toba_form.php',
			'nucleo/lib/salidas/toba_impr_html.php',
			'nucleo/lib/salidas/toba_impresion.php',
			'nucleo/lib/salidas/toba_pdf.php',
			'nucleo/lib/interface/toba_ei.php',
			'nucleo/lib/interface/toba_formateo.php',
			'nucleo/lib/toba_admin_fuentes.php',
			'nucleo/lib/toba_contexto_ejecucion.php',
			'nucleo/lib/toba_cronometro.php',
			'nucleo/lib/toba_db.php',
			'nucleo/lib/toba_dba.php',
			'nucleo/lib/toba_debug.php',
			'nucleo/lib/toba_editor.php',
			'nucleo/lib/toba_error.php',
			'nucleo/lib/toba_fuente_datos.php',
			'nucleo/lib/toba_http.php',
			'nucleo/lib/toba_instalacion.php',
			'nucleo/lib/toba_instancia.php',
			'nucleo/lib/toba_interface_contexto_ejecucion.php',
			'nucleo/lib/toba_interface_usuario.php',
			'nucleo/lib/toba_js.php',
			'nucleo/lib/toba_logger.php',
			'nucleo/lib/toba_manejador_sesiones.php',
			'nucleo/lib/toba_memoria.php',
			'nucleo/lib/toba_mensajes.php',
			'nucleo/lib/toba_notificacion.php',
			'nucleo/lib/toba_parser_ayuda.php',
			'nucleo/lib/toba_derechos.php',
			'nucleo/lib/toba_proyecto.php',
			'nucleo/lib/toba_proyecto_db.php',
			'nucleo/lib/toba_puntos_control.php',
			'nucleo/lib/toba_recurso.php',
			'nucleo/lib/toba_sesion.php',
			'nucleo/lib/toba_usuario.php',
			'nucleo/lib/toba_usuario_anonimo.php',
			'nucleo/lib/toba_usuario_basico.php',
			'nucleo/lib/toba_usuario_no_autenticado.php',
			'nucleo/lib/toba_vinculador.php',
			'nucleo/lib/toba_vinculo.php',
			'nucleo/lib/toba_zona.php',
			'nucleo/menu/toba_menu.php',
			'nucleo/tipo_pagina/toba_tipo_pagina.php',
			'nucleo/tipo_pagina/toba_tp_basico.php',
			'nucleo/tipo_pagina/toba_tp_basico_titulo.php',
			'nucleo/tipo_pagina/toba_tp_logon.php', 
			'nucleo/tipo_pagina/toba_tp_popup.php', 
			'nucleo/tipo_pagina/toba_tp_normal.php',
			'nucleo/toba_solicitud.php',
			'nucleo/toba_solicitud_web.php',
			'nucleo/toba_solicitud_accion.php',
			'nucleo/toba_solicitud_consola.php',
			'nucleo/componentes/toba_definicion_componentes.php',
			'nucleo/componentes/toba_cargador.php',
			'nucleo/componentes/toba_catalogo.php',
			'nucleo/componentes/toba_constructor.php',
			'nucleo/componentes/toba_componente.php',
			'nucleo/componentes/interface/botones/toba_boton.php',
			'nucleo/componentes/interface/botones/toba_evento_usuario.php',
			'nucleo/componentes/interface/botones/toba_tab.php',
			'nucleo/componentes/interface/efs/toba_ef.php',
			'nucleo/componentes/interface/efs/toba_ef_combo.php',
			'nucleo/componentes/interface/efs/toba_ef_cuit.php',
			'nucleo/componentes/interface/efs/toba_ef_editable.php',
			'nucleo/componentes/interface/efs/toba_ef_multi_seleccion.php',
			'nucleo/componentes/interface/efs/toba_ef_oculto.php',
			'nucleo/componentes/interface/efs/toba_ef_popup.php',
			'nucleo/componentes/interface/efs/toba_ef_sin_estado.php',
			'nucleo/componentes/interface/efs/toba_ef_upload.php',
			'nucleo/componentes/interface/efs/toba_ef_varios.php',
			'nucleo/componentes/interface/interfaces.php',
			'nucleo/componentes/interface/toba_ei.php',
			'nucleo/componentes/interface/toba_ci.php',
			'nucleo/componentes/interface/toba_ei_arbol.php',
			'nucleo/componentes/interface/toba_ei_archivos.php',
			'nucleo/componentes/interface/toba_ei_calendario.php',
			'nucleo/componentes/interface/toba_ei_cuadro.php',
			'nucleo/componentes/interface/toba_ei_esquema.php',
			'nucleo/componentes/interface/toba_ei_filtro.php',
			'nucleo/componentes/interface/toba_ei_formulario.php',
			'nucleo/componentes/interface/toba_ei_formulario_ml.php',
			'nucleo/componentes/interface/toba_ei_pantalla.php',
			'nucleo/componentes/persistencia/toba_ap.php',
			'nucleo/componentes/persistencia/toba_ap_relacion_db.php',
			'nucleo/componentes/persistencia/toba_ap_tabla_db.php',
			'nucleo/componentes/persistencia/toba_ap_tabla_db_mt.php',
			'nucleo/componentes/persistencia/toba_ap_tabla_db_s.php',
			'nucleo/componentes/persistencia/toba_datos_busqueda.php',
			'nucleo/componentes/persistencia/toba_datos_relacion.php',
			'nucleo/componentes/persistencia/toba_datos_tabla.php',
			'nucleo/componentes/persistencia/toba_relacion_entre_tablas.php',
			'nucleo/componentes/persistencia/toba_tipo_datos.php',
			'nucleo/componentes/negocio/toba_cn.php'
		);
		$this->comp_archivos_modelo = array(
			'modelo/info/componentes/toba_datos_editores.php',
			'modelo/info/componentes/toba_interface_meta_clase.php',
			'modelo/info/componentes/toba_componente_info.php',
			'modelo/info/componentes/toba_ap_relacion_db_info.php',
			'modelo/info/componentes/toba_ap_tabla_db_info.php',
			'modelo/info/componentes/toba_cn_info.php',
			'modelo/info/componentes/toba_datos_relacion_info.php',
			'modelo/info/componentes/toba_datos_tabla_info.php',
			'modelo/info/componentes/toba_ei_info.php',
			'modelo/info/componentes/toba_ci_info.php',
			'modelo/info/componentes/toba_ci_pantalla_info.php',
			'modelo/info/componentes/toba_ei_arbol_info.php',
			'modelo/info/componentes/toba_ei_archivos_info.php',
			'modelo/info/componentes/toba_ei_calendario_info.php',
			'modelo/info/componentes/toba_ei_cuadro_info.php',
			'modelo/info/componentes/toba_ei_esquema_info.php',
			'modelo/info/componentes/toba_ei_formulario_info.php',
			'modelo/info/componentes/toba_ei_formulario_ml_info.php',
			'modelo/info/componentes/toba_ei_filtro_info.php',
			'modelo/info/componentes/toba_item_info.php',
			'modelo/info/toba_info_editores.php',
			'modelo/info/toba_info_permisos.php',
			'modelo/info/toba_contexto_info.php'		
		);
	}
}
?>
