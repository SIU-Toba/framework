<?php

class contexto_ejecucion_editor implements toba_interface_contexto_ejecucion
{
	var $tiempo_minimo_control = 30; //tiempo expresado en segundos
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
		toba_zona::set_modo_url(true);
	}

	function conf__final()
	{
		//Realizo el control sobre las revisiones de codigo
		$ultimo_control = toba::memoria()->get_dato('ultimo_control_revision');
		$actual = time();
		if (is_null($ultimo_control)) { //Para la primera vez que entra
			$ultimo_control = $actual;
		}
		//Si pasaron mas de 30 sgs disparo el control
		if ($ultimo_control < ($actual - $this->tiempo_minimo_control)) {			
			$proy = toba_editor::get_modelo_proyecto();
			try {
					$proy->chequear_actualizacion_prematura();
			} catch(toba_error_def $e) {
					//Hubo update prematuro
					$msg = $e->getMessage();
					$msg = str_replace("\n", '<BR>', $msg); //Lo pongo bonito
					toba::notificacion()->agregar($msg, 'error');
			} catch(toba_error $e) {}
			//Seteo el ts del ultimo control
			toba::memoria()->set_dato('ultimo_control_revision', $actual);
			//Muestro msg de error si los hubiera
			toba::notificacion()->mostrar();
		}	
	}

}
?>