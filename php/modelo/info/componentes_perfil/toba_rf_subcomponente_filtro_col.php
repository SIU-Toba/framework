<?php 
class toba_rf_subcomponente_filtro_col extends toba_rf_subcomponente
{
	protected $filtro;
	protected $id_columna;
	
	function __construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $estado_original, $filtro) 
	{
		$this->filtro = $filtro;
		$this->id_columna = $id;
		$id = 'filtro_col_'.$id;
		parent::__construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $estado_original);
		
	}
	
	function inicializar()
	{
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( 'objetos/columna.gif', false),
				'ayuda' => "Columna de un filtro",
				);		
	}

	function get_input($id)
	{
		$id_input = $id.'_oculto';
		$valor_inicial = $this->no_visible_actual ? 1 : 0;
		$img_inicial = $this->no_visible_actual ? $this->img_oculto : $this->img_visible;		
		$html = '';		
		$html .= "<img src='$img_inicial' id='".$id_input."_img' title='Visible / Oculto' onclick='cambiar_oculto(\"$id_input\")' />";
		$html .= "<input type='hidden' value='$valor_inicial' id='$id_input' name='$id_input' />";		
		return $html;
	}
	
	function cargar_estado_post($id)
	{
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
		if ($this->no_visible_actual != $this->no_visible_original) {
			if ($this->no_visible_actual == 1) {
				$sql = "INSERT INTO 
							apex_restriccion_funcional_filtro_cols (proyecto, restriccion_funcional, item, objeto_ei_filtro, objeto_ei_filtro_col, no_visible)
						VALUES
							('$this->proyecto', '$this->restriccion', '$this->item', '$this->filtro', '$this->id_columna', '$this->no_visible_actual');";
			}else{
				$sql = "DELETE FROM
							apex_restriccion_funcional_filtro_cols
						WHERE
								proyecto = '$this->proyecto'
							AND	restriccion_funcional = '$this->restriccion'
							AND objeto_ei_filtro_col = '$this->id_columna';";
			}
			toba::db()->ejecutar($sql);
		}
		
	}
}
?>