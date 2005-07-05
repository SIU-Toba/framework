<?php
require_once('archivo_php.php');

class clase_php
{		
	protected $nombre;
	protected $archivo;
	protected $padre_nombre;
	protected $archivo_padre_nombre;
	
	protected $elemento_toba;			//Elemento toba asociado a la clase (ej. ei_formulario, ci, etc)
	
	function __construct($nombre, $archivo, $clase_padre_nombre, $archivo_padre_nombre)
	{
		$this->nombre = $nombre;
		$this->archivo = $archivo;
		$this->padre_nombre = $clase_padre_nombre;
		$this->archivo_padre_nombre = $archivo_padre_nombre;
	}
	
	//Asocia la clase-php al objeto-toba apropiado
	function set_objeto($proyecto, $objeto)
	{
		//Carga el elemento toba con este objeto
		$this->elemento_toba = call_user_func(array($this->padre_nombre, 'elemento_toba'));
		$this->elemento_toba->cargar_db($proyecto, $objeto);		
	}
	
	//--Generación de clases	
	function generar($opciones)
	{		
		$this->archivo->edicion_inicio();
		//¿Está incluido la clase padre en el archivo
		if (strpos($this->archivo->contenido(), $this->archivo_padre_nombre) === false) {
			$this->archivo->insertar_al_inicio("require_once('{$this->archivo_padre_nombre}');");
		}
		$this->archivo->insertar_al_final($this->generar_clase($opciones));
		$this->archivo->edicion_fin();	
		
	}
	
	function generar_clase($opciones)
	{
		//Incluir el código que hace la subclase
		$codigo = $this->separador_clases();
		$cuerpo = $this->generar_clase_cuerpo($opciones);
		$codigo .= "class {$this->nombre} extends {$this->padre_nombre}\n{\n$cuerpo\n}\n";
		return $codigo;
	
	}
	
	function generar_clase_cuerpo($opciones)
	{
		if (!isset($this->elemento_toba))
			return '';

		$this->elemento_toba->set_nivel_comentarios($opciones['nivel_comentarios']);			
		$cuerpo = '';
		if ($opciones['constructor']) {
			$cuerpo .= $this->elemento_toba->generar_constructor()."\n";
		}
		if ($opciones['basicos']) {
			foreach ($this->elemento_toba->generar_metodos_basicos() as $metodo_basico) {
				$cuerpo .= $metodo_basico."\n";
			}
		}
		if ($opciones['eventos']) {
			$grupo_eventos = $this->elemento_toba->generar_eventos($opciones['eventos']);
			if (count($grupo_eventos) > 0) {
				$cuerpo .= $this->separador_seccion_grande('Eventos');
				foreach ($grupo_eventos as $seccion =>$eventos) {
					$cuerpo .= $this->separador_seccion_chica($seccion);
					foreach ($eventos as $evento) {
						$cuerpo .= $evento."\n";
					}
				}
			}
		}
		return $cuerpo;
	}
	
	//--Utilerías de formateo para la generación
	protected function separador_clases()
	{
		return "//----------------------------------------------------------------\n";	
	}

	protected function separador_seccion_chica($nombre='')
	{
		return "\t//------------------------------$nombre------------------------------\n";	
	}	
	
	protected function separador_seccion_grande($nombre)
	{
		return  "\t//-------------------------------------------------------------------\n".
				"\t//---$nombre\n";	
	}	
		
	
	//--Analisis
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
				$estilo = '';
				if ($this->elemento_toba->es_evento($metodo->getName())) {
					$tipo = recurso::imagen_apl('reflexion/evento.gif');
					if (! $this->elemento_toba->es_evento_valido($metodo->getName())) {
						$tipo = recurso::imagen_apl('reflexion/problema.gif');
					}
					if ($this->elemento_toba->es_evento_sospechoso($metodo->getName())) {
						$tipo = recurso::imagen_apl('warning.gif');
					}
					$estilo =  "list-style-image: url($tipo)";					
				} 
				echo "<li style='padding-right: 10px; $estilo'>&nbsp;";
				echo $metodo->getName();
				echo "</li>\n";
			}
		}	
		echo "</ul></li>";	
	}
	
	//--------------------------------------------------------------------------
	

		
}		

?>