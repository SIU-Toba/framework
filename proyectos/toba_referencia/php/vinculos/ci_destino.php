<?php

class ci_destino extends toba_ci
{
	function ini()
	{
		// Esta es la forma de recuperar valores recibidos
		$parametros = toba::hilo()->get_parametros();
		ei_arbol( $parametros, 'PARAMETROS recibidos' );
	}

}
?>