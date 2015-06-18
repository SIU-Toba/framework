<?php
class sesion_editor extends toba_sesion
{
	function conf__inicial($datos=null)
	{
		if (!isset($datos)) {
			throw new toba_error('ERROR: El editor no se cargo correctamente.');
		}
		$instancia = toba::instancia()->get_id();
		toba_editor::iniciar($instancia, $datos['proyecto']);
		//Seteo el valor en la memoria global para toba_usuarios		
		toba::memoria()->set_dato_instancia('instancia', $instancia);
		toba::memoria()->set_dato_instancia('proyecto', $datos['proyecto']);
	}

	function conf__activacion()
	{
		toba_editor::referenciar_memoria();
	}

	function conf__final()
	{
		$this->conf__activacion(); //Necesito referenciar memoria del editor.
		//Si la sesion del proyecto editado no esta finalizada, la finalizo a la fuerza.
		toba_editor::limpiar_memoria_proyecto_cargado();
		toba_editor::finalizar();		
	}
}
?>