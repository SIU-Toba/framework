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
			$eventos += eventos::evento_estandar('crear_archivo', '&Crear Archivo');		
		}
		return $eventos;
	}
	
	function evt__abrir()
	{
		$archivo = str_replace('/', "\\", $this->archivo());
		exec("start $archivo");
	}
	
	function evt__crear_archivo()
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
	

	//--- Archivo Plano	
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
	
	//--- Análisis de la clase
	function obtener_html_contenido__2()
	{
		$archivo = $this->archivo();
		if(file_exists($archivo)){
			ei_separador("ARCHIVO: ". $archivo);
			include_once($archivo);
			$clase = new ReflectionClass($this->datos['subclase']);
			$metodos = $clase->getMethods();
			
			echo "<h3>Clase ".$clase->getName()."</h3>";
			echo "<ul>";
			//Métodos propios
			$this->analizar_metodos('propios', $clase, $metodos, true);
			$padre = $clase->getParentClass();
			while ($padre != null) {
				$titulo = "heredados de {$padre->getName()}";
				$this->analizar_metodos($titulo, $padre, $metodos, false);
				$padre = $padre->getParentClass();
			}
			echo "</ul>";
		}
	}	
	
	function analizar_metodos($titulo, $clase, $metodos, $mostrar=true)
	{
		$display = ($mostrar) ? "" : "style='display: none'";
		echo "<li><a href='javascript: ' onclick=\"o = this.nextSibling; o.style.display = (o.style.display == 'none') ? '' : 'none';\">";
		echo "Métodos $titulo</a></li>";
		echo "<ul $display >";
		foreach ($metodos as $metodo) {
			if ($metodo->getDeclaringClass() == $clase) {
				$img_evt = recurso::imagen_apl('reflexion/evento.gif');
				$estilo = $this->es_evento($metodo) ? "list-style-image: url($img_evt)" : "";
				echo "<li style='padding-right: 10px; $estilo'>&nbsp;";
				echo $metodo->getName();
				echo "</li>\n";
			}
		}	
		echo "</ul></li>";	
	}
	
	function es_evento($metodo)
	{
		if (strstr($metodo->getName(), 'evt__'))
			return true;
		else
			return false;
	}

}


?>