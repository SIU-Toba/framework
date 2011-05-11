<?php
php_referencia::instancia()->agregar(__FILE__);

class pantalla_destino extends toba_ei_pantalla
{
	function generar_layout()
	{
		$parametros = toba::memoria()->get_parametros();
		ei_arbol($parametros, 'PARAMETROS recibidos');
		$clave_get = toba::memoria()->get_parametro('fila_safe');	//Recupero el parametro puntual del get.
		
		$claves_originales = toba_ei_cuadro::recuperar_clave_fila('5000004', $clave_get);
		ei_arbol($claves_originales, 'claves propagadas de manera segura');	
	}
	
}
?>