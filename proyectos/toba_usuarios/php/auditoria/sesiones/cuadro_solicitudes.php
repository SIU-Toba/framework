<?php 
class cuadro_solicitudes extends toba_ei_cuadro
{
	function conf_evt__obs($evento, $fila)
	{
		if (!($this->datos[$fila]['observaciones'] > 0)) {
			$evento->anular();	
		}
	}
}

?>