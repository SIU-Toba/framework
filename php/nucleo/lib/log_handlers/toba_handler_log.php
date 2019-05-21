<?php

class toba_handler_log
{
		
	static protected $instancia;	
	protected $nombre_archivo = 'sistema.log';

	public static $limite_mensaje = 100000; //100 KB	
	protected $proximo = 0;
	public static $separador = "-o-o-o-o-o-";
	public static $fin_encabezado = "==========";
	
	//--- Variables que son necesarias para cuando el logger se muestra antes de terminar la pág.
	
	protected function __construct($proyecto)
	{
		$this->proyecto_actual = $proyecto;
	}
	
	/**
	 * Este es un singleton por proyecto
	 * @return logger
	 */
	static function instancia($proyecto=null)
	{
		if (is_null($proyecto)) {
			$proyecto = \toba_basic_logger::get_proyecto_actual();
		}
		if (!isset(self::$instancia[$proyecto])) {
			self::$instancia[$proyecto] = new toba_handler_log($proyecto);
		}
		return self::$instancia[$proyecto];	
	}

	static public function get_proyecto_actual()
	{
		if (class_exists('toba_proyecto')) {
			try {
				return toba_proyecto::get_id();			
			} catch (Exception $e) {
			}
		}
		return 'toba';
	}
	
	public function get_usuario_actual()
	{
		if( php_sapi_name() === 'cli' ) {
			return null;
		} else {
			return toba::usuario()->get_id();
		}
	}
	
	public function directorio_logs()
	{
		if (! isset($this->dir_logs)) {
			$id_instancia = toba_instancia::get_id();
			$this->dir_logs = toba_nucleo::toba_instalacion_dir()."/i__$id_instancia/p__{$this->proyecto_actual}/logs";
		}
		return $this->dir_logs;
	}
	public function set_directorio_logs($dir)
	{
		$this->dir_logs = $dir;	
	}
	
	public function set_archivo_destino($nombre)
	{
		$this->nombre_archivo = $nombre;
	}
		
	public function log($nivel, $mensaje, $context = array())
	{
		$this->registrar_mensaje($mensaje, $this->proyecto_actual, $nivel);		
	}
	
	public function guardar()
	{		
		$this->guardar_en_archivo($this->nombre_archivo);
	}
			
	public function guardar_en_archivo($archivo, $forzar_salida = false)
	{
		$salto = "\r\n";
		
		$texto = $this->armar_encabezado();
		$texto .= self::$fin_encabezado.$salto;		
		$hay_salida = (! empty($this->mensajes));
		if ($hay_salida || $forzar_salida) {
			$texto .= implode('', $this->mensajes);
			$this->guardar_archivo_log($texto, $archivo);
		}
	}
	
	public function borrar_archivos_logs()
	{
		$patron = "/sistema.log/";
		$archivos = toba_manejador_archivos::get_archivos_directorio($this->directorio_logs(), $patron);
		foreach ($archivos as $archivo) {
			unlink($archivo);			
		}
	}
	//-------------------------------------------------------------------------------------------------------------------//
	//				METODOS PROTEGIDOS
	//-------------------------------------------------------------------------------------------------------------------//
	protected function registrar_mensaje($mensaje, $proyecto, $nivel)
	{
	//	$msg = $this->extraer_mensaje($mensaje);
		$msg = $this->truncar_msg($mensaje);
		$this->registrar_msg_cli($msg, $nivel);
		
		$this->mensajes[$this->proximo] = $msg;
		//$this->niveles[$this->proximo] = $nivel;
		$this->proyectos[$this->proximo]  = (isset($proyecto)) ? $proyecto : $this->proyecto_actual;
		$this->proximo++;
	}
	
