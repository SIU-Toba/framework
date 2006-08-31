<?
require_once("lib/asercion.php"); 		      	   	//Aserciones
require_once("nucleo/lib/toba_cronometro.php");          //Cronometrar ejecucion
require_once("lib/varios.php");				//Funciones genericas (Manejo de paths, etc.)
require_once("lib/sql.php");					//Libreria de manipulacion del SQL
require_once("nucleo/lib/toba_excepcion.php");		//Excepciones del TOBA
require_once("nucleo/lib/toba_logger.php");				//Logger
require_once("lib/parseo.php");       	   	//Parseo
require_once("lib/texto.php");       	   	//Manipulacion de texto
require_once("modelo/lib/gui.php");
require_once("consola/lib/Table.php");
require_once("consola/lib/Getopt.php");
require_once("consola/lib/formulario_consola.php");

/**
*	@todo: 	- Interprete de comandos
*			- Nombres abreviados de comandos			
*/
class consola implements gui
{
	const display_ancho = 95;
	const display_coleccion_espacio_nombre = 25;
	const display_prefijo_linea = ' ';
	protected 	$ubicacion_comandos;
	protected	$menu;
	
	function __construct( $ubicacion_comandos, $clase_menu )
	{
		ini_set("error_reporting", E_ALL);
		if( ! is_dir( $ubicacion_comandos ) ) {
			throw new toba_excepcion("CONSOLA: El directorio de comandos '$ubicacion_comandos' es invalido");
		}
		$this->ubicacion_comandos = $ubicacion_comandos;
		require_once( $this->ubicacion_comandos ."/$clase_menu.php");
		$this->menu = new $clase_menu( $this );
		toba_cronometro::instancia()->marcar('Consola online');
	}

	function get_ubicacion_comandos()
	{
		return $this->ubicacion_comandos;	
	}

	function run( $argumentos )
	{
		toba_cronometro::instancia()->marcar('Inicio proceso.');
		if ( count($argumentos) > 0 ) {
			try {
				$comando = $argumentos[0];
				array_shift( $argumentos );
				$this->invocar_comando( $comando, $argumentos );
			} catch (toba_excepcion $e ) {
				$this->mensaje( $e->getMessage() );	
			}
		} else {
			//Aca se tendria que abrir el INTERPRETE
			$this->menu->mostrar_ayuda_raiz();
		}
		toba_cronometro::instancia()->marcar('Fin proceso.');
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
			throw new toba_excepcion("ERROR: El COMANDO '$nombre_comando' no existe.");
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
	//-------------------------------------------------------------------------
	// Primitivas de display
	//-------------------------------------------------------------------------

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

	function mensaje( $texto, $bajar_linea=true )
	{
		$lineas = separar_texto_lineas( $texto, self::display_ancho );
		for ($i=0; $i< count($lineas); $i++) {
			if ($bajar_linea || $i < count($lineas) - 1) {
				$extra = "\n";
			} else {
				$extra = '';
			}
			echo self::display_prefijo_linea . $lineas[$i] . $extra;
		}
	}
	
	function mensaje_directo($texto)
	{
		echo $texto;	
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
		toba_logger::instancia()->error($texto);
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
	
	function lista_asociativa( $lista, $titulo = null )
	{
		if( count( $lista ) > 0 ) {
			$i=0;
			foreach($lista as $id => $l) {
				$datos[$i]['id'] = $id;
				$datos[$i]['valor'] = $l;
				$i++;
			}
			if ( ! isset( $titulo ) ) {
				$titulo = array( 'ID', 'Valor' );	
			} else {
				if ( ! is_array( $titulo ) ) $titulo = array( $titulo );
			}
			echo Console_Table::fromArray( $titulo, $datos );	
		}
	}

	function tabla( $tabla, $titulos )
	{
		if ( count( $tabla ) > 0 ) {
			echo Console_Table::fromArray( $titulos, $tabla );
		} else {
			self::mensaje('...No hay DATOS!');	
		}
	}
	
	//------------------------------------------------------------------------
	// Interaccion con el usuario
	//------------------------------------------------------------------------

	function dialogo_simple( $texto, $defecto = null )
	{
		echo "$texto (Si o No)\n";
		do {
			echo "(s/n):";
			$respuesta = trim( fgets( STDIN ) );
			if (isset($defecto) && $respuesta == '') {
				$respuesta = ($defecto) ? 's' : 'n';
			}
			$ok = ($respuesta == 's') || ( $respuesta == 'n');
		} while ( ! $ok );
		if( $respuesta == 's') return true;
		return false;
	}	

	function dialogo_ingresar_texto( $categoria, $obligatorio = true )
	{
		do {
			echo "$categoria: ";
			$respuesta = trim( fgets( STDIN ) );
			$ok = ( ( trim($respuesta != '') && ( $obligatorio ) ) ||  ( ! $obligatorio  ) );
		} while ( ! $ok );
		return $respuesta;
	}

	/**
	*	Muestra una lista de opciones y espera que el usuario seleccione.
	*		Soporta: multiple selecciones, seleccion no obligaroria (--) y seleccion por defecto (vacio).
	*/
	function dialogo_lista_opciones( $opciones, $texto, $multiple_seleccion = false, 
									$titulo = 'VALORES', $obligatorio = true, 
									$defecto = null, $defecto_texto = '' )
	{
		self::subtitulo( $texto );
		self::lista_asociativa( $opciones, array( 'ID', $titulo ) );
		if ( $multiple_seleccion ) {
			self::mensaje('Seleccione valores pertenecientes a la columna "ID"');
			self::mensaje('(Puede seleccionar varios valores separandolos por ",")');
			$txt = 'valores';
		} else {
			self::mensaje('Seleccione un valor perteneciente a la columna "ID"');
			$txt = 'valor';
		}
		if ( ! $obligatorio ) {
			self::mensaje('Si no desea seleccionar nada escriba "--"');
		}
		self::enter();
		$valores_posibles = implode( ',', array_keys( $opciones ) );
		do {
			if (isset($defecto)) {
				echo "$txt (ENTER selecciona ".$defecto_texto."): ";
			} else {
				echo "($txt): ";
			}
			$respuesta = trim( fgets( STDIN ) );
			if (isset($defecto) && $respuesta == '') {
				return $defecto;
			}	
			if ( ! $obligatorio && $respuesta == '--' ) {	// Salida para opcionales
				return ($multiple_seleccion) ? array() : null;				
			}
			if ( $multiple_seleccion ) {
				$ok = true;
				$valores = explode(',', $respuesta);
				$valores = array_map('trim',$valores);
				foreach ( $valores as $valor ) {
					if ( ! isset( $opciones[ $valor ] ) ) {
						self::error("El valor '$valor' es invalido");
						$ok = false;
					}
				}
				if ( $ok ) return $valores;
			} else {	// Seleccion simple
				if ( isset( $opciones[ $respuesta ] ) ) {
					return $respuesta;
				}
			}
		} while ( true );
	}
	
	function get_formulario( $titulo )
	{
		return new formulario_consola( $this, $titulo );
	}
}
?>
