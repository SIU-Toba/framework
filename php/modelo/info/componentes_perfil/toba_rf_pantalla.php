<?php 
class toba_rf_pantalla extends toba_rf
{
	protected $icono = 'solic_consola.gif';
	protected $pantalla;
	protected $ci;

	function __construct($restriccion, $item, $datos, $padre=null, $id=null)
	{
		$this->no_visible_original = ($datos['no_visible'] !='') ? true : false;
		$this->no_visible_actual = $this->no_visible_original;
		$this->pantalla = $datos['pantalla'];
		$this->proyecto = $datos['proyecto'];
		$this->restriccion = $restriccion;
		$this->ci = $datos['objeto_ci'];
		$this->nombre_largo = $datos['etiqueta'];
		$this->imagen = $datos['imagen'];
		$this->imagen_origen = $datos['imagen_recurso_origen'];
		$this->item = $item;
		parent::__construct($datos['etiqueta'], $padre, $id);
		if ($this->no_visible_original) {
			$this->marcar_abiertos();
		}
		$this->get_imagen();
	}
	
	function sincronizar()
	{
		if($this->no_visible_original != $this->no_visible_actual) {			
			$proyecto = quote($this->proyecto); 
			$restriccion = quote($this->restriccion); 
			$item = quote($this->item);
			$pantalla = quote($this->pantalla);
			$ci = quote($this->ci);
			$invisible = ($this->no_visible_actual) ? '1' : '0';
						
			if ($this->no_visible_actual) {
				$sql = "INSERT INTO
							apex_restriccion_funcional_pantalla (proyecto, restriccion_funcional, item, pantalla, objeto_ci, no_visible)
						VALUES
							($proyecto, $restriccion, $item, $pantalla, $ci, '$invisible');";
			} else {
				$sql = "DELETE FROM
							apex_restriccion_funcional_pantalla
						WHERE
							proyecto = $proyecto
						AND restriccion_funcional = $restriccion
						AND pantalla = $pantalla;";
			}
			toba::db()->ejecutar($sql);
		}
		foreach ($this->get_hijos() as $hijos){			
			$hijos->sincronizar();
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