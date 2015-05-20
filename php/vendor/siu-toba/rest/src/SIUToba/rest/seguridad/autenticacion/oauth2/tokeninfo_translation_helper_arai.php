<?php
/**
 * Created by IntelliJ IDEA.
 * User: andres
 * Date: 2/4/15
 * Time: 3:24 PM.
 */

namespace SIUToba\rest\seguridad\autenticacion\oauth2;

class tokeninfo_translation_helper_arai implements tokeninfo_translation_helper
{
    /**
     * Recibe un arreglo con un formato específico y lo transforma a un arreglo con las siguientes claves:
     *  'user_id'
     *  'scopes'.
     *
     * @param $raw
     *
     * @return token_info
     */
    public function translate_token_info($raw)
    {
        $info = new token_info();
        $info->set_user_id($raw['user_id']);
        $info->set_scopes($raw['scope']);

        return $info;
    }
}
