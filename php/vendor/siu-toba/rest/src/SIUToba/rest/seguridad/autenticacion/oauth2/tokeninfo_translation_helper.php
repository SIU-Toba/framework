<?php
namespace SIUToba\rest\seguridad\autenticacion\oauth2;

/**
 * Se utiliza para transformar los arreglos específicos que vienen de cada endpoint proveedor de autorización a un formato
 * estándar para la librería
 * Interface decoder_web_helper.
 */
interface tokeninfo_translation_helper
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
    public function translate_token_info($raw);
}
