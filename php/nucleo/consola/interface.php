<?php

	function i_arbol($arbol,$identificador="ARBOL")
	{
		$salida .= "------------------------------------------------\n";
		$salida .= "----< $identificador >----\n";
		if(is_array($arbol)){
			i_arbol_nivel($arbol);
		}else{
			$salida .= $arbol;
		}
		$salida .= "-------------------------------------------------\n";
		return $salida;
	}

	function i_arbol_nivel($nivel)
	{
		static $n = 0;
		$salida = "<table width='100%' class='tabla-0'>\n";
		foreach( $nivel as $valor => $contenido )
		{
			$salida .= "\n";
			if (is_array($contenido))
			{
				$salida .= "<td class='arbol-valor-array'>\n";
				$n++;
				i_arbol_nivel($contenido);
				$n--;
				$salida .= "</td>\n";
			} else {
				$salida .= "<td class='arbol-valor'>" . $contenido ."</td>\n";
			}
			$salida .= "</tr>\n";
			
		}
		$salida .= "</table>\n";	
		return $salida;
	}
//----------------------------------------------------------------------------------
?>