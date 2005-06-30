<?php
require_once('archivo_php.php');

class clase_php
{		
	protected $nombre;
	protected $archivo;
	protected $padre_nombre;
	protected $archivo_padre_nombre;
	
	function __construct($nombre, $archivo, $clase_padre_nombre, $archivo_padre_nombre)
	{
		$this->nombre = $nombre;
		$this->archivo = $archivo;
		$this->padre_nombre = $clase_padre_nombre;
		$this->archivo_padre_nombre = $archivo_padre_nombre;
	}
	
	function generar()
	{		
		$this->archivo->edicion_inicio();
		$inclusion = "_once('{$this->archivo_padre_nombre}');";
		$inclusion_comillas = "_once(\"{$this->archivo_padre_nombre}\");";		
		//¿Está incluido la clase padre en el archivo
		if (strpos($this->archivo->contenido(), $inclusion) === false && 
			strpos($this->archivo->contenido(), $inclusion_comillas) === false) {
			$this->archivo->insertar_al_inicio("require$inclusion");
		}
		
		//Incluir el código que hace la subclase
		$codigo ="//----------------------------------------------------------------\n";
		$codigo .= "class {$this->nombre} extends {$this->padre_nombre}\n{\n\n}\n";
		$this->archivo->insertar_al_final($codigo);
		$this->archivo->edicion_fin();		
	}
	
	function analizar()
	{
		try {
			$clase = new ReflectionClass($this->nombre);
			$metodos = $clase->getMethods();
			
			echo "<div style='text-align: left;'><h3>Clase ".$clase->getName()."</h3>";
			echo "<ul>";
			//Métodos propios
			$this->analizar_metodos('propios', $clase, $metodos, true);
			$padre = $clase->getParentClass();
			while ($padre != null) {
				$titulo = "heredados de {$padre->getName()}";
				$this->analizar_metodos($titulo, $padre, $metodos, false);
				$padre = $padre->getParentClass();
			}
			echo "</ul></div>";	
		} catch (Exception $e) {
			echo ei_mensaje("No se encuentra la clase {$this->nombre} en este archivo.", "error");
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
	
	//--------------------------------------------------------------------------
	
	function es_evento($metodo)
	{
		if (strstr($metodo->getName(), 'evt__'))
			return true;
		else
			return false;
	}
		
}		

?>