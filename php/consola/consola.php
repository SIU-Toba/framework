<?php
require_once("consola/lib/Table.php");
require_once("consola/lib/Getopt.php");
require_once("consola/lib/formulario_consola.php");
require_once('modelo/lib/toba_proceso_gui.php');
require_once('nucleo/toba_nucleo.php');

/**
*	@todo: 	- Interprete de comandos
*			- Nombres abreviados de comandos			
*/
class consola implements toba_proceso_gui
{
	static protected $display_ancho = 79;
	const display_coleccion_espacio_nombre = 25;
	const display_prefijo_linea = ' ';
	protected 	$ubicacion_comandos;
	protected	$menu;
	protected $verbose = true;
	
	function __construct( $ubicacion_comandos, $clase_menu )
	{
		toba_nucleo::instancia();
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			self::$display_ancho = 79;
		} else {
			self::$display_ancho = 1000;
		}
		ini_set("error_reporting", E_ALL);
		if( ! is_dir( $ubicacion_comandos ) ) {
			throw new toba_error("CONSOLA: El directorio de comandos '$ubicacion_comandos' es invalido");
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
			} catch (toba_error $e ) {
				$this->mensaje( $e->get_mensaje() );	
				toba_logger::instancia()->error($e);
			}
		} else {
			//Aca se tendria que abrir el INTERPRETE
			$this->menu->mostrar_ayuda_raiz();
		}
		toba_cronometro::instancia()->marcar('Fin proceso.');
		if ($this->verbose) {
			$this->menu->mostrar_resumen();
		}
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
			throw new toba_error("ERROR: El COMANDO '$nombre_comando' no existe.");
		}
	}
	
	function set_verbose($verbose)
	{
		$this->verbose = $verbose;
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

	/**
	 * Wrap para cambiar el encoding del texto
	 */
	protected function imprimir($texto)
	{
		if (toba_manejador_archivos::es_windows() && function_exists('iconv')) {
			echo iconv('latin1', 'IBM850', $texto);
		} else {
			echo $texto;
		}
	}
	
	function separador( $texto='', $caracter='-' )
	{
		if($texto!='') $texto = "--  $texto  ";
		$this->imprimir("\n");
		$this->linea_completa( $texto, $caracter );
		$this->imprimir("\n");
	}

	function titulo( $texto )
	{
		$this->imprimir("\n");
		$this->linea_completa( null, '-' );
		$this->linea_completa( " $texto  ", ' ' );
		$this->linea_completa( null, '-' );
		$this->imprimir("\n");
	}
	
	function subtitulo( $texto )
	{
		$this->imprimir(self::display_prefijo_linea . $texto . "\n");
		$this->imprimir(self::display_prefijo_linea . str_repeat('-', strlen( $texto ) ) );
		$this->imprimir("\n\n");
	}

	function mensaje( $texto, $bajar_linea=true )
	{
		$lineas = toba_texto::separar_texto_lineas( $texto, self::$display_ancho );
		for ($i=0; $i< count($lineas); $i++) {
			if ($bajar_linea || $i < count($lineas) - 1) {
				$extra = "\n";
			} else {
				$extra = '';
			}
			$this->imprimir(self::display_prefijo_linea . $lineas[$i] . $extra);
		}
	}
	
	function progreso_avanzar()
	{
		$this->imprimir('.');	
	}
	
	function progreso_fin()
	{
		$this->imprimir("OK\n");	
	}	
	
	function enter()
	{
		$this->imprimir("\n");	
	}

	/*
	* Imprime un error en la error estandar
	*/
	function error( $texto )
	{
		toba_logger::instancia()->error($texto);
		$lineas = toba_texto::separar_texto_lineas( $texto, self::$display_ancho );
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
		$espacio_descripcion = self::$display_ancho - self::display_coleccion_espacio_nombre 
								- strlen( self::display_prefijo_linea );
		foreach( $coleccion as $nombre => $descripcion ) {
			$lineas = toba_texto::separar_texto_lineas( $descripcion, $espacio_descripcion );
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
		if (self::$display_ancho > 100) {
			$ancho = 100;
		} else {
			$ancho = self::$display_ancho;
		}
		$this->imprimir(str_pad( self::display_prefijo_linea . $base, $ancho, $caracter_relleno ));
		$this->imprimir("\n");
	}

	function lista( $lista, $titulo )
	{
		if( count( $lista ) > 0 ) {
			$i=0;
			foreach($lista as $l) {
				$datos[$i][0] = $l;
				$i++;
			}
			$this->imprimir(Console_Table::fromArray( array( $titulo ), $datos ));	
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
			$this->imprimir(Console_Table::fromArray( $titulo, $datos ));	
		}
	}

	function tabla( $tabla, $titulos )
	{
		if ( count( $tabla ) > 0 ) {
			$this->imprimir(Console_Table::fromArray( $titulos, $tabla ));
		} else {
			self::mensaje('...No hay DATOS!');	
		}
	}
	
	//------------------------------------------------------------------------
	// Interaccion con el usuario
	//------------------------------------------------------------------------

	function dialogo_simple( $texto, $defecto = null, $extra=null)
	{
		$this->imprimir("$texto (Si o No)\n");
		if (isset($extra)) {
			$this->imprimir($extra);		
		}		
		do {
			$this->imprimir("(s/n):");
		
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
			$this->imprimir("$categoria: ");
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
				$this->imprimir("$txt (ENTER selecciona ".$defecto_texto."): ");
			} else {
				$this->imprimir("($txt): ");
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