<?php
require_once('lib/admin_instancia.php');

class sesion extends toba_sesion
{

	//-------------------------------------------------------------
	//-- Ventanas toba -------------------------------------
	//-------------------------------------------------------------

	function conf__inicial($datos=null)
	{
		toba_contexto_info::set_db(admin_instancia::ref()->db());
		toba_contexto_info::set_proyecto(toba::proyecto()->get_id());
	}

	function conf__final()
	{
		// Me abrieron desde el ADMIN
		if (toba::memoria()->existe_dato_instancia('instancia')) {
			echo toba_js::ejecutar('window.close();');
		}
	}

	function conf__activacion()
	{
		toba_contexto_info::set_db(admin_instancia::ref()->db());
		toba_contexto_info::set_proyecto(toba::proyecto()->get_id());
	}


}
?>