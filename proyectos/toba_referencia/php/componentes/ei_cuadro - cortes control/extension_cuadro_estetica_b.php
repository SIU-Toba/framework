<?
require_once("nucleo/componentes/interface/toba_ei_cuadro.php");

class extension_cuadro_estetica_b extends toba_ei_cuadro
{
	function html_pie_cc_contenido__zona(&$nodo)
	{
		//Preparo una descripcion
		$zona = $nodo['descripcion']['zona'];
		$locs = count($nodo['filas']);
		$deps = count($nodo['hijos']);
		echo "La Zona <strong>$zona</strong> tiene <strong>$deps</strong>
				departamentos y <strong>$locs</strong> localidades.<br>";
		//Hago unos calculos
		$habitantes = 0;
		foreach($nodo['filas'] as $fila){
			$habitantes += $this->datos[$fila]['hab_total'];
		}
		$promedio = $habitantes / count($nodo['filas']);
		$resultado = number_format($promedio,2,',','.') ;
		echo "El promedio de habitantes por localidad es: <strong>$promedio</strong>.";
	}
}
?>