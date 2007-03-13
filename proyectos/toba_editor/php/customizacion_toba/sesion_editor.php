<?php
require_once('modelo/info/contexto_info.php');

class sesion_editor extends toba_sesion
{
	function conf__inicial($datos)
	{
		if(!isset($datos)) throw new toba_error('ERROR: El editor no se cargo correctamente.');
		toba_editor::iniciar($datos['instancia'], $datos['proyecto']);
	}

	function conf__activacion()
	{
		toba_editor::referenciar_memoria();
		contexto_info::set_proyecto( toba_editor::get_proyecto_cargado() );
		contexto_info::set_db( toba_editor::get_base_activa() );
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