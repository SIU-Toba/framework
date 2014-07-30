<?php
require_once(toba_dir() . '/php/3ros/securimage/securimage.php');

class toba_imagen_captcha extends Securimage
{
	
	function __construct($texto=null)
	{
		parent::__construct();		
		$this->inicializar();
		
		if (!isset($texto)) {
			$this->code = false;	
		} else {
			$this->code = $texto;
		}
	}
	
	function inicializar()
	{		
		$this->set_parametros_default();
	}
	
	//-- Seteos
	/**
	 * Permite setear parametros que afectan a la generacion de la imagen.
	 * @param Array Arreglo asociativo con alguno de los siguientes indices
	 * integer image_width => default 230
	 * integer image_height => default 80
	 * integer image_type => ej: SI_IMAGE_JPEG: JPG, SI_IMAGE_PNG: PNG (default), SI_IMAGE_GIF: GIF
	 * integer code_length => default 6
	 * string  charset => default 'ABCDEFGHKLMNPRSTUVWYZabcdefghklmnprstuvwyz23456789'
	 * string  wordlist_file - path a un archivo con lista de palabras
	 * boolean use_wordlist => default false
	 * boolean use_gd_font' => default false
	 * string  gd_font_file => default toba_dir() . '/php/3ros/securimage/gdfonts/bubblebath.gdf'
	 * integer gd_font_size => default 24
	 * string  ttf_file  => default toba_dir() . '/php/3ros/securimage/elephant.ttf'
	 * integer font_size => default 24
	 * float perturbation  => default 0.75
	 * integer text_angle_minimum => default 0
	 * integer text_angle_maximum => default 0
	 * integer text_x_start => default 15 (deprecated)
	 * integer text_minimum_distance => default 30
	 * integer text_maximum_distance => default 33
	 * string  image_bg_color => default '#ffffff'
	 * string  text_color => default '#3d3d3d'
	 * boolean use_multi_text => default false
	 * string  multi_text_color => default '#0020cc,#0030ee,#0040cc,#0050ee,#0060cc'
	 * boolean use_transparent_text => default true
	 * integer text_transparency_percentage => default 15
	 * boolean draw_lines => default true
	 * string  line_color => default '#80BFFF'
	 * boolean draw_lines_over_text => default true
	 * string  audio_path => default './audio/'
	 * string audio_format => default 'mp3'
	 * string  bgimg => path a una imagen de background default
	*/
  
	function set_parametros_captcha($parametros)
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
	function set_parametros_default()
	{
		$this->image_width   = 175;
		$this->image_height  = 45;
		$this->line_color =  new Securimage_Color(0x80, 0x80, 0xff);
		$this->set_path_fuentes();		
	}
	
	/**
	 * Coloca el path de las fuentes apuntando al directorio correcto
	 * @ignore
	 */
	function set_path_fuentes()
	{
		$this->gd_font_file = toba_dir() . '/php/3ros/securimage/gdfonts/bubblebath.gdf';
		$this->ttf_file = toba_dir() . '/php/3ros/securimage/elephant.ttf';
	}
	
	function set_codigo($codigo)
	{
		$this->code = $codigo;
	}
	
	//-- Gets
	
	function getCode()
	{
		//-- No se utiliza.
	}
	
	/**
	 * Devuelve una lista de las variables de la clase que despues se van a acceder 
	 * @ignore
	 * @return array
	 */
	function get_lista_variables()
	{	//(mmmmm... queda por tomuer compatibility)
		$vars = get_class_vars(get_class($this));
		
		//-- Parametros que no se permiten setear.
		unset($vars['im']);
		unset($vars['code']);
		unset($vars['code_entered']);
		unset($vars['correct_code']);
		
		return $vars;
	}
	
	function createCode()
	{
		if ($this->use_wordlist && is_readable($this->wordlist_file)) {
		  $this->code = $this->readCodeFromFile();
		}
		
		if ($this->code == false) {
		  $this->code = $this->generateCode($this->code_length);
		}
	}
	
}

?>