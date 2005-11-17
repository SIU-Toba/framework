<?php
require_once('nucleo/browser/clases/objeto_ci.php');
require_once('nucleo/lib/reflexion/archivo_php.php');
require_once('nucleo/lib/reflexion/clase_php.php');

class ci_editor_php extends objeto_ci
{
	protected $datos;
	protected $archivo_php;
	protected $clase_php;
	protected $meta_clase;	//Al CI le sirve para contextualizar el FORM de opciones

	//Desde la accion se deben suministrar los datos de la extension sobre la que se esta trabajando
	function set_datos($datos)
	{
		$this->datos = $datos;
		$clase = $this->datos['subclase'];
		$archivo = $this->datos['archivo'];
		$padre_clase = $this->datos['clase'];
		$padre_archivo = $this->datos['clase_archivo'];
		//- 1 - Obtengo la clase INFO del compomente que se selecciono.
		require_once($padre_archivo);
		if (class_exists($padre_clase)) {
			$clase_info = call_user_func(array($padre_clase, 'elemento_toba'));
			$clase_info->cargar_db($this->datos['proyecto'], $this->datos['objeto']);		
		}else{
			throw new exception_toba('Error: no es posible acceder a los METADATOS del componente seleccionado');
		}
		//- 2 - Controlo si tengo que mostrar el componente o un SUBCOMPONENTE.
		// Este mecanismo no es optimo... hay que pensarlo bien.
		$subcomponente = toba::get_hilo()->obtener_parametro('subcomponente');
		if ($subcomponente) {
			$mts = $clase_info->get_metadatos_subcomponente($subcomponente);
			if($mts){
				$clase = $mts['clase'];
				$archivo = $mts['archivo'];
				$padre_clase = $mts['padre_clase'];
				$padre_archivo = $mts['padre_archivo'];
				$this->meta_clase = $mts['meta_clase'];
			}else{
				throw new exception_toba('ERROR cargando el SUBCOMPONENTE: El subcomponente esta declarado pero su metaclase no existe.');			
			}
		}else{
			//La metaclase del componente es su CLASE INFO
			$this->meta_clase = $clase_info;
		}
		//- 3 - Creo el archivo_php y la clase_php que quiero mostrar
		$path = toba::get_hilo()->obtener_proyecto_path() . "/php/" . $archivo;
		$this->archivo_php = new archivo_php($path);
		$this->clase_php = new clase_php($clase, $this->archivo_php, $padre_clase, $padre_archivo);
		$this->clase_php->set_meta_clase($this->meta_clase);
		//- 4 - Se escucha el hilo para saber si se pidio algun evento desde afuera
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