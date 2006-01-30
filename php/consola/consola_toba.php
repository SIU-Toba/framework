<?
/*
	FALTA:
		- Hay que definir el metodo de obtencion de instancias y proyectos:
			- aca queda mal, hay que hacer una clase 'contexto'?
		- Si se pide un comando que no existe salta un error
		- Hay que incluir el directorio de los proyectos!!
		- Escuchar al usuario con un interprete o recibir parametros de la invocacion
			son dos cosas que deberian tener el mismo resultado
		- Tendria que existir un esquema para extender un comando
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
require_once("interface_usuario.php");

define('apex_pa_instancia','desarrollo');

class consola_toba implements interface_usuario
{
	const display_ancho = 80;
	const display_coleccion_espacio_nombre = 25;
	const display_prefijo_linea = ' ';
	private $dir_raiz;
	private $ubicacion_comandos;
	private $instancia = 'desarrollo';
	private $proyecto = 'referencia';
	
	/**
	*	dir_raiz: instalacion toba sobre la que se va a trabajar
	*	ubicacion_comandos: Directorio donde se encuentran los comandos posibles
	*/
	function __construct( $dir_raiz, $ubicacion_comandos = null )
	{
		$this->dir_raiz = $dir_raiz;
		$this->ubicacion_comandos =	isset( $ubicacion_comandos ) ? $ubicacion_comandos : 'consola/comandos';
		require_once( $this->ubicacion_comandos .'/menu.php');
		cronometro::instancia()->marcar('Consola online');
	}
	
	function get_dir_raiz()
	{
		return $this->dir_raiz;	
	}

	function get_instancia()
	{
		return $this->instancia;	
	}

	function get_proyecto()
	{
		return $this->proyecto;	
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
				$this->mostrar_menu();
			}
		} else {
			//Aca se tendria que abrir el INTERPRETE
			$this->titulo( menu::get_titulo() );
			$this->mostrar_menu();
		}
		cronometro::instancia()->marcar('Fin proceso.');
		$this->mostrar_resumen();
	}

	function invocar_comando($nombre_comando, $argumentos)
	{
		if ( true ) {																//Atencion! falta control de que el comando exista
			$clase_comando = 'comando_' . $nombre_comando;
			require_once( $this->ubicacion_comandos .'/'.$clase_comando.'.php');
			$comando = new $clase_comando( $this );
			$comando->set_argumentos( $argumentos );			
			$comando->procesar();
		} else {
			throw new excepcion_toba("ERROR: El COMANDO '$nombre_comando' no existe.");
		}
	}
	
	function get_info_comandos()
	{
		$info = array();
		$comandos = menu::get_comandos();
		foreach( $comandos as $comando )
		{
			$clase_comando = 'comando_' . $comando;
			require_once( $this->ubicacion_comandos .'/'.$clase_comando.'.php');
			$info[$clase_comando] = call_user_func( array( $clase_comando, 'get_info') );
		}
		return $info;
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
	//----------------------------------------------
	// Interface grafica
	//----------------------------------------------

	function mostrar_menu()
	{
		// Armo la coleccion de comandos
		$comandos = array();
		foreach ( $this->get_info_comandos() as $comando => $info ) {
			$temp = explode('_', $comando);
			$comandos[ $temp[1] ] = $info;
		}
		// Muestro la lista
		$this->subtitulo("Comandos disponibles");
		$this->coleccion( $comandos );
	}

	function mostrar_resumen()
	{
		echo "\n";
		$this->linea_completa( null, '_');
		$c = cronometro::instancia();
		$tiempo = number_format($c->tiempo_acumulado(),3,",",".");
		$this->mensaje("TIEMPO: $tiempo segundos");
		//print_r( $c->get_marcas() );
	}

	//----------------------------------------------
	// Primitivas de display
	//----------------------------------------------

	function separador( $texto='' )
	{
		if($texto!='') $texto = "--  $texto  ";
		echo "\n";
		$this->linea_completa( $texto, '-');
		echo "\n";
	}

	function titulo( $texto )
	{
		echo "\n";
		$this->linea_completa( null, '*' );
		$this->linea_completa( "***  $texto  ", '*' );
		$this->linea_completa( null, '*' );
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
	* Genera la salida de una linea completando el espacio faltante del display con un caracter
	*/
	private function linea_completa( $base='', $caracter_relleno )
	{
		echo str_pad( self::display_prefijo_linea . $base, self::display_ancho, $caracter_relleno );
		echo "\n";
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

	//----------------------------------------------
}
?>