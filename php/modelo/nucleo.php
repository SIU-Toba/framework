<?php
require_once('lib/elemento_modelo.php');
require_once('modelo/estructura_db/tablas_nucleo.php');
require_once('lib/toba_manejador_archivos.php');
require_once('lib/toba_sincronizador_archivos.php');
require_once('lib/reflexion/toba_clase_datos.php');

class nucleo extends elemento_modelo
{
	// Directorios de trabajo
	protected $dir_sql;
	protected $dir_ddl;
	protected $ba_instancia = 'tablas_instancia';	// ba = base archivo
	protected $ba_nucleo = 'tablas_nucleo';
	protected $ba_proyecto = 'tablas_proyecto';
	protected $ba_componente = 'tablas_componente';
	protected $ba_no_clasificadas = 'tablas_no_clasificadas';
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
			//-- Controles de integridad de la DEFINICION del plan --
			if ( $dump_componente && ( $es_instancia || $es_log ) ) {
				throw new toba_error("La tabla '$id' posee un error en el plan de dumpeo: componente + (historica || instancia).");
			}
			if( $es_instancia && $es_log ) {
				throw new toba_error("La tabla '$id' posee un error en el plan de dumpeo: historica + instancia.");
			}
			if( !( $dump_componente || $dump_proyecto || $dump_nucleo || $dump_permisos ) ) {
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
				$this->plan[ $this->ba_instancia ]['tablas'][] = $id;
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
			$this->catalogo['catalogo_general'][] = $id;
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
	// EXPORTACION de TABLAS con METADATOS del NUCLEO
	//-------------------------------------------------------------------

	/*
	*	Exporta los metadatos correspondientes a las tablas maestras del sistema
	*/
	function exportar(instancia $instancia)
	{
		$this->exportar_tablas_nucleo($instancia);
		$this->exportar_tablas_nucleo_multiproyecto($instancia);
		$this->get_sincronizador()->sincronizar();
	}

	function exportar_tablas_nucleo(instancia $instancia)
	{
		try {
			$this->manejador_interface->titulo( "Tablas NUCLEO" );
			toba_manejador_archivos::crear_arbol_directorios( $this->get_dir_metadatos() );
			foreach ( tablas_nucleo::get_lista() as $tabla ) {
				$this->manejador_interface->mensaje( "tabla  --  $tabla" );
				$definicion = tablas_nucleo::$tabla();
				//Genero el SQL
				$sql = "SELECT " . implode(', ', $definicion['columnas']) .
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
					$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
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

	private function exportar_tablas_nucleo_multiproyecto(instancia $instancia)
	{
		$this->manejador_interface->titulo( "Tablas NUCLEO - PROYECTO" );
		foreach ( tablas_nucleo::get_lista_nucleo_multiproyecto() as $tabla ) {
			$definicion = tablas_nucleo::$tabla();
			//Genero el SQL
			if( isset($definicion['dump_where']) && ( trim($definicion['dump_where']) != '') ) {
       			$w = stripslashes($definicion['dump_where']);
       			$where = ereg_replace("%%",'toba', $w);
            } else {
       			$where = " ( proyecto = 'toba')";
			}
			$sql = "SELECT " . implode(', ', $definicion['columnas']) .
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
				$contenido .= sql_array_a_insert( $tabla, $datos[$a] );
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

	function parsear_editores(instancia $instancia)
	{
		require_once("modelo/consultas/dao_editores.php");
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
					c.clase IN ('". implode("','", dao_editores::get_clases_validas() ) ."')	AND
					c.proyecto = 'toba' AND
					c.editor_item = io.item AND				-- Se busca el item editor
					c.editor_proyecto = io.proyecto AND
					io.objeto = o.objeto AND				-- Se busca el CI del item
					io.proyecto = o.proyecto AND
					o.clase = 'objeto_ci'";
		$rs = $instancia->get_db()->consultar($sql);
		
		$clase_php = new toba_clase_datos( "datos_editores" );		
		foreach ($rs as $datos) {
			//--- Se buscan las pantallas asociadas a un CI especifico
			$this->manejador_interface->mensaje("Procesando " . $datos['clase'] . "...");
			$sql = "
				SELECT
					pant.identificador,
					pant.etiqueta,
					pant.imagen,
					pant.imagen_recurso_origen
				FROM
					apex_objeto_ci_pantalla pant
				WHERE
						pant.objeto_ci_proyecto = '{$datos['proyecto']}' 
					AND pant.objeto_ci = '{$datos['objeto']}' 
				ORDER BY pant.orden
			";
			$pantallas = $instancia->get_db()->consultar($sql);
			$clase_php->agregar_metodo_datos( 'get_pantallas_'.$datos['clase'] , $pantallas );
		}
		$dir = toba_dir()."/php/modelo/componentes";
		$clase_php->guardar( $dir.'/datos_editores.php' );
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
		require_once('3ros/jscomp/JavaScriptCompressor.class.php');
		require_once('3ros/jscomp/BaseConvert.class.php');
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
			$this->manejador_interface->mensaje_directo('.');			
		}
		$this->manejador_interface->mensaje('OK');		
		$todo = implode("\n", $salida);
		$version = instalacion::get_version_actual();
		$version = $version->__toString();
		$archivo = toba_dir()."/www/js/toba_$version.js";
		file_put_contents($archivo, $todo);
		$atr = stat($archivo);
		$nuevo_total = $atr['size'];		
		$this->manejador_interface->mensaje("Antes: $total bytes");
		$this->manejador_interface->mensaje("Despues: ".$nuevo_total." bytes");
		$this->manejador_interface->mensaje("Radio: ".number_format($nuevo_total/$total, 2));
		instalacion::cambiar_info_basica(array('js_comprimido' => 1));
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
				echo implode("\n", $salida);
				$ok = false;
				break;
			}
			$this->manejador_interface->mensaje_directo('.');
		}
		if ($ok) {
			$this->manejador_interface->mensaje('OK');
		}
	}

	//------------------------------------------------------------------------
	//-- Compilacion del nucleo ----------------------------------------------
	//------------------------------------------------------------------------

	function compilar()
	{
		//$this->resumir_nucleo();
		$this->resumir_definicion_componentes();
	}
	
	/**
	* Resume las definicines de los componentes en un solo archivo
	*	(Esto evita un monton de requires dinamicos cuando se cargan componentes)
	*/
	function resumir_definicion_componentes()
	{
		$resumen = '';
		$directorio =  toba_dir() . '/php/nucleo/componentes/definicion';
		$archivos = toba_manejador_archivos::get_archivos_directorio( $directorio, '|.*\.php|' );
		$buscar = array(	'|<\?php|',
							'|\?>|',
							'|require_once.*;|',
							'|/\*\*.*?\*/|s',
							'|\s*//.*|',
							'|^\s*$|m'
						);
		foreach($archivos as $archivo) {
			$php = file_get_contents($archivo);
			$php = preg_replace($buscar,'',$php);
			$resumen .= $php;
		}
		$resumen = "<?php\n" . $resumen . "\n?>";
		$destino = toba_dir() . '/php/nucleo/componentes/toba_definicion.php';
		file_put_contents($destino, $resumen);
	}

	function resumir_nucleo()
	{
		$destino = toba_dir() . '/php/nucleo/engine_toba.php';
		if(file_exists($destino)) unlink($destino);
		$this->manejador_interface->titulo('Compilando el nucleo');
		$resumen = '';
		$directorio =  toba_dir() . '/php/nucleo';
		$archivos = toba_manejador_archivos::get_archivos_directorio( $directorio, '|toba_.*?\.php|', true );
		$buscar = array(	'|<\?php|',
							'|\?>|',
							'|require_once.*;|',
							'|/\*\*.*?\*/|s',
							'|/\*.*?\*/|s',
							'|\s*//.*|',
							'|^\s*$|m'
						);
		foreach($archivos as $archivo) {
			$php = file_get_contents($archivo);
			$php = preg_replace($buscar,'',$php);
			$resumen .= $php;
			$this->manejador_interface->mensaje($archivo);
		}
		$resumen = "<?php\n" . $resumen . "\n?>";
		file_put_contents($destino, $resumen);
	}
}
?>