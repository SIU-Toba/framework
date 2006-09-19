<?php

/**
 * Triple editbox que constituyen las 3 partes del CUIT/CUIL
 */
class toba_ef_cuit extends toba_ef
{
    static function get_lista_parametros_carga()
    {
		return array();
    }
    	
    static function get_lista_parametros()
    {
    	return array();
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
	
	function validar_estado()
	{
		$padre = parent::validar_estado();
		if ($padre !== true) {
			return $padre;	
		}		
		if ($this->estado == '') {
			return true;
		}
		$cuit_rearmado = $this->estado;
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

	function get_input()
	{
		if( !isset($this->estado)) { 
			$this->estado="";
		}
		$this->input_extra .= " tabindex='".$this->padre->get_tab_index()."'";
		$html = '<div class="ef-cuit">';
		$html .= toba_form::text($this->id_form . "_1", substr($this->estado,0,2),$this->solo_lectura, 2, 2, 'ef-input', $this->javascript.$this->input_extra); 
		$html .= ' - ';
		$html .= toba_form::text($this->id_form . "_2", substr($this->estado,2,8),$this->solo_lectura, 8, 8, 'ef-input', $this->javascript.$this->input_extra); 
		$html .= ' - ';		
		$html .= toba_form::text($this->id_form . "_3", substr($this->estado,10,1),$this->solo_lectura, 1, 1, 'ef-input', $this->javascript.$this->input_extra); 
		$html .= '</div>';
		return $html;
	}
	
	function crear_objeto_js()
	{
		return "new ef_cuit({$this->parametros_js()})";
	}
		
	function get_consumo_javascript()
	{
		$consumos = array('efs/mascaras', 'efs/ef', 'efs/ef_cuit');
		return $consumos;
	}	
	
}
   
?>
