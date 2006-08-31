<?php
require_once('nucleo/componentes/interface/toba_ci.php');
require_once('lib/reflexion/archivo_php.php');
require_once('lib/reflexion/clase_php.php');

class ci_editor_php extends toba_ci
{
	protected $datos;
	protected $archivo_php;
	protected $clase_php;
	protected $meta_clase;	//Al CI le sirve para contextualizar el FORM de opciones
	protected $subcomponente;

	function ini()
	{
		$this->set_datos(toba::get_zona()->get_info());		
	}
	
	function mantener_estado_sesion()
	{
		$props = parent::mantener_estado_sesion();
		$props[] = 'subcomponente';
		return $props;
	}
	
	function conf()
	{
		if (isset($this->archivo_php) && $this->archivo_php->existe()) {
			if (! $this->archivo_php->contiene_clase($this->datos['subclase'])) {
				$this->pantalla()->agregar_dep('subclase');
			}
			$this->pantalla()->eliminar_evento('crear_archivo');			
		} else {
			$this->pantalla()->eliminar_evento('abrir');	
		}
	}
	
	
	/**
	 * Desde la accion se deben suministrar los datos de la extension sobre la que se esta trabajando
	 */
	function set_datos($datos)
	{
		$this->datos = $datos;
		//- 1 - Obtengo la clase INFO del compomente que se selecciono.
		
//		require_once($this->datos['clase_archivo']);
//		if (class_exists($this->datos['clase'])) {
			$clave = array( 'componente'=>$this->datos['objeto'], 'proyecto'=>$this->datos['proyecto'] );		
			$clase_info = constructor_toba::get_info( $clave, $this->datos['clase']);
/*		}else{
			throw new toba_excepcion('Error: no es posible acceder a los METADATOS del componente seleccionado');
		}*/
		
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
				throw new toba_excepcion('ERROR cargando el SUBCOMPONENTE: El subcomponente esta declarado pero su metaclase no existe.');
			}
		}else{
			//La metaclase del componente es su CLASE INFO
			$this->meta_clase = $clase_info;
		}
		//- 3 - Creo el archivo_php y la clase_php que quiero mostrar
		$path = toba_instancia::get_path_proyecto(toba_editor::get_proyecto_cargado()) . "/php/" . $this->datos['archivo'];
		$this->archivo_php = new archivo_php($path);
		$this->clase_php = new clase_php($this->datos['subclase'], $this->archivo_php, $this->datos['clase'], $this->datos['clase_archivo']);
		$this->clase_php->set_meta_clase($this->meta_clase);
	}
	

	function evt__abrir()
	{
		$arch = toba::get_hilo()->obtener_parametro('archivo');
		if (isset($arch)) {
			$path_proyecto = toba_instancia::get_path_proyecto(toba_editor::get_proyecto_cargado()) . "/php/";
			$arch =  $path_proyecto . $arch;
			$this->archivo_php = new archivo_php($arch);	
		}
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
	
	function archivo_php()
	{
		return $this->archivo_php;
	}	
	
	function clase_php()
	{
		return $this->clase_php;	
	}
	
}

class pantalla_codigo extends toba_ei_pantalla 
{
	function archivo_php()
	{
		return $this->controlador->archivo_php();	
	}
	
	//--- Archivo Plano	
	function generar_html_dependencias()
	{
		parent::generar_html_dependencias();
		echo "<br>";
		if($this->archivo_php()->existe()){
			ei_separador("ARCHIVO: ". $this->archivo_php()->nombre());
			echo "<div style='overflow: auto; height: 420px; width: 550px; padding: 5px; text-align:left; background-color: #ffffff; font-size: 11px;'>";
			$this->archivo_php()->mostrar();
			echo "</div>";
		}
	}	
}


class pantalla_analisis extends toba_ei_pantalla 
{
	function archivo_php()
	{
		return $this->controlador->archivo_php();	
	}	

	function generar_html_dependencias()
	{
		parent::generar_html_dependencias();
		echo "<br>";	
		if($this->archivo_php()->existe()){
			ei_separador("ARCHIVO: ". $this->archivo_php()->nombre());
			$this->archivo_php()->incluir();
			$this->controlador->clase_php()->analizar();
		}
	}	
}
?>