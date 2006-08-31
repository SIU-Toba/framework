<?php

/**
* Error interno de toba
*/
class toba_excepcion extends Exception
{

}

/**
* Excepción ejecutando SQL contra la DB
*/
class toba_excepcion_db extends toba_excepcion
{
	protected $codigo;

	function __construct($mensaje, $codigo)
	{
		$this->codigo = $codigo;
		parent::__construct($mensaje);			
	}
	
	function get_sqlstate()
	{
		return $this->codigo;	
	}
}

/**
* Excepción producida por alguna interacción del usuario
*/
class toba_excepcion_usuario extends toba_excepcion
{

}

/**
* Excepción producida por error del la definicion en el desarrollo
*/
class toba_excepcion_def extends toba_excepcion
{

}

/**
 * Excepción producida cuando el usuario no tiene permitido algún derecho
 */
class toba_excepcion_permisos extends toba_excepcion 
{
	
}

/**
 * Excepción producida por un login incorrecto
 */
class toba_excepcion_login extends toba_excepcion
{

}


class toba_excepcion_validacion extends toba_excepcion 
{
	protected $causante;
	
	function __construct($mensaje, $causante=null)
	{
		$this->causante = $causante;
		parent::__construct($mensaje);
	}	
	
	function get_causante()
	{
		return $this->causante;	
	}
}

/**
 * Excepción para recargar una solicitud
 */
class excepcion_reset_nucleo extends Exception
{
	private $item = null;

	function set_item($item)
	{
		$this->item = $item;
	}
	
	function get_item()
	{
		return $this->item;
	}
}
?>