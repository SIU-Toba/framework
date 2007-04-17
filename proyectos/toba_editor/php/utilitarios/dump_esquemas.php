<?php
		toba::memoria()->desactivar_reciclado();
		$esquema = toba::memoria()->get_parametro('esquema');
		toba::logger()->debug("Recibiendo el esquema $esquema");
		$grafico = toba::memoria()->get_dato_sincronizado($esquema);
		toba_ei_esquema::servicio__mostrar_esquema($grafico);

?>