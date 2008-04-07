<?php 
class toba_rf_pantalla extends toba_rf
{
	protected $icono = 'solic_consola.gif';
	protected $pantalla;
	protected $ci;
	protected $primer_pantalla;

	function __construct($restriccion, $item, $datos, $padre=null, $id=null, $primer_pantalla)
	{
		$this->no_visible_original = ($datos['no_visible'] !='') ? 1 : 0;
		$this->no_visible_actual = $this->no_visible_original;
		$this->pantalla = $datos['pantalla'];
		$this->proyecto = $datos['proyecto'];
		$this->restriccion = $restriccion;
		$this->ci = $datos['objeto_ci'];
		$this->nombre_largo = $datos['etiqueta'];
		$this->imagen = $datos['imagen'];
		$this->imagen_origen = $datos['imagen_recurso_origen'];
		$this->primer_pantalla = $primer_pantalla;
		$this->item = $item;
		parent::__construct($datos['etiqueta'], $padre, $id);
		if ($this->no_visible_original) {
			$this->marcar_abiertos();
		}
		$this->get_imagen();
	}
	
	function sincronizar()
	{
		if (!$this->primer_pantalla) {
			if($this->no_visible_original != $this->no_visible_actual) {
				if ($this->no_visible_actual == 1) {
					$sql = "INSERT INTO 
								apex_restriccion_funcional_pantalla (proyecto, restriccion_funcional, item, pantalla, objeto_ci, no_visible) 
							VALUES 
								('$this->proyecto','$this->restriccion','$this->item','$this->pantalla', '$this->ci', '$this->no_visible_actual');";
				} else {
					$sql = "DELETE FROM 
								apex_restriccion_funcional_pantalla
							WHERE
								proyecto = '$this->proyecto'
							AND restriccion_funcional = '$this->restriccion'
							AND pantalla = '$this->pantalla';";
				}
				toba::db()->ejecutar($sql);
			}				
		}
		foreach ($this->get_hijos() as $hijos){			
			$hijos->sincronizar();
		}
	}
	
	function get_input($id)
	{
		if (!$this->primer_pantalla) {
			$id_input = $id.'_oculto';
			$valor_inicial = $this->no_visible_actual ? 1 : 0;
			$img_inicial = $this->no_visible_actual ? $this->img_oculto : $this->img_visible;		
			$html = '';		
			$html .= "<img src='$img_inicial' id='".$id_input."_img' onclick='cambiar_oculto(\"$id_input\")' />";
			$html .= "<input type='hidden' value='$valor_inicial' id='$id_input' name='$id_input' />";		
			return $html;
		}		
	}
	
	function cargar_estado_post($id)
	{
		if (!$this->primer_pantalla) {
			if (isset($_POST[$id.'_oculto'])) {
				if ($_POST[$id.'_oculto']) {
					$this->no_visible_actual = $_POST[$id.'_oculto'];	
				} else {
					$this->no_visible_actual = 0;
				}
			}			
		}		
	}
	
}
?>