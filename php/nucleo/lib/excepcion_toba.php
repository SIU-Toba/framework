<?php

/**
* Error interno de toba
*/
class excepcion_toba extends Exception
{

}

/**
* Excepci�n producida en tiempo de ejecuci�n ejecutando SQL contra la DB
*/
class excepcion_toba_db extends Exception
{
	function get_sqlstate()
	{
		return $this->getCode();	
	}
}

/**
* Excepci�n producida en tiempo de ejecuci�n por alguna interacci�n del usuario
*/
class excepcion_toba_usuario extends excepcion_toba
{

}

/**
* Excepci�n producida en tiempo de definici�n producidas por error del desarrollo
*/
class excepcion_toba_def extends excepcion_toba
{

}

/**
 * Excepci�n producida en tiempo de ejecuci�n cuando el usuario no tiene permitido alg�n derecho
 */
class excepcion_toba_permisos extends excepcion_toba 
{
	
}

/**
 * Excepci�n producida por un login incorrecto
 */
class excepcion_toba_login extends excepcion_toba
{

}


class excepcion_toba_validacion extends excepcion_toba 
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