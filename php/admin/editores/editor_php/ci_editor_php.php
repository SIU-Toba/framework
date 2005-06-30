<?php
require_once('nucleo/browser/clases/objeto_ci.php');
require_once('nucleo/lib/reflexion/archivo_php.php');
require_once('nucleo/lib/reflexion/clase_php.php');

class ci_editor_php extends objeto_ci
{
	protected $datos;
	protected $archivo_php;

	function set_datos($datos)
	{
		$this->datos = $datos;
		$archivo = $this->datos['archivo'];
		$proyecto = $this->datos['proyecto'];
		if($proyecto == "toba")
			$path = $_SESSION["path_php"] . "/" . $archivo;
		else
			$path = $_SESSION["path"] . "/proyectos/$proyecto/php/" . $archivo;
		$this->archivo_php = new archivo_php($path);
		$this->clase_php = new clase_php(	$this->datos['subclase'], $this->archivo_php, 
											$this->datos['clase'], $this->datos['clase_archivo']);
	}
	
	//--- EVENTOS
	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		if($this->archivo_php->existe()) {
			$eventos += eventos::evento_estandar('abrir', '&Abrir', true, 
												  recurso::imagen_apl('reflexion/abrir.gif'),
												  'Intenta abrir el archivo en el servidor con el editor asociado');
			$this->archivo_php->incluir();
			if (! class_exists($this->datos['subclase']))
				$eventos += eventos::evento_estandar('crear_clase', '&Crear Subclase');
		} else {
			$eventos += eventos::evento_estandar('crear_archivo', '&Crear Archivo y Subclase');		
		}
		return $eventos;
	}
	
	function evt__abrir()
	{
		$this->archivo_php->abrir();
	}
	
	function evt__crear_archivo()
	{
		$this->archivo_php->crear_basico();
		$this->evt__crear_clase();
	}
	
	function evt__crear_clase()
	{
		$this->clase_php->generar();
	}
	

	//--- Archivo Plano	
	function obtener_html_contenido__1()
	{
		if($this->archivo_php->existe()){
			ei_separador("ARCHIVO: ". $this->archivo_php->nombre());
			echo "<div style='padding: 5px; text-align:left; background-color: #ffffff; font-size: 11px;'>";
			$this->archivo_php->mostrar();
			echo "</div>";
		}
	}
	
	//--- Análisis de la clase
	function obtener_html_contenido__2()
	{
		if($this->archivo_php->existe()){
			ei_separador("ARCHIVO: ". $this->archivo_php->nombre());
			$this->archivo_php->incluir();
			$this->clase_php->analizar();
		}
	}	
	
}


?>