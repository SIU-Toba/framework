<?php
/**
 * Created by IntelliJ IDEA.
 * User: andres
 * Date: 1/14/15
 * Time: 6:01 PM.
 */

namespace SIUToba\rest\seguridad\autenticacion\oauth2;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class oauth_token_decoder_web extends oauth_token_decoder
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzle_client;

    /**
     * @var tokeninfo_translation_helper
     */
    protected $tokeninfo_translation_helper;

    /**
     * @param \GuzzleHttp\Client $guzzle_client un cliente guzzle inicializado con la URL para pedir los tokens.
     *                                          La URL debe estar completa, lo único que se agrega en el pedido realizado es el parámetro del token
     */
    public function __construct(Client $guzzle_client)
    {
        $this->guzzle_client = $guzzle_client;
    }

    private function get_cache_key_for_token($token)
    {
        return $token;
    }

    /**
     * @param string $token un token oauth
     *
     * @return token_info
     */
    public function decode($token)
    {
        if ($this->cache->contains($this->get_cache_key_for_token($token))) {
            return $this->cache->fetch($token);
        }

        try {
            $res = $this->guzzle_client->get("?access_token=$token");
        } catch (ClientException $e) {
            // si falló el cliente por alguna razón (500, 400, etc) se retorna nulo
            return;
        }

        $tokeninfo = $this->tokeninfo_translation_helper->translate_token_info($res->json());
        $this->cache->save($token, $tokeninfo);

        return $tokeninfo;
    }

    /**
     * @param \SIUToba\rest\seguridad\autenticacion\oauth2\tokeninfo_translation_helper $tokeninfo_translation_helper
     */
    public function set_tokeninfo_translation_helper($tokeninfo_translation_helper)
    {
        $this->tokeninfo_translation_helper = $tokeninfo_translation_helper;
    }
}
