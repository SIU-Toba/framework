<?php
require_once("objeto.php");
require_once("objeto_ei.php");
require_once("nucleo/lib/manejador_archivos.php");

class objeto_ei_archivos extends objeto_ei
{
	protected $dir_actual;
	protected $path_relativo_inicial;
	protected $filtro;

    function __construct($id)
    {
        parent::__construct($id);
        $this->objeto_js = "objeto_archivo_{$id[1]}";
        $this->submit = "ei_archivo" . $this->id[1];
		if (isset($this->memoria['dir_actual'])) {
			$this->dir_actual = $this->memoria['dir_actual'];
		}
		$this->extensiones = array("php");
		$this->ocultos = array(".svn");
	}
	
	function destruir()
	{
		$this->memoria["eventos"] = array();
		if(isset($this->eventos)){
			foreach($this->eventos as $id => $evento ){
				$this->memoria["eventos"][$id] = true;
			}
		}
		$this->memoria['dir_actual'] = $this->dir_actual;
		$this->memoria['path_relativo_inicial'] = $this->path_relativo_inicial;
		parent::destruir();
	}

	function inicializar($parametros)
	{
		$this->id_en_padre = $parametros['id'];
	}
	
    function cargar_datos($datos=null,$memorizar=true)
    {
	}


	function get_lista_eventos()
	{
		$eventos = array();
		$eventos['ir_a_carpeta'] = array();
		$eventos['seleccionar_archivo'] = array();		
		return $eventos;
	}
	
	function disparar_eventos()
	{
		if(isset($_POST[$this->submit]) && $_POST[$this->submit]!="") {
			$evento = $_POST[$this->submit];	
			//El evento estaba entre los ofrecidos?
			if (isset($this->memoria['eventos'][$evento]) ) {
				$parametros = $_POST[$this->submit."__seleccion"];
				$seleccion = $this->dir_actual."/$parametros";
				if ($evento == 'ir_a_carpeta') {
					$this->dir_actual = manejador_archivos::path_a_unix(realpath($seleccion));
				} else {
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
		$relativo = substr($this->dir_actual, $pos + 1);
		return $relativo;
	}
	
	function set_path_relativo_inicial($dir)
	/*
	*	El listado de archivos comienza desde este directorio y la respuesta tambien sera analizada en este contexto
	*/
	{
		$this->path_relativo_inicial = $dir;
		if (!isset($this->dir_actual))
			$this->dir_actual = $dir;
	}
	
	function set_path($path)
	{
		$this->dir_actual = $this->path_relativo_inicial."/".$path;
	}

	function obtener_html()
	{
		echo form::hidden($this->submit, '');
		echo form::hidden($this->submit."__seleccion", '');		
	
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
		$this->barra_superior($this->dir_actual, false,"objeto-ei-barra-superior");
		echo "<div style='width:300px'>\n";
		if ($hay_padre) {
			$img_subir = recurso::imagen_apl('archivos/subir.gif', true);
			echo "<div><a href='#' onclick='{$this->objeto_js}.ir_a_carpeta(\"..\")' 
					 title='Subir de carpeta'>$img_subir</a></div>";
		}
		$img_carpeta = recurso::imagen_apl('archivos/carpeta.gif', true);
		foreach ($carpetas as $carpeta) {
			echo "<div class='ei_archivos-carpeta'>$img_carpeta 
				<a href='#' onclick='{$this->objeto_js}.ir_a_carpeta(\"$carpeta\")' 
					title='Entrar a la carpeta'>$carpeta</a></div>\n";
		}
		$img_archivo = recurso::imagen_apl('archivos/php.gif', true);
		foreach ($archivos as $archivo) {
			echo "<div class='ei_archivos-archivo'>$img_archivo 
					<a href='#' onclick='{$this->objeto_js}.seleccionar_archivo(\"$archivo\")' 
					 title='Seleccionar el archivo'>$archivo</a>\n</div>";
		}
		echo "</div>\n";
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	protected function crear_objeto_js()
	{
		$identado = js::instancia()->identado();
		$path = addslashes($this->path_relativo());
		echo $identado."var {$this->objeto_js} = new objeto_ei_archivos('{$this->objeto_js}', '{$this->submit}', '$path');\n";
	}

	//-------------------------------------------------------------------------------

	public function consumo_javascript_global()
	{
		$consumo = parent::consumo_javascript_global();
		$consumo[] = 'clases/objeto_ei_archivos';
		return $consumo;
	}	

}

?>