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
	
	function barra_superior()
	{
		echo "<div id='barra-superior' class='barra-superior barra-superior-tit barra-popup'>\n";		
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
