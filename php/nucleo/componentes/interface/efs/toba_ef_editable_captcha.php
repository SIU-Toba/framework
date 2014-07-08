<?php

/**
 * Editbox + imagen aleatoria para captcha
 * @package Componentes
 * @subpackage Efs 
 */
class toba_ef_editable_captcha extends toba_ef_editable 
{
	/**
	 * @var toba_imagen_captcha
	 */
	protected $antispam;									// Variable que mantiene la referencia al objeto AntiSpam.
	protected $texto;										// Texto aleatorio generado.
	protected $longitud = 5;								// Longitud del texto
	protected $css_captcha = 'ef-captcha';					// Clase css
	protected $permite_refrescar_codigo = true;				// Indica si permite o no refrescar el codigo.
	protected $permite_generar_audio = false;				// Indica si permite o no generar el audio del codigo.
	
	function __construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
	{
		if (!extension_loaded('gd')) {
			throw new toba_error('<b>toba_ef_editable_captcha:</b> Necesita instalar en PHP el soporte para la extensión GD.');
		}
		
		$this->antispam = new toba_imagen_captcha();
		//$this->generar_texto_aleatorio();
		$parametros['estado_defecto'] = false;
		
		parent::__construct($padre, $nombre_formulario, $id,$etiqueta, $descripcion, $dato, $obligatorio, $parametros);
	}
	
	//-- Gets
	
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
    
	//-- Sets
	/**
	 * Permite setear parametros que afectan a la generacion de la imagen.
	 * Las lista de parámetros posibles es la siguiente:
	 * 
	 * - integer image_width => default 175
	 * - integer image_height => default 45
	 * - integer image_type => ej: SI_IMAGE_JPEG: JPG, SI_IMAGE_PNG: PNG (default), SI_IMAGE_GIF: GIF
	 * - integer code_length => default 4
	 * - string  charset => default 'ABCDEFGHKLMNPRSTUVWYZ23456789'
	 * - string  wordlist_file - path a un archivo con lista de palabras
	 * - boolean use_wordlist => default true
	 * - boolean use_gd_font' => default false
	 * - string  gd_font_file => default toba_dir() . '/php/3ros/securimage/gdfonts/bubblebath.gdf'
	 * - integer gd_font_size => default 20
	 * - string  ttf_file  => default toba_dir() . '/php/3ros/securimage/elephant.ttf'
	 * - integer font_size => default 24
	 * - integer text_angle_minimum => default 20
	 * - integer text_angle_maximum => default 20
	 * - integer text_x_start => default 8
	 * - integer text_minimum_distance => default 30
	 * - integer text_maximum_distance => default 33
	 * - string  image_bg_color => default '#e3daed'
	 * - string  text_color => default '#ff0000'
	 * - boolean use_multi_text => default true
	 * - string  multi_text_color => default '#0a68dd,#f65c47,#8d32fd'
	 * - boolean use_transparent_text => default true
	 * - integer text_transparency_percentage => default 15
	 * - boolean draw_lines => default true
	 * - string  line_color => default '#80BFFF'
	 * - integer ine_distance => default 5
	 * - integer line_thickness' => default 1
	 * - boolean draw_angled_lines => default false
	 * - boolean draw_lines_over_text => default false
	 * - boolean arc_linethrough => default true
	 * - string  arc_line_colors => default '#8080ff'
	 * - string  audio_path => default './audio/'
	 * - string  bgimg => path a una imagen de background default
	 * 
	 * @param Array Arreglo asociativo con alguno de los siguientes indices
	*/
	
	function set_parametros_captcha($parametros)
	{
		toba::memoria()->set_dato_operacion('parametros-captcha', $parametros);
	}
	
	function set_permite_refrescar_codigo($permite=true)
	{
		$this->permite_refrescar_codigo = $permite;
	}
	
	function set_permite_generar_audio($permite=true)
	{
		$this->permite_generar_audio = $permite;
	}
	
	function set_longitud_codigo($longitud)
	{
		$this->longitud = $longitud;
	}
	
	/**
	 * Genera el texto aleatorio que se muestra en la imagen distorsionada.
	 */	
	function generar_texto_aleatorio()
	{
		$this->texto = $this->antispam->generateCode($this->longitud);
	}
	
	function get_input()
	{
		$this->input_extra .= $this->get_estilo_visualizacion_pixeles();
		$this->input_extra .= $this->get_info_placeholder();
		$this->generar_texto_aleatorio();
		toba::memoria()->set_dato_operacion('texto-captcha', $this->texto);
		toba::memoria()->set_dato_operacion('tamanio-texto-captcha', $this->longitud);
		
		$this->estado  = false;
		$longitud = strlen($this->texto); //la longitud maxima de caracteres del ef
		$tab = ' tabindex="'.$this->padre->get_tab_index().'"';		
		$text_input  = toba_form::text($this->id_form, $this->estado, $this->es_solo_lectura(), $longitud, $this->tamano, $this->clase_css, $this->javascript.' '.$this->input_extra.$tab);
		$url = toba::vinculador()->get_url(null, null, array(), array('servicio' => 'mostrar_captchas_efs', 'objetos_destino' => array( $this->padre->get_id() )));
		
		if ($this->permite_refrescar_codigo) {
			$url_refrescar = toba::vinculador()->get_url(null, null, array('refrescar' => 1), array('servicio' => 'mostrar_captchas_efs', 'objetos_destino' => array( $this->padre->get_id() )));
			$js = "\"document.getElementById('{$this->id}-captcha').src = '$url_refrescar' + Math.random(); return false;\"";
			$img_refrescar = toba_recurso::imagen_toba('refrescar.png');
			$refrescar = "<a href='#' onclick=$js><img src='$img_refrescar' alt='Refrescar código de imágen' title='Refrescar código de imágen' /></a>";
		} else {
			$refrescar = '';
		}
		
		//-- TODO: si alguien tiene ganas... metele que son pasteles!!!
		if ($this->permite_generar_audio) {
			$audio = '';
		} else {
			$audio = '';
		}
															
		$input = "<div>
					<div align='absmiddle' class='{$this->css_captcha}'>
						<img id='{$this->id}-captcha' src='$url' /> $refrescar $audio
					</div>
					<div class='{$this->clase_css}'>
						 $text_input
					</div>
				</div>";
		
		$input .= $this->get_html_iconos_utilerias();
		
		return $input;
	}
	
	function cargar_estado_post()
	{
		if (isset($_POST[$this->id_form])) {
			$texto_imagen = strtoupper(toba::memoria()->get_dato_operacion('texto-captcha'));
			$texto_ef 	  = strtoupper(trim($_POST[$this->id_form]));

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