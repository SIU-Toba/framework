<?php

/**
 * La fuente de datos encapsula un mecanismo de entrada/salida de datos, típicamente una base relacional
 * Esta clase contiene ventanas antes y despues de la conexión de la fuente
 * 
 * @package Librerias
 * @subpackage Fuentes
 */
class toba_fuente_datos
{
	protected $definicion;
	protected $db;
	
	function __construct($definicion)
	{
		$this->definicion = $definicion;
	}
	
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
	*/
	function pre_conectar() {}
	
	/**
	*	Ventana para personalizar las acciones posteriores a la conexión
	*/
	function post_conectar() {}
}
?>