<?
require_once("nucleo/browser/clases/objeto_ei_cuadro.php");

class extendion_cuadro_estetica_a extends objeto_ei_cuadro
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