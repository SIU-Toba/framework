<?php

require_once(toba_dir() . '/php/3ros/jpgraph/jpgraph_antispam.php');

/**
 * Editbox + imagen aleatoria para captcha
 * @package Componentes
 * @subpackage Efs 
 */
class toba_ef_editable_captcha extends toba_ef_editable 
{
	protected $antispam;									// Variable que mantiene la referencia al objeto AntiSpam.
	protected $texto;										// Texto aleatorio generado.
	protected $longuitud = 5;								// Longuitud del texto
	protected $tamano_imagen = array( 'ancho' => '100px',   // Tamaño en pixeles de la imagen a mostrar.
									  'alto' => '30px');
	protected $css_captcha = 'ef-captcha';
	
    static function get_lista_parametros_carga()
    {
     	$parametros = array();    
		return $parametros;    	
    }
    
    static function get_lista_parametros()
    {
	   	$param = parent::get_lista_parametros();
    	array_borrar_valor($param, 'edit_expreg');
    	array_borrar_valor($param, 'edit_mascara');
    	array_borrar_valor($param, 'edit_unidad');
    	array_borrar_valor($param, 'edit_maximo');
    	return $param;    	
    }	
	
	
	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		if (!extension_loaded('gd')) {
			throw new toba_error('<b>toba_ef_editable_captcha:</b> Necesita instalar en PHP el soporte para la extensión GD.');
		}
		$this->antispam = new AntiSpam();
		$this->generar_texto_aleatorio();
		$parametros['estado_defecto'] = false;
		parent::__construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}
	
	/**
	 * Permite setear las dimensiones de la imagen aleatoria
	 * @param array tamanio Arrary ('ancho' => valor1, 'alto' => valor2) con la dimension en pixeles
	 */
	function set_tamanio_imagen($tamanio)
	{
		$this->tamano_imagen = $tamanio;
	}
	
	/**
	 * Genera el texto aleatorio que se muestra en la imagen distorsionada.
	 * @param entero $tamanio Longitud de la cadena de caracteres retornada.
	 */	
	function generar_texto_aleatorio($tamanio=5)
	{
		$this->texto = $this->antispam->Rand($tamanio);
	}
	
	function get_input()
	{
		toba::memoria()->set_dato_sincronizado('texto-captcha',$this->texto);
		$this->estado = false;
		$longuitud = strlen($this->texto); //la longuitud maxima de caracteres del ef
		$tab = ' tabindex="'.$this->padre->get_tab_index().'"';		
		$input = toba_form::text($this->id_form,$this->estado,$this->solo_lectura,$longuitud,$this->tamano,$this->clase_css, $this->javascript.' '.$this->input_extra.$tab);
		$url = toba::vinculador()->get_url(null,null,array(), array('servicio' => 'mostrar_captchas_efs',
													'objetos_destino' => array( $this->padre->get_id() )));
		$input .= $this->get_html_iconos_utilerias();
		$input = "<div>
					<div class='{$this->css_captcha}'>	
						<img src='$url' width='{$this->tamano_imagen['ancho']}' heigth='{$this->tamano_imagen['alto']}' />
					</div>
					<div class='{$this->clase_css}'>
						 $input 
					</div>
				</div>";
		return $input;
	}
	
	function cargar_estado_post()
	{
		if (isset($_POST[$this->id_form])) {
			$texto_imagen = strtoupper(toba::memoria()->get_dato_sincronizado('texto-captcha'));
			$texto_ef = strtoupper(trim($_POST[$this->id_form]));
			$this->estado = ($texto_imagen == $texto_ef) ? true : false;
    	} else {
    		$this->estado = false;
    	}
	}

	function get_estado()
	{
		return $this->estado;			
	}

	
	function tiene_estado()
	{
		return isset($this->estado);
	}
	
}

?>