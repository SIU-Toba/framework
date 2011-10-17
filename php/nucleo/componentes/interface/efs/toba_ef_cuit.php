<?php

/**
 * Triple editbox que constituyen las 3 partes del CUIT/CUIL
 * @package Componentes
 * @subpackage Efs 
 * @jsdoc ef_cuit ef_cuit
 */
class toba_ef_cuit extends toba_ef
{
	static protected $_excepciones;	
	protected $clase_css = 'ef-cuit';
	protected $_desactivar_validacion = false;

	static function get_lista_parametros_carga()
	{
		return array();
	}

	static function get_lista_parametros()
	{
		return array();
	}	

	/**
	 * Permite agregar excepciones al algoritmo de validacion de CUIT
	 * @param array $excepciones
	 */
	static function set_excepciones($excepciones)
	{
		self::$_excepciones = $excepciones;
	}
	
	static function get_excepciones() 
	{
		return self::$_excepciones;
	}
    
	function cargar_estado_post()
	{
	    if(isset($_POST[$this->id_form . '_1']) || isset($_POST[$this->id_form . '_2']) || isset($_POST[$this->id_form . '_3'])){
   			$this->estado =  str_pad(trim($_POST[$this->id_form . "_1"]), 2, '0', STR_PAD_LEFT) .
   							 str_pad(trim($_POST[$this->id_form . "_2"]), 8, '0', STR_PAD_LEFT) . 
   							 trim($_POST[$this->id_form . "_3"]);
   			if ($this->estado == 0) {
   				$this->estado = '';	
   			}
  		}
	}
	
	function set_estado($estado)
	{
		if(isset($estado)){								
			$this->estado=trim($estado);
		} else {
			$this->estado = null;	
		}
	}
	
	function tiene_estado()
	{
		if (isset($this->estado)) {
			return ($this->estado != "");
		} else{
			return false;
		}
	}
	
	function desactivar_validacion($desactivar)
	{
		$this->_desactivar_validacion = $desactivar;
	}	
	
	function get_desactivar_validacion()
	{
		return $this->_desactivar_validacion;
	}
	
	
	function validar_estado()
	{
		$padre = parent::validar_estado();
		if ($padre !== true) {
			return $padre;	
		}		
		if ($this->estado == '' || $this->_desactivar_validacion) {
			return true;
		}
		if ($this->confirma_excepcion_validacion()) {
			return true;
		}
		return self::validar_cuit($this->estado);
	}

	function get_input()
	{
		if( !isset($this->estado)) { 
			$this->estado="";
		}
		
		$tab = ' tabindex="'.$this->padre->get_tab_index().'"';		
		$html = "<div class='{$this->clase_css}'>";
		$html .= toba_form::text($this->id_form . "_1", substr($this->estado,0,2),$this->es_solo_lectura(), 2, 2, 'ef-input', $this->javascript.$this->input_extra.$tab); 
		$html .= ' - ';
		$html .= toba_form::text($this->id_form . "_2", substr($this->estado,2,8),$this->es_solo_lectura(), 8, 8, 'ef-input', $this->javascript.$this->input_extra.$tab); 
		$html .= ' - ';		
		$html .= toba_form::text($this->id_form . "_3", substr($this->estado,10,1),$this->es_solo_lectura(), 1, 1, 'ef-input', $this->javascript.$this->input_extra.$tab);
		$html .= $this->get_html_iconos_utilerias();
		$html .= '</div>';
		return $html;
	}
	
	function get_descripcion_estado($tipo_salida)
	{
		$formato = new toba_formateo($tipo_salida);
		$estado = $this->get_estado();
		$desc = ($estado != '') ? $formato->formato_cuit($estado) : '';
		switch ($tipo_salida) {
			case 'html':
			case 'impresion_html':
				return "<div class='{$this->clase_css}'>$desc</div>";
				break;
			case 'pdf':
			case 'excel':			
				return $desc;
				break;
		}
	}		
	
	function crear_objeto_js()
	{
		$desactivar_validacion = $this->_desactivar_validacion ? '1' : '0';
		return "new ef_cuit({$this->parametros_js()}, $desactivar_validacion)";
	}
		
	function get_consumo_javascript()
	{
		$consumos = array('efs/mascaras', 'efs/ef', 'efs/ef_cuit');
		if (isset(self::$_excepciones)) {
			$consumos[] = 'ef_cuit_excepciones';
		}
		return $consumos;
	}	
	
	
	static function validar_cuit($cuit_rearmado)
	{
		if (isset(self::$_excepciones)) {
			// Busca el cuit en las excepciones
			if (in_array($cuit_rearmado, self::$_excepciones)) {
				return true;
			}
		}
		$coeficiente = array(5, 4, 3, 2, 7, 6, 5, 4, 3, 2);
		$resultado=1;
		if (strlen($cuit_rearmado) != 11) {  // si to estan todos los digitos
			return "Un CUIT/CUIL consta de 11 digitos (encontrados ".strlen($cuit_rearmado)." )";
		} else {
			$sumador = 0;
			$verificador = intval(substr($cuit_rearmado, 10, 1)); //tomo el digito verificador
			//--- separo cada digito y lo multiplico por el coeficiente			
			for ($i=0; $i <=9; $i++) { 
				$sumador = $sumador + (substr($cuit_rearmado, $i, 1)) * $coeficiente[$i];
			}
			$resultado = 11 - ($sumador % 11); //saco el digito verificador
			if ($resultado == 11) {
				$resultado = 0;
			} elseif ($resultado == 10) {
				$resultado = 9;	
			}
			if ($verificador != $resultado) {
				return "clave incorrecta";
			} 
		}
		return true;		
	}
}
   
?>
