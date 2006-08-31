<?php
		toba::get_hilo()->desactivar_reciclado();
		require_once('nucleo/componentes/interface/toba_ei_esquema.php');
		$esquema = toba::get_hilo()->obtener_parametro('esquema');
		toba::get_logger()->debug("Recibiendo el esquema $esquema");
		$grafico = toba::get_hilo()->recuperar_dato($esquema);
		toba_ei_esquema::servicio__mostrar_esquema($grafico);

?>