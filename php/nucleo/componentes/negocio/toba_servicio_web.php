<?php
/**
 * @package Componentes
 * @subpackage Negocio
 */
abstract class toba_servicio_web extends toba_componente
{

	final function __construct($id)
	{
		parent::__construct($id);
		// Cargo las dependencias
		foreach( $this->_lista_dependencias as $dep){
			$this->cargar_dependencia($dep);
			$this->_dependencias[$dep]->set_controlador($this, $dep);
			$this->dep($dep)->inicializar();
		}		
	}

	function get_opciones()
	{
		return array();
	}
	
	
	/**
	 * Rutea WSF hacia la extensión
	 */
	function __call($nombre, $argumentos)
	{
		$metodo = substr($nombre, 1);
		$mensaje_entrada = new toba_servicio_web_mensaje($argumentos[0]);
		$mensaje_salida = $this->$metodo($mensaje_entrada);
		return $mensaje_salida->wsf();
	}


}

?>