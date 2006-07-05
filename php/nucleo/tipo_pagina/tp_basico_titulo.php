<?php
require_once("tp_basico.php");

class tp_basico_titulo extends tp_basico
{
	protected function barra_superior()
	{
		echo "<table width='100%' class='item-barra'><tr>";
		echo "<td width='1'>". gif_nulo(8,22) . "</td>";
		$info = toba::get_solicitud()->get_datos_item();			
	
		echo "<td width='99%' class='item-barra-tit'>".$info['item_nombre']."</td>";

		if (trim($info['item_descripcion']) != '') {
			echo "<td class='item-barra-ayuda' width='1'>";
			echo recurso::imagen_apl("ayuda_grande.gif", true, 22, 22, trim($info['item_descripcion']));
			echo "</td>";
		}			
		/*
		if (toba::get_solicitud()->existe_ayuda()){
			$parametros = array("item"=>$info["item"],
								"proyecto"=>$info["item_proyecto"]);
			echo "<td  class='barra-0-tit' width='1'>&nbsp;";
			echo toba::get_vinculador()->obtener_vinculo_a_item("toba","/basicos/ayuda",$parametros,true);
			echo "&nbsp;</td>";
		}*/

		echo "</tr></table>\n\n";
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