<?
require_once("nucleo/componentes/interface/objeto_ci.php");

class extension_ci extends objeto_ci
{

	function conf__eventos_a()
	{
		return $this->get_info_localidades();
	}

	private function get_info_localidades()
	{
		require_once('objetos/datos_ejemplos.php');
		return datos_ejemplos::get_localidades();
	}

}
?>