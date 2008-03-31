<?php 
class toba_rf_subcomponente_ef extends toba_rf_subcomponente
{
	protected $formulario;
	protected $solo_lectura_original;
	protected $solo_lectura_actual;


	function __construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $no_visible, $no_editable, $formulario) 
	{
		parent::__construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $no_visible) ;
		$this->formulario = $formulario;
		$this->solo_lectura_original = $no_editable;
		$this->solo_lectura_actual = $this->solo_lectura_original;
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
		if (($this->no_visible_actual != $this->no_visible_original) && 
			($this->solo_lectura_actual != $this->solo_lectura_original)) {	
				if ($this->no_visible_actual == 1) {
					if ($this->solo_lectura_actual == 1) {
						$op = 'I';
					}else{
						$op = 'U';
					}
				}else{
					if ($this->solo_lectura_actual == 1) {
						$op = 'I';
					}else{
						$op = 'D';
					}
				}	
		}elseif ($this->no_visible_actual != $this->no_visible_original) {
			if ($this->no_visible_actual == 1) {
					if ($this->solo_lectura_original == 1) {
						$op = 'U';
					}else{
						$op = 'I';
					}
				}else{
					if ($this->solo_lectura_original == 1) {
						$op = 'U';
					}else{
						$op = 'D';
					}
				}
		}elseif ($this->solo_lectura_actual != $this->solo_lectura_original) {
			if ($this->solo_lectura_actual == 1) {
					if ($this->no_visible_original == 1) {
						$op = 'U';
					}else{
						$op = 'I';
					}
				}else{
					if ($this->no_visible_original == 1) {
						$op = 'U';
					}else{
						$op = 'D';
					}
				}
		}
		
		if (isset($op)) {
			$solo_lectura = $this->solo_lectura_actual == '' ? 0 : 1;
			$no_visible = $this->no_visible_actual == '' ? 0 : 1;
 			switch ($op) {
				case 'I':
					$sql = "INSERT INTO 
							apex_restriccion_funcional_ef 	(proyecto, 
															 restriccion_funcional, 
															 item, 
															 objeto_ei_formulario_fila, 
															 objeto_ei_formulario, 
															 no_visible, no_editable)
						VALUES
							('$this->proyecto', 
							 '$this->restriccion', 
							 '$this->item', 
							 '$this->id', 
							 '$this->formulario', 
							 '$no_visible', '$solo_lectura');";
					break;
				case 'U':
					$sql = "UPDATE 
							apex_restriccion_funcional_ef 
						SET
							no_visible = '$no_visible', no_editable = '$solo_lectura'
						WHERE
								proyecto = '$this->proyecto'
							AND	restriccion_funcional = '$this->restriccion'
							AND objeto_ei_formulario_fila = '$this->id';";					
					break;
				case 'D':
					$sql = "DELETE FROM
							apex_restriccion_funcional_ef
						WHERE
								proyecto = '$this->proyecto'
							AND	restriccion_funcional = '$this->restriccion'
							AND objeto_ei_formulario_fila = '$this->id';";					
					break;					
			}
			echo $sql."<br>";
			//toba::db()->ejecutar($sql);
		}
		
	}
	
}
?>