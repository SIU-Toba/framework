<?php

/**
 * @deprecated
 */
class toba_imagen_captcha_empty // buscar metodo faltantes
{
    public function __construct()
    {
        $this->inicializar();
    }

    public function inicializar()
    {
        $this->set_parametros_default();
    }

    public function createCode($int)
    {
        $this->setcode('die ef die!!');
    }

    public function check($texto, $whatever)
    {
        return false;
    }

    //-- Seteos
    /**
     * Permite setear parametros que afectan a la generacion de la imagen.
     * @param Array Arreglo asociativo con alguno de los siguientes indices
    */

    public function set_parametros_captcha($parametros)
    {
        $param_securimage = array_keys($this->get_lista_variables());

        foreach ($parametros as $indice => $parametro) {
            if (in_array($indice, $param_securimage)) {
                $this->$indice = $parametro;
            }
        }
    }

    /**
     *  Inicializa con parametros basicos
     * @ignore
     */
    public function set_parametros_default()
    {
        $this->image_width   = 175;
        $this->image_height  = 45;
        $this->line_color =  0x8080ff;
        $this->text_color = 0x000000;
    }

    /**
     * Le indica a la libreria el codigo que debe mostrar, la validacion se debe realizar manualmente
     * @param string $codigo
     */
    public function set_codigo($codigo)
    {
        $this->display_value = $codigo;
    }

    //-- Gets
    /**
     * Devuelve el codigo cargado manualmente (si existe)
     * @return string
     */
    public function get_codigo()
    {
        //$obj = parent::getCode(false, true);
        return !empty($obj) ? $obj->code_display : '';
    }

    /**
     * Devuelve una lista de las variables de la clase que despues se van a acceder
     * @ignore
     * @return array
     */
    public function get_lista_variables()
    {	//(mmmmm... queda por tomuer compatibility)
        $vars = get_class_vars(get_class($this));

        //-- Parametros que no se permiten setear.
        unset($vars['im']);
        unset($vars['code']);
        unset($vars['code_entered']);
        unset($vars['correct_code']);

        return $vars;
    }

    public function get_input()
    {
        $this->estado  = false;
        $text_input = 'Este ef reemplaza la version deprecada de ef_captcha, por favor cambielo';
        $input = "<div>
					<div align='absmiddle' class='{$this->css_captcha}'>
					</div>
					<div class='{$this->clase_css}'>
						 $text_input
					</div>
				</div>";

        return $input;
    }
}
