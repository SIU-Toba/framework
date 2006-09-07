<?php
/**
 * Excepciones particulares disparadas dentro del n�cleo
 * @package Nucleo
 * @subpackage Errores
 */

/**
* Error interno de toba
*/
class toba_error extends Exception
{

}

/**
* Excepci�n ejecutando SQL contra la DB
*/
class toba_error_db extends toba_error
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
class toba_error_usuario extends toba_error
{

}

/**
* Excepci�n producida por error del la definicion en el desarrollo
*/
class toba_error_def extends toba_error
{

}

/**
 * Excepci�n producida cuando el usuario no tiene permitido alg�n derecho
 */
class toba_error_permisos extends toba_error 
{
	
}

/**
 * Excepci�n producida por un login incorrecto
 */
class toba_error_login extends toba_error
{

}


class toba_error_validacion extends toba_error 
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
class toba_reset_nucleo extends Exception
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