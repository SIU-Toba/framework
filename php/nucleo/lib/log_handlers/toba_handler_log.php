<?php

class toba_handler_log
{
	use \toba_basic_logger;	
	
	static protected $instancia;	
	protected $nombre_archivo = 'sistema.log';
			
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
	
	public function set_archivo_destino($nombre)
	{
		$this->nombre_archivo = $nombre;
	}
		
	public function log($nivel, $mensaje, $context = array())
	{
		$this->registrar_mensaje($mensaje, $this->proyecto_actual, $nivel);		
	}
	
	function guardar()
	{		
		$this->guardar_en_archivo($this->nombre_archivo);
	}
			
	function guardar_en_archivo($archivo, $forzar_salida = false)
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
	
	function borrar_archivos_logs()
	{
		$patron = "/sistema.log/";
		$archivos = toba_manejador_archivos::get_archivos_directorio($this->directorio_logs(), $patron);
		foreach ($archivos as $archivo) {
			unlink($archivo);			
		}
	}
}
?>