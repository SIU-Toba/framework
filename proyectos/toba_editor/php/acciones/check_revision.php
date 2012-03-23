<?php
	/*
	 * Este archivo manejara los chequeos que realice el editor para ver si
	 * hubo un update de metadatos antes de realizar la exportacion de
	 * los mismos, en ese caso se devuelve un msg informando el problema.
	 */
	$parametro = toba::memoria()->get_parametro('chequeo_exportacion');
	//Si viene en 1 entonces chequeo la exportacion
	if (isset($parametro) && $parametro == '1') {
		$proy = toba_editor::get_modelo_proyecto();
		$ajx_response = new toba_ajax_respuesta('H');
		//Unicamente informo si hubo un update prematuro, osea si salta la excepcion
		try {
			$proy->chequear_actualizacion_prematura();
		} catch(toba_error_def $e) {
			//Hubo update prematuro
			$msg = $e->getMessage();
			$ajx_response->set($msg);
			$ajx_response->comunicar();
		} catch(toba_error $e) {

		}
	}
?>
