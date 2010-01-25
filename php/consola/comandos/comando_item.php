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
	 * @consola_parametros [-p Proyecto] [-t id_item]
	 * @gtk_icono instanciar.png  
	 */
	function opcion__ejecutar()
	{
		$id_instancia = $this->get_id_instancia_actual(true);
		$id_proyecto = $this->get_id_proyecto_actual(true);
		$param = $this->get_parametros();
		if (!isset($param['-t'])) {
			throw new toba_error("Es necesario definir una OPERACION. Utilice el modificador '-t'");
		}
		$item = $param['-t'];
		
		require_once("nucleo/toba.php");
		toba::nucleo()->acceso_consola($id_instancia,$id_proyecto, $item);
	}	

	/**
	 * Permite exportar un item completo de proyecto
	 * @consola_parametros [-p Proyecto] [-t id_item]
	 * 
	 */
	function opcion__exportar()
	{
		//Pido los datos aunque no los use al principio para forzar a que esten
		$id_instancia = $this->get_id_instancia_actual(true);
		$id_proyecto = $this->get_id_proyecto_actual(true);
		$param = $this->get_parametros();
		if (!isset($param['-t'])) {
			throw new toba_error("Es necesario definir un ITEM. Utilice el modificador '-t'");
		}
		$item = $param['-t'];

		//Aca tengo que realizar el llamado a toba_proyecto.
		$proyecto = $this->get_proyecto($id_proyecto);
		$proyecto->exportar_item($item);
	}
}
?>