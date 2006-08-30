<?php

class ci_destino extends objeto_ci
{
	function ini()
	{
		// Esta es la forma de recuperar valores recibidos
		$parametros = toba::get_hilo()->obtener_parametros();
		ei_arbol( $parametros, 'PARAMETROS recibidos' );
	}

}
?>