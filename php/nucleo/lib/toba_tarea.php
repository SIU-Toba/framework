<?php

/**
 * Interface que deben respetar aquellas clases que deseen incluirse en el planificador de tareas
 * Tener en cuenta que luego de programar una tarea, la clase no puede cambiar en su implementación debido a que la tarea se serializa en la base
 * Si la clase del objeto cambia entre la serialización y la ejecución, la deserialización de la base tirara un error fatal
 * @package Centrales
 */
interface toba_tarea
{
	function ejecutar();	
}

/**
 * Tarea generica que incluye y ejecuta un método específico de una clase
 * @package Centrales 
 */
class toba_tarea_php implements toba_tarea
{
	protected $clase;
	protected $archivo;
	protected $metodo;
	protected $parametros = array();
	
	function __construct($clase, $archivo_php=null)
	{
		$this->clase = $clase;
		$this->archivo = $archivo_php;
	}
	
	function ejecutar()
	{
		if (isset($this->archivo)) {
			require_once($this->archivo);
		}
		if (isset($this->clase)) {
			//-- Es un metodo de una clase
			call_user_func_array(array($this->clase, $this->metodo), $this->parametros);			
		} else {
			//-- Es una funcion
			call_user_func_array($this->metodo, $this->parametros);			
		}
	}
	
	function set_metodo($metodo)
	{
		$this->metodo = $metodo;
		$parametros = func_get_args();
		array_splice($parametros, 0 , 1);
		$this->parametros = $parametros;
	}	
	
}


?>