<?php

class odt_eventos extends toba_datos_tabla
{
	function hay_evento_de_fila()
	{
		foreach ($this->get_filas(null, false, false) as $fila) {
			if ($fila['sobre_fila']) {
				return true;
			}			
		}
		return false;
	}
	
	function hay_evento_implicito_maneja_datos()
	{
		foreach ($this->get_filas(null, false, false) as $fila) {
			if ($fila['implicito'] && $fila['maneja_datos']) {
				return true;
			}
		}
		return false;
	}
	
	function hay_evento_maneja_datos()
	{
		foreach ($this->get_filas(null, false, false) as $fila) {
			if ($fila['maneja_datos']) {
				return true;
			}
		}
		return false;
	}
}
?>
