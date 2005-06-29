<?php
require_once('nucleo/browser/clases/objeto_ci.php');

class ci_editor_php extends objeto_ci
{
	protected $datos;

	//--- EVENTOS
	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		if(file_exists($this->archivo())) {
			$eventos += eventos::evento_estandar('abrir', '&Abrir Archivo');
		} else {
			$eventos += eventos::evento_estandar('crear', '&Crear Archivo');		
		}
		return $eventos;
	}
	
	function evt__abrir()
	{
		$archivo = str_replace('/', "\\", $this->archivo());
		exec("start $archivo");
	}
	
	function evt__crear()
	{
		$require = "require_once('{$this->datos['clase_archivo']}');";
		$clase = "class {$this->datos['subclase']}\n{\n}";
		$template = "<?php\n$require\n\n$clase\n\n?>";
		$fp = fopen($this->archivo(), 'x');
		fwrite($fp, $template);
		fclose($fp);
	}
	
	//--- VARIOS
	function set_datos($datos)
	{
		$this->datos = $datos;
	}
	
	function archivo()
	{
		$archivo = $this->datos['archivo'];
		$proyecto = $this->datos['proyecto'];
		if($proyecto == "toba")
			return $_SESSION["path_php"] . "/" . $archivo;
		else
			return $_SESSION["path"] . "/proyectos/$proyecto/php/" . $archivo;
	}
	

	//--- SALIDA	
	function obtener_html_contenido__1()
	{
		$archivo = $this->archivo();
		if(file_exists($archivo)){
			ei_separador("ARCHIVO: ". $archivo);
			echo "<div style='padding: 5px; text-align:left; background-color: #ffffff; font-size: 11px;'>";
			highlight_file($archivo);
			echo "</div>";
		}
	}

}


?>