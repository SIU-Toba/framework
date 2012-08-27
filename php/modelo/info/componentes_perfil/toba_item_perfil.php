<?php

class toba_item_perfil extends toba_elemento_perfil
{
	protected $icono = "item.gif";
	
	function permiso_activo()
	{
		return ($this->acceso_actual == true);	
	}
	
	//------------------------------------------------
	//----------- Interface FORM
	//------------------------------------------------

	function cargar_estado_post($id)
	{
		if (isset($_POST[$id.'_acceso'])) {
			if ($_POST[$id.'_acceso'] == '1') {
				$this->acceso_actual = true;	
			} else {
				$this->acceso_actual = false;
			}
		}
	}
	
	function get_input($id)
	{
		$id_js = $this->id_js_arbol;		
		$id_input = $id.'_acceso';
		$valor_inicial = $this->acceso_actual ? 1 : 0;
		$img_inicial = $this->acceso_actual ? $this->img_acceso : $this->img_sin_acceso;		
		$html = "<img src='$img_inicial' id='".$id_input."_img' onclick='$id_js.cambiar_acceso(\"{$this->get_id()}\")' />";
		if ($this->comunicacion_elemento_input) {
			$html .= "<input type='hidden' value='$valor_inicial' id='$id_input' name='$id_input' />";		
		}
		return $html;
	}
		
}

?>