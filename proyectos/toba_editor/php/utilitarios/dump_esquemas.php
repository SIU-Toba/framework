<?php
		toba::memoria()->desactivar_reciclado();
		toba::logger()->desactivar();
		$esquema = toba::memoria()->get_parametro('esquema');
		toba::logger()->debug("Recibiendo el esquema $esquema");
		$grafico = toba::memoria()->get_dato_instancia($esquema, true);
		toba_ei_esquema::servicio__mostrar_esquema($grafico);
?>