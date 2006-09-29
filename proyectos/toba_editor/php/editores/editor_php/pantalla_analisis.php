<?php 

class pantalla_analisis extends toba_ei_pantalla 
{
	function generar_layout()
	{
		ei_separador($this->controlador->archivo_php()->nombre());
		$analisis = $this->controlador->clase_php()->analizar();
		echo "<div style='text-align: left;'>";
		foreach( $analisis as $clase => $seccion ) {
			echo "<h3>Clase: ".$clase."</h3>";
			echo "<ul>";
			foreach( $seccion as $nombre => $metodos ) {			
				echo "<li><strong>$nombre</strong></li>\n";
				echo "<ul>\n";				
				foreach( $metodos as $metodo ) {			
					echo "<li style='padding-right: 10px;'>&nbsp;";
					echo $metodo;
					echo "</li>\n";	
				}
				echo "</ul>";
			}
			echo "</ul>";
		}
		echo "</div>";
	}	
}
?>