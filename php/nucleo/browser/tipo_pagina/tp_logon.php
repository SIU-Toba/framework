<?
require_once("tp_basico.php");

class tp_logon extends tp_basico
{

	function pre_contenido()
	{
		echo "\n<div align='center' class='cuerpo'>\n";		
		echo "<div style='padding-top: 10px;padding-bottom: 25px;'>". recurso::imagen_apl("siu.gif",true) . "</div>";
	}
	
	function post_contenido()
	{
		
		echo "<div style='padding-top: 25px;' class='portada-comentario'>Desarrollado por el <strong><a href='http://www.siu.edu.ar' style='text-decoration: none' target='_blank'>SIU</a></strong></div>
			<div class='portada-comentario'>2002-2006</div>";
	}

}