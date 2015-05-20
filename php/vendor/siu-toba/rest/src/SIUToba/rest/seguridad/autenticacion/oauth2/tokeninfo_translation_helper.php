<?php
namespace SIUToba\rest\seguridad\autenticacion\oauth2;

/**
 * Se utiliza para transformar los arreglos espec�ficos que vienen de cada endpoint proveedor de autorizaci�n a un formato
 * est�ndar para la librer�a
 * Interface decoder_web_helper.
 */
interface tokeninfo_translation_helper
{
    /**
     * Recibe un arreglo con un formato espec�fico y lo transforma a un arreglo con las siguientes claves:
     *  'user_id'
     *  'scopes'.
     *
     * @param $raw
     *
     * @return token_info
     */
    public function translate_token_info($raw);
}
