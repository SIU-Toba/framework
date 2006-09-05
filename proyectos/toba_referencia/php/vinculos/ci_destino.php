<?php

class ci_destino extends toba_ci
{
	function ini()
	{
		// Esta es la forma de recuperar valores recibidos
		$parametros = toba::memoria()->get_parametros();
		ei_arbol( $parametros, 'PARAMETROS recibidos' );
	}

}
?>