<?php 
class cuadro_sesiones extends toba_ei_cuadro
{
	//---- Config. EVENTOS sobre fila ---------------------------------------------------

	function conf_evt__seleccion($evento, $fila)
	{
		if (!($this->datos[$fila]['solicitudes'] > 0)) {
			$evento->anular();	
		}
	}
}

?>