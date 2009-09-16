<?php 
php_referencia::instancia()->agregar(__FILE__);

class ci_captcha extends toba_ci
{
	
	//---- form_antispam ----------------------------------------------------------------
	
	function conf__form_antispam(toba_ei_formulario $form)
	{
		$parametros = array('image_type' => 1, 'use_gd_font' => true, 'image_width' => 200);
		$form->ef('ef_antispam')->set_longitud_codigo(6);
		$form->ef('ef_antispam')->set_parametros_captcha($parametros);
	}

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