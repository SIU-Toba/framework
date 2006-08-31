<?php
		toba::hilo()->desactivar_reciclado();
		require_once('nucleo/componentes/interface/toba_ei_esquema.php');
		$esquema = toba::hilo()->obtener_parametro('esquema');
		toba::logger()->debug("Recibiendo el esquema $esquema");
		$grafico = toba::hilo()->recuperar_dato($esquema);
		toba_ei_esquema::servicio__mostrar_esquema($grafico);

?>