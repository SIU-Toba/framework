<?php
require_once("tutorial/pant_tutorial.php");

class pant_pantallas extends pant_tutorial 
{
	function generar_layout()
	{
		$icono = toba_recurso::imagen_toba('objetos/multi_etapa.gif', true);
		$icono_pant = toba_recurso::imagen_toba('objetos/pantalla.gif', true);
		$arbol = toba_recurso::imagen_proyecto('tutorial/ci-arbol.png');
		$tabs = toba_recurso::imagen_proyecto('tutorial/ci-tabs.png');
		echo "
			<div style='float:right;padding: 10px;'>
			<img src='$arbol'><br>
			<em style='font-size:80%'>Definici�n de una operaci�n con un CI de dos pantallas.</em>
			</div>
			<p>
			El Controlador de interface $icono  o CI es el componente raiz que necesitamos definir en nuestra
			operaci�n, ya que tiene la capacidad de contener otros componentes, formando las 
			distintas ramas del �rbol de una operaci�n.
			</p>
			
			<p>
			Para contener otros componentes el CI tiene la capcidad de definir
			 <span style='white-space:nowrap;'>$icono_pant <strong>Pantallas</strong> </span>
			y ser responsable de la l�gica de navegaci�n entre las mismas. La forma m�s
			usual de navegaci�n entre estas pantallas es usar solapas o tabs horizontales.
			</p>
			
			<div style='padding: 10px;'>
			<img src='$tabs'><br>
			<em style='font-size:80%'>Pantallas de un CI dispuestas en solapas horizontales</em>
			</div>
		
		";
	}
}

?>