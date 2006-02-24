<?
require_once('consola/menu.php');

class menu_toba extends menu
{
	function get_titulo()
	{
		return "SIU-TOBA ( Ambiente de desarrollo WEB )";	
	}
	
	function mostrar_observaciones()
	{
		$this->consola->mensaje("Directorio de la INSTALACION: " . toba_dir() );
	}
	
	function get_comandos()
	{
		return array(
						'instancia',
						'proyecto',
						'instalacion',
						'conversion',
						'test'
					);	
	}
}
?>