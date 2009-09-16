<?php
require_once(toba_dir() . '/php/3ros/securimage/securimage.php');

class toba_imagen_captcha extends Securimage
{
	
	function __construct($texto=null)
	{
		$this->inicializar();
		
		if (!isset($texto)) {
			$this->code = false;	
		} else {
			$this->code = $texto;
		}
	}
	
	function inicializar()
	{
		$this->set_path_fuentes();
	}
	
	//-- Seteos
	/**
	 * Permite setear parametros que afectan a la generacion de la imagen.
	 * @param Array Arreglo asociativo con alguno de los siguientes indices
	 * integer image_width => default 175
	 * integer image_height => default 45
	 * integer image_type => ej: SI_IMAGE_JPEG: JPG, SI_IMAGE_PNG: PNG (default), SI_IMAGE_GIF: GIF
	 * integer code_length => default 4
	 * string  charset => default 'ABCDEFGHKLMNPRSTUVWYZ23456789'
	 * string  wordlist_file - path a un archivo con lista de palabras
	 * boolean use_wordlist => default true
	 * boolean use_gd_font' => default false
	 * string  gd_font_file => default toba_dir() . '/php/3ros/securimage/gdfonts/bubblebath.gdf'
	 * integer gd_font_size => default 20
	 * string  ttf_file  => default toba_dir() . '/php/3ros/securimage/elephant.ttf'
	 * integer font_size => default 24
	 * integer text_angle_minimum => default 20
	 * integer text_angle_maximum => default 20
	 * integer text_x_start => default 8
	 * integer text_minimum_distance => default 30
	 * integer text_maximum_distance => default 33
	 * string  image_bg_color => default '#e3daed'
	 * string  text_color => default '#ff0000'
	 * boolean use_multi_text => default true
	 * string  multi_text_color => default '#0a68dd,#f65c47,#8d32fd'
	 * boolean use_transparent_text => default true
	 * integer text_transparency_percentage => default 15
	 * boolean draw_lines => default true
	 * string  line_color => default '#80BFFF'
	 * integer ine_distance => default 5
	 * integer line_thickness' => default 1
	 * boolean draw_angled_lines => default false
	 * boolean draw_lines_over_text => default false
	 * boolean arc_linethrough => default true
	 * string  arc_line_colors => default '#8080ff'
	 * string  audio_path => default './audio/'
	 * string  bgimg => path a una imagen de background default
	*/
  
	function set_parametros_captcha($parametros)
	{		
		$param_securimage = $this->get_lista_variables();
		
		foreach ($parametros as $indice => $parametro)
		{
			if (in_array($parametro[$indice], $param_securimage)) {
				$this->$indice = $parametro;
			}
		}
	}
	
	function set_path_fuentes()
	{
		$this->gd_font_file = toba_dir() . '/php/3ros/securimage/gdfonts/bubblebath.gdf';
		$this->ttf_file	 	= toba_dir() . '/php/3ros/securimage/elephant.ttf';
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
	
	function get_lista_variables()
	{
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