<?php
/**
 * Description of toba_ef_cbu
 *
 * @author jpiazza
 */
class toba_ef_cbu extends toba_ef_editable {

	static function get_lista_parametros()
	{
		$param = parent::get_lista_parametros();
		array_borrar_valor($param, 'edit_tamano');
		array_borrar_valor($param, 'edit_expreg');
		array_borrar_valor($param, 'edit_mascara');
		array_borrar_valor($param, 'edit_unidad');
		array_borrar_valor($param, 'edit_maximo');
		return $param;    	
	}
	
	function validar_estado() 
	{
		$padre = parent::validar_estado();
		if ($padre !== true) {
			return $padre;	
		}


		if (!empty($this->estado)) {
			return $this->validar_cbu($this->estado);
		} else {
			return true;
		}

	}

	function get_input() 
	{
		$tab = ' tabindex="'.$this->padre->get_tab_index().'" ';
		$tab .= $this->get_info_placeholder();
		$html = toba_form::text($this->id_form, $this->estado,$this->es_solo_lectura(),22,29, $this->clase_css, $this->javascript.' '.$tab);
		$html .= $this->get_html_iconos_utilerias();
		return $html;
	}

	
	function crear_objeto_js()
	{
		return "new ef_cbu({$this->parametros_js()})";
	}	

	function get_consumo_javascript()
	{
		$consumos = array('efs/mascaras', 'efs/ef', 'efs/ef_editable', 'efs/ef_cbu');
		return $consumos;
	}
	
	/*************************************/
	private function validar_cbu($cbu)
	{
		if (strlen(trim($cbu)) != 22) {
			return false;
		}		
		$rta = toba_validaciones::cbu_valido(trim($cbu));
		if (!$rta) {
			return "CBU inválido";
		}		
		return true;
	}
}

?>
