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
			if ($_POST[$id.'_acceso']) {
				$this->acceso_actual = $_POST[$id.'_acceso'];	
			} else {
				$this->acceso_actual = 0;
			}
		}
	}
	
	function get_input($id)
	{
		$id_input = $id.'_acceso';
		$valor_inicial = $this->acceso_actual ? 1 : 0;
		$img_inicial = $this->acceso_actual ? $this->img_sin_acceso : $this->img_acceso;		
		$html = '';		
		$html .= "<img src='$img_inicial' id='".$id_input."_img' onclick='cambiar_acceso(\"$id_input\")' />";
		$html .= "<input type='hidden' value='$valor_inicial' id='$id_input' name='$id_input' />";		
		return $html;
		
		/*
		$check_acceso = $this->acceso_actual ? 'checked' : '';		
		$html = '';
		$html .= "<input type='checkbox' $check_acceso value='1' name='".$id."_acceso' />";
		return $html;
		*/
	}

}

?>