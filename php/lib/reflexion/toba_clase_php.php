<?php
require_once('toba_archivo_php.php');
/**
*	Representa a la EDICION de una CLASE del ambiente. 
*	Tiene capacidades de generacion y analisis MOLDES aportados por la METACLASE del compone
*/
class toba_clase_php
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
		if ($this->archivo->esta_vacio()) {
			$this->archivo->crear_basico();
		}
		$this->archivo->edicion_inicio();
		$this->archivo->insertar_al_final( $this->get_codigo($opciones) );
		$this->archivo->edicion_fin();
	}

	function get_codigo($opciones)
	{
		$molde_clase = $this->meta_clase->get_molde_subclase();
		return $molde_clase->get_codigo($opciones);
	}
	
	//---------------------------------------------------------------
	//-- Analisis de codigo
	//---------------------------------------------------------------
	
	function analizar()
	{
		toba_editor::iniciar_contexto_proyecto_cargado();
		require_once($this->meta_clase->get_clase_archivo());
		$this->archivo->incluir();		
		try {
			$molde = $this->meta_clase->get_molde_subclase();
			$molde->set_muestra_analisis(new ReflectionClass($this->nombre()));
			return $molde->get_analisis();
		} catch (Exception $e) {
			echo ei_mensaje("No se encuentra la clase ".$this->nombre()." en este archivo.", "error");
		}
	}	
}		
?>