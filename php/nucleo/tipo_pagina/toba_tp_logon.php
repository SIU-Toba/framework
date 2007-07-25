<?php
/**
 * Tipo de página pensado para pantallas de login, presenta un logo y un pie de página básico
 * 
 * @package SalidaGrafica
 */
class toba_tp_logon extends toba_tp_basico
{

	function pre_contenido()
	{
		echo "<div class='login-titulo'>". toba_recurso::imagen_proyecto("logo.gif",true) . "</div>";		
		echo "\n<div align='center' class='cuerpo'>\n";		
	}

	function post_contenido()
	{
		echo "</div>";		
		echo "<div class='login-pie'>";
		echo "<div>Desarrollado por <strong><a href='http://www.siu.edu.ar' style='text-decoration: none' target='_blank'>SIU</a></strong></div>
			<div>2002-".date('Y')."</div>";
		echo "</div>";
	}
}
?>