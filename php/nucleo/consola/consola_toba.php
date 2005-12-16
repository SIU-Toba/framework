<?
/*
	FALTA:
		- Escuchar al usuario o recibir parametros de la invocacion
			son dos cosas que deberian tener el mismo resultado
		- interprete
		- lista de operaciones desdobladas de las clases.

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
	private $ubicacion_procesos = 'modelo/procesos';
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
				$proceso = $argumentos[0];
				array_shift($argumentos);
				$this->invocar_proceso( $proceso, $argumentos );
			} catch (excepcion_toba $e ) {
				echo $e->getMessage();	
				$this->mostrar_menu();
			}
		} else {
			//Aca se tendria que abrir el INTERPRETE
			$this->mostrar_menu();
		}
		cronometro::instancia()->marcar('Fin proceso.');
		$this->mostrar_resumen();
	}

	function invocar_proceso($id_proceso, $argumentos)
	{
		if (in_array($id_proceso, $this->get_procesos_disponibles() )) {
			require_once( $this->ubicacion_procesos .'/'.$id_proceso.'.php');
			$proceso = new $id_proceso( $this->dir_raiz, $this->instancia, $this->proyecto );
			$proceso->set_interface_usuario( $this );
			try{
				$proceso->procesar( $argumentos );		
			} catch (excepcion_toba $e) {
				echo "Error ejecutando el proceso.\n" . $e->getMessage();	
			}
		} else {
			throw new excepcion_toba("ERROR: El PROCESO '$id_proceso' no existe\n");
		}
	}

	function get_procesos_disponibles()
	{
		if ($dir = opendir($this->dir_procesos)) {	
		   while (false	!==	($archivo = readdir($dir)))	{ 
				if(preg_match('%.*\.php%',$archivo)){
					$temp = explode('.',$archivo);
					$procesos[] = $temp[0];
				}
		   } 
		   closedir($dir); 
		}
		return $procesos;	
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
	
	function mostrar_menu()
	{
		echo "TOBA: Comandos disponibles\n";
		print_r( $this->get_procesos_disponibles() );
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
}
?>