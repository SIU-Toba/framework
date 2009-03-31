<?php
php_referencia::instancia()->agregar(__FILE__);

class pantalla_destino extends toba_ei_pantalla
{
	function generar_layout()
	{
		$parametros = toba::memoria()->get_parametros();
		ei_arbol($parametros, 'PARAMETROS recibidos');
	}
	
}
?>