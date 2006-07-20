<?php 
require_once('ci_principal.php');

class ci_carpetas extends ci_principal
{
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		//$propiedades[] = 'propiedad_a_persistir';
		return $propiedades;
	}
	
	
	function evt__prop_basicas__modificacion($registro)
	{
		$registro['carpeta'] = 1;
		$this->get_entidad()->tabla("base")->set($registro);
	}
}

?>