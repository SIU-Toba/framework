<?php
/**
 * Tipo de p�gina pensado para pantallas de login, presenta un logo y un pie de p�gina b�sico
 * 
 * @package SalidaGrafica
 */
class toba_tp_logon extends toba_tp_basico
{
	function barra_superior()
	{
		echo "
			<style type='text/css'>
				.cuerpo {
					
				}
			</style>
		";
		echo "<div id='barra-superior' class='barra-superior-login'>\n";		
	}	

	function pre_contenido()
	{
		echo "<div class='login-titulo'>". toba_recurso::imagen_proyecto("logo.gif",true);
		echo "<div>versi�n ".toba::proyecto()->get_version()."</div>";
		echo "</div>";
		echo "\n<div align='center' class='cuerpo'>\n";		
	}

	function post_contenido()
	{
		echo "</div>";		
		echo "<div class='login-pie'>";
		echo "<div>Desarrollado por <strong><a href='http://www.siu.edu.ar' style='text-decoration: none' target='_blank'>SIU-CIN</a></strong></div>
			<div>2002-".date('Y')."</div>";
		echo "</div>";
	}
}
?>