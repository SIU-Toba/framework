<?php 
class cuadro_visor_columnas extends toba_ei_cuadro
{
	//---- Config. EVENTOS sobre fila ---------------------------------------------------

	function conf_evt__seleccion($evento, $fila)
	{
		if ($this->datos[$fila]['fk_tabla'] == '') {
			$evento->anular();
		}
	}
}

?>