<?php
require_once("toba_tp_basico_titulo.php");

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
		echo "<table width='100%' class='item-barra'><tr>";
		echo "<td width='1'>". gif_nulo(8,22) . "</td>";
		echo "<td width='95%' class='item-barra-tit'>".$this->titulo_pagina()."</td>";
		echo "</tr></table>\n\n";		
	}
	
	function pie()
	{
		echo "\n\n";
		echo "<table width='100%' class='item-barra'><tr>";
		echo "<td>".gif_nulo(8,22)."</td>";
		echo "</tr></table>\n\n";
		parent::pie();
	}

}


?>
