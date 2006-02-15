<?
/*
	FALTA:
		- Escuchar al usuario con un interprete o recibir parametros de la invocacion
			son dos cosas que deberian tener el mismo resultado
*/
require_once("nucleo/lib/error.php");	    		//Error Handling
require_once("nucleo/lib/cronometro.php");          //Cronometrar ejecucion
require_once("nucleo/lib/db.php");		    		//Manejo de bases (utiliza abodb340)
require_once("nucleo/lib/varios.php");				//Funciones genericas (Manejo de paths, etc.)
require_once("nucleo/lib/sql.php");					//Libreria de manipulacion del SQL
require_once("nucleo/lib/excepcion_toba.php");		//Excepciones del TOBA
require_once("nucleo/lib/logger.php");				//Logger
require_once("nucleo/lib/asercion.php");       	   	//Aserciones
require_once("nucleo/lib/parseo.php");       	   	//Parseo
require_once("nucleo/lib/texto.php");       	   	//Manipulacion de texto
require_once("modelo/lib/gui.php");
require_once("consola/lib/Table.php");
require_once("consola/lib/Getopt.php");

class consola implements gui
{
	const display_ancho = 95;
	const display_coleccion_espacio_nombre = 25;
	const display_prefijo_linea = ' ';
	protected 	$ubicacion_comandos;
	protected	$menu;
	
	function __construct( $ubicacion_comandos, $clase_menu )
	{
		if( ! is_dir( $ubicacion_comandos ) ) {
			throw new excepcion_toba("CONSOLA: El directorio de comandos '$ubicacion_comandos' es invalido");
		}
		$this->ubicacion_comandos = $ubicacion_comandos;
		require_once( $this->ubicacion_comandos ."/$clase_menu.php");
		$this->menu = new $clase_menu( $this );
		cronometro::instancia()->marcar('Consola online');
	}

	function run( $argumentos )
	{
		cronometro::instancia()->marcar('Inicio proceso.');
		if ( count($argumentos) > 0 ) {
			try {
				$comando = $argumentos[0];
				array_shift( $argumentos );
				$this->invocar_comando( $comando, $argumentos );
			} catch (excepcion_toba $e ) {
				$this->mensaje( $e->getMessage() );	
			}
		} else {
			//Aca se tendria que abrir el INTERPRETE
			$this->menu->mostrar_ayuda_raiz();
		}
		cronometro::instancia()->marcar('Fin proceso.');
		$this->menu->mostrar_resumen();
	}

	function invocar_comando($nombre_comando, $argumentos)
	{
		$clase_comando = 'comando_' . $nombre_comando;
		$archivo = $this->ubicacion_comandos .'/'.$clase_comando.'.php';
		if ( file_exists( $archivo ) ) {
			require_once( $this->ubicacion_comandos .'/'.$clase_comando.'.php');
			$comando = new $clase_comando( $this );
			$comando->set_argumentos( $argumentos );			
			$comando->procesar();
		} else {
			throw new excepcion_toba("ERROR: El COMANDO '$nombre_comando' no existe.");
		}
	}
/*
	function interprete()
	{
		fwrite(STDOUT, "Enter 'q' to quit\n");
		do {
		   do {	
			   $selection =	fgetc(STDIN);
		   } while ( trim($selection) == ''	);
			echo $selection;
		} while	( $selection !=	'q'	);
		
		exit(0); 
	}
*/
	function get_ubicacion_comandos()
	{
		return $this->ubicacion_comandos;	
	}

	//----------------------------------------------
	// Primitivas de display
	//----------------------------------------------

	function separador( $texto='', $caracter='-' )
	{
		if($texto!='') $texto = "--  $texto  ";
		echo "\n";
		$this->linea_completa( $texto, $caracter );
		echo "\n";
	}

	function titulo( $texto )
	{
		echo "\n";
		$this->linea_completa( null, '-' );
		$this->linea_completa( " $texto  ", ' ' );
		$this->linea_completa( null, '-' );
		echo "\n";
	}
	
	function subtitulo( $texto )
	{
		echo self::display_prefijo_linea . $texto . "\n";
		echo self::display_prefijo_linea . str_repeat('-', strlen( $texto ) ) ;
		echo "\n\n";
	}

	function mensaje( $texto )
	{
		$lineas = separar_texto_lineas( $texto, self::display_ancho );
		foreach( $lineas as $linea ) {
			echo self::display_prefijo_linea . $linea . "\n";
		}
	}

	function enter()
	{
		echo "\n";	
	}

	/*
	* Imprime un error en la error estandar
	*/
	function error( $texto )
	{
		$lineas = separar_texto_lineas( $texto, self::display_ancho );
		foreach( $lineas as $linea ) {
			fwrite( STDERR, self::display_prefijo_linea . $linea . "\n" );
		}
	}

	/*
	* Muestra una lista de elementos con su descripcion
	* Hay que pasarle un array asociativo elemento/descripcion
	*/
	function coleccion( $coleccion )
	{
		$espacio_descripcion = self::display_ancho - self::display_coleccion_espacio_nombre 
								- strlen( self::display_prefijo_linea );
		foreach( $coleccion as $nombre => $descripcion ) {
			$lineas = separar_texto_lineas( $descripcion, $espacio_descripcion );
			$this->mensaje( str_pad( $nombre, self::display_coleccion_espacio_nombre, ' ' ) . array_shift( $lineas ) );
			foreach( $lineas as $linea ) {
				$this->mensaje( str_repeat(' ', self::display_coleccion_espacio_nombre ) . $linea );	
			}
		}
	}

	/*
	*	Dumpea un ARBOL
	*/
	function dump_arbol( $arbol, $titulo )
	{
		$this->enter();
		$this->subtitulo( $titulo );
		print_r( $arbol );
	}
	
	/*
	* Genera la salida de una linea completando el espacio faltante del display con un caracter
	*/
	function linea_completa( $base='', $caracter_relleno )
	{
		echo str_pad( self::display_prefijo_linea . $base, self::display_ancho, $caracter_relleno );
		echo "\n";
	}

	function lista( $lista, $titulo )
	{
		if( count( $lista ) > 0 ) {
			$i=0;
			foreach($lista as $l) {
				$datos[$i][0] = $l;
				$i++;
			}
			echo Console_Table::fromArray( array( $titulo ), $datos );	
		}
	}
	
	function tabla( $tabla, $titulos )
	{
		echo Console_Table::fromArray( $titulos, $tabla );
	}
	
	//----------------------------------------------
	// Interaccion con el usuario
	//----------------------------------------------

	function dialogo_simple($texto)
	{
		echo "$texto (Si o No)\n";
		do {
			echo "(s/n):";
			$respuesta = trim( fgets( STDIN ) );
			$ok = ($respuesta == 's') || ( $respuesta == 'n');
		} while ( ! $ok );
		if( $respuesta == 's') return true;
		return false;
	}	
}
?>