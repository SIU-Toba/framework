<?
require_once("toba_tp_basico.php");

class tp_logon extends tp_basico
{

	function pre_contenido()
	{
		echo "<div class='login-titulo'>". toba_recurso::imagen_pro("logo.gif",true) . "</div>";		
		echo "\n<div align='center' class='cuerpo'>\n";		
	}

	function post_contenido()
	{
		echo "</div>";		
		echo "<div class='login-pie'>";
		echo "<div>Desarrollado por <strong><a href='http://www.siu.edu.ar' style='text-decoration: none' target='_blank'>SIU</a></strong></div>
			<div>2002-2006</div>";
		echo "</div>";
	}

}