<?php
require_once('nucleo/componentes/interface/toba_ci.php');
require_once('lib/reflexion/toba_archivo_php.php');
require_once('lib/reflexion/toba_clase_php.php');

/**
*	Este CI tiene dos objetivos: crear, mostrar y analizar subclases del framework
*		y abrirlos en editor externo (como evento o mediante el servicio ejecutar)
*/
class ci_editor_php extends toba_ci
{
	protected $archivo_php;			
	protected $clase_php;			
	protected $meta_clase;
	protected $s__subcomponente;
	protected $opciones_previsualizacion;
	protected $previsualizacion = '';

	/**
	* Determino el archivo sobre el que se voy a trabajar.
	*/
	function ini()
	{
		$archivo = toba::memoria()->get_parametro('archivo');
		if (isset($archivo)) {	//********* Se indico un archivo especifico por GET
			$path_proyecto = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()) . "/php/";
			$archivo =  $path_proyecto . $archivo;
			$this->archivo_php = new toba_archivo_php($archivo);	
		} else {				//********* Se accedio a un componente a travez de su ZONA
			$datos = toba::zona()->get_info();
			if(!isset($datos)){
				throw new toba_error('No es posible definir cual es el archivo a editar');	
			}
			//- 1 - Obtengo la METACLASE correspondiente
			$clave_componente = array( 'componente'=>$datos['objeto'], 'proyecto'=>$datos['proyecto'] );		
			$info_componente = toba_constructor::get_info( $clave_componente, $datos['clase']);
			// Acceso a un SUBCOMPONENTE????
			if(isset($this->s__subcomponente)){ //Cargue un subcomponente en un request anterior.
				$subcomponente = $this->s__subcomponente;
			}else{
				$subcomponente = toba::memoria()->get_parametro('subcomponente');
			}
			if (isset($subcomponente)) {
				$meta_clase = $info_componente->get_metaclase_subcomponente($subcomponente);
				if($meta_clase){
					$this->s__subcomponente = $subcomponente;
				}else{
					throw new toba_error('ERROR cargando el SUBCOMPONENTE: No es posible acceder a la definicion del mismo.');
				}
			}else{
				//La metaclase del componente es su CLASE INFO
				$meta_clase = $info_componente;
			}
			// - 2 - Creo el editor de clases y el archivo.
			$subclase_archivo = $meta_clase->get_subclase_archivo();
			$subclase_nombre = $meta_clase->get_subclase_nombre();
			if(!$subclase_archivo || !$subclase_nombre){
				throw new toba_error('El componente no tiene una subclase definida');	
			}			
			$path = toba::instancia()->get_path_proyecto(toba_editor::get_proyecto_cargado()) . "/php/" . $subclase_archivo;
			$this->archivo_php = new toba_archivo_php($path);
			$this->clase_php = new toba_clase_php($this->archivo_php, $meta_clase);
			$this->meta_clase = $meta_clase;
		}
	}

	function archivo_php()
	{
		return $this->archivo_php;
	}	
	
	function clase_php()
	{
		return $this->clase_php;	
	}
	
	function get_previsualizacion()
	{
		return $this->previsualizacion;	
	}
	
	//---  ACCIONES POSIBLES ------------------------------------------------------

	function abrir_archivo()
	{
		if( !$this->archivo_php->existe() ) {
			throw new toba_error('Se solicito la apertura de un archivo inexistente (\'' . $this->archivo_php->nombre() . '\').');	
		}
		$this->archivo_php->abrir();		
	}
	
	function crear_archivo()
	{
		$this->archivo_php->crear_basico();
	}
	
	function crear_subclase($opciones)
	{
		$this->clase_php->generar($opciones);
	}

	//-------------------------------------------------------------------------------
	//-- Apertura de archivos por AJAX ----------------------------------------------
	//-------------------------------------------------------------------------------

	function servicio__ejecutar()
	{ 
		$this->abrir_archivo();
	}

	//-------------------------------------------------------------------------------
	//-- Interface grafica del EDITOR -----------------------------------------------
	//-------------------------------------------------------------------------------
		
	function conf()
	{
		if ( ! $this->archivo_php->existe() ) {
			$this->set_pantalla(0);
			$this->pantalla()->eliminar_tab(1);
			$this->pantalla()->eliminar_tab(2);
			$this->dep('subclase')->eliminar_evento('crear_clase');
		} else {
			if ( ! $this->archivo_php->contiene_clase( $this->clase_php->nombre() ) ) {
				// No existe la clase
				$this->set_pantalla(0);
				$this->pantalla()->eliminar_tab(1);
				$this->pantalla()->eliminar_tab(2);
				$this->dep('subclase')->eliminar_evento('crear_archivo');
			} else {
				$this->pantalla()->eliminar_tab(0);
			}
		}
		if( $this->s__subcomponente ) {
			$desc = 'SUBCOMPONENTE: ' . $this->meta_clase->get_descripcion_subcomponente();
			$this->pantalla()->set_descripcion($desc);
		}
	}
	
	function evt__subclase__previsualizar($opciones)
	{
		$codigo = "<?" . salto_linea() . $this->clase_php->get_codigo($opciones['metodos']) . "?>" . salto_linea() ;
		require_once("3ros/PHP_Highlight.php");
		$h = new PHP_Highlight(false);
		$h->loadString($codigo);
		$formato_linea = "<span style='background-color:#D4D0C8; color: black; font-size: 10px;".
						" padding-top: 2px; padding-right: 2px; margin-left: -4px; width: 20px; text-align: right;'>".
						"%2d</span>&nbsp;&nbsp;";
		$this->previsualizacion = $h->toHtml(true, true, $formato_linea, true);
		$this->opciones_previsualizacion = $opciones;
		if(count($this->clase_php->get_lista_metodos_posibles())>5) {
			$this->dep('subclase')->colapsar();
		}
	}
	
	function evt__subclase__crear_archivo($opciones)
	{
		$this->crear_archivo();
		$this->crear_subclase($opciones['metodos']);
		$this->set_pantalla(1);
	}
	
	function evt__subclase__crear_clase($opciones)
	{
		$this->crear_subclase($opciones['metodos']);
		$this->set_pantalla(1);
	}

	function conf__subclase($obj)
	{
		if(isset($this->opciones_previsualizacion)) {
			$obj->set_datos($this->opciones_previsualizacion);
		}		
	}

	function get_lista_metodos()
	{
		return $this->clase_php->get_lista_metodos_posibles();
	}

	function evt__abrir()
	{
		$this->abrir_archivo();
	}
}
?>