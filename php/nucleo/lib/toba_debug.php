<?php

/**
 * Utilidades para hacer DUMPs de Arboles, Tablas y Variable
 * @package Debug
 */

	function dump_sql($sql)
	{
		echo "<pre>$sql</pre><br />";
	}

	function dump_POST()
	{
		ei_arbol($_POST,"Contenido de \$_POST[]");
	}
//----------------------------------------------------------------------------------	

	function dump_GET()
	{
		ei_arbol($_GET,"Contenido de \$_GET[]");
	}
//----------------------------------------------------------------------------------	
	function dump_SESSION()
	{
		ei_arbol($_SESSION,"Contenido de \$_SESSION[]");
	}
//----------------------------------------------------------------------------------	
	
	function dump_CONST()
	{
		ei_arbol(get_defined_constants());
	}
//----------------------------------------------------------------------------------

	//Dumpeador precario para etapas en las que no se puede acceder a
	//lo HOJA de ESTILOS

	function dump_arbol($arbol,$identificador="ARBOL",$ancho="80%")
	{
		echo "<br>";
		echo "<table align='center' width='$ancho'>";
		echo "<tr><td  bgcolor='ff0000'><b>$identificador</b></td></tr>\n";		
		echo "<tr><td  bgcolor='000000'>\n";
		if(is_array($arbol)){
			dump_arbol_nivel($arbol);
		}else{
			echo $arbol;
		}
		echo "</td></tr>\n";
		echo "</table>\n";
		echo "<br>";
	}

	function dump_arbol_nivel($nivel)
	{
		$color="";
		static $n = 0;
		echo "<table width='100%' bgcolor='000000'>\n";
		foreach( $nivel as $valor => $contenido )
		{
			if($color=="ffff00"){
				$color="00ffff";
			}else{
				$color="ffff00";
			}
			echo "<tr><td bgcolor='$color'><b>$valor</b></td>\n";
			if (is_array($contenido))
			{
				echo "<td bgcolor='ff0000'>\n";
				$n++;
				dump_arbol_nivel($contenido);
				$n--;
				echo "</td>\n";
			} else {
				echo "<td bgcolor='ffffff'>" . $contenido ."</td>\n";
			}
			echo "</tr>\n";
			
		}
		echo "</table>\n";	
	}

?>