	protected function guardar_archivo_log($texto, $archivo)
	{		
		$permisos = 0774;		
		$es_nuevo = false;
		$path = $this->directorio_logs();
		toba_manejador_archivos::crear_arbol_directorios(basename($path), $permisos);
		
		$path_completo = $path ."/".$archivo;
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
			@toba_manejador_archivos::chmod_recursivo($path, $permisos);
		}
	}	
	
	protected function armar_encabezado()
	{
		$salto = "\r\n";
		$texto = self::$separador.$salto;
		$texto .= "Fecha: ".date("d-m-Y H:i:s").$salto;
		if (class_exists('toba') && is_object(toba::solicitud())) {
			$texto .= "Operacion: ".toba::solicitud()->get_datos_item('item_nombre').$salto;
		}
		$usuario = self::get_usuario_actual();
		if (isset($usuario)) {
			$texto .= "Usuario: ".$usuario.$salto;
		}
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
		if( php_sapi_name() === 'cli' ) {
			global $argv;
			$texto .= 'Ruta: '.getcwd().$salto;	
			$texto .= 'Argumentos: '.implode(' ', $argv).$salto;
		}
		return $texto;
	}
	
	protected function truncar_msg($msg)
	{
		if (strlen($msg) > self::$limite_mensaje) {
			$msg = substr($msg, 0, self::$limite_mensaje).
					"..TEXTO CORTADO POR EXCEDER EL LIMITE DE ".
					self::$limite_mensaje.
					" bytes";
		}
		return $msg;
	}
		
	protected function registrar_msg_cli($msg, $nivel)
	{
		if (PHP_SAPI != 'cli' && apex_log_error_log && $nivel <= apex_log_error_log_nivel) {
			$error_log_max = ini_get("log_errors_max_len");
			if (! isset($error_log_max) || !is_numeric($error_log_max) || strlen($error_log_max) <= 1) {
				$error_log_max = 1024;
			}
			$error_log_extra = "...SIGUE...";
			$msg_error_log = $msg;
			if (strlen($msg_error_log) > $error_log_max) {
				$msg_error_log = substr($msg_error_log, 0 , $error_log_max - strlen($error_log_extra));
				$msg_error_log .= $error_log_extra;
			}
			error_log($msg_error_log);
		}		
	}
		
	protected function anexar_a_archivo($texto, $archivo)
	{
		$res = file_put_contents($archivo, "$texto\r\n", FILE_APPEND);
		if ($res === FALSE) {
			throw new toba_error("Imposible guardar el archivo de log '$archivo'. Chequee los permisos de escritura del usuario apache sobre esta carpeta/archivo");
		}
	}	
	
	protected function ciclar_archivos_logs($path, $archivo)
	{
		if (apex_log_archivo_backup_cant == 0) {
			//Si es un unico archivo hay que borrarlo
			unlink($path."/".$archivo);
			return;
		}
		//Encuentra los archivos
		$patron = "/$archivo\.([0-9]+)/";
		$archivos = toba_manejador_archivos::get_archivos_directorio($path, $patron);
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
		$this->purgar_archivos_viejos($arch_ordenados);
	
		//Se procede a mover el archivo actual
		$path_completo = $path . "/" . $archivo;
		if (apex_log_archivo_backup_compr) {
			//Se comprime
			$nuevo = $path_completo . ".$sig.gz";
			toba_manejador_archivos::comprimir_archivo($path_completo, 5, $nuevo);
			unlink($path_completo);
		} else {
			$nuevo = $path_completo . ".$sig";
			rename($path_completo, $nuevo);
		}
	}

	protected function purgar_archivos_viejos($lista_archivos)
	{
		//¿Hay que purgar algunos?
		$puede_purgar = (apex_log_archivo_backup_cant != -1);
		$a_purgar = count($lista_archivos) - (apex_log_archivo_backup_cant -1); 			//Se dejan solo N-1 archivos
		if ($puede_purgar && $a_purgar > 0) {
			ksort($lista_archivos);
			reset($lista_archivos);
			 do {
				unlink(current($lista_archivos));
				$a_purgar--;
				next($lista_archivos);
			} while ($a_purgar > 0);
		}		
	}	
}
?>