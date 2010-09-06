<?php

class ci_grafico extends toba_testing_pers_ci
{

	function conf__grafico(toba_ei_grafico $grafico)
	{
		$grafico->conf()->set_titulo_canvas('Gráfico de días');

		/**********************PRIMER GRAFICO********************************/
		$datos = array(13, 5, 3, 15, 10);
		$leyendas = array('lunes', 'martes', 'miercoles', 'jueves', 'viernes');
		
		$grafico->conf()
				->agregar_serie('nombre', $datos)
				->set_leyendas($leyendas)
				->set_titulo('Semana uno')
				->set_centro(0.2);

		/**********************SEGUNDO GRAFICO********************************/
		foreach (array_keys($datos) as $key) {
			$datos[$key] = rand(0, 13);
		}
		
		$grafico->conf()
				->agregar_serie('otro_nombre', $datos)
				->set_titulo('Semana dos')
				->separar_porciones(array(1, 3))
				->set_centro(0.6);
	}

	function conf__barras(toba_ei_grafico $grafico)
	{
		$grafico->conf()->set_titulo_canvas("Barras!");
		$datos = array(13, 5, 3, 15, 10);

		$grafico->conf()
				->agregar_serie('barras_1', $datos);
	}

}

?>