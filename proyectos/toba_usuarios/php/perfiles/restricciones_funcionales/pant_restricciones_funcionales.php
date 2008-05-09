<?php 
class pant_restricciones_funcionales extends toba_ei_pantalla
{
	function generar_layout()
	{
		$this->dep('form_restriccion')->generar_html();
		
		$img_oculto = toba_recurso::imagen_toba('error.png', true);
		$img_visible = toba_recurso::imagen_toba('vacio.png', true);
		$img_solo_lectura = toba_recurso::imagen_toba('editar.gif', true);
		$img_editable = toba_recurso::imagen_toba('no_editar.gif', true);
		$titulo = '<div style="text-align:center; background-color: white; 
								border: 1px solid gray; padding: 2px; margin-top: 10px; margin-bottom: 5px">';
		$titulo .= "<table width=100%><tr><td>$img_visible Visible</td>";
		$titulo .= "<td>$img_oculto Oculto</td>";
		$titulo .= "<td>$img_solo_lectura No Editable</td>";
		$titulo .= "<td>$img_editable Editable</td></tr></table>";
		$titulo .= '</div>';
		echo $titulo;
				
		$this->dep('arbol')->generar_html();

	}
}

?>