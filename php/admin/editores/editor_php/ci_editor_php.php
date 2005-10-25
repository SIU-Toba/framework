<?php
require_once('nucleo/browser/clases/objeto_ci.php');
require_once('nucleo/lib/reflexion/archivo_php.php');
require_once('nucleo/lib/reflexion/clase_php.php');

class ci_editor_php extends objeto_ci
{
	protected $datos;
	protected $archivo_php;

	//Desde la accion se deben suministrar los datos de la extension sobre la que se esta trabajando
	function set_datos($datos)
	{
		$this->datos = $datos;
		$archivo = $this->datos['archivo'];
		$proyecto = $this->datos['proyecto'];
		$path = toba::get_hilo()->obtener_proyecto_path() . "/php/" . $archivo;
			
		//Manejo de archivos			
		$this->archivo_php = new archivo_php($path);
		
		//Manejo de clases
		$this->clase_php = new clase_php(	$this->datos['subclase'], $this->archivo_php, 
											$this->datos['clase'], $this->datos['clase_archivo']);
		$this->clase_php->set_objeto($this->datos['proyecto'], $this->datos['objeto']);
		
		//Se escucha el hilo para saber si se pidio algun evento desde afuera
		$evento = toba::get_hilo()->obtener_parametro("evento");
		if ($evento == 'abrir') {
			$this->evt__abrir();
		}
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
			echo "<div style='overflow: auto; height: 420px; width: 550px; padding: 5px; text-align:left; background-color: #ffffff; font-size: 11px;'>";
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