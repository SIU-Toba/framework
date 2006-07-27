<?php
require_once("tp_basico.php");

class tp_basico_titulo extends tp_basico
{
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
	
		/*
		if (toba::get_solicitud()->existe_ayuda()){
			$parametros = array("item"=>$info["item"],
								"proyecto"=>$info["item_proyecto"]);
			echo "<td  class='barra-0-tit' width='1'>&nbsp;";
			echo toba::get_vinculador()->obtener_vinculo_a_item("toba","/basicos/ayuda",$parametros,true);
			echo "&nbsp;</td>";
		}*/

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