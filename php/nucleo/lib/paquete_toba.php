<?
require_once("nucleo/lib/elemento_toba.php");

class paquete_toba
/*
	Unidad de intercambio entre instancias toba.
	Falta:
	
		- un resumen de los elementos que tiene.
		- una forma de ir desarrollando un paquete para despues publicarlo
			una especie de editor de paquetes. Hay que guardarlos en la base.
			(En algun punto hay que firmarlos... spoofing...)
*/
{
	protected $elementos = array();
	protected $descripcion;

	function __construct()
	{
	}
	
	//------- Interface con quien crea el paquete
	
	function set_descripcion($descripcion)
	{
		$this->descripcion = $descripcion;
	}
	
	function agregar_elemento($elemento)
	{
		$this->elementos[] = $elemento;
	}
	
	//------- Interface con el importador

	function get_descripcion()
	{
		
	}

	function get_elementos()
	{
		return $this->elementos;	
	}
}
?>