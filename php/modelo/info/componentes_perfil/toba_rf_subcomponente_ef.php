<?php 
class toba_rf_subcomponente_ef extends toba_rf_subcomponente
{
	protected $formulario;
	protected $solo_lectura_original;
	protected $solo_lectura_actual;
	protected $img_solo_lectura;
	protected $img_editable;
	protected $id_ef;
	protected $deshabilitar_rest;

	function __construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $no_visible, $no_editable, $formulario, $deshabilitar_rest) 
	{
		$this->id_ef = $id;
		$id = 'ef_'.$id;
		parent::__construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $no_visible) ;
		$this->formulario = $formulario;
		$this->solo_lectura_original = $no_editable;
		$this->solo_lectura_actual = $this->solo_lectura_original;
		$this->deshabilitar_rest = $deshabilitar_rest;
		if ($this->solo_lectura_original) {
			$this->marcar_abiertos();
		}
	}	
	
	function inicializar()
	{
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( 'objetos/efs.gif', false),
				'ayuda' => 'Campo de un formulario',
				);		
	}
	
	function get_imagenes_estado()
	{
		$this->img_solo_lectura = toba_recurso::imagen_toba('no-editable.gif', false);
		$this->img_editable = toba_recurso::imagen_toba('editable.gif', false);
		parent::get_imagenes_estado();
	}

	function get_input($id)
	{
		$id_solo_lectura = $id.'_solo_lectura';
		$id_oculto = $id.'_oculto';
		$check_solo_lectura = $this->solo_lectura_actual ? 1 : 0;				
		$check_oculto = $this->no_visible_actual ? 1 : 0;
		$img_solo_lectura = $this->solo_lectura_actual ? $this->img_solo_lectura : $this->img_editable;		
		$img_oculto = $this->no_visible_actual ? $this->img_oculto : $this->img_visible;		
		
		$html = "<img src='$img_solo_lectura' id='".$id_solo_lectura."_img' title='Editable / Solo Lectura' onclick='{$this->id_js_arbol}.cambiar_editable(\"$id_solo_lectura\")' />";
		$html .= "<input type='hidden' value='$check_solo_lectura' id='$id_solo_lectura' name='$id_solo_lectura' />";
		
		if ($this->deshabilitar_rest != '1') {					//Si no desactiva explicitamente las restricciones funcionales el 
			$html .= "<img src='$img_oculto' id='".$id_oculto."_img' title='Visible / Oculto' onclick='{$this->id_js_arbol}.cambiar_oculto(\"{$this->get_id()}\")' />";
			if ($this->comunicacion_elemento_input) {
				$html .= "<input type='hidden' value='$check_oculto' id='$id_oculto' name='$id_oculto' />";
			}
		}
		return $html;
	}
	
	function cargar_estado_post($id)
	{
		if (isset($_POST[$id.'_solo_lectura'])) {
			if ($_POST[$id.'_solo_lectura'] == '1') {
				$this->solo_lectura_actual = true;	
			} else {
				$this->solo_lectura_actual = false;
			}
		}
		
		if (isset($_POST[$id.'_oculto'])) {
			if ($_POST[$id.'_oculto'] == '1') {
				$this->no_visible_actual = true;	
			} else {
				$this->no_visible_actual = false;
			}
		}	
	}
	
	function sincronizar()
	{
		$cambio_visibilidad = ($this->no_visible_actual != $this->no_visible_original);
		$cambio_modificabilidad = ($this->solo_lectura_actual != $this->solo_lectura_original);
		$ambos_cambios = ($cambio_visibilidad && $cambio_modificabilidad);
		switch (true)
		{
			case $ambos_cambios:										
							if ($this->solo_lectura_actual) {								
								$op = 'I';								
							} else {
								$op = ($this->no_visible_actual) ? 'U': 'D';	
							}				
							break;
			
			case $cambio_visibilidad: 
							if ($this->solo_lectura_actual) {
								$op = 'U';
							} else {
								$op = ($this->no_visible_actual) ? 'I': 'D';			//Si no es visible inserto, sino borro
							}
							break;

			case $cambio_modificabilidad: 
							if ($this->no_visible_actual) {
								$op = 'U';
							} else {
								$op = ($this->solo_lectura_actual) ? 'I': 'D';		//Si esta solo lectura inserto, sino borro
							}
							break;
		}
		
		if (isset($op)) {
			$solo_lectura = ($this->solo_lectura_actual) ? '1' : '0';
			$invisible = ($this->no_visible_actual) ?  '1': '0';			
			$proyecto = quote($this->proyecto);
			$restriccion = quote($this->restriccion);
			$id_ef = quote($this->id_ef);						
			
 			switch ($op) {
				case 'I':
					$item = quote($this->item);
					$form = quote($this->formulario);
					$sql = "INSERT INTO 
							apex_restriccion_funcional_ef 	(proyecto, restriccion_funcional, 
													 item,  objeto_ei_formulario_fila, 
													 objeto_ei_formulario, no_visible, no_editable)
						VALUES
							($proyecto, $restriccion, $item, $id_ef, $form, '$invisible', '$solo_lectura');";
					break;
				case 'U':
					$sql = "UPDATE 
							apex_restriccion_funcional_ef 
						SET
							no_visible = '$invisible', no_editable = '$solo_lectura'
						WHERE
								proyecto = $proyecto
							AND	restriccion_funcional = $restriccion
							AND objeto_ei_formulario_fila = $id_ef;";					
					break;
				case 'D':
					$sql = "DELETE FROM
							apex_restriccion_funcional_ef
						WHERE
								proyecto = $proyecto
							AND	restriccion_funcional = $restriccion
							AND objeto_ei_formulario_fila = $id_ef;";	
					break;					
			}
			toba::db()->ejecutar($sql);
		}		
	}	
}
?>