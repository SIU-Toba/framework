<?php
/**
 * Created by IntelliJ IDEA.
 * User: andres
 * Date: 1/14/15
 * Time: 6:01 PM
 */

namespace rest\seguridad\autenticacion\oauth2;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class oauth_token_decoder_web extends oauth_token_decoder
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzle_client;

    /**
     * @param \GuzzleHttp\Client $guzzle_client un cliente guzzle inicializado con la URL para pedir los tokens.
     * La URL debe estar completa, lo único que se agrega en el pedido realizado es el parámetro del token
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
     * @return array asociativo con la información correspondiente a el token. Si el token es inválido o expirado
     * devuelve null
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
            return null;
        }

        $info = $res->json();
        $this->cache->save($token, $info);
        return $info;
    }
}