<?php
/**
 * Muestra un checkbox con el tag <input type='checkbox'>
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_checkbox ef_checkbox
 */
class toba_ef_checkbox extends toba_ef
{
	protected $valor;
	protected $valor_no_seteado;
	protected $valor_info = 'Sí';
	protected $valor_info_no_seteado = 'No';
	protected $clase_css = 'ef-checkbox';

	static function get_lista_parametros()
	{
		return array(
						'check_valor_si',
						'check_valor_no',
						'check_desc_si',
						'check_desc_no',
						'check_ml_toggle'
		);
	}
 
    
	function __construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
	{
		//VAlor FIJO
		if (isset($parametros['estado_defecto'])) {
			$this->estado_defecto = $parametros['estado_defecto'];		
			$this->estado = $this->estado_defecto;
		}
		if (isset($parametros['check_valor_si'])) {
			$this->valor = $parametros['check_valor_si'];
		} else {
			$this->valor = '1';
		}
		if (isset($parametros['check_valor_no'])) {
			$this->valor_no_seteado = $parametros['check_valor_no'];
		} else {
			$this->valor_no_seteado = '0';	
		}	
		if (isset($parametros["check_desc_si"])) {
			$this->valor_info = $parametros["check_desc_si"];
		}
		if (isset($parametros["check_desc_no"])) {
			$this->valor_info_no_seteado = $parametros["check_desc_no"];
		}		
		if (isset($parametros["check_ml_toggle"])) {
			$this->check_ml_toggle = $parametros["check_ml_toggle"];
		}			
		parent::__construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio,$parametros);
	}

	function get_input()
	{
		//Esto es para eliminar un notice en php 5.0.4
		if (!isset($this->estado)) {
			$this->estado = null;
		}
		 if ($this->es_solo_lectura()) {
			$html = toba_form::hidden($this->id_form, $this->seleccionado() ? $this->valor : $this->valor_no_seteado);
			if ($this->seleccionado()) {
				$html .= toba_recurso::imagen_toba('nucleo/efcheck_on.gif',true,16,16);
			} else {
				$html .= toba_recurso::imagen_toba('nucleo/efcheck_off.gif',true,16,16);            
			}
		 } else {
			$js = '';
			if ($this->cuando_cambia_valor != '') {
				$js = "onchange=\"{$this->get_cuando_cambia_valor()}\"";
			}         	
			$tab = $this->padre->get_tab_index();
			$extra = " tabindex='$tab'";		
			$html = toba_form::checkbox($this->id_form, $this->estado, $this->valor, $this->clase_css, $extra.' '.$js);
		 }
		 $html .= $this->get_html_iconos_utilerias();
		 return $html;
	}

	function set_estado($estado)
	//Carga el estado interno
	{
		if(isset($estado)){								
			$this->estado=$estado;
			return true;
		}else{
			//Si el valor no seteado existe, paso el estado a ese valor.
			if (isset($this->valor_no_seteado)) {
				$this->estado = $this->valor_no_seteado;
				return true;
			} else {
				$this->estado = null;			
			}
		}
		return false;
	}
	
	function cargar_estado_post()
	{
		if(isset($_POST[$this->id_form])) {
			$this->set_estado($_POST[$this->id_form]);
		} else {
			$this->set_estado(null);
		}
		return false;		
	}
	
	function get_consumo_javascript()
	{
		$consumos = array('efs/ef','efs/ef_checkbox');
		return $consumos;
	}	
	
	function tiene_estado()
	{
		return isset($this->estado) && 
				($this->estado == $this->valor || $this->estado == $this->valor_no_seteado);
	}	

	function seleccionado()
	{
		return isset($this->estado) && 
				($this->estado == $this->valor);
	}	

	protected function parametros_js()
	{
		$param_padre = parent::parametros_js();
		$params = "$param_padre,  '{$this->valor}' ";		//Le paso el valor que tomaria estando checkeado para comparar en modo solo lectura
		return $params;
	}
	
	function crear_objeto_js()
	{
		return "new ef_checkbox({$this->parametros_js()})";
	}	

	function get_descripcion_estado($tipo_salida)
	{
		if ( !isset($this->estado) || $this->estado == $this->valor_no_seteado ) {
			$valor = $this->valor_info_no_seteado;
		} else {
			$valor = $this->valor_info;
		}
		switch ($tipo_salida) {
			case 'html':
			case 'impresion_html':
				return "<div class='{$this->clase_css}'>$valor</div>";
			break;
			case 'pdf':
				return $valor;
			case 'excel':
				return array($valor, null);
		}		
	}
	
}
// ########################################################################################################
// ########################################################################################################

/**
 * Muestra un <div> con el estado actual dentro
 * Útil para incluir contenidos estáticos en el formulario
 * @jsdoc ef_fijo ef_fijo
 */
class toba_ef_fijo extends toba_ef_oculto
{
	protected $clase_css = 'ef-fijo';
	private $maneja_datos;
	
	static function get_lista_parametros()
	{
		$parametros[] = 'fijo_sin_estado';
		return $parametros;
	}
	
    
	static function get_lista_parametros_carga()
	{
		$parametros = toba_ef::get_lista_parametros_carga_basico();    
		array_borrar_valor($parametros, 'carga_lista');
		array_borrar_valor($parametros, 'carga_col_clave');
		array_borrar_valor($parametros, 'carga_col_desc');
		return $parametros;
	}  	
	
