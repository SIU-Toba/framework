<?php 

class pant_tipo_operacion extends toba_ei_pantalla
{
	function generar_layout()
	{
		$tipos_rs = toba_info_editores::get_lista_tipo_molde();
		$tipos = array();
		foreach ($tipos_rs as $tipo) {
			$tipos[$tipo['operacion_tipo']] = array('descripcion' => $tipo['descripcion'], 'vista_previa' => toba_recurso::imagen_proyecto($tipo['vista_previa'], false));
		}
		echo toba_js::abrir();
		echo 'var tipos_operacion = '.toba_js::arreglo($tipos, true, true)."\n";
		echo toba_js::cerrar();
		$this->dep('form_tipo_operacion')->generar_html();
		//$this->generar_botones();
		//$this->generar_boton('siguiente_editar');
		echo "<div style='background-color: #808080;padding-bottom: 15px;margin-top: 5px; color: white; text-align:center; font-size:12px; font-weight: bold;'><hr>";
		echo "<div id='operacion_descripcion'></div>";
		echo "<div id='operacion_vista_previa' style='display:none; padding-top:10px;'></div>";		
		echo '</div>';
	}
	
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
		.".evt__show = function() {
			toggle_nodo($$('operacion_vista_previa'));
			return false;
		}
		";
	}
	
}


?>