<?php

class odt_eventos extends toba_datos_tabla
{
	function configuracion()
	{
		$this->set_no_duplicado(array('identificador'));
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
