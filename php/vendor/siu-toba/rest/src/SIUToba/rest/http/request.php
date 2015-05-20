<?php

namespace SIUToba\rest\http;

use SIUToba\rest\lib\rest_error;

/**
 * Clase basada en Slim - a micro PHP 5 framework para abstraer el Request.
 */
class request
{
    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_OVERRIDE = '_METHOD';

    /**
     * Special-case HTTP headers that are otherwise unidentifiable as HTTP headers.
     * Typically, HTTP headers in the $_SERVER array will be prefixed with
     * `HTTP_` or `X_`. These are not so we list them here for later reference.
     *
     * @var array
     */
    protected static $special = array(
        'CONTENT_TYPE',
        'CONTENT_LENGTH',
        'PHP_AUTH_USER',
        'PHP_AUTH_PW',
        'PHP_AUTH_DIGEST',
        'AUTH_TYPE',
    );

    protected $union; //get + post
    protected $body;

    public $headers;

    protected $encoding;

    public function __construct()
    {
        $this->headers = $this->extract_headers();
    }

    public function set_encoding_datos($encoding)
    {
        $this->encoding = $encoding;
    }

    public function get_encoding_datos()
    {
        return $this->encoding;
    }

    public function get_method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Obtiene parametros del $_GET o $_POST unidos.
     *
     * Si key es nulo devuelve todos. Sino devuelve el parametro key si existe o su default
     */
    public function params($key = null, $default = null)
    {
        if (!$this->union) {
            $this->union = array_merge($this->get(), $this->post());
        }

        return $this->get_valor_o_default($this->union, $key, $default);
    }

    /**
     * Devuelve parametros del _GET.
     *
     * Si key es nulo devuelve todos. Sino devuelve el parametro key si existe o su default
     */
    public function get($key = null, $default = null)
    {
        return $this->get_valor_o_default($_GET, $key, $default);
    }

    /**
     * Devuelve parametros del _POST - Solo se setea para formularios html.
     *
     * Si key es nulo devuelve todos. Sino devuelve el parametro key si existe o su default
     */
    public function post($key = null, $default = null)
    {
        $datos = $this->get_valor_o_default($_POST, $key, $default);

        return $this->manejar_encoding($datos);
    }

    /**
     * Devuelve parametros del POST en formato json como un arreglo.
     */
    public function get_body_json()
    {
        $body = $this->get_body();
        $json = json_decode($body, true);
        if ($body && null === $json) {
            throw new rest_error(400, "No se pudo decodificar el mensaje '$body'");
        }
        $arreglo = $this->manejar_encoding($json);

        return $arreglo;
    }

    /**
     * Devuelve los headers.
     *
     * Si key es nulo devuelve todos. Sino devuelve el parametro key si existe o su default
     */
    public function headers($key = null, $default = null)
    {
        return $this->get_valor_o_default($this->headers, $key, $default);
    }

    /**
     * Retorna el body en crudo - Usar cuando no aplica el $_POST get_post().
     *
     * @return string
     */
    public function get_body()
    {
        if (!$this->body) {
            $this->body = file_get_contents('php://input');
            if (!$this->body) {
                $this->body = '';
            }
        }

        return $this->body;
    }

    /**
     * Get Host.
     *
     * @return string
     */
    public function get_host()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            if (strpos($_SERVER['HTTP_HOST'], ':') !== false) {
                $hostParts = explode(':', $_SERVER['HTTP_HOST']);

                return $hostParts[0];
            }

            return $_SERVER['HTTP_HOST'];
        }

        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Get Port.
     *
     * @return int
     */
    public function get_puerto()
    {
        return (int) $_SERVER['SERVER_PORT'];
    }

    /**
     * Devuelve el esquema (https or http).
     *
     * @return string
     */
    public function get_esquema()
    {
        return empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off' ? 'http' : 'https';
    }

    /**
     *  URL (schema + host [ + port si no es 80 ]).
     *
     * @return string
     */
    public function get_url()
    {
        $url = $this->get_esquema().'://'.$this->get_host();
        if (($this->get_esquema() === 'https' && $this->get_puerto() !== 443) || ($this->get_esquema() === 'http' && $this->get_puerto() !== 80)) {
            $url .= sprintf(':%s', $this->get_puerto());
        }

        return $url;
    }

    public function get_request_uri()
    {
        return $_SERVER["REQUEST_URI"];
    }

    protected function extract_headers()
    {
        $results = array();
        foreach ($_SERVER as $key => $value) {
            $key = strtoupper($key);
            if (strpos($key, 'X_') === 0 || strpos($key, 'HTTP_') === 0 || in_array($key, static::$special)) {
                if ($key === 'HTTP_CONTENT_TYPE' || $key === 'HTTP_CONTENT_LENGTH') {
                    continue;
                }
                $results[$key] = $value;
            }
        }

        return $results;
    }

    protected function get_valor_o_default($arreglo, $key = null, $default = null)
    {
        if ($key) {
            if (isset($arreglo[$key])) {
                return $arreglo[$key];
            } else {
                return $default;
            }
        } else {
            return $arreglo;
        }
    }

    protected function manejar_encoding($datos)
    {
        if ($this->encoding !== 'utf-8') {
            $datos = $this->utf8_decode_fields($datos);
        }

        return $datos;
    }

    protected function utf8_decode_fields($entrada)
    {
        if (is_array($entrada)) {
            $salida = array();
            foreach ($entrada as $clave => $valor) {
                $salida[$clave] = $this->utf8_decode_fields($valor);
            }

            return $salida;
        } elseif (is_string($entrada)) {
            return utf8_decode($entrada);
        } else {
            return $entrada;
        }
    }
}
