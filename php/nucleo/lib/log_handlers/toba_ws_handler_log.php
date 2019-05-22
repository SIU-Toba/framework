<?php
	
/**
 * Mantiene una serie de sucesos generados durante un WS no visibles al usuario y los almacena para el posterior analisis
 * Los sucesos tienen una categoria (debug, info, error, etc.) y el proyecto que la produjo
 * 
 * @package Debug
 */	
class toba_ws_handler_log extends toba_handler_log
{	
	
	protected $nombre_archivo = 'web_services.log';
	protected $archivos_individuales = false;
	protected $modo_archivo = false;
	
	static protected $instancia;	
	protected $activo = true;
			
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
			self::$instancia[$proyecto] = new toba_ws_handler_log($proyecto);			
		}
		return self::$instancia[$proyecto];	
	}	
		
	public function __construct($proyecto)
	{
		$this->proyecto_actual = (isset($proyecto)) ? $proyecto : $this->get_proyecto_actual();
	}
	
	/**
	 * Guarda los sucesos actuales en el sist. de archivos
	 */
	public function guardar()
	{		
		if ($this->activo && $this->archivos_individuales) {
			$this->guardar_en_archivo($this->get_nombre_archivo());
		}
	}
	
	/**
	 *  Permite disparar un guardado parcial de la informacion
	 */
	public function set_checkpoint()
	{
		$this->guardar();
	}
	
	/**
	 * Le dice al log que guarde un archivo por cada solicitud e ip 
	 * @param boolean $activo
	 */
	public function loguear_pedidos_separados($activo)
	{
		$this->archivos_individuales = $activo;
	}
		
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	//							METODOS AUXILIARES
	//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------//		
	protected function instanciar_handler()
	{
		$permisos = 0774;
		$dir_log = $this->directorio_logs();
		toba_manejador_archivos::crear_arbol_directorios(basename($dir_log), $permisos);
		
		$path_completo = realpath($dir_log) . '/' . $this->nombre_archivo;		
		$stream_source = ($this->modo_archivo) ? 'file://' . $path_completo : 'php://stderr';
		
		if (file_exists($path_completo)) {
			$excede_tamanio = (filesize($path_completo) > apex_log_archivo_tamanio * 1024);
			if (apex_log_archivo_tamanio != null && $excede_tamanio) {
				$this->ciclar_archivos_logs($dir_log, $this->nombre_archivo);
			}
			$this->stream_handler = fopen($stream_source, 'a');
		} elseif ($this->modo_archivo) {
			$this->stream_handler = fopen($stream_source, 'x');
		} else {
			$this->stream_handler = fopen($stream_source, 'a');
		}
	}
	
	public function log($nivel, $mensaje, $context = array())
	{
		if (! isset($this->stream_handler)) {
			$this->instanciar_handler();
		}
		fwrite($this->stream_handler, $mensaje);
	}
		
	//------------------------------------------------------------------------------------------------------------------------------//
	//			METODOS PARA LOGUEO EN ARCHIVOS INDIVIDUALES
	//------------------------------------------------------------------------------------------------------------------------------//
	
	protected function get_nombre_archivo()
	{
		if (! isset($this->nombre_archivo)) {
			$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"]: 'sin_ip';
			$this->nombre_archivo = '/web_services/web_services_'. $ip. ".log";			//Agrego el dir final aca para mantener el viejo esquema
		}		
		return $this->nombre_archivo;		
	}
}
?>
