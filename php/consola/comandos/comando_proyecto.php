<?
require_once('comando.php');
require_once('modelo/proyecto.php');

class comando_proyecto extends comando_toba
{
	static function get_info()
	{
		return 'Administracion de PROYECTOS';
	}
}
?>