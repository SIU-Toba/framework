<?php
require_once("toba_tp_basico.php");

/**
* 
* Incluye una barra con nombre y ayuda contextual del item, 
* y centraliza el contenido de la salida del item
* 
* @package SalidaGrafica
*/
class toba_tp_basico_titulo extends toba_tp_basico
{
	protected $clase_encabezado = 'encabezado';	

	protected function barra_superior()
	{
		$info = toba::solicitud()->get_datos_item();			
		echo "<div class='item-barra'>";
		if (trim($info['item_descripcion']) != '') {
			echo "<div class='item-barra-ayuda'>";
			echo toba_recurso::imagen_toba("ayuda_grande.gif", true, 22, 22, trim($info['item_descripcion']));
			echo "</div>";
		}		
		echo "<div class='item-barra-tit'>".$info['item_nombre']."</div>";
		echo "</div>\n\n";
	}
	
	function pre_contenido()
	{
		echo "\n<div align='center' class='cuerpo'>\n";
	}
	
	function post_contenido()
	{
		echo "\n</div>\n";		
	}
			
}
?>