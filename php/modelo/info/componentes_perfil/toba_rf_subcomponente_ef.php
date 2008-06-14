<?php 
class toba_rf_subcomponente_ef extends toba_rf_subcomponente
{
	protected $formulario;
	protected $solo_lectura_original;
	protected $solo_lectura_actual;
	protected $img_solo_lectura;
	protected $img_editable;
	protected $id_ef;

	function __construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $no_visible, $no_editable, $formulario) 
	{
		$this->id_ef = $id;
		$id = 'ef_'.$id;
		parent::__construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $no_visible) ;
		$this->formulario = $formulario;
		$this->solo_lectura_original = $no_editable;
		$this->solo_lectura_actual = $this->solo_lectura_original;
		if ($this->solo_lectura_original) {
			$this->marcar_abiertos();
		}
	}	
	
	function inicializar()
	{
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( 'objetos/efs.gif', false),
				'ayuda' => "Campo de un formulario",
				);		
	}
	
	function get_imagenes_estado()
	{
		$this->img_solo_lectura = toba_recurso::imagen_proyecto('no-editable.gif', false);
		$this->img_editable = toba_recurso::imagen_proyecto('editable.gif', false);
		parent::get_imagenes_estado();
	}

	function get_input($id)
	{
		$check_solo_lectura = $this->solo_lectura_actual ? 1 : 0;		
		$check_oculto = $this->no_visible_actual ? 1 : 0;
		$id_solo_lectura = $id.'_solo_lectura';
		$id_oculto = $id.'_oculto';
		$img_solo_lectura = $this->solo_lectura_actual ? $this->img_solo_lectura : $this->img_editable;		
		$img_oculto = $this->no_visible_actual ? $this->img_oculto : $this->img_visible;		
		
		$html = '';
		$html .= "<img src='$img_solo_lectura' id='".$id_solo_lectura."_img' title='Editable / Solo Lectura' onclick='cambiar_editable(\"$id_solo_lectura\")' />";
		$html .= "<input type='hidden' value='$check_solo_lectura' id='$id_solo_lectura' name='$id_solo_lectura' />";
		$html .= "<img src='$img_oculto' id='".$id_oculto."_img' title='Visible / Oculto' onclick='cambiar_oculto(\"$id_oculto\")' />";
		$html .= "<input type='hidden' value='$check_oculto' id='$id_oculto' name='$id_oculto' />";
		return $html;
	}
	
	function cargar_estado_post($id)
	{
		if (isset($_POST[$id.'_solo_lectura'])) {
			if ($_POST[$id.'_solo_lectura']) {
				$this->solo_lectura_actual = $_POST[$id.'_solo_lectura'];	
			} else {
				$this->solo_lectura_actual = 0;
			}
		}
		
		if (isset($_POST[$id.'_oculto'])) {
			if ($_POST[$id.'_oculto']) {
				$this->no_visible_actual = $_POST[$id.'_oculto'];	
			} else {
				$this->no_visible_actual = 0;
			}
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
			$solo_lectura = $this->solo_lectura_actual;
			$no_visible = $this->no_visible_actual;
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
							 '$this->id_ef', 
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
							AND objeto_ei_formulario_fila = '$this->id_ef';";					
					break;
				case 'D':
					$sql = "DELETE FROM
							apex_restriccion_funcional_ef
						WHERE
								proyecto = '$this->proyecto'
							AND	restriccion_funcional = '$this->restriccion'
							AND objeto_ei_formulario_fila = '$this->id_ef';";					
					break;					
			}
			toba::db()->ejecutar($sql);
		}
		
	}
	
}
?>