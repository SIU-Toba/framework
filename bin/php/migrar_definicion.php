<?

if (isset($_SERVER['toba_dir'])) {
	$dir = $_SERVER['toba_dir']."/php"; 
	$separador = (substr(PHP_OS, 0, 3) == 'WIN') ? ";.;" : ":.:";
	$barra = (substr(PHP_OS, 0, 3) == 'WIN') ? "\\" : "/";
	ini_set("include_path", ini_get("include_path"). $separador . $dir);
} else {
	exit("La variable de entorno 'toba_dir' no existe");	
}

require_once('nucleo/lib/db.php');
require_once('nucleo/lib/reflexion/clase_datos.php');
require_once('instancias.php');
require_once('utilerias_graficas.php');

	$dir_instalacion = $_SERVER['toba_dir'] . '/instalacion/';
	$bases_registradas = array();

	//===============================================
	// 1) BASES
	//===============================================

	$nombre = 'info_bases';
	$path = $dir_instalacion .$nombre.'.php';

	paso( "Migrar la definicion de BASES. (php/instancias.php)" );
	if( ! is_file ( $path ) ) {
		$clase = new clase_datos( $nombre );
		foreach( $instancia as $i => $datos ) {
		    $base['motor']= $datos[apex_db_motor];
		    $base['profile'] = $datos[apex_db_profile];
		    $base['usuario'] = $datos[apex_db_usuario];
		    $base['clave'] = $datos[apex_db_clave];
		    $base['base'] = $datos[apex_db_base];
			$clase->agregar_metodo_datos( $i, $base );	
			$bases_registradas[] = $i;
		}
		$clase->guardar( $path );
		mensaje("la definicion de BASES se encuentra ahora en '$path'");	
	} else {
		alerta( "ya existe una archivo '$path'" );
	}

	//===============================================
	// 2) CLAVES
	//===============================================

	$nombre = 'info_instalacion';
	$path = $dir_instalacion .$nombre.'.php';
	
	paso( "Migrar la definicion de CLAVES. (php/instancias.php)" );
	if( ! is_file ( $path ) ) {
		$clase = new clase_datos( $nombre );
		$clase->agregar_metodo_datos( 'get_clave_querystring', apex_clave_get );	
		$clase->agregar_metodo_datos( 'get_clave_db', apex_clave_db );	
		$clase->guardar( $path );
		mensaje("la definicion de CLAVES se encuentra ahora en '$path'");	
	} else {
		alerta( "ya existe una archivo '$path'" );
	}

	//===============================================
	// 2) INSTANCIAS
	//===============================================

	paso( "Migrar INSTANCIAS toba" );
	mensaje( "Indique bases BASES son INSTANCIAS toba"); 
	//Busco la lista de proyectos de la instalacion
	$proyectos = array();
	$directorio_proyectos = $_SERVER['toba_dir'] . $barra . 'proyectos';
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
	//Creo las instancias, preguntando en cada caso
	//Existe la opcion de conectarse a la base y preguntar si existe la tabla 'apex_objeto',
	//pero puede ser que por algun motivo la base no este online y sea una instancia
	foreach( $instancia as $i => $datos ) {
		if( $datos[apex_db_motor] == 'postgres7' ) {
			separador("BASE: $i");
			print_r($datos);
			if ( dialogo_simple("La base '$i' corresponde a una INSTANCIA TOBA?") ) {
				$path = $dir_instalacion . 'i__' . $i;
				if( is_dir( $path ) ) {
					alerta("No es posible crearla instancia '$i'");
					mensaje("Ya exite una carpeta: $path"); 	
				} else {
					//Creo la carpeta
					mkdir( $path );
					//Creo la clase que proporciona informacion sobre la instancia
					$nombre = 'info_instancia';
					$clase = new clase_datos( $nombre );
					$clase->agregar_metodo_datos('get_base', $i);
					$clase->agregar_metodo_datos('get_lista_proyectos', $proyectos);
					$clase->guardar( $path . '/' . $nombre . '.php');
				}
			}
		}
	}
	
	//===============================================

	separador("FIN");		
	mensaje("Puede borrar el archivo 'php/instancias.php'");
	mensaje("Toda la informacion correspondiente a la instalacion, se encuentra ahora en la carpeta 'instalacion'");

	//===============================================
?>