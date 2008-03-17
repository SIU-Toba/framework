<?php

class toba_item_perfil extends toba_elemento_perfil
{
	protected $icono = "item.gif";

	function permiso_activo()
	{
		return $this->acceso_actual == 1;	
	}
	
	//------------------------------------------------
	//----------- Interface FORM
	//------------------------------------------------

	function cargar_estado_post($id)
	{
		if (isset($_POST[$id.'_acceso'])) {
			$this->acceso_actual = $_POST[$id.'_acceso'];
		} else {
			$this->acceso_actual = '';
		}
	}
	
	function get_input($id)
	{
		$check_acceso = $this->acceso_actual ? 'checked' : '';		
		$html = '';
		$html .= "<input type='checkbox' $check_acceso value='1' name='".$id."_acceso' />";
		return $html;
	}

}

?>