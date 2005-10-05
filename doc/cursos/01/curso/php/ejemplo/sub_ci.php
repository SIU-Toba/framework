<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
//----------------------------------------------------------------
class sub_ci extends objeto_ci
{
	function get_pantalla_actual()
	{
		return parent::get_pantalla_actual();
		//$this->controlador
		//return "chau";	
	}
	
	function evt__salida__hola()
	{
		//throw new excepcion_curso("No sale de hola");
		toba::get_cola_mensajes()->agregar("Sali de HOLA");
	}
	
	function evt__entrada__chau()
	{
	}

}

?>