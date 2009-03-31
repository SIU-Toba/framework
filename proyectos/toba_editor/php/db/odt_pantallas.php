<?php

class odt_pantallas extends toba_datos_tabla
{
	function get_ids_pantallas()
	{
		$pantallas = array();
		$filas = $this->get_filas(null, true);		
		foreach ($filas as $id => $pantalla) {
			$pantallas[] = $pantalla['identificador'];
		}
		return $pantallas;
	}
}
?>