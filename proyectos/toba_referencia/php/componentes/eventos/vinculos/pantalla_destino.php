<?php

class pantalla_destino extends toba_ei_pantalla 
{
	function generar_html_dependencias()
	{
		$parametros = toba::memoria()->get_parametros();
		ei_arbol( $parametros, 'PARAMETROS recibidos' );
	}
	
}
?>