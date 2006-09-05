<?php

class pantalla_destino extends toba_ei_pantalla 
{
	function generar_html_dependencias()
	{
		$parametros = toba::hilo()->get_parametros();
		ei_arbol( $parametros, 'PARAMETROS recibidos' );
	}
	
}
?>