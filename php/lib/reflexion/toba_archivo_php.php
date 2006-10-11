<?php
require_once('lib/toba_manejador_archivos.php');

class toba_archivo_php
{
	protected $nombre;
	protected $fp = null;
	protected $contenido = '';
	protected $archivo_abierto = false;
	
	function __construct($nombre)
	{
		$this->nombre = $nombre;	
	}
	
	function nombre()
	{
		return $this->nombre;
	}
	
	function esta_vacio()
	{
		$this->edicion_inicio();
		if (trim($this->contenido) == '')
			return true;
		else
			return false;
	}
	
	function existe()
	{
		return file_exists($this->nombre) && is_file($this->nombre);
	}

	function contiene_clase($nombre)
	{
		return strpos(file_get_contents($this->nombre), "class $nombre") !== false;
	}
	
	function mostrar()
	{
		require_once("3ros/PHP_Highlight.php");
		$h = new PHP_Highlight(false);
		$h->loadFile($this->nombre);		
		$formato_linea = "<span style='background-color:#D4D0C8; color: black; font-size: 10px;".
						" padding-top: 2px; padding-right: 2px; margin-left: -4px; width: 20px; text-align: right;'>".
						"%2d</span>&nbsp;&nbsp;";
		$h->toHtml(false, true, $formato_linea, true);
	}
	
	function abrir()
	{
		$cmd = toba::instalacion()->get_editor_php();
		if (strlen($cmd) == 0) {
			throw new toba_error_def("No se encuentra definido el editor por defecto a utilizar en la instalación");
		}
		if (toba_manejador_archivos::es_windows()) {
			$archivo = toba_manejador_archivos::path_a_windows($this->nombre);
			$com = "$cmd $archivo";
			toba::logger()->debug("Intentando abrir archivo con comando: '$com'");
			exec($com);
		} else {
			$archivo = toba_manejador_archivos::path_a_unix($this->nombre);
			$archivo = str_replace(" ", "\\ ", $archivo);
			$com = "$cmd $archivo";
			toba::logger()->debug("Intentando abrir archivo con comando: '$com'");
			$fp = popen($com, 'r');
			pclose($fp);
		}
	}
	
	function incluir()
	{
		//Verifica que no se haya incluido previamente
		$ya_incluido = false;
		foreach (get_included_files() as $archivo_incluido) {
			$nombre_incluido = str_replace('\\', '/', $archivo_incluido);
			if (strcasecmp($this->nombre, $nombre_incluido) == 0)
				$ya_incluido = true;
		}
		if (!$ya_incluido) {
			include_once($this->nombre);
		}
	}	
	
	function crear_basico()
	{
		$this->edicion_inicio();
		$this->contenido = "<?php ?>";
		$this->edicion_fin();
	}
	
	//--------------------------------------------------------------------------------
	//-------------------------EDITAR EL ARCHIVO -------------------------------------
	//--------------------------------------------------------------------------------
	
	function edicion_inicio()
	{
		if( ! $this->archivo_abierto ) {
			if (file_exists($this->nombre))
				$this->contenido = file_get_contents($this->nombre);
			else
				$this->contenido = '';
			$this->archivo_abierto = true;
		}
	}
	
	function edicion_fin()
	{
/*		echo "Contenido: <pre>";
		echo htmlentities($this->contenido);
		echo "</pre>";
*/		
		toba_manejador_archivos::crear_arbol_directorios(dirname($this->nombre));
		file_put_contents($this->nombre, $this->contenido);
	}	
	
	function contenido()
	{
		return $this->contenido;
	}
	
	function insertar_al_inicio($codigo)
	{
		$pos = strpos($this->contenido, '<?php');
		if ($pos !== false) {
			$inicio = "<?php";
			$final = substr($this->contenido, $pos + 5);
		} else {
			$pos = strpos($this->contenido, '<?');
			if ($pos !== false) {
				$inicio = "<?";
				$final = substr($this->contenido, $pos + 2);
			} else {
				throw new toba_error("El archivo no contiene las marcas PHP de inicio de archivo");
			}
		}
		$this->contenido = $inicio."\n".$codigo.$final;
	}
	
	function insertar_al_final($codigo)
	{
		$pos = strrpos($this->contenido, '?>');
		if ($pos !== false) {
			$final = "?>";
			$inicio = substr($this->contenido, 0, $pos);
		} else {
			throw new toba_error("El archivo no contiene las marcas PHP de fin de archivo");
		}
		$this->contenido = $inicio."\n".$codigo."\n".$final;	
	}

}
?>