<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_antispam extends toba_ci
{
	//---- form_antispam ----------------------------------------------------------------

	function evt__form_antispam__modificacion($datos)
	{
		//var_dump($datos);
		if ($datos['ef_antispam']) {
			toba::notificacion()->info('El c�digo ingresado es correcto. Felicitaciones, has superado la prueba!.');			
		}else{
			toba::notificacion()->error('El c�digo ingresado es incorrecto. Int�ntalo de nuevo, vamos que no es tan dif�cil!.');
		}
	}
}

?>