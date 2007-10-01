<?php
/**
 * Permite navegar el sistema de archivos del servidor bajo una carpeta dada
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei_archivos ei_archivos
 * @wiki Referencia/Objetos/ei_archivos
 */
class toba_ei_archivos extends toba_ei
{
	protected $_dir_actual;
	protected $_path_absoluto;
	protected $_filtro;
	protected $_extensiones = array('php');
	protected $_ocultos = array('.svn');
	protected $solo_carpetas = false;

    function __construct($id)
    {
        parent::__construct($id);
		if (isset($this->_memoria['dir_actual'])) {
			$this->_dir_actual = $this->_memoria['dir_actual'];
		}
		if (isset($this->_memoria['path_absoluto'])) {
			$this->_path_absoluto = $this->_memoria['path_absoluto'];
		}		
	}
	
	function destruir()
	{
		$this->_memoria['dir_actual'] = $this->_dir_actual;
		$this->_memoria['path_absoluto'] = $this->_path_absoluto;
		parent::destruir();
	}

	/**
	 * @ignore 
	 */
	protected function cargar_lista_eventos()
	{
		parent::cargar_lista_eventos();
		$this->_eventos['ir_a_carpeta'] = array();
		$this->_eventos['seleccionar_archivo'] = array();		
		$this->_eventos['crear_carpeta'] = array();		
		$this->_eventos['crear_archivo'] = array();
	}
	
	/**
	 * @ignore 
	 */	
	function disparar_eventos()
	{
		if(isset($_POST[$this->_submit]) && $_POST[$this->_submit]!="") {
			$evento = $_POST[$this->_submit];	
			//El evento estaba entre los ofrecidos?
			if (isset($this->_memoria['eventos'][$evento]) ) {
				$parametros = $_POST[$this->_submit."__seleccion"];
				switch($evento){
					case 'ir_a_carpeta':
						$seleccion = $this->_dir_actual."/$parametros";						
						//--- Chequeo de seguridad
						if (isset($this->_path_absoluto)) {
							if (strpos(realpath($seleccion), realpath($this->_path_absoluto)) !== 0) {
							   throw new toba_error("El path es invalido");
							}				
						}
						$this->_dir_actual = toba_manejador_archivos::path_a_unix(realpath($seleccion));
						break;
					case 'crear_carpeta': 
						$parametros = str_replace('.', '', $parametros);
						$seleccion = $this->_dir_actual."/$parametros";
						toba_manejador_archivos::crear_arbol_directorios($seleccion);
						break;
					case 'crear_archivo': 
						$parametros = str_replace('/', '', $parametros);
						$seleccion = $this->_dir_actual."/$parametros";	
						toba_manejador_archivos::crear_archivo_con_datos($seleccion, "");
						break;
					default:
						$this->reportar_evento( $evento, $seleccion );
				}
			}
		}
	}
	
	/**
	 * Retorna el path relativo en donde se encuentra apuntando actualmente
	 * @return string
	 */
	function get_path_relativo()
	{
		if (! isset($this->_path_absoluto))
			return $this->_dir_actual;
		$pos = strlen($this->_path_absoluto);
		$relativo = substr($this->_dir_actual, $pos);
		return $relativo;
	}	
	
	/**
	 * Indica que el listado de archivos comienza desde este directorio y la respuesta tambien sera analizada en este contexto
	 * @param string $dir Path absoluto del que sera la base de todos los paths relativos
	 */
	function set_path_absoluto($dir)
	{
		$this->_path_absoluto = $dir;
		if (!isset($this->_dir_actual))
			$this->_dir_actual = $dir;
	}
	
	/**
	 * Cambia el directorio actual dentro del path absoluto inicial
	 * @param string $path Path relativo
	 */
	function set_path($path)
	{
		$this->_dir_actual = $this->_path_absoluto.$path;
		if( ! is_dir($this->_dir_actual) ) {
			toba::logger()->notice("El directorio especificado ('$path') no existe.");
			$this->_dir_actual = $this->_path_absoluto;
		}
	}

	/**
	 * Muestra solamente las carpetas y no archivos
	 * @param boolean $solo
	 */
	function set_solo_carpetas($solo)
	{
		$this->solo_carpetas = $solo;
	}
	
