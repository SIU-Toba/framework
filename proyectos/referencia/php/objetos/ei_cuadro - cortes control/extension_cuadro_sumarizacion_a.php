<?
require_once("nucleo/componentes/interface/objeto_ei_cuadro.php");

class extension_cuadro_sumarizacion_a extends objeto_ei_cuadro
{
	/**
		Cantidad de Localidades
	*/
	function sumarizar_cc__zona__loc($filas)
	{
		return count($filas);
	}

	/**
		Promedio de habitantes por localidad
	*/
	function sumarizar_cc__zona__prom_hab_loc($filas)
	{
		$habitantes = 0;
		foreach($filas as $fila){
			$habitantes += $this->datos[$fila]['hab_total'];
		}
		$resultado = $habitantes / count($filas);
		return number_format($resultado,2,',','.');
	}


	/**
		Habitantes por kilometro cuadrado
	*/
	function sumarizar_cc__zona__habkil($filas)
	{
		$habitantes = 0;
		$superficie = 0;
		foreach($filas as $fila){
			$habitantes += $this->datos[$fila]['hab_total'];
			$superficie += $this->datos[$fila]['superficie'];
		}
		$resultado = $habitantes / $superficie;
		return number_format($resultado,2,',','.');
	}
}
?>