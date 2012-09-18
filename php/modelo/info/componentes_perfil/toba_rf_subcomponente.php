<?php 
class toba_rf_subcomponente extends toba_rf
{
	
	function __construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $estado_original) 
	{
		parent::__construct($nombre, $padre);
		$this->proyecto = $proyecto;
		$this->nombre_largo = $this->nombre_corto;
		$this->id = $item.'_'.$id;
		$this->restriccion = $restriccion;
		$this->item = $item;
		$this->no_visible_original = ($estado_original != '') ? true : false;
		$this->no_visible_actual = $this->no_visible_original;
		if ($this->no_visible_original) {
			$this->marcar_abiertos();
		}
	}
	
	function get_input($id)
	{
		$id_input = $id.'_oculto';
		$valor_inicial = $this->no_visible_actual ? 1 : 0;
		$img_inicial = $this->no_visible_actual ? $this->img_oculto : $this->img_visible;		
		$html = "<img src='$img_inicial' id='".$id_input."_img' title='Visible / Oculto' onclick='{$this->id_js_arbol}.cambiar_oculto(\"{$this->get_id()}\")' />";
		if ($this->comunicacion_elemento_input) {
			$html .= "<input type='hidden' value='$valor_inicial' id='$id_input' name='$id_input' />";		
		}
		return $html;
	}
	
	function cargar_estado_post($id)
	{
		if (isset($_POST[$id.'_oculto'])) {
			if ($_POST[$id.'_oculto'] == '1') {
				$this->no_visible_actual = true;	
			} else {
				$this->no_visible_actual = false;
			}
		}	
	}	
}
?>