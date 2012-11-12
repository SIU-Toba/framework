<?php
	
class toba_logger_ws extends toba_logger
{	
	private $nombre_archivo;
	private $hubo_encabezado = false;
	
	static protected $instancia;
		
	/**
	 * Este es un singleton por proyecto
	 * @return logger
	 */
	static function instancia($proyecto=null)
	{
		if (!isset(self::$instancia[$proyecto])) {
			self::$instancia[$proyecto] = new toba_logger_ws($proyecto);
		}
		return self::$instancia[$proyecto];	
	}	

	function directorio_logs()
	{
		if (! isset($this->dir_logs)) {
			$id_instancia = toba_instancia::get_id();
			$this->dir_logs = toba_nucleo::toba_instalacion_dir()."/i__$id_instancia/p__{$this->proyecto_actual}/logs/web_services";
		}
		return $this->dir_logs;
	}	
	
	/**
	 * Guarda los sucesos actuales en el sist. de archivos
	 */
	function guardar()
	{		
		if ($this->activo) {
			$this->guardar_en_archivo($this->get_nombre_archivo());
		}
	}
	
	/**
	 *  Permite disparar un guardado parcial de la informacion
	 */
	function set_checkpoint()
	{
		$this->guardar();
	}
	
	protected function armar_encabezado()
	{
		$salto = "\r\n";
		$texto = self::separador.$salto;
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
		$this->hubo_encabezado = true;
		
		return $texto;
	}
	
	function guardar_en_archivo($archivo, $forzar_salida = false)
	{
		$salto = "\r\n";
		$texto = '';
		if (! $this->hubo_encabezado) {
			$texto .= $this->armar_encabezado();
			$texto .= self::fin_encabezado.$salto;		
			$this->hubo_encabezado = true;
		}
		$mensajes = $this->armar_mensajes();
		$hay_salida = (trim($mensajes) != '');
		if ($hay_salida || $forzar_salida) {
			$texto .= $mensajes;
			$this->guardar_archivo_log($texto, $archivo);
		}
	}	
	
	protected function guardar_archivo_log($texto, $archivo)
	{
		$permisos = 0774;
		//--- Asegura que el path esta creado
		$path = $this->directorio_logs();
		$path_completo = $path ."/".$archivo;
		toba_manejador_archivos::crear_arbol_directorios($path, $permisos);

		//Grabo el archivo
		$es_nuevo = (!file_exists($path_completo));
		$this->anexar_a_archivo($texto, $path_completo);
		
		//Reseteo las variables internas para no escribir lo mismo varias veces
		$this->proyectos = array(); 
		$this->mensajes = array();
		$this->niveles = array();
		$this->proximo = 0;
		
		if ($es_nuevo) {
			//Cambiar permisos
			@toba_manejador_archivos::chmod_recursivo($path, $permisos);
		}
	}	
	
	protected function get_nombre_archivo()
	{
		if (! isset($this->nombre_archivo)) {
			$id_sol = toba::solicitud()->get_id();
			$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"]: 'sin_ip';
			$this->nombre_archivo = 'web_services_'. $ip. "_$id_sol.log";
		}		
		return $this->nombre_archivo;		
	}
	
}
?>
