<?php

class cuadro_actividad_local extends toba_ei_cuadro
{
	function conf_evt__seleccion($evento, $fila)
	{
		$evento->vinculo()->set_item($this->datos[$fila]['editor_proyecto'], $this->datos[$fila]['editor_item']);
		$evento->vinculo()->agregar_opcion('menu', true);
		$evento->vinculo()->set_parametros(array( apex_hilo_qs_zona => $this->datos[$fila]['componente_proyecto'] 
										. apex_qs_separador . $this->datos[$fila]['componente_id']));
																				
	}
}
?>