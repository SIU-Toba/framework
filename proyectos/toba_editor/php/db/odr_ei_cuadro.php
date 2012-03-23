<?php

class odr_ei_cuadro extends toba_datos_relacion
{
	/**
	*	Si hay un evento sobre_fila hay que validar que haya una clave definida
	*/
	function evt__validar()
	{
		$hay_sobre_fila = false;
		foreach ($this->tabla('eventos')->get_filas() as $evt) {
			if (isset($evt['sobre_fila']) && $evt['sobre_fila'] == 1) {
				$hay_sobre_fila = true;
				break;
			}
		}
		if ($hay_sobre_fila) {
			$datos = $this->tabla('prop_basicas')->get();
			if (!isset($datos['clave_dbr']) && ( !isset($datos['columnas_clave']) || (trim($datos['columnas_clave']) == ''))) {
				throw new toba_error_def("Al existir un evento catalogado como 'a nivel de fila', es necesario definir una clave para el cuadro");
			}
		}
	}


}

?>