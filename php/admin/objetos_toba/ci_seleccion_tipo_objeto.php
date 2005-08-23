<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
require_once('admin/db/dao_editores.php');
//----------------------------------------------------------------
class ci_seleccion_tipo_objeto extends objeto_ci
{
	function obtener_html_contenido__tipos()
	{
		$tipos = dao_editores::get_clases_tipos();
		$clases = dao_editores::get_clases_editores();		
		foreach ($tipos as $tipo) {
			echo "<div style='margin: 15px'>";
			echo "<div style='margin: 5px; font-weight: bold;'>{$tipo['descripcion_corta']}</div>";
			foreach ($clases as $clase) {
				$param = array();				
				$url = toba::get_vinculador()->obtener_vinculo_a_item($clase['editor_proyecto'],$clase['editor_item'], $param, false);
				if ($clase['clase_tipo'] == $tipo['clase_tipo'] && $url) {
					$html = "";
					$html .= recurso::imagen_pro($clase['icono'], true);
					$html .= " {$clase['clase']}";
					echo form::button_html($clase['clase'], $html, "style='width: 200px' onclick='location.href=\"$url\"'");
					echo "<br>";
				}
			}
			echo "</div>";
		}
	}

}

?>