<?php

/**
 * La fuente de datos encapsula un mecanismo de entrada/salida de datos, típicamente una base relacional
 * Esta clase contiene ventanas antes y despues de la conexión de la fuente y permite acceder al objeto db 
 * que es el que tiene el API de consultas/comandos
 * 
 * @package Fuentes
 */
class toba_fuente_datos
{
	protected $definicion;
	protected $db;
	
	function __construct($definicion)
	{
		$this->definicion = $definicion;
	}
	
	/**
	 * Accede al objeto db que tiene el API para consultas/comandos sobre la fuente
	 * @return toba_db
	 */
	function get_db()
	{
		if (!isset($this->db)) {
			$this->pre_conectar();
			$this->db = toba_dba::get_db($this->definicion['instancia_id']);
			$this->post_conectar();
		}
		return $this->db;
	}
	
	/**
	*	Ventana para personalizar las acciones previas a la conexión
	* @ventana
	*/
	function pre_conectar() {}
	
	/**
	* Ventana para personalizar las acciones posteriores a la conexión
	* @ventana
	*/
	function post_conectar() {}
}
?>