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
			
		//Manejo de archivos			
		$this->archivo_php = new archivo_php($path);
		
		//Manejo de clases
		$this->clase_php = new clase_php(	$this->datos['subclase'], $this->archivo_php, 
											$this->datos['clase'], $this->datos['clase_archivo']);
		$this->clase_php->set_objeto($this->datos['proyecto'], $this->datos['objeto']);
	}
	
	//--- EVENTOS
	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		if($this->archivo_php->existe()) {
			$eventos += eventos::evento_estandar('abrir', '&Abrir', true, 
												  recurso::imagen_apl('reflexion/abrir.gif'),
												  'Intenta abrir el archivo en el servidor con el editor asociado');
		} else {
			$eventos += eventos::evento_estandar('crear_archivo', '&Crear Archivo');		
		}
		$eventos += eventos::evento_estandar('refrescar', '&Refrescar', true, recurso::imagen_apl('refrescar.gif'));
		return $eventos;
	}
	
	function get_lista_ei()
	//Sobreescribir la lista de EIs a mostrar
	{
		$eis = parent::get_lista_ei();
		if($this->archivo_php->existe()) {
			$this->archivo_php->incluir();
			if (! class_exists($this->datos['subclase']))
				$eis[] = "subclase";	//Se incluye el formulario para dar de alta subclases
		}		
		return $eis;
	}
	
	function evt__abrir()
	{
		$this->archivo_php->abrir();
	}
	
	function evt__crear_archivo()
	{
		$this->archivo_php->crear_basico();
	}
	
	function evt__subclase__alta($opciones)
	{
		$this->clase_php->generar($opciones);
	}
	

	//--- Archivo Plano	
	function obtener_html_contenido__1()
	{
		$this->obtener_html_dependencias();
		echo "<br>";
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
		$this->obtener_html_dependencias();
		echo "<br>";	
		if($this->archivo_php->existe()){
			ei_separador("ARCHIVO: ". $this->archivo_php->nombre());
			$this->archivo_php->incluir();
			$this->clase_php->analizar();
		}
	}	
	
}


?>