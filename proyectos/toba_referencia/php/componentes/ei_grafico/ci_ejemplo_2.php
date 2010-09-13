<?php
class ci_ejemplo_2 extends toba_ci
{
	function conf__grafico(toba_ei_grafico $grafico)
	{
		$grafico->conf()->canvas__set_titulo("Barras!");
		$datos = array(13, 5, 3, 15, 10);

		$grafico->conf()
				->serie__agregar('barras_1', $datos)
				->serie__set_color('green');

		// Manejando el canvas directamente
		$grafico->conf()->canvas()->ygrid->SetFill(true, '#EFEFEF@0.7', '#BBCCFF@0.3');
	}
}
?>