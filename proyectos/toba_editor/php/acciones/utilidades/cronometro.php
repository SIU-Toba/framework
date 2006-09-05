<?
	if($solicitud=$this->hilo->get_parametro("solicitud")){
		ei_cronometro_solicitud($solicitud);
	}else{
		echo ei_mensaje("No se especifico el ID de la SOLICITUD");
	}
?>