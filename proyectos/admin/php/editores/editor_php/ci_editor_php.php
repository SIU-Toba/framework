<?php
require_once('nucleo/browser/clases/objeto_ci.php');
require_once('lib/reflexion/archivo_php.php');
require_once('lib/reflexion/clase_php.php');

class ci_editor_php extends objeto_ci
{
	protected $datos;
	protected $archivo_php;
	protected $clase_php;
	protected $meta_clase;	//Al CI le sirve para contextualizar el FORM de opciones
	protected $subcomponente;

	function evt__inicializar()
	{
		$this->set_datos(toba::get_solicitud()->zona()->editable_info);		
	}
	
	function mantener_estado_sesion()
	{
		$props = parent::mantener_estado_sesion();
		$props[] = 'subcomponente';
		return $props;
	}

	
	/**
	 * Desde la accion se deben suministrar los datos de la extension sobre la que se esta trabajando
	 * @todo El path absoluto del proyecto se esta hardcoreando con /proyectos/
	 */
	function set_datos($datos)
	{
		$this->datos = $datos;
		//- 1 - Obtengo la clase INFO del compomente que se selecciono.
		require_once($this->datos['clase_archivo']);
		if (class_exists($this->datos['clase'])) {
			$clave = array( 'componente'=>$this->datos['objeto'], 'proyecto'=>$this->datos['proyecto'] );		
			$clase_info = constructor_toba::get_info( $clave, $this->datos['clase']);
		}else{
			throw new exception_toba('Error: no es posible acceder a los METADATOS del componente seleccionado');
		}
		//- 2 - Controlo si tengo que mostrar el componente o un SUBCOMPONENTE.
		/* Este mecanismo no es optimo... hay que pensarlo bien.
			Se inagura el caso de que un objeto contenga una clase que no sea un objeto.
		*/
		if(isset($this->subcomponente)){
			//Cargue un subcomponente en un request anterior.
			$subcomponente = $this->subcomponente;
		}else{
			$subcomponente = toba::get_hilo()->obtener_parametro('subcomponente');
		}
		if ($subcomponente) {
			$mts = $clase_info->get_metadatos_subcomponente($subcomponente);
			if($mts){
				$this->subcomponente = $subcomponente;
				$this->datos['subclase'] = $mts['clase'];
				$this->datos['archivo'] = $mts['archivo'];
				$this->datos['clase'] = $mts['padre_clase'];
				$this->datos['clase_archivo'] = $mts['padre_archivo'];
				$this->meta_clase = $mts['meta_clase'];
			}else{
				throw new exception_toba('ERROR cargando el SUBCOMPONENTE: El subcomponente esta declarado pero su metaclase no existe.');			
			}
		}else{
			//La metaclase del componente es su CLASE INFO
			$this->meta_clase = $clase_info;
		}
		//- 3 - Creo el archivo_php y la clase_php que quiero mostrar
		$path = toba_dir(). "/proyectos/". editor::get_proyecto_cargado() . "/php/" . $this->datos['archivo'];
		$this->archivo_php = new archivo_php($path);
		$this->clase_php = new clase_php($this->datos['subclase'], $this->archivo_php, $this->datos['clase'], $this->datos['clase_archivo']);
		$this->clase_php->set_meta_clase($this->meta_clase);
	}
	
	//--- EVENTOS
	function get_lista_eventos(){
		$eventos = parent::get_lista_eventos();
		if($this->archivo_php->existe()) {
			unset($eventos['crear_archivo']);
		} else {
			unset($eventos['abrir']);
		}
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
	

	/**
	 * Servicio de ejecución externo
	 */
	function servicio__ejecutar()
	{ 
		$this->evt__abrir();
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