<?php
class ci_ejemplo_1 extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- grafico ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__grafico(toba_ei_grafico $grafico)
	{
		$grafico->conf()->canvas__set_titulo('Gráfico de torta');

		/**********************PRIMER SERIE********************************/
		$datos = array(13, 5, 3, 15, 10);
		$leyendas = array('lunes', 'martes', 'miercoles', 'jueves', 'viernes');
		
		$grafico->conf()
				->serie__agregar('nombre', $datos)    // Automáticamente setea la serie actual
				->serie__set_leyendas($leyendas)
				->serie__set_titulo('Semana uno')
				->serie__set_tema('water')
				->serie__set_centro(0.2);


		/**********************SEGUNDA SERIE********************************/
		foreach (array_keys($datos) as $key) {
			$datos[$key] = rand(1, 13);
		}
		
		$grafico->conf()
				->serie__agregar('otro_nombre', $datos)
				->serie__set_titulo('Semana dos')
				->serie__set_tema('water')
				->serie__separar_porciones(array(1, 3))
				->serie__set_centro(0.6);
	}

}
?>