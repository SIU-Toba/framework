<?php
require_once('manejador_archivos.php');
require_once('nucleo/browser/hilo.php');

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
	const separador = "-o-o-o-o-o-";
	const fin_encabezado = "==========";
	static private $instancia;
	private $ref_niveles;
	private $proyecto_actual;
	
	//--- Arreglos que contienen info de los logs en runtime
	private $mensajes = array();
	private $niveles = array();
	private $proyectos = array();
	
	private $proximo = 0;
	private $nivel_maximo = 0;
	private $datos_registrados = false;
	private $activo = true;
	
	private $dir_logs;
	
	//--- Variables que son necesarias para cuando el logger se muestra antes de terminar la pág.
	private $mostrado = false;				//Ya fue guardado en este pedido de página
	private $cant_mostrada;					//Cant. de logs que había cuando se mostro

	/**
	 * @todo Para sacar el path de la instalacion y la instancia se necesitaria tener acceso a la clase instalacion
	 * pero no se carga en el runtime, solo en la parte administrativa, por ahora se replica el lugar
	 * donde se encuentra el dir de instalacion
	 */
	private function __construct($proyecto = null)
	{
		$this->proyecto_actual = (isset($proyecto)) ? $proyecto : hilo::obtener_proyecto();		
		$this->dir_logs = toba_dir()."/instalacion/i__".apex_pa_instancia."/p__{$this->proyecto_actual}/logs";
		$this->ref_niveles[2] = "CRITICAL";
		$this->ref_niveles[3] = "ERROR";
		$this->ref_niveles[4] = "WARNING";
		$this->ref_niveles[5] = "NOTICE";
		$this->ref_niveles[6] = "INFO";
		$this->ref_niveles[7] = "DEBUG";
		
		//--- Valores por defecto
		if (!defined('apex_pa_log_archivo')) define('apex_pa_log_archivo', true);
		if (!defined('apex_pa_log_db'))	define('apex_pa_log_db', false);
		if (!defined('apex_pa_log_archivo_nivel')) define('apex_pa_log_archivo_nivel', 10);
		if (!defined('apex_pa_log_db_nivel')) define('apex_pa_log_db_nivel', 0);
		if (apex_pa_log_db  && apex_pa_log_db_nivel > $this->nivel_maximo) {
			$this->nivel_maximo = apex_pa_log_db_nivel;
		}
		if (apex_pa_log_archivo && apex_pa_log_archivo_nivel > $this->nivel_maximo) {
			$this->nivel_maximo = apex_pa_log_archivo_nivel;
		}		
		if (!defined('apex_log_archivo_tamanio')) define('apex_log_archivo_tamanio', 1024);
		if (!defined('apex_log_archivo_backup_cant')) define('apex_log_archivo_backup_cant', 10);
		if (!defined('apex_log_archivo_backup_compr')) define('apex_log_archivo_backup_compr', false);		
	}		
	
	/**
	* @deprecated Desde 0.9.1
	*/
	function ocultar()
	{
	}
	
	/**
	 * Desactiva el logger en el pedido de página actual
	 */
	function desactivar()
	{
		$this->nivel_maximo = 0;
		$this->activo = false;
	}
	
	function verificar_datos_registrados()
	//Informa si se guardo la informacion
	{
		return $this->datos_registrados;	
	}
	
	/**
	 * Este es un singleton por proyecto
	 */
	static function instancia($proyecto=null)
	{
		if (!isset(self::$instancia[$proyecto])) {
			self::$instancia[$proyecto] = new logger($proyecto);
		}
		return self::$instancia[$proyecto];	
	}

	
	protected function registrar_mensaje($mensaje, $proyecto, $nivel)
	{
		if ($nivel <= $this->nivel_maximo) {
			$this->mensajes[$this->proximo] = $this->extraer_mensaje($mensaje, false);
			$this->niveles[$this->proximo] = $nivel;
			if (!isset($proyecto)) {
				//Se hace estatica para poder loguear antes de construido el hilo
				$proyecto = $this->proyecto_actual;
			}
			$this->proyectos[$this->proximo] = $proyecto;
			$this->proximo++;
		}
	}
	
	protected function extraer_mensaje($mensaje, $web)
	/*
		Adecuar el mecanismo para meter excepciones
	*/
	{
        if (is_object($mensaje)) {
            if ($web && method_exists($mensaje, 'mensaje_web')) {
				//Excepciones!
                $mensaje = $mensaje->mensaje_web();
			} else if (!$web && method_exists($mensaje, 'mensaje_txt')) {
				//Excepciones!
                $mensaje = $mensaje->mensaje_txt();
            } else if (method_exists($mensaje, 'getMessage')) {
                $mensaje = $mensaje->getMessage();
            } else if (method_exists($mensaje, 'tostring')) {
                $mensaje = $mensaje->toString();
            } else if (method_exists($mensaje, '__tostring')) {
                $mensaje = (string)$mensaje;
            } else {
                $mensaje = var_export($mensaje, true);
            }
        } else if (is_array($mensaje)) {
            $mensaje = var_export($mensaje, true);
        }
		return $mensaje;
	}
	
	protected function mensajes()
	{
		return $this->mensajes;
	}
	
	protected function mensajes_web()
	{
		return $this->mensajes_web;
	}
	
	function get_cantidad_mensajes()
	{
		return count($this->mensajes);	
	}
	
	function get_niveles()
	{
		return $this->ref_niveles;	
	}

	//------------------------------------------------------------------
	//------ Entradas para los distintos tipos de error
	//------------------------------------------------------------------

	function trace()
	{
		$this->$nivel( debug_backtrace() );
	}

    function crit($mensaje, $proyecto=null)
    {
        return $this->registrar_mensaje($mensaje, $proyecto, TOBA_LOG_CRIT);
    }
    
    function error($mensaje, $proyecto=null)
    {
        return $this->registrar_mensaje($mensaje, $proyecto, TOBA_LOG_ERROR);
    }

    function warning($mensaje, $proyecto=null)
    {
        return $this->registrar_mensaje($mensaje, $proyecto, TOBA_LOG_WARNING);
    }

    /**
    *	Indica la llamada a un metodo/funcion obsoleto, es un alias de notice
    *	@param string $version  Versión desde la cual el metodo/funcion deja de estar disponible
    */
    function obsoleto($clase, $metodo, $version, $extra=null) 
    {
    	if (TOBA_LOG_NOTICE <= $this->nivel_maximo) {
	    	//Se saca el archivo que llamo el metodo obsoleto
	    	$traza = debug_backtrace();
	    	$archivo = $traza[2]['file'];
			$linea = $traza[2]['line']; 	
	    	if ($clase != '') {
	    		$unidad = "Método '$clase::$metodo'";
	    	} elseif ($metodo != '') {
				$unidad = "Función '$metodo'";
	    	} else {
	    		$unidad = '';	
	    	}
	    	$msg = "OBSOLETO: $unidad desde versión $version. $extra\nArchivo $archivo, linea $linea.";
	    	$this->notice($msg);
    	}
    }
    
    function notice($mensaje, $proyecto=null)
    {
        return $this->registrar_mensaje($mensaje, $proyecto, TOBA_LOG_NOTICE);
    }

    function info($mensaje, $proyecto=null)
    {
        return $this->registrar_mensaje($mensaje, $proyecto, TOBA_LOG_INFO);
    }

    function debug($mensaje, $proyecto=null)
    {
        return $this->registrar_mensaje($mensaje, $proyecto, TOBA_LOG_DEBUG);
    }

	//------------------------------------------------------------------
	//---- Manejo de MASCARAS
	//------------------------------------------------------------------

    protected function mascara($nivel)
    {
        return (1 << $nivel);
    }

    protected function mascara_hasta($nivel)
    {
        return ((1 << ($nivel + 1)) - 1);
    }

	//------------------------------------------------------------------
	//---- Manejo de las fuentes de log
	//------------------------------------------------------------------


	function guardar()
	{
		if (!$this->activo) return;
		if(apex_pa_log_archivo){
			$this->guardar_en_archivo("sistema.log");
		}
		if(apex_pa_log_db){
			$this->guardar_db();
		}
		$this->datos_registrados = true;		
	}
	
	function directorio_logs()
	{
		return $this->dir_logs;
	}	
	
	function guardar_en_archivo($archivo)
	{
		$hay_salida = false;
		$mascara_ok = $this->mascara_hasta( apex_pa_log_archivo_nivel );
		$salto = "\r\n";
		$texto = self::separador.$salto;
		$texto .= "Fecha: ".date("d-m-Y H:i:s").$salto;
		$texto .= "Operacion: ".toba::get_solicitud()->get_datos_item('item_nombre').$salto;
		$texto .= "Usuario: ".toba::get_hilo()->obtener_usuario().$salto;
		$texto .= "Version-PHP: ". phpversion().$salto;
		if (isset($_SERVER['SERVER_NAME'])) {
			$texto .= "Servidor: ".$_SERVER['SERVER_NAME'].$salto;
		}
		if (isset($_SERVER['REQUEST_URI'])) {
			$texto .= "URI: ".$_SERVER['REQUEST_URI'].$salto;	
		}		
		if (isset($_SERVER["HTTP_REFERER"])) {
			$texto .= "Referrer: ".$_SERVER["HTTP_REFERER"].$salto;
		}
		if (isset($_SERVER["REMOTE_ADDR"])) {
			$texto .= "Host: ".$_SERVER["REMOTE_ADDR"].$salto;			
		}
		$texto .= self::fin_encabezado.$salto;
		for($a=0; $a<count($this->mensajes); $a++) {
			if( $mascara_ok & $this->mascara( $this->niveles[$a] ) ) {
				$hay_salida = true;
				$texto .= "[" . $this->ref_niveles[$this->niveles[$a]] . 
						"] " . $this->mensajes[$a] . "\r\n";
			}			
		}
		if ($hay_salida) {
			$this->guardar_archivo_log($texto, $archivo);
		}
	}
	
	protected function guardar_archivo_log($texto, $archivo)
	{
		$permisos = 0700;
		//--- Asegura que el path esta creado
		$path = $this->directorio_logs();
		$path_completo = $path ."/".$archivo;
		manejador_archivos::crear_arbol_directorios($path, $permisos);

		$es_nuevo = false;
		if (!file_exists($path_completo)) {
			//Caso base el archivo no existe
			$this->anexar_a_archivo($texto, $path_completo);
			$es_nuevo = true;
		} else {
			//El archivo existe, ¿Hay que ciclarlo?
			$excede_tamanio = (filesize($path_completo) > apex_log_archivo_tamanio * 1024);
			if (apex_log_archivo_tamanio != null && $excede_tamanio) {
				$this->ciclar_archivos_logs($path, $archivo);
				$es_nuevo = true;
			}
			$this->anexar_a_archivo($texto, $path_completo);
		}
		
		if ($es_nuevo) {
			//Cambiar permisos
			chmod($path_completo, $permisos);			
		}
	}
	
	protected function anexar_a_archivo($texto, $archivo)
	{
		$handle = fopen($archivo, "a");
		fwrite($handle, "$texto\r\n");
		fclose($handle);		
	}
	
	protected function ciclar_archivos_logs($path, $archivo)
	{
		$arreglo = array();
		if (apex_log_archivo_backup_cant == 0) {
			//Si es un unico archivo hay que borrarlo
			unlink($path."/".$archivo);
			return;
		}
		//Encuentra los archivos
		$patron = "/$archivo\.([0-9]+)/";
		$archivos = manejador_archivos::get_archivos_directorio($path);
		sort($archivos);

		//¿Cual es el numero de cada uno?
		$ultimo = 0;
		$arch_ordenados = array();
		foreach ($archivos as $arch_actual) {
			$version = array();
			preg_match($patron, $arch_actual, $version);
			if (! empty($version) && count($version) > 1) {
				$pos = $version[1];
				$arch_ordenados[$pos] = $arch_actual;
				if ($pos > $ultimo) {
					$ultimo = $pos;
				}
			}
		}
		//Se determina el siguiente numero
		$sig = $ultimo + 1;
		
		//¿Hay que purgar algunos?
		$puede_purgar = (apex_log_archivo_backup_cant != -1);
		$debe_purgar = count($arch_ordenados) >= (apex_log_archivo_backup_cant -1);
		if ($puede_purgar && $debe_purgar) {
			ksort($arch_ordenados);
			reset($arch_ordenados);
			//Se dejan solo N-1 archivos			
			$a_purgar = count($arch_ordenados) - (apex_log_archivo_backup_cant - 1);
			while ($a_purgar > 0) {
				unlink(current($arch_ordenados));
				$a_purgar--;
				next($arch_ordenados);
			}
		}
	
		//Se procede a mover el archivo actual
		$path_completo = $path . "/" . $archivo;
		if (apex_log_archivo_backup_compr) {
			//Se comprime
			$nuevo = $path_completo . ".$sig.gz";
			manejador_archivos::comprimir_archivo($path_completo, 5, $nuevo);
			unlink($path_completo);
		} else {
			$nuevo = $path_completo . ".$sig";
			rename($path_completo, $nuevo);
		}
	}
	
	function borrar_archivos_logs()
	{
		$patron = "/sistema.log/";
		$archivos = manejador_archivos::get_archivos_directorio($this->directorio_logs(), $patron);
		foreach ($archivos as $archivo) {
			unlink($archivo);			
		}
	}
	
	//------------------------------------------------------------------
	
	function guardar_db()
	//Guardar LOG en archivo
	{
/*
		Tiene que haber un metodo para que el log en la DB se haga con un objeto asociado
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

	function pantalla_browser()
	{
		if (!$this->mostrado || (count($this->mensajes_web) != $this->cant_mostrada)) {
			js::cargar_consumos_globales(array('basico'));
			$hay_salida = false;
			$html = "</script>";	//Por si estaba un tag abierto
			$html .= "<div id='logger_salida' style='display:none'> <table width='90%'><tr><td>";
			$html .= "<pre class='texto-ss'>";
			$mensajes = $this->filtrar_mensajes_web();
			$hay_salida = ($mensajes != '');
			$html .= $mensajes;
			$html .= "</pre></td></tr></table></div>";
			if ($hay_salida) {
				echo "<div style='text-align:left;'>
						<a href='javascript: toggle_nodo(document.getElementById(\"logger_salida\"))'>Log</a>
						$html</div>";
			}
			$this->mostrado = true;
			$this->cant_mostrada = count($this->mensajes_web);
		}
	}

	protected function filtrar_mensajes_web()
	{
		$mascara_ok = $this->mascara_hasta( apex_pa_log_pantalla_nivel );
		$html = '';
		for($a=0; $a<count($this->mensajes_web); $a++){
			if( $mascara_ok & $this->mascara( $this->niveles[$a] ) ){
				$estilo = $this->estilo_grafico($this->niveles[$a]);
				$html .= $estilo. $this->mensajes_web[$a] . "<br>";
			}			
		}
		return $html;
	}

	private function estilo_grafico($nivel)
	{
		$icono = gif_nulo(16,1);
		$estilo = "";
		if ($nivel <= TOBA_LOG_WARNING) {
			$estilo = "font-weight: bold;";
			$icono = recurso::imagen_apl('warning.gif',true);
		} elseif ($nivel <= TOBA_LOG_NOTICE) {
			$estilo = 'font-weight: bold;';			
		} elseif ($nivel <= TOBA_LOG_INFO) {
			$icono = recurso::imagen_apl('info_chico.gif',true);
		} else {
			$estilo = '';
		}
		$salida = "<span style='$estilo'>$icono".$this->ref_niveles[$nivel]." * </span> ";
		return $salida;
	}
	

	//------------------------------------------------------------------
}
?>