<?php
php_referencia::instancia()->agregar(__FILE__);

class formateo_referencia extends toba_formateo
{
	function formato_pesos_sin_coma($valor)
	{
		if ($this->tipo_salida != 'excel') {
			return '$ '.number_format($valor, 0, ',', '.');
		} else {
			return array($valor, array('numberformat' =>
						array('code' =>'"$"#,##0_-')));
		}
	}
}
?>
