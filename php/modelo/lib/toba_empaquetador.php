<?php
class toba_empaquetador
{
	//protected $ini_file; 
	const ARCHIVO_CONFIG = 'proyecto.ini';
	const FRAMEWORK_VENDOR_DIR = 'siu-toba/framework';
	
	private $_dir_proyecto;
	private $_dir_destino;
	private $_dir_instalador;
	private $_paquete_legacy;
	
	protected $manejador_interface;
	protected $ini_file;
	protected $proyecto;
	
	/**
	 * @param toba_mock_proceso_gui $m_interface
	 * @param toba_modelo_proyecto $proyecto
	 */
	function __construct($m_interface, $proyecto)
	{
		$this->manejador_interface = $m_interface;
		$this->proyecto = $proyecto;		
	}
	
	/**
	 * Inicializa el empaquetador con los distintos directorios
	 * @param string $dir_proyecto
	 * @param string $dir_destino
	 * @param string $dir_instalador
	 * @param boolean $paquete_legacy
	 * @throws toba_error
	 */
	function inicializar($dir_proyecto, $dir_destino, $dir_instalador, $paquete_legacy=false)
	{
		$this->_paquete_legacy = $paquete_legacy;		
		$this->_dir_proyecto = $dir_proyecto;
		$this->_dir_destino = $dir_destino;
		$this->_dir_instalador = $dir_instalador;
		
		$path_ini = $this->proyecto->get_dir().'/'.self::ARCHIVO_CONFIG;
		if (! file_exists($path_ini)) {
			throw new toba_error("Para crear el paquete de instalación debe existir el archivo '".self::ARCHIVO_CONFIG."' en la raiz del proyecto");
		}
		$this->ini_file = new toba_ini($path_ini);
	}
	
	/**
	 * Genera el paquete para el instalador web
	 */
	function empaquetar()
	{
		//Definir carpeta destino y crearla 
		$this->crear_destino($this->get_dir_destino());
		
		//Compilar el proyecto
		$this->proyecto->compilar();
		
		//Generar el instalador
		$this->manejador_interface->titulo("Generando instalador");		
		$this->manejador_interface->mensaje("Copiando instalador..", false);				
		$this->copiar_instalador($this->get_dir_instalador(), $this->get_dir_destino());
		$this->generar_ini_instalador($this->get_dir_instalador(), $this->get_dir_destino());
		$this->manejador_interface->progreso_fin();
		
		//Generar autoload instalador
		$this->manejador_interface->mensaje('Generando autoload instalador...', false);		
		$this->generar_autoload_instalador();
		$this->manejador_interface->progreso_fin();
		
		//Copiar los archivos del proyecto
		$this->manejador_interface->titulo("Copiando archivos");
		$this->copiar_aplicacion();
		$this->manejador_interface->progreso_fin();
		
		//Completar la instalacion via composer
		if (! $this->_paquete_legacy) {
			$this->make_pre_install($this->get_dir_destino_aplicacion());
			$this->manejador_interface->progreso_fin();
			$this->manejador_interface->mensaje('Eliminando carpetas innecesarias...', false);
			$this->borrar_datos_innecesarios($this->get_dir_destino_aplicacion());
		} else {
			$this->copiar_framework($this->get_dir_destino());
		}
		$this->manejador_interface->progreso_fin();
	}
	
	/**
	 * Permite forzar la generacion de un paquete con el formato anterior, sin usar composer
	 */
	function forzar_empaquetado_legacy()
	{
		$this->_paquete_legacy = true;
	}
	
	//----------------------------------------------------------------------------------------------------------------------------------------------------//
	//							METODOS AUXILIARES
	//----------------------------------------------------------------------------------------------------------------------------------------------------//
	/**
	 * @ignore
	 * @return string
	 */
	protected function get_dir()
	{
		return $this->_dir_proyecto;
	}
	
	/**
	 * @ignore
	 * @return string
	 */
	protected function get_dir_destino()
	{
		return $this->_dir_destino;		
	}

	/**
	 * @ignore
	 * @return string
	 */
	protected function get_dir_instalador()
	{
		return $this->_dir_instalador;
	}
	
	/**
	 * @ignore
	 * @return string
	 */
	protected function get_dir_destino_aplicacion()
	{
		return $this->get_dir_destino() . $this->get_dir_proyecto_aplicacion();
	}
	
	/**
	 * Devuelve un string con la ruta de la aplicacion segun el proyecto
	 * @return string
	 */
	protected function get_dir_proyecto_aplicacion()
	{
		return '/proyectos/'.$this->proyecto->get_id().'/aplicacion';
	}	
	
