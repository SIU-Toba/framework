<?php 
require_once('editores/editor_item/ci_principal.php');

class ci_carpetas extends ci_principal
{
	function evt__prop_basicas__modificacion($registro)
	{
		$registro['carpeta'] = 1;
		$this->get_entidad()->tabla('base')->set($registro);
	}

	function conf()
	{
		if (! $this->get_entidad()->esta_cargada()) {
			$this->pantalla()->eliminar_evento('eliminar');
		}
	}
}

?>