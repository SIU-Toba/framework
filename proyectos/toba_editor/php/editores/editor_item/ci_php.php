<?php 
require_once('lib/reflexion/archivo_php.php');

class ci_php extends toba_ci
{
	//-------------------------------------------------------------------
	//--- INICIALIZACION
	//-------------------------------------------------------------------

	/**
	* Determino el archivo sobre el que se voy a trabajar
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
			toba::logger()->var_dump($datos);
			if(!isset($datos)){
				throw new toba_error('No es posible definir cual es el archivo a editar');	
			}
			//- 1 - Obtengo la clase INFO del compomente que se selecciono.
			$clave_componente = array( 'componente'=>$datos['item'], 'proyecto'=>$datos['proyecto'] );		
			$meta_clase = toba_constructor::get_info( $clave_componente, 'item');
			//Si el componente no tiene definida una subclase, no tiene sentido estar aca.
			if (!$datos['actividad_accion']) {
				throw new toba_error('El item no tiene un archivo php definido');	
			}			
			//- 3 - Creo el archivo_php y la clase_php que quiero mostrar
			$path = toba_instancia::get_path_proyecto(toba_editor::get_proyecto_cargado()) . "/php/" . $datos['actividad_accion'];
			$this->archivo_php = new archivo_php($path);
		}
	}
	
	function archivo_php()
	{
		return $this->archivo_php;
	}		
	
	//-------------------------------------------------------------------------------
	//-- Apertura general de archivos  ----------------------------------------------
	//-------------------------------------------------------------------------------

	function servicio__ejecutar()
	{ 
		$this->abrir_archivo();
	}
	
	function abrir_archivo()
	{
		if( !$this->archivo_php->existe() ) {
			throw new toba_error('Se solicito la apertura de un archivo inexistente (\'' . $this->archivo_php->nombre() . '\').');	
		}
		$this->archivo_php->abrir();		
	}	
	
}


/*********************************************************************************************/

class pantalla_codigo extends toba_ei_pantalla 
{
	function generar_layout()
	{
		ei_separador("ARCHIVO: ". $this->controlador->archivo_php()->nombre());
		echo "<div style='overflow: auto; height: 420px; width: 550px; padding: 5px; text-align:left; background-color: #ffffff; font-size: 11px;'>";
		$this->controlador->archivo_php()->mostrar();
	}
}

?>