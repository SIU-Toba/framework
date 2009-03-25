<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_captcha extends toba_ci
{
	//---- form_antispam ----------------------------------------------------------------

	function evt__form_antispam__modificacion($datos)
	{
		if ($datos['ef_antispam']) {
			toba::notificacion()->info('El código ingresado es correcto. Felicitaciones, has superado la prueba!.');			
		} else {
			toba::notificacion()->error('El código ingresado es incorrecto. Inténtalo de nuevo, vamos que no es tan difícil!.');
		}
	}
}

?>