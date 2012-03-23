<?php

class contexto_ejecucion_editor implements toba_interface_contexto_ejecucion
{
	public $tiempo_minimo_control = 30; //tiempo expresado en segundos
	protected $chequeado = false;
	
	function conf__inicial()
	{
		require_once('admin_util.php');		
		//*********  FRAMES entorno EDICION ************
		//-- FRAME control
		define('apex_frame_control', 'frame_control');
		//-- FRAME lista
		define('apex_frame_lista', 'frame_lista');
		//-- FRAME central
		define('apex_frame_centro', 'frame_centro');
		// Emular el contexto en el consumo por consola.
		if (php_sapi_name() === 'cli') {
			toba_editor::iniciar(toba::instancia()->get_id(), toba_editor::get_id());
			toba_editor::referenciar_memoria();
		}
		toba_zona::set_modo_url(true);
		$clases = array('ci_editores_toba');
		toba_modelo_proyecto::set_clases_excluidas_autoload($clases);
	}

	function conf__final()
	{
		//Deshabilitado hasta encontrar una forma mas eficiente de llevarlo a cabo
		/*$chequeo_activado = toba::instalacion()->chequea_sincro_svn();
		if (toba_editor::activado() && ! $this->chequeado && $chequeo_activado) {
			//Realizo el control sobre las revisiones de codigo
			$ultimo_control = toba::memoria()->get_dato('ultimo_control_revision');
			$actual = time();
			if (is_null($ultimo_control)) { //Para la primera vez que entra
				$ultimo_control = 0;
			}
			//Si pasaron mas de 30 sgs disparo el control
			if ($ultimo_control < ($actual - $this->tiempo_minimo_control)) {
				$this->chequear_sincro_svn();
				//Seteo el ts del ultimo control
				toba::memoria()->set_dato('ultimo_control_revision', $actual);
			}
		}*/
	}
	
	function chequear_sincro_svn()
	{
		$this->chequeado = true;
		$proy = toba_editor::get_modelo_proyecto();
		try {
				$proy->chequear_actualizacion_prematura();
		} catch(toba_error_def $e) {
				//Hubo update prematuro
				$msg = $e->getMessage();
				$msg = str_replace("\\", "\\\\", $msg);
				$msg = nl2br($msg);
				toba::notificacion()->agregar($msg, 'error');
				//Muestro msg de error si los hubiera
				$tags_js = toba::memoria()->get_servicio_solicitado() != 'html_parcial';
				toba::notificacion()->mostrar($tags_js);
		} catch(toba_error $e) {}		
	}

}
?>