	/**
	 * Cambia el conjunto de extensiones permitidas en la visualización
	 * @param array $extensiones
	 */
	function set_extensiones_validas($extensiones)
	{
		$this->_extensiones = $extensiones;	
	}
	
	function generar_html()
	{
		echo toba_form::hidden($this->_submit, '');
		echo toba_form::hidden($this->_submit."__seleccion", '');		
	
		$dir = opendir($this->_dir_actual);
		$archivos = array();
		$carpetas = array();
		$hay_padre = false;

		//Es el directorio relativo inicial?
		$es_el_relativo = false;
		if (isset($this->_path_absoluto)) {
			$es_el_relativo = (realpath($this->_path_absoluto) == realpath($this->_dir_actual));
		}
		//Filtra Archivos y directorios
		while(($archivo = readdir($dir)) !== false)  
		{  
			$ruta = $this->_dir_actual."/".$archivo;
			$info = pathinfo($ruta);
			if (!isset($info['extension']))
				$info['extension'] = '';

			$es_padre = ($archivo == '..');
			if ($es_padre && !$es_el_relativo)
				$hay_padre = true;
			$es_actual = ($archivo == '.');
			if (!$es_padre && !$es_actual && is_dir($ruta) && !in_array($archivo, $this->_ocultos)) {
				$carpetas[] = $archivo;
			} elseif (in_array($info['extension'], $this->_extensiones)) {
				$archivos[] = $archivo;
			}
		}
		closedir($dir);
		sort($archivos);
		sort($carpetas);
		$path = pathinfo($this->_dir_actual);
		echo "<div class='ei-base ei-archivos-base'>\n";		
		echo $this->get_html_barra_editor();				
		$this->generar_html_barra_sup("<span title='{$this->_dir_actual}'>{$path['basename']}</span>", false,"ei-arch-barra-sup");		
		echo "<div  id='cuerpo_{$this->objeto_js}'>\n";		

		
		$img_crear_carpeta = toba_recurso::imagen_toba('nucleo/carpeta_nueva_24.gif', true);
		$img_crear_archivo = toba_recurso::imagen_toba('nucleo/archivo_nuevo.gif', true);

		
		echo "<span style='float: right'>";
		echo "<a href='#' onclick='{$this->objeto_js}.crear_carpeta()' title='Crear carpeta'>$img_crear_carpeta</a>";
		if (! $this->solo_carpetas) {
			echo "<a href='#' onclick='{$this->objeto_js}.crear_archivo()' title='Crear archivo'>$img_crear_archivo</a>";
		}
		echo "</span>\n";			
		
		if ($hay_padre) {
			$img_subir = toba_recurso::imagen_toba('nucleo/subir.gif', true);
			echo "<span style='float: left'>
					<a href='#' onclick='{$this->objeto_js}.ir_a_carpeta(\"..\")' title='Subir de carpeta'>$img_subir</a>
				  </span>\n";						
		}

		$img_carpeta = toba_recurso::imagen_toba('nucleo/carpeta.gif', true);
		echo "<div style='clear:left'>";
		foreach ($carpetas as $carpeta) {
			echo "<div class='ei_archivos-carpeta'>$img_carpeta 
				<a href='#' onclick='{$this->objeto_js}.ir_a_carpeta(\"$carpeta\")' 
					title='Entrar a la carpeta'>$carpeta</a></div>\n";
		}
		if (! $this->solo_carpetas) {
			$img_archivo = toba_recurso::imagen_toba('nucleo/php_22.gif', true);
			foreach ($archivos as $archivo) {
				echo "<div class='ei_archivos-archivo'>$img_archivo 
						<a href='#' onclick='{$this->objeto_js}.seleccionar_archivo(\"$archivo\")' 
						 title='Seleccionar el archivo'>$archivo</a>\n</div>";
			}
		}
		echo "</div>";
		echo "</div>\n";		
		echo "</div>\n";
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * @ignore 
	 */
	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		$path = addslashes($this->get_path_relativo());
		echo $identado."window.{$this->objeto_js} = new ei_archivos('{$this->objeto_js}', '{$this->_submit}', '$path');\n";
	}

	/**
	 * @ignore 
	 */	
	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'componentes/ei_archivos';
		return $consumo;
	}	

}

?>