	/**
	 * Crea la carpeta destino si no existe
	 * @ignore
	 * @param string $path
	 * @throws toba_error
	 */
	protected function crear_destino($path)
	{
		if (file_exists($path)) {
			if (! is_dir($path)) {
				throw new toba_error(self::ARCHIVO_CONFIG. ": La ruta '$path' no es un directorio valido");
			}
			if (! toba_manejador_archivos::es_directorio_vacio($path)) {
				//-- Existe la carpeta y no está vacia, se borra?
				if ($this->manejador_interface->dialogo_simple("La carpeta destino '$path' no esta vacia. Desea borrarla?", 's')) {
					toba_manejador_archivos::eliminar_directorio($path);
					toba_manejador_archivos::crear_arbol_directorios($path);
				}
			}
		} else {
			toba_manejador_archivos::crear_arbol_directorios($path);
		}
	}	
	
	/**
	 * @ignore
	 * @param string $dir_instalador
	 * @param string $dir_destino
	 * @throws toba_error
	 */
	protected function copiar_instalador($dir_instalador, $dir_destino)
	{
		$excepciones = array();		
		if (! isset($dir_instalador)) {
			$dir_instalador = toba_dir().'/instalador'; 
		}
		$path_relativo = $dir_instalador;
		$dir_instalador = realpath($dir_instalador);
		if (!file_exists($dir_instalador) || !is_dir($dir_instalador)) {
			throw new toba_error(self::ARCHIVO_CONFIG.": La ruta '$path_relativo' no es un directorio valido");
		}				
		$excepciones = array($dir_instalador.'/ejemplo.proyecto.ini'); 
		toba_manejador_archivos::copiar_directorio($dir_instalador, $dir_destino, $excepciones, $this->manejador_interface, false);
	}
	
	/**
	 * @ignore
	 * @param string $dir_instalador
	 * @param string $dir_destino
	 */
	protected function generar_ini_instalador($dir_instalador, $dir_destino)
	{
		$svn = new toba_svn();
		$rev = $svn->get_revision($dir_instalador);
		if (trim($rev) == '') {
			$rev = 'ND';
		}			
		$inst_ini = new toba_ini($dir_destino.'/instalador.ini');
		$inst_ini->agregar_entrada('revision', $rev);
		$inst_ini->agregar_entrada('instalacion_produccion', 1);
		$inst_ini->agregar_entrada('paquete_legacy', $this->_paquete_legacy ? 1: 0);
		$inst_ini->guardar();
	}
	
	/**
	 * Genera el autoload para que el instalador encuentre las clases redefinidas por el proyecto
	 * @ignore
	 */
	protected function generar_autoload_instalador()
	{
		$destino_relativo = $this->get_dir_proyecto_aplicacion();	
		$extras = array();
		$redefinidos = $this->ini_file->get('instalador_clases_redefinidas', null, null, false);
		if (! is_null($redefinidos)) {
			foreach($redefinidos as $clase) {
				$nombre = basename($clase, '.php');
				$extras[$nombre] = $destino_relativo . '/'. $clase;		
			}
		}
		
		$pm = array($this->get_dir_destino() => array('archivo_salida' => "instalador_autoload.php",
											'dirs_excluidos' => array(),
											'extras' => $extras));		
		$generador_autoload = new toba_extractor_clases($pm);
		$generador_autoload->generar();	
	}	
	
	/**
	 * Arma lista de excepciones, copia el proyecto y actualiza el punto de acceso
	 */
	protected function copiar_aplicacion()
	{
		$empaquetado = $this->ini_file->get_datos_entrada('empaquetado');
		$this->manejador_interface->mensaje("Copiando aplicacion", false);		
		$destino_aplicacion = $this->get_dir_destino_aplicacion();
		$excepciones = array(toba_dir(), $this->proyecto->get_instancia()->get_path_proyecto('toba_editor'), toba_modelo_instalacion::dir_base());							//Se excluye el editor		
		if (isset($empaquetado['excepciones_proyecto'])) {
			$excepciones_extra = explode(',', $empaquetado['excepciones_proyecto']);
			$origen = $this->get_dir();
			foreach (array_keys($excepciones_extra) as $i) {
				if (trim($excepciones_extra[$i]) != '') {
					$excepciones[] = $origen.'/'.trim($excepciones_extra[$i]);
				}
			}			
		}
		
		$this->empaquetar_proyecto($destino_aplicacion, $excepciones);	
		$this->actualizar_punto_acceso($destino_aplicacion);
	}
	
