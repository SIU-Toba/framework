<?
/*
	FALTA:
		- Escuchar al usuario o recibir parametros de la invocacion
			son dos cosas que deberian tener el mismo resultado
		- interprete
*/
require_once("nucleo/lib/error.php");	    		//Error Handling
require_once("nucleo/lib/cronometro.php");          //Cronometrar ejecucion
require_once("nucleo/lib/db.php");		    		//Manejo de bases (utiliza abodb340)
require_once("nucleo/lib/varios.php");				//Funciones genericas (Manejo de paths, etc.)
require_once("nucleo/lib/sql.php");					//Libreria de manipulacion del SQL
require_once("nucleo/lib/excepcion_toba.php");		//Excepciones del TOBA
require_once("nucleo/lib/logger.php");				//Logger
require_once("nucleo/lib/asercion.php");       	   	//Aserciones

define('apex_pa_instancia','desarrollo');

class consola_toba
{
	private $instancia = 'desarrollo';
	private $proyecto = 'toba';
	private $ubicacion_procesos = 'consola/comandos';
	private $dir_raiz;
	private $dir_procesos;
	
	function __construct( $dir_raiz )
	{
		//Apuntar el include PATH
		$this->dir_raiz = $dir_raiz;
		$this->dir_procesos = $this->dir_raiz . '/php/' . $this->ubicacion_procesos;
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
				echo "\n ". $e->getMessage() ."\n\n";	
				$this->mostrar_menu();
			}
		} else {
			//Aca se tendria que abrir el INTERPRETE
			echo "\n SIU-TOBA ( Ambiente de desarrollo WEB )\n\n";
			$this->mostrar_menu();
		}
		cronometro::instancia()->marcar('Fin proceso.');
		$this->mostrar_resumen();
	}

	function invocar_comando($nombre_comando, $argumentos)
	{
		$clase_comando = 'comando_' . $nombre_comando;
		if ( in_array( $clase_comando, $this->get_comandos_disponibles() ) ) {
			require_once( $this->ubicacion_procesos .'/'.$clase_comando.'.php');
			$comando = new $clase_comando( $this );
			$comando->set_argumentos( $argumentos );			
			$comando->procesar();
		} else {
			throw new excepcion_toba("ERROR: El COMANDO '$nombre_comando' no existe.");
		}
	}

	function get_comandos_disponibles()
	{
		if ($dir = opendir($this->dir_procesos)) {	
		   while (false	!==	($archivo = readdir($dir)))	{ 
				if(preg_match('%comando_.*\.php%',$archivo)){
					$temp = explode('.',$archivo);
					$procesos[] = $temp[0];
				}
		   } 
		   closedir($dir); 
		}
		return $procesos;	
	}
	
	function get_info_comandos()
	{
		$info = array();
		$comandos = $this->get_comandos_disponibles();
		foreach( $comandos as $comando )
		{
			require_once( 'comandos/' . $comando . '.php' );
			$info[$comando] = call_user_func( array( $comando, 'get_info') );
		}
		return $info;
	}
	
	function get_dir_raiz()
	{
		return $this->dir_raiz;	
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

	function separador($texto)
	{
		if($texto!="") $texto = "   $texto   ";
		echo "\n\n===$texto============================================================\n\n";
	}
	
	function titulo( $texto )
	{
		echo "=====================================================\n";
		echo "==  $texto\n";
		echo "=====================================================\n";
	}

	function mensaje( $texto )
	{
		echo $texto . "\n";
	}

	function alerta($texto)
	{
		echo "*** ATENCION ***  $texto \n";
	}
	
	function mostrar_menu()
	{
		$maximo_largo_nombre = 30;
		echo " Comandos disponibles\n";
		echo " --------------------\n\n";
		foreach ( $this->get_info_comandos() as $comando => $info ) {
			$temp = explode('_', $comando);
			$nombre = $temp[1];
			echo ' ';
			echo str_pad( $nombre, $maximo_largo_nombre, ' ' );
			echo $info;
			echo "\n";
		}
	}

	function mostrar_resumen()
	{
		echo "\n____________________________________\n";
		$c = cronometro::instancia();
		$tiempo = number_format($c->tiempo_acumulado(),3,",",".");
		echo "TIEMPO: $tiempo segundos\n";
		//echo "=================================\n";
		//print_r( $c->get_marcas() );
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