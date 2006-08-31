<?php

/**
* Error interno de toba
*/
class toba_excepcion extends Exception
{

}

/**
* Excepci�n ejecutando SQL contra la DB
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
* Excepci�n producida por alguna interacci�n del usuario
*/
class toba_excepcion_usuario extends toba_excepcion
{

}

/**
* Excepci�n producida por error del la definicion en el desarrollo
*/
class toba_excepcion_def extends toba_excepcion
{

}

/**
 * Excepci�n producida cuando el usuario no tiene permitido alg�n derecho
 */
class toba_excepcion_permisos extends toba_excepcion 
{
	
}

/**
 * Excepci�n producida por un login incorrecto
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
 * Excepci�n para recargar una solicitud
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