<?php 

class odr_ci extends objeto_datos_relacion 
{

	/**
	 * Si hay un evento sobre_fila hay que validar que haya una clave definida
	 */
	function evt__validar()
	{
		$ids_pantallas = $this->tabla('pantallas')->get_ids_pantallas();
		foreach ($this->tabla('dependencias')->get_filas() as $dep) {
			if (in_array($dep['identificador'], $ids_pantallas)) {
				throw new excepcion_toba_def("El identificador '{$dep['identificador']}' se está usando tanto para una pantalla como para una dependencia.");
			}
		}
	}	
}

?>