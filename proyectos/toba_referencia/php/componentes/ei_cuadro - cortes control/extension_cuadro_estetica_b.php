<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_cuadro_estetica_b extends toba_ei_cuadro
{
	
	function html_pie_cc_contenido__zona(&$nodo)
	{
		//Preparo una descripcion
		$escapador = toba::escaper();
		$zona = $escapador->escapeHtml($nodo['descripcion']['zona']);
		$locs = count($nodo['filas']);
		$deps = count($nodo['hijos']);
		echo "La Zona <strong>$zona</strong> tiene <strong>$deps</strong>
				departamentos y <strong>$locs</strong> localidades.<br>";
		//Hago unos calculos
		$habitantes = 0;
		foreach ($nodo['filas'] as $fila) {
			$habitantes += $this->datos[$fila]['hab_total'];
		}
		$promedio = $escapador->escapeHtml($habitantes / count($nodo['filas']));
		//$resultado = number_format($promedio, 2, ',', '.');
		echo "El promedio de habitantes por localidad es: <strong>$promedio</strong>.";
	}
	
	function pdf_pie_cc_contenido__zona(&$nodo)
	{
		//Preparo una descripcion
		$zona = $nodo['descripcion']['zona'];
		$locs = count($nodo['filas']);
		$deps = count($nodo['hijos']);
		$this->salida->texto("La Zona <b>$zona</b> tiene <b>$deps</b> departamentos y <b>$locs</b> localidades.");
		//Hago unos calculos
		$habitantes = 0;
		foreach ($nodo['filas'] as $fila) {
			$habitantes += $this->datos[$fila]['hab_total'];
		}
		$promedio = $habitantes / count($nodo['filas']);
		$resultado = number_format($promedio, 2, ',', '.');
		$this->salida->texto("El promedio de habitantes por localidad es: <b>$promedio</b>.");
	}	
	
	function excel_pie_cc_contenido__zona(&$nodo)
	{
		$estilos = array('font' => array('size'=> '8'));
		//Preparo una descripcion
		$zona = $nodo['descripcion']['zona'];
		$locs = count($nodo['filas']);
		$deps = count($nodo['hijos']);
		$this->salida->texto("La Zona $zona tiene $deps departamentos y $locs localidades.", $estilos);
		//Hago unos calculos
		$habitantes = 0;
		foreach ($nodo['filas'] as $fila) {
			$habitantes += $this->datos[$fila]['hab_total'];
		}
		$promedio = $habitantes / count($nodo['filas']);
		$resultado = number_format($promedio, 2, ',', '.');
		$this->salida->texto("El promedio de habitantes por localidad es: $promedio.", $estilos);
	}
}
?>