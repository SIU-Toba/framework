<?php
require_once('modelo/info/contexto_info.php');

class sesion_editor extends toba_sesion
{
	function conf__inicial($datos)
	{
		if(!isset($datos)) throw new toba_error('ERROR: El editor no se cargo correctamente');
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
		$this->conf__activacion();
		//Si la sesion del proyecto editado no esta finalizada, la finalizo a la fuerza.
		$proyecto = toba_editor::get_proyecto_cargado();
		if ( $proyecto != toba_editor::get_id() ) {
			if ( toba::manejador_sesiones()->existe_sesion_activa($proyecto) ) {
				$msg = 'El proyecto estaba en modo edicion y el usuario finalizo la sesion del editor.';
				toba::manejador_sesiones()->abortar_sesion_proyecto($proyecto, $msg);
			} elseif (toba::manejador_sesiones()->existe_proyecto_cargado($proyecto)) {
				//El proyecto puede estar cargado para mostrar un item publico, como la pantalla de login.
				toba::manejador_sesiones()->borrar_segmento_proyecto($proyecto);
			}
			
		}
		toba_editor::finalizar();		
	}
}
?>