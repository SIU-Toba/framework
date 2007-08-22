<?php
/**
 * Formato de página pensado para un popup. 
 * Se incluye un javascript para poder comunicarse con la ventana padre y además se almacena
 * en la memoria el ef que origino la apertura del popup para poder hacer esta comunicación.
 *
 * @package SalidaGrafica
 */
class toba_tp_popup extends toba_tp_basico_titulo 
{
	
	protected function comienzo_cuerpo()
	{
		parent::comienzo_cuerpo();
		$hilo = toba::memoria();
	    $ef_popup = $hilo->get_parametro('ef_popup');
	    if ($ef_popup == null) {
	        $ef_popup = $hilo->get_dato_sincronizado('ef_popup');
	    }
		$hilo->set_dato_sincronizado('ef_popup', $ef_popup);
	
		echo toba_js::abrir();
		echo "
			function seleccionar(clave, descripcion) {
				window.opener.popup_callback('". $ef_popup ."', clave, descripcion);
				window.close();
			}
			function respuesta_ef_popup(parametros) {
				var seleccion = parametros.split('||');
				seleccionar(seleccion[0], seleccion[1]);
			}
		";
		echo toba_js::cerrar();
		echo "\n\n";
	}
	
	protected function barra_superior()
	{
	
		echo "<div class='barra-superior barra-superior-tit barra-popup'>\n";		
		$info = toba::solicitud()->get_datos_item();
		echo "<div class='item-barra'>";
		if (trim($info['item_descripcion']) != '') {
			$desc = toba_parser_ayuda::parsear(trim($info['item_descripcion']));
			$ayuda = toba_recurso::ayuda(null, $desc, 'item-barra-ayuda', 0);
			echo "<div $ayuda>";
			echo toba_recurso::imagen_toba("ayuda_grande.gif", true);
			echo "</div>";
		}		
		echo "<div class='item-barra-tit'>".$this->titulo_item()."</div>";
		echo "</div>\n\n";
	}

}


?>
