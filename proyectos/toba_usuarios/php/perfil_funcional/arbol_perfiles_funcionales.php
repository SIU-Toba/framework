<?php 
class arbol_perfiles_funcionales extends toba_ei_arbol
{
	function extender_objeto_js()
	{
		$img_acceso = toba_recurso::imagen_toba('vacio.png', false);
		$img_sin_acceso = toba_recurso::imagen_toba('error.png', false);
		echo "
			function cambiar_acceso(id_input) {
				var valor_actual = $(id_input).value;
				if (valor_actual == 1) {
					//Esta oculto, hay que mostrarlo
					$(id_input + '_img').src = '$img_acceso';
					$(id_input).value = 0;
				} else {
					//Esta visible, hay que ocultarlo
					$(id_input + '_img').src = '$img_sin_acceso';
					$(id_input).value = 1;
				}
			}
		";
	}
}

?>