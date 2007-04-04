<?php
require_once('comando_toba.php');

class comando_item extends comando_toba
{
	
	static function get_info()
	{
		return 'Generacion de SOLICITUDES desde la consola';
	}
	
	/**
	 * Permite ejecutar items de consola
	 *	 -p: proyecto del item
	 *	 -t: identificador del item
	 *	 -u: usuario toba con el que se ejecuta el item
	 */
	function opcion__ejecutar()
	{
		$id_instancia = $this->get_id_instancia_actual(true);
		$id_proyecto = $this->get_id_proyecto_actual(true);
		$param = $this->get_parametros();
		$usuario = 'toba';
		if (isset($param['-u'])) {
			$usuario = $param['-u'];
		}
		if (!isset($param['-t'])) {
			throw new toba_error("Es necesario definir una ITEM. Utilice el modificador '-t'");
		}
		$item = $param['-t'];
		
		define('apex_pa_ID', $_SERVER['PHP_SELF']);
		require_once("nucleo/toba.php");
		toba::nucleo()->acceso_consola($id_instancia,$id_proyecto, $item, $usuario);
	}	
	
}
?>