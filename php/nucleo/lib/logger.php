<?
define('TOBA_LOG_EMERG',    0);     /** System is unusable */
define('TOBA_LOG_ALERT',    1);     /** Immediate action required */
define('TOBA_LOG_CRIT',     2);     /** Critical conditions */
define('TOBA_LOG_ERROR',    3);     /** Error conditions */
define('TOBA_LOG_WARNING',  4);     /** Warning conditions */
define('TOBA_LOG_NOTICE',   5);     /** Normal but significant */
define('TOBA_LOG_INFO',     6);     /** Informational */
define('TOBA_LOG_DEBUG',    7);     /** Debug-level messages */
/*
	Esto esta basado en la clase de LOG de PEAR
	Ver tema de mascaras y niveles

	ATENCION: 	esta clase compite con los metodos de registro de la solicitud
				y con el monitor... hay que pasar lo montado en esos elementos
				sobre este.
*/
class logger
{
	private $solicitud;
	private $mensajes;
	private $niveles;
	private $proximo = 0;
	private $datos_registrados = false;
	
	function __construct($solicitud)
	{
		$this->solicitud = $solicitud;
	}	

	function registrar_mensaje($mensaje, $nivel)
	{
		$this->mensajes[$proximo] = $this->extraer_mensaje($mensaje);
		$this->niveles[$proximo] = $nivel;
		$proximo++;
	}

	function extraer_mensaje($mensaje)
	/*
		Adecuar el mecanismo para meter excepciones
	*/
	{
        if (is_object($mensaje)) {
            if (method_exists($mensaje, 'getMessage')) {
                $mensaje = $mensaje->getMessage();
            } else if (method_exists($mensaje, 'tostring')) {
                $mensaje = $mensaje->toString();
            } else if (method_exists($mensaje, '__tostring')) {
                $mensaje = (string)$mensaje;
            } else {
                $mensaje = print_r($mensaje, true);
            }
        } else if (is_array($mensaje)) {
            $mensaje = print_r($mensaje, true);
        }
		return $mensaje;
	}

	/*------   Esto hay que sacarlo   ----------*/
	
	function registrar_excepcion($excepcion)
	{
		ei_arbol($excepcion->obtener_resumen(),"Excepcion");
	}

	function verificar_datos_registrados()
	{
		return $this->datos_registrados;	
	}

	//------------------------------------------------------------------
	//------ Entradas para los distintos tipos de error
	//------------------------------------------------------------------

    function emerg($mensaje)
    {
        return $this->registrar_mensaje($mensaje, TOBA_LOG_EMERG);
    }

    function alert($mensaje)
    {
        return $this->registrar_mensaje($mensaje, TOBA_LOG_ALERT);
    }
    
    function crit($mensaje)
    {
        return $this->registrar_mensaje($mensaje, TOBA_LOG_CRIT);
    }
    
    function error($mensaje)
    {
        return $this->registrar_mensaje($mensaje, TOBA_LOG_ERROR);
    }

    function warning($mensaje)
    {
        return $this->registrar_mensaje($mensaje, TOBA_LOG_WARNING);
    }

    function notice($mensaje)
    {
        return $this->registrar_mensaje($mensaje, TOBA_LOG_NOTICE);
    }

    function info($mensaje)
    {
        return $this->registrar_mensaje($mensaje, TOBA_LOG_INFO);
    }

    function debug($mensaje)
    {
        return $this->registrar_mensaje($mensaje, TOBA_LOG_DEBUG);
    }

	//------------------------------------------------------------------
	//---- Manejo de MASCARAS
	//------------------------------------------------------------------

    function mascara($nivel)
    {
        return (1 << $nivel);
    }

    function mascara_hasta($nivel)
    {
        return ((1 << ($nivel + 1)) - 1);
    }

	//------------------------------------------------------------------
	//---- Metodos de registro
	//------------------------------------------------------------------

	function guardar()
	{
		$this->datos_registrados = true;
		if(apex_pa_log_archivo){
			$this->guardar_archivo();
		}
		if(apex_pa_log_db){
			$this->guardar_db();
		}
	}
	//------------------------------------------------------------------
	
	function guardar_archivo()
	//Guardar LOG en archivo
	{
		$archivo = $this->solicitud->hilo->obtener_proyecto_path() . "/log_sistema.txt";
		//Abro el archivo
		$a = fopen($archivo,"a");
		fwrite($a, "--------- INICIO ---------\n");
		$mascara_ok = $this->mascara_hasta( apex_pa_log_archivo_nivel );
		for($a=0; $a<count($this->mensajes); $a++)
		{
			if( $mascara_ok & $this->mascara( $this->niveles[$a] ) )
			{
				fwrite($a, $this->mensajes[$a]);
			}			
		}
		fclose($a);		
	}

	//------------------------------------------------------------------
	
	function guardar_db()
	//Guardar LOG en archivo
	{
/*
		Esto tiene que pisar una tabla del TOBA
	
		$archivo = $this->solicitud->hilo->obtener_proyecto_path() . "/log_sistema.txt";
		//Abro el archivo
		$a = fopen($archivo,"a");
		fwrite($a, "--------- INICIO ---------\n");
		$mascara_ok = $this->mascara_hasta( apex_pa_log_archivo_nivel );
		for($a=0; $a<count($this->mensajes); $a++)
		{
			if( $mascara_ok & $this->mascara( $this->niveles[$a] ) )
			{
				fwrite($a, $this->mensajes[$a]);
			}			
		}
		fclose($a);		
*/
	}

	//------------------------------------------------------------------
	//---- Salida a pantalla
	//------------------------------------------------------------------

	function mostrar_pantalla()
	{
		$mascara_ok = $this->mascara_hasta( apex_pa_log_pantalla_nivel );
		for($a=0; $a<count($this->mensajes); $a++)
		{
			if( $mascara_ok & $this->mascara( $this->niveles[$a] ) )
			{
				fwrite($a, $this->mensajes[$a]);
			}			
		}
		fclose($a);		
	}
	//------------------------------------------------------------------
}
?>