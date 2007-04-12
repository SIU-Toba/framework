<?php
php_referencia::instancia()->agregar(__FILE__);

class extendion_cuadro_estetica_a extends toba_ei_cuadro
{
	function html_cabecera_cc_contenido__zona(&$nodo)
	{
		$zona = $nodo['descripcion']['zona'];
		$locs = count($nodo['filas']);
		$deps = count($nodo['hijos']);
		echo "<strong>Zona</strong>: $zona - 
			<strong>Departamentos</strong>: $deps - 
			<strong>Localidades</strong>: $locs";
	}
}
?>