	function __construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
	{
		parent::__construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio,$parametros);
		if(isset($parametros['fijo_sin_estado']) && $parametros['fijo_sin_estado'] == 1){
			$this->maneja_datos = false;
		}else{
			$this->maneja_datos = true;
		}

	}
   
	function set_estado($estado=null)
	{
		/*
			Si el EF maneja datos utilizo la logica de persistencia del padre
		*/
		if($this->maneja_datos){
			return parent::set_estado($estado);
		}else{
			if(isset($estado)) {
				$this->estado = $estado;
			}		
		}
	}

	function set_opciones($descripcion, $maestros_cargados=true)
	{
		$this->set_estado($descripcion);
	}	
	
	function get_input()
	{
		$estado = (isset($this->estado)) ? $this->estado : null;
		if (! $this->permitir_html) {
			$estado = texto_plano($estado);
		}
		$html = "<div class='{$this->clase_css}' id='{$this->id_form}'>".$estado."</div>";
		$html .= $this->get_html_iconos_utilerias();
		return $html;
	}
	
	function get_consumo_javascript()
	{
		$consumos = array('efs/ef');
		return $consumos;
	}	
	
	function crear_objeto_js()
	{
		return "new ef_fijo({$this->parametros_js()})";
	}	
			
}


// ########################################################################################################
// ########################################################################################################
//Editor WYSIWYG de HTML

/**
 * Incluye un editor HTML WYSYWYG llamado fckeditor
 * El HTML generado por este editor es bastante pobre en estructura, debería ser utilizado solo por usuarios finales
 * y no por desarrolladores que quieran agregar contenido dinámicamente a la aplicación.
 * @jsdoc ef ef
 */
class toba_ef_html extends toba_ef
{
	protected $ancho;
	protected $alto;
	protected $botonera;
	protected $templates_ck;
	protected $fckeditor;
	protected $colapsada = false;
	protected $js_config;

	static function get_lista_parametros()
	{
		$parametros[] = 'editor_ancho';
		$parametros[] = 'editor_alto';
		$parametros[] = 'editor_botonera';
		return $parametros;
	}	
	
	function __construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros)
	{
		$this->ancho = (isset($parametros['editor_ancho']))? $parametros['editor_ancho'] : "100%";
		$this->alto = (isset($parametros['editor_alto']))? $parametros['editor_alto'] : "300px";
		$this->botonera = (isset($parametros['editor_botonera']))? $parametros['editor_botonera'] : "Toba";
		parent::__construct($padre, $nombre_formulario, $id, $etiqueta, $descripcion, $dato, $obligatorio, $parametros);
	}

	/*function get_consumo_javascript()
	{
		$consumo = parent::get_consumo_javascript();
		$consumo[] = "ckeditor/ckeditor";
		return $consumo;
	}*/
	
	/**
	 * Retorna el objeto fckeditor para poder modificarlo según su propia API
	 * @param mixed valor a pasarle al editor
	 * @return fckeditor
	 */
	function get_editor($valor)
	{
		if (! isset($this->fckeditor)) {
			require_once(toba_dir().'/www/js/ckeditor/ckeditor_php5.php');
			$url = toba_recurso::url_toba().'/js/ckeditor/';
			$this->fckeditor = new CKeditor($url) ;
		}
		
		$opciones = array();		
		$opciones['width'] = $this->ancho;
		$opciones['height'] = $this->alto;
		$opciones['toolbar'] = $this->botonera;
		$opciones['skin'] = 'kama';
		if (isset($this->templates_ck)) {
			$opciones['templates_files'] = $this->templates_ck;

		} 
		
		$this->fckeditor->returnOutput = true;							//Reinicializo variable para que no haga el echo del html
		$editor =  $this->fckeditor->editor($this->id_form, $valor, $opciones, array(), false);
		$this->js_config = $this->fckeditor->encoded_config($opciones,array());	

		return $editor;
	}
	
	function get_estado()
	{
		if ($this->tiene_estado()) {
			return trim($this->estado);			
		} else {
			return null;
		}
	}	
	
	function set_barra_colapsada($colapsada)
	{
		$this->colapsada = $colapsada;
	}

	function set_botonera($botonera)
	{
		$this->botonera = $botonera;
	}
	
	function set_alto($alto)
	{
		$this->alto = $alto;
	}
	
	function set_ancho($ancho)
	{
		$this->ancho = $ancho;
	}
	
	function set_path_template($path)
	{
		$this->templates_ck = $path;
	}
	
	function get_input()
	{
		if(isset($this->estado)){
			$estado = $this->estado;
		}else{
			$estado = "";
		}		
		if ($this->es_solo_lectura()) {
			$html = "<div class='ef-html' style='width: {$this->ancho}'>$estado</div>";
		} else {
			$html = $this->get_editor($estado);			
		}
		return $html;
	}
	
	protected function parametros_js()
	{
		$params = parent::parametros_js();
		if (isset($this->js_config)) {
			$params .= ', ' . $this->js_config;
		}
		return $params;
	}
	
	
	function crear_objeto_js()
	{
		return "new ef_html({$this->parametros_js()})";
	}	
}

?>
