<?
require_once("nucleo/componentes/interface/toba_ei_cuadro.php");

class extension_seleccion_evt extends toba_ei_cuadro
{
	/**
		El evento seleccion es solo para las localidades que
		tienen mas de 1000 habitantes.
	*/

	function conf_evt__seleccion($evento, $fila)
	{
		if( !($this->datos[$fila]['hab_total']>1000)) {
			$evento->anular();
		}
	}

	function conf_evt__eliminar($evento, $fila)
	{
		if( !($this->datos[$fila]['hab_varones']< $this->datos[$fila]['hab_mujeres']) ) {
			$evento->anular();	
		}
	}
}
?>