<?
require_once("nucleo/browser/clases/objeto_ei_cuadro.php");

class extension_seleccion_evt extends objeto_ei_cuadro
{
	/**
		Aca se filtra completamente un evento
	*/
	function get_lista_eventos()
	{
		return parent::get_lista_eventos();	
	}

	/**
		El evento seleccion es solo para las localidades que
		tienen mas de 1000 habitantes.
	*/

	function filtrar_evt__seleccion($fila)
	{
		if($this->datos[$fila]['hab_total']>1000) return true;
		return false;
	}

	function filtrar_evt__eliminar($fila)
	{
		if($this->datos[$fila]['hab_varones']<
			$this->datos[$fila]['hab_mujeres']) return true;
		return false;
	}
}
?>