<?php

require_once('lib/admin_instancia.php');

class sesion extends toba_sesion
{
    //-------------------------------------------------------------
    //-- Ventanas toba -------------------------------------
    //-------------------------------------------------------------

    public function conf__inicial($datos = null)
    {
        toba_contexto_info::set_db(admin_instancia::ref()->db());
        toba_contexto_info::set_proyecto(toba::proyecto()->get_id());
    }

    public function conf__final()
    {
        // Me abrieron desde el ADMIN
        if (toba::memoria()->existe_dato_instancia('instancia')) {
            echo toba_js::ejecutar('window.close();');
        }
    }

    public function conf__activacion()
    {
        toba_contexto_info::set_db(admin_instancia::ref()->db());
        toba_contexto_info::set_proyecto(toba::proyecto()->get_id());
    }


}
