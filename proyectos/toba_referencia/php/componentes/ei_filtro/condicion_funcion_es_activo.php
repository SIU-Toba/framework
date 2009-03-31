<?php
php_referencia::instancia()->agregar(__FILE__);

/**
 * Se crea una nueva condicion para el filtro de la columna 'activo'
 */
class condicion_funcion_es_activo extends toba_filtro_condicion
{
	function get_sql($campo, $valor)
	{
		$valor = toba::db()->quote($valor);
		return "funcion_x($campo) = $valor";
	}
}

?>
