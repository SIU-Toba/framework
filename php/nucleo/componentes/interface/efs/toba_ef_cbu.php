<?php
/**
 * Description of toba_ef_cbu
 *
 * @author jpiazza
 */
class toba_ef_cbu extends toba_ef_editable {

	static function get_lista_parametros()
	{
		return null;
	}
	
	function validar_estado() 
	{
		$padre = parent::validar_estado();
		if ($padre !== true) {
			return $padre;	
		}


		if (!empty($this->estado)) {
			return $this->validar_dbu($this->estado);
		} else {
			return true;
		}

	}

	function get_input() 
	{
		$tab = ' tabindex="'.$this->padre->get_tab_index().'"';
		$html = toba_form::text($this->id_form, $this->estado,$this->es_solo_lectura(),22,22, $this->clase_css, $this->javascript.' '.$tab);
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
	private function validar_dbu($cbu)
	{
		if (strlen(trim($cbu)) != 22) {
			return false;
		}
		$rta = true;
		$v = str_split($cbu);

		//Valido bloque 1
		$suma1 = $v[0]*7 + $v[1]*1 + $v[2]*3 + $v[3]*9 + $v[4]*7 + $v[5]*1 + $v[6]*3;
		$d1 =  10 - intval(substr($suma1, -1, 1));
		if ($d1 !=  $v[7]) {
			$rta = false;
		}
				
		//Valido Bloque 2
		$suma2 = $v[8]*3 + $v[9]*9 + $v[10]*7 + $v[11]*1 + $v[12]*3 + $v[13]*9 + $v[14]*7 + $v[15]*1 + $v[16]*3 + $v[17]*9 + $v[18]*7 + $v[19]*1 + $v[20]*3 ;
		$d2 =  10 - intval(substr($suma2, -1, 1));
		if( $d2 !=  $v[21]) {
			$rta = false;
		}
		//ei_arbol(array( $suma1, $d1, $v[7], $d2, $v[21] ));		
		if (!$rta) {
			return "CBU inválido";
		}		
		return true;
	}
}

?>
