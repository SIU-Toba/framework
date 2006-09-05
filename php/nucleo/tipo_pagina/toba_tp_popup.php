<?php
require_once("toba_tp_basico_titulo.php");

class toba_tp_popup extends toba_tp_basico_titulo 
{
	
	protected function comienzo_cuerpo()
	{
		parent::comienzo_cuerpo();
		$hilo = toba::hilo();
	    $ef_popup = $hilo->get_parametro('ef_popup');
	    if ($ef_popup == null) {
	        $ef_popup = $hilo->recuperar_dato('ef_popup');
	    }
		$hilo->persistir_dato('ef_popup', $ef_popup);
	
		echo toba_js::abrir();
		echo "
			function seleccionar(clave, descripcion)
			{
				window.opener.popup_callback('". $ef_popup ."', clave, descripcion);
				window.close();
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
