<?php

/*
* desde la hoja de datos
* 
 	function procesar_vinculos()
	{
		global $canal;
		$indice = 0;
		foreach($this->def_vinculos as $vinculo){
			$this->vinculos[$vinculo["tipo"]][$vinculo["orden"]]["destino"] = $canal->generar_vinculo_pers($vinculo["catalogo"]);
			$this->vinculos[$vinculo["tipo"]][$vinculo["orden"]]["nombre"] = $vinculo["nombre"];
			$this->vinculos[$vinculo["tipo"]][$vinculo["orden"]]["indice"] = $indice++;
		}
	}

	function generar_popup_vinculos()
	{
		if(is_array($this->def_vinculos))
		{
			if(count($this->def_vinculos)>0){
				$html ="<div ID='navegar' style='position:absolute; top:300px; left:400px; z-index:99;  visibility:hidden; width:200px;'>
					<table border='1' cellspacing='0' cellpadding='2' width='100%'>
					<tr><td class='hoja-vinculo-cabecera'>
							<table border='0' cellspacing='0' cellpadding='1' width='100%'>
							<tr>
								<td class='hoja-vinculo-titulo' width='100%'>Seleccionar Destino</td>
								<td><a href='#' onClick=\"toggleBox('navegar',0);return false\"><img src='". $canal->imagen_general("cerrar.gif") ."' border='0'></a></td>
							</tr>
							</table>
					</td></tr>
					<tr><td class='hoja-vinculo-cuerpo'>
					<table border='0' cellspacing='1' cellpadding='2' width='100%'>";
				foreach($this->vinculos["zoom"] as $vinculo){
					$html .= "<tr><td><a href=\"javascript:navegar_zoom('{$vinculo['indice']}')\" class='hoja-vinculo-zoom'>{$vinculo['nombre']}</a></td></tr>";
				}
				foreach($this->vinculos["popup"] as $vinculo){
					$html .= "<tr><td><a href=\"javascript:navegar_popup('{$vinculo['indice']}')\" class='hoja-vinculo-popup'>{$vinculo['nombre']}</a></td></tr>";
				}
				$html .= "</table></td></tr></table></div>";
				return $html;
			}
		}
	}

 	function generar_array_vinculos()
	{
		$js = "array_vinculos = new Array();\n";
		foreach($this->vinculos["zoom"] as $vinculo){
			$js .= "array_vinculos[{$vinculo['indice']}]=\"{$vinculo['destino']}\";\n";
		}
		foreach($this->vinculos["popup"] as $vinculo){
			$js .= "array_vinculos[{$vinculo['indice']}]=\"{$vinculo['destino']}\";\n";
		}
		return 	$js;	
	}
*/

?>