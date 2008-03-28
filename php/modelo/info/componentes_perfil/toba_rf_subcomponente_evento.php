<?php 
class toba_rf_subcomponente_evento extends toba_rf_subcomponente
{
	function inicializar()
	{
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( 'enter.png', false),
				'ayuda' => "Carpeta que contiene operaciones.",
				);		
	}

	function get_input($id)
	{
		$id_input = $id.'_oculto';
		$check_oculto = $this->no_visible_actual ? 'checked' : '';
		$html = '';
		$html .= "<LABEL for='$id_input'>Ocultar</LABEL>";
		$html .= "<input type='checkbox' $check_oculto value='1' id='$id_input' name='$id_input' />";
		return $html;
	}
	
	function cargar_estado_post($id)
	{
		if (isset($_POST[$id.'_oculto'])) {
			$this->no_visible_actual = $_POST[$id.'_oculto'];
		} else {
			$this->no_visible_actual = '';
		}		
	}

	function sincronizar()
	{
		if ($this->no_visible_actual != $this->no_visible_original) {
			if ($this->no_visible_actual == 1) {
				$sql = "INSERT INTO 
							apex_restriccion_funcional_evt (proyecto, restriccion_funcional, item, evento_id, no_visible)
						VALUES
							('$this->proyecto', '$this->restriccion', '$this->item', '$this->id', '$this->no_visible_actual');";
			}else{
				$sql = "DELETE FROM
							apex_restriccion_funcional_evt
						WHERE
								proyecto = '$this->proyecto'
							AND	restriccion_funcional = '$this->restriccion'
							AND evento_id = '$this->id';";
			}
			echo $sql."<br>";
			//toba::db()->ejecutar($sql);
		}
	}
	
}
?>