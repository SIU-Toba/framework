<?php
/**
 * Excepciones particulares disparadas dentro del núcleo
 * @package Centrales
 * @subpackage Errores
 */

/**
* Error interno de toba
*/
class toba_error extends Exception
{

}

/**
* Excepción ejecutando SQL contra la DB
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
* Excepción producida por alguna interacción del usuario
*/
class toba_error_usuario extends toba_error
{

}

/**
* Excepción producida por error del la definicion en el desarrollo
*/
class toba_error_def extends toba_error
{

}

/**
 * Excepción producida cuando el usuario no tiene permitido algún derecho
 */
class toba_error_permisos extends toba_error 
{
	
}

/**
 * Excepción producida por un error en la autenticacion del usuario
 */
class toba_error_autenticacion extends toba_error
{

}

/**
 * Excepción producida por un error en la autorizacion del usuario
 */
class toba_error_autorizacion extends toba_error
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
 * Excepción producida cuando falla la incializacion predefinida de una sesion
 */
class toba_error_ini_sesion extends toba_error
{
	
}

/**
 * Excepción para recargar una solicitud
 * es necesario que el item donde se ejecute esta excepcion este marcado como "recar
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