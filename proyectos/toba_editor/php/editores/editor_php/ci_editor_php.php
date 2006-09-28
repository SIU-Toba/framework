<?php
require_once('nucleo/componentes/interface/toba_ci.php');
require_once('lib/reflexion/archivo_php.php');
require_once('lib/reflexion/clase_php.php');

/**
*	Este CI tiene dos objetivos: crear, mostrar y analizar subclases del framework
*		y abrirlos en editor externo (como evento o mediante el servicio ejecutar)
*/
class ci_editor_php extends toba_ci
{
	protected $archivo_php;			
	protected $clase_php;			
	protected $s__subcomponente;

	/**
	* Determino el archivo sobre el que se voy a trabajar.
	*/
	function ini()
	{
		$archivo = toba::memoria()->get_parametro('archivo');
		if (isset($archivo)) {	//********* Se indico un archivo especifico por GET
			$path_proyecto = toba_instancia::get_path_proyecto(toba_editor::get_proyecto_cargado()) . "/php/";
			$archivo =  $path_proyecto . $archivo;
			$this->archivo_php = new archivo_php($archivo);	
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
			$path = toba_instancia::get_path_proyecto(toba_editor::get_proyecto_cargado()) . "/php/" . $subclase_archivo;
			$this->archivo_php = new archivo_php($path);
			$this->clase_php = new clase_php($this->archivo_php, $meta_clase);
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
			$this->pantalla()->eliminar_tab(1);
			$this->pantalla()->eliminar_tab(2);
			$this->dep('subclase')->eliminar_evento('crear_clase');
		} else {
			if ( ! $this->archivo_php->contiene_clase( $this->clase_php->nombre() ) ) {
				// No existe la clase
				$this->pantalla()->eliminar_tab(1);
				$this->pantalla()->eliminar_tab(2);
				$this->dep('subclase')->eliminar_evento('crear_archivo');
			} else {
				$this->pantalla()->eliminar_tab(0);
			}
		}
	}
	
	function evt__subclase__previsualizar($opciones)
	{
		$php = $this->clase_php->get_codigo($opciones['metodos']);
		$this->pantalla()->set_codigo($php);
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