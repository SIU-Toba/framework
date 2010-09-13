<?php
class ci_ejemplo_3 extends toba_ci
{
	function conf__grafico(toba_ei_grafico $grafico)
	{
		$datos_1 = array();
		for ($i = 0; $i < 15; $i++) {
			$datos_1[$i] = rand(1, 50);
		}
		
		$grafico->conf()->canvas__set_titulo("Lineas!");

		$grafico->conf()
				->serie__agregar('lineas_1', $datos_1)
				->serie__set_color('green')
				->serie__set_leyenda('Unos datos');

		$datos_2 = array();
		for ($i = 0; $i < 15; $i++) {
			$datos_2[$i] = rand(1, 50);
		}
		
		$grafico->conf()
				->serie__agregar('lineas_2', $datos_2)
				->serie__set_color('blue')
				->serie__set_leyenda('Otros datos');

		$prom = array();
		for ($i = 0; $i < 15; $i++) {
			$prom[$i] = ($datos_1[$i] + $datos_2[$i]) / 2;
		}

		$grafico->conf()
				->serie__agregar('prom', $prom)
				->serie__set_color('red')
				->serie__set_leyenda('Promedio');

		$serie = $grafico->conf()->serie('prom')->SetWeight(3);
		$grafico->conf()->canvas()->ygrid->SetFill(true, '#EFEFEF@0.8', '#BBCCFF@0.1');
	}
}
?>