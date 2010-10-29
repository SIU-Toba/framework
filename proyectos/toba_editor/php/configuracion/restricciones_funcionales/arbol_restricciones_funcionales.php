<?php 
class arbol_restricciones_funcionales extends toba_ei_arbol
{

	function extender_objeto_js()
	{
		$img_oculto = toba_recurso::imagen_toba('no-visible.png', false);
		$img_visible = toba_recurso::imagen_toba('visible.png', false);
		$img_solo_lectura = toba_recurso::imagen_toba('no-editable.gif', false);
		$img_editable = toba_recurso::imagen_toba('editable.gif', false);
		echo "
			function cambiar_oculto(id_input) {
				var valor_actual = $(id_input).value;
				if (valor_actual == 1) {
					//Esta oculto, hay que mostrarlo
					$$(id_input + '_img').src = '$img_visible';
					$$(id_input).value = 0;
				} else {
					//Esta visible, hay que ocultarlo
					$$(id_input + '_img').src = '$img_oculto';
					$$(id_input).value = 1;
				}
			}
			
			function cambiar_editable(id_input) {
				var valor_actual = $$(id_input).value;
				if (valor_actual == 1) {
					//Esta oculto, hay que mostrarlo
					$$(id_input + '_img').src = '$img_editable';
					$$(id_input).value = 0;
				} else {
					//Esta visible, hay que ocultarlo
					$$(id_input + '_img').src = '$img_solo_lectura';
					$$(id_input).value = 1;
				}
			}
		";
	}
	
}

?>