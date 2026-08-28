<?php

php_referencia::instancia()->agregar(__FILE__);

class ci_captcha extends toba_ci
{
    protected $sitekey = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
    protected $pkey = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
    //---- form_antispam ----------------------------------------------------------------

    public function conf__form_antispam(toba_ei_formulario $form)
    {
        if ($form->ef('ef_antispam')->es_legacy_ef()) {
            $parametros = array('image_type' => 1, 'use_gd_font' => true, 'image_width' => 200);
            $form->ef('ef_antispam')->set_longitud_codigo(6);
            $form->ef('ef_antispam')->set_parametros_captcha($parametros);
        } else {
            //Aca se deberian recuperar los valores de los parametros del proyecto y/o la instalacion
            $obj = new GCaptchav2($this->sitekey, $this->pkey);

            $form->ef('ef_antispam')->set_antispam_obj($obj);
        }
    }

    public function evt__form_antispam__modificacion($datos)
    {
        $respuesta = $_POST['g-recaptcha-response'] ?? null;
        if (isset($respuesta)) {
            $obj = new GCaptchav2($this->sitekey, $this->pkey);
            if ($obj->check($respuesta)) {
                toba::notificacion()->info('El código ingresado es correcto. Felicitaciones, has superado la prueba!.');
                return;
            }
        }

        toba::notificacion()->error('El código ingresado es incorrecto. Inténtalo de nuevo, vamos que no es tan difícil!.');
    }
}
