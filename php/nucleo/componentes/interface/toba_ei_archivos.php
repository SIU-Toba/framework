<?php
require_once("toba_ei.php");
require_once("lib/manejador_archivos.php");

/**
 * Permite navegar el sistema de archivos del servidor bajo una carpeta dada
 * @package Componentes
 * @subpackage Eis
 */
class toba_ei_archivos extends toba_ei
{
	protected $prefijo = 'arch';	
	protected $dir_actual;
	protected $path_relativo_inicial;
	protected $filtro;

    function __construct($id)
    {
        parent::__construct($id);
		if (isset($this->memoria['dir_actual'])) {
			$this->dir_actual = $this->memoria['dir_actual'];
		}
		if (isset($this->memoria['path_relativo_inicial'])) {
			$this->path_relativo_inicial = $this->memoria['path_relativo_inicial'];
		}		
		$this->extensiones = array("php");
		$this->ocultos = array(".svn");
	}
	
	function destruir()
	{
		$this->memoria['dir_actual'] = $this->dir_actual;
		$this->memoria['path_relativo_inicial'] = $this->path_relativo_inicial;
		parent::destruir();
	}

	protected function cargar_lista_eventos()
	{
		parent::cargar_lista_eventos();
		$this->eventos['ir_a_carpeta'] = array();
		$this->eventos['seleccionar_archivo'] = array();		
		$this->eventos['crear_carpeta'] = array();		
		$this->eventos['crear_archivo'] = array();
	}
	
	function disparar_eventos()
	{
		if(isset($_POST[$this->submit]) && $_POST[$this->submit]!="") {
			$evento = $_POST[$this->submit];	
			//El evento estaba entre los ofrecidos?
			if (isset($this->memoria['eventos'][$evento]) ) {
				$parametros = $_POST[$this->submit."__seleccion"];
				switch($evento){
					case 'ir_a_carpeta':
						$seleccion = $this->dir_actual."/$parametros";						
						//--- Chequeo de seguridad
						if (isset($this->path_relativo_inicial)) {
							if (strpos(realpath($seleccion), realpath($this->path_relativo_inicial)) !== 0) {
							   throw new toba_error("El path es invalido");
							}				
						}
						$this->dir_actual = manejador_archivos::path_a_unix(realpath($seleccion));
						break;
					case 'crear_carpeta': 
						$parametros = str_replace('.', '', $parametros);
						$seleccion = $this->dir_actual."/$parametros";
						manejador_archivos::crear_arbol_directorios($seleccion);
						break;
					case 'crear_archivo': 
						$parametros = str_replace('/', '', $parametros);
						$seleccion = $this->dir_actual."/$parametros";	
						manejador_archivos::crear_archivo_con_datos($seleccion, "");
						break;
					default:
						$this->reportar_evento( $evento, $seleccion );
				}
			}
		}
	}
	
	function path_relativo()
	{
		if (! isset($this->path_relativo_inicial))
			return $this->dir_actual;
		$pos = strlen($this->path_relativo_inicial);
		$relativo = substr($this->dir_actual, $pos);
		return $relativo;
	}

	/*
	*	El listado de archivos comienza desde este directorio y la respuesta tambien sera analizada en este contexto
	*/	
	function set_path_relativo_inicial($dir)
	{
		$this->path_relativo_inicial = $dir;
		if (!isset($this->dir_actual))
			$this->dir_actual = $dir;
	}
	
	function set_path($path)
	{
		$this->dir_actual = $this->path_relativo_inicial.$path;
	}

	function generar_html()
	{
		echo toba_form::hidden($this->submit, '');
		echo toba_form::hidden($this->submit."__seleccion", '');		
	
		$dir = opendir($this->dir_actual);
		$archivos = array();
		$carpetas = array();
		$hay_padre = false;

		//Es el directorio relativo inicial?
		$es_el_relativo = false;
		if (isset($this->path_relativo_inicial)) {
			$es_el_relativo = (realpath($this->path_relativo_inicial) == realpath($this->dir_actual));
		}
		//Filtra Archivos y directorios
		while(($archivo = readdir($dir)) !== false)  
		{  
			$ruta = $this->dir_actual."/".$archivo;
			$info = pathinfo($ruta);
			if (!isset($info['extension']))
				$info['extension'] = '';

			$es_padre = ($archivo == '..');
			if ($es_padre && !$es_el_relativo)
				$hay_padre = true;
			$es_actual = ($archivo == '.');
			if (!$es_padre && !$es_actual && is_dir($ruta) && !in_array($archivo, $this->ocultos)) {
				$carpetas[] = $archivo;
			} elseif (in_array($info['extension'], $this->extensiones)) {
				$archivos[] = $archivo;
			}
		}
		closedir($dir);
		sort($archivos);
		sort($carpetas);
		$path = pathinfo($this->dir_actual);
		$this->generar_html_barra_sup("<span title='{$this->dir_actual}'>{$path['basename']}</span>", false,"ei-arch-barra-sup");
		echo "<div style=''>\n";
		
		$img_crear_carpeta = toba_recurso::imagen_apl('archivos/carpeta_nueva.gif', true);
		$img_crear_archivo = toba_recurso::imagen_apl('archivos/archivo_nuevo.gif', true);
		
		echo "<span style='float: right'>
				<a href='#' onclick='{$this->objeto_js}.crear_carpeta()' title='Crear carpeta'>$img_crear_carpeta</a>
				<a href='#' onclick='{$this->objeto_js}.crear_archivo()' title='Crear archivo'>$img_crear_archivo</a>
			  </span>\n";			
		
		if ($hay_padre) {
			$img_subir = toba_recurso::imagen_apl('archivos/subir.gif', true);
			echo "<span style='float: left'>
					<a href='#' onclick='{$this->objeto_js}.ir_a_carpeta(\"..\")' title='Subir de carpeta'>$img_subir</a>
				  </span>\n";						
		}

		$img_carpeta = toba_recurso::imagen_apl('archivos/carpeta.gif', true);
		echo "<div style='clear:left'>";
		foreach ($carpetas as $carpeta) {
			echo "<div class='ei_archivos-carpeta'>$img_carpeta 
				<a href='#' onclick='{$this->objeto_js}.ir_a_carpeta(\"$carpeta\")' 
					title='Entrar a la carpeta'>$carpeta</a></div>\n";
		}
		$img_archivo = toba_recurso::imagen_apl('archivos/php.gif', true);
		foreach ($archivos as $archivo) {
			echo "<div class='ei_archivos-archivo'>$img_archivo 
					<a href='#' onclick='{$this->objeto_js}.seleccionar_archivo(\"$archivo\")' 
					 title='Seleccionar el archivo'>$archivo</a>\n</div>";
		}
		echo "</div>";
		echo "</div>\n";
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	protected function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();
		$path = addslashes($this->path_relativo());
		echo $identado."window.{$this->objeto_js} = new ei_archivos('{$this->objeto_js}', '{$this->submit}', '$path');\n";
	}

	//-------------------------------------------------------------------------------

	function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = 'componentes/ei_archivos';
		return $consumo;
	}	

}

?>