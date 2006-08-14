<?php
require_once("tp_basico.php");

class tp_basico_titulo extends tp_basico
{
	protected $clase_encabezado = 'encabezado';	

	protected function barra_superior()
	{
		$info = toba::get_solicitud()->get_datos_item();			
		echo "<div class='item-barra'>";
		if (trim($info['item_descripcion']) != '') {
			echo "<div class='item-barra-ayuda'>";
			echo recurso::imagen_apl("ayuda_grande.gif", true, 22, 22, trim($info['item_descripcion']));
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