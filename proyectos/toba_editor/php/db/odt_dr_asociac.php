<?php

class odt_dr_asociac extends toba_datos_tabla
{
	/**
	 * Valida que una relaci�n tenga al menos un par de columnas asociadas
	 */
	function evt__validar_fila($fila)
	{
		$ok = true;
		if (isset($fila['padre_clave']) && isset($fila['hijo_clave'])) {
			if ($fila['padre_clave'] == '' || $fila['hijo_clave'] == '') {
				$ok = false;
			}
		} else {
			$ok = false;	
		}
		if (!$ok) {
			$mensaje = 'Debe especificar al menos una asociaci�n de columnas en la '.
						"relaci�n entre {$fila['padre_id']} y {$fila['hijo_id']}";
			throw new toba_error($mensaje);
		}
	}
	
}
?>