<?php

class contexto_ejecucion_editor implements toba_interface_contexto_ejecucion
{
	function conf__inicial()
	{
		require_once('admin_util.php');		
		//*********  FRAMES entorno EDICION ************
		//-- FRAME control
		define("apex_frame_control","frame_control");
		//-- FRAME lista
		define("apex_frame_lista","frame_lista");
		//-- FRAME central
		define("apex_frame_centro","frame_centro");
		// Emular el contexto en el consumo por consola.
		if (php_sapi_name() === 'cli') {
			toba_editor::iniciar(toba::instancia()->get_id(), toba_editor::get_id());
			toba_editor::referenciar_memoria();
		}		
	}

	function conf__final() {}

}
?>