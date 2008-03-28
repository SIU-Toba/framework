<?php 
class toba_rf_subcomponente_columna extends toba_rf_subcomponente
{
	protected $cuadro;
	
	function __construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $estado_original, $cuadro) 
	{
		$this->cuadro = $cuadro;
		parent::__construct($nombre, $padre, $id, $proyecto, $item, $restriccion, $estado_original);
		
	}
	
	function inicializar()
	{
		$this->iconos[] = array(
				'imagen' => toba_recurso::imagen_toba( 'objetos/columna.gif', false),
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
							apex_restriccion_funcional_cols (proyecto, restriccion_funcional, item, objeto_cuadro, objeto_cuadro_col, no_visible)
						VALUES
							('$this->proyecto', '$this->restriccion', '$this->item', '$this->cuadro', '$this->id', '$this->no_visible_actual');";
			}else{
				$sql = "DELETE FROM
							apex_restriccion_funcional_cols
						WHERE
								proyecto = '$this->proyecto'
							AND	restriccion_funcional = '$this->restriccion'
							AND objeto_cuadro_col = '$this->id';";
			}
			echo $sql."<br>";
			//toba::db()->ejecutar($sql);
		}
		
	}
}
?>