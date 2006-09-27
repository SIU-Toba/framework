<?php
require_once('archivo_php.php');
/**
*	Representa a la EDICION de una CLASE del ambiente. 
*	Tiene capacidades de generacion y analisis MOLDES aportados por la METACLASE del compone
*/
class clase_php
{		
	protected $archivo;
	protected $meta_clase;				//la clase que conoce el contenido de la clase que se esta editando
	
	function __construct($archivo, $meta_clase)
	{
		$this->meta_clase = $meta_clase;
		$this->archivo = $archivo;
	}
	
	function nombre()
	{
		return $this->meta_clase->get_subclase_nombre();
	}
	
	function incluir_clase_padre()
	{
		require_once($this->meta_clase->get_clase_archivo());
	}	
	
	//---------------------------------------------------------------
	//-- Generacion de codigo
	//---------------------------------------------------------------

	/**
	*	Informa la lista de metodos a generar
	*/
	function get_lista_metodos_posibles() 
	{
		$molde_clase = $this->meta_clase->get_molde_subclase();
		return $molde_clase->get_lista_metodos();
	}

	/**
	*	Genera la clase
	*/
	function generar($opciones)
	{
		$molde_clase = $this->meta_clase->get_molde_subclase();
		echo "<br><br><pre style='background-color: white'>" . $molde_clase->get_codigo($opciones) . "</pre>";
		return;
		if ($this->archivo->esta_vacio()) {
			$this->archivo->crear_basico();
		}
		$this->archivo->edicion_inicio();
		$this->archivo->insertar_al_final($molde_clase->get_codigo($opciones));
		$this->archivo->edicion_fin();
	}
	
	//---------------------------------------------------------------
	//-- Analisis de codigo
	//---------------------------------------------------------------
	
	function analizar()
	{
		$this->incluir_clase_padre();
		$this->archivo->incluir();		
		try {
			$clase = new ReflectionClass($this->nombre());
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
			echo ei_mensaje("No se encuentra la clase ".$this->nombre()." en este archivo.", "error");
		}
	}	
	
	function analizar_metodos($titulo, $clase, $metodos, $mostrar=true)
	{
		static $id=0;$id++;
		$display = ($mostrar) ? "" : "style='display: none'";
		echo "<li><a href='#' onclick=\"o = getElementById('_id$id'); o.style.display = (o.style.display == 'none') ? '' : 'none';\">";
		echo "Métodos $titulo</a></li>\n";
		echo "<ul id='_id$id' $display>\n";
		foreach ($metodos as $metodo) {
			if ($metodo->getDeclaringClass() == $clase) {
				$estilo = '';
				if (isset($this->meta_clase)){
					if ($this->meta_clase->es_evento($metodo->getName())) {
						$tipo = toba_recurso::imagen_apl('reflexion/desconocido.gif');
						if (! $this->meta_clase->es_evento_valido($metodo->getName())) {
							$tipo = toba_recurso::imagen_apl('reflexion/problema.gif');
						}
						if ($this->meta_clase->es_evento_sospechoso($metodo->getName())) {
							$tipo = toba_recurso::imagen_apl('warning.gif');
						}
						if ($this->meta_clase->es_evento_predefinido($metodo->getName())) {
							$tipo = toba_recurso::imagen_apl('reflexion/evento.gif');
						}
						$estilo =  "list-style-image: url($tipo)";					
					} 
				}
				echo "<li style='padding-right: 10px; $estilo'>&nbsp;";
				echo $metodo->getName();
				echo "</li>\n";
			}
		}	
		echo "</ul></li>\n";	
	}
}		
?>