<?php 
class toba_rf_subcomponente_ef extends toba_rf_subcomponente
{
	protected $estado_original_editable = null;

	function __construct($nombre, $padre, $id, $item, $restriccion, $estado_original_visible, $estado_original_editable) 
	{
		parent::__construct($nombre, $padre, $id, $item, $restriccion, $estado_original_visible) ;
		$this->estado_original_editable = $estado_original_editable;
	}	
	
	function inicializar()
	{
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( 'objetos/efs.gif', false),
				'ayuda' => "Carpeta que contiene operaciones.",
				);		
	}

	function get_input($id)
	{
		$check_solo_lectura = $this->solo_lectura_actual ? 'checked' : '';		
		$check_oculto = $this->no_visible_actual ? 'checked' : '';
		$html = '';
		$html .= "<LABEL for='".$id."_solo_lectura'>Solo lectura</LABEL>";
		$html .= "<input type='checkbox' $check_solo_lectura value='1' name='".$id."_solo_lectura' />";
		$html .= "<LABEL for='".$id."_oculto'>Ocultar</LABEL>";
		$html .= "<input type='checkbox' $check_oculto value='1' name='".$id."_oculto' />";
		return $html;
	}
	
	function cargar_estado_post($id)
	{
		if (isset($_POST[$id.'_solo_lectura'])) {
			$this->solo_lectura_actual = $_POST[$id.'_solo_lectura'];
		} else {
			$this->solo_lectura_actual = '';
		}
		
		if (isset($_POST[$id.'_oculto'])) {
			$this->no_visible_actual = $_POST[$id.'_oculto'];
		} else {
			$this->no_visible_actual = '';
		}		
	}

	function sincronizar()
	{
		
	}
	
}
?>