	/**
	 * Realiza la copia de archivos puntualmente
	 * @ignore
	 * @param string $destino
	 * @param array $excepciones
	 */
	protected function empaquetar_proyecto($destino, $excepciones)
	{
		$origen = $this->get_dir();	
		toba_manejador_archivos::crear_arbol_directorios($destino);
		toba_manejador_archivos::copiar_directorio($origen, $destino, 
													$excepciones, $this->manejador_interface, false);

		//-- Crea un archivo revision con la actual de toba
		file_put_contents($destino.'/REVISION', revision_svn($origen, true));		
	}
	
	/**
	 * @ignore
	 * @param string $destino
	 */
	protected function actualizar_punto_acceso($destino)
	{
		//-- Modifica aplicacion.php
		$dir_template = toba_dir().toba_modelo_proyecto::template_proyecto;
		copy( $dir_template.'/www/aplicacion.produccion.php', $destino.'/www/aplicacion.php');
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion( '|__proyecto__|', $this->proyecto->get_id() );
		$editor->procesar_archivo( $destino . '/www/aplicacion.php' );		
	}
		
	/**
	 * Realiza una instalacion de composer sin librerias de desarrollo y optimizando el autoload.
	 * @param string $path
	 * @throws toba_error
	 */
	protected function make_pre_install($path)
	{
		$stderr = $stdout = '';
		$this->manejador_interface->mensaje("Copiando framework via composer, esto puede tardar...", false);	
		$actual = getcwd();
		if (chdir($path)) {													//Intento cambiar al dir destino para ejecutar composer
			$this->manejador_interface->progreso_avanzar();
			$cmd = ' composer install --no-dev  --optimize-autoloader';
			$exit_status =  toba_manejador_procesos::ejecutar($cmd, $stdout, $stderr);
			if ($exit_status != 0) {
				toba_logger::instancia()->debug($stderr);
				throw new toba_error('Se produjo un error al querer utilizar composer, revise el log');	
			}
		} else {
			throw new toba_error('No se puede cambiar al directorio destino '. $path);
		}
		chdir($actual);														//Vuelvo al directorio actual
		$this->manejador_interface->progreso_avanzar();
	}

	/**
	 * Elimina las carpetas que antes se excluian en la copia del framework
	 * @ignore
	 * @param string $dir_destino
	 * @throws toba_error
	 */
	protected function borrar_datos_innecesarios($dir_destino)
	{
		$librerias = $proyectos_extra = array();
		if (file_exists($dir_destino . '/composer.json')) {
			$valores = json_decode(file_get_contents($dir_destino . '/composer.json'), true); 			
			$vendor_dir =  (isset($valores['config']['vendor-dir'])) ? $valores['config']['vendor-dir'] : 'vendor';
		} else {
			throw new toba_error ('No se encuentra el archivo composer.json en el directorio destino de la aplicacion' );
		}				
		if ($this->ini_file->existe_entrada('empaquetado', 'librerias')) {
			$lista = explode(',', $this->ini_file->get('empaquetado', 'librerias'));
			$librerias = array_map('trim', $lista);
		}
		if ($this->ini_file->existe_entrada('empaquetado', 'proyectos_extra')) {
			$lista = explode(',', $this->ini_file->get('empaquetado', 'proyectos_extra'));
			$proyectos_extra = array_map('trim', $lista);
		}
				
		$path_base = $dir_destino. '/'. $vendor_dir . '/'. self::FRAMEWORK_VENDOR_DIR;				
		$excepciones = $this->proyecto->get_instalacion()->get_lista_excepciones_instalacion($path_base, $librerias, array('toba_usuarios'));
		foreach ($excepciones as $dir) {			
			if (file_exists($dir) && is_dir($dir)) {
				toba_manejador_archivos::eliminar_directorio($dir);
				$this->manejador_interface->progreso_avanzar();
			}
		}
	}
	
	/**
	 * Copia el framework de toba a la antigua
	 */
	protected function copiar_framework()
	{
		$this->manejador_interface->mensaje("Copiando framework", false);	
		$empaquetado = $this->ini_file->get_datos_entrada('empaquetado');
		$librerias = array();
		$proyectos = array();
		if (isset($empaquetado['librerias'])) {
			$librerias = explode(',', $empaquetado['librerias']);
			$librerias = array_map('trim', $librerias);
		}
		if (isset($empaquetado['proyectos_extra'])) {
			$proyectos = explode(',', $empaquetado['proyectos_extra']);
			$proyectos = array_map('trim', $proyectos);
			$proyectos = array_diff($proyectos, array('toba_editor'));
		}		
		$instalacion = $this->proyecto->get_instalacion();
		$destino_instalacion = $this->get_dir_destino(). '/proyectos/'.$this->proyecto->get_id().'/toba';
		$instalacion->empaquetar_en_carpeta($destino_instalacion, $librerias, $proyectos);		
	}	
}
?>