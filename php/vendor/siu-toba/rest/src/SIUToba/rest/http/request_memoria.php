<?php

namespace SIUToba\rest\http;

/**
 * Un request que saca las cosas de memoria en lugar del servidor.
 * Utilizado para testeo.
 */
class request_memoria extends request
{
    protected $union;
    protected $body;

    public $headers;
    public $get;
    public $post;
    public $method;
    public $url;

    public function __construct($method, $url, $get, $post, $headers)
    {
        $this->method = $method;
        $this->headers = $headers;
        $this->url = $url;
        $this->get = $get;
        $this->post = $post;
    }

    public function get_method()
    {
        return $this->method;
    }

    public function get($key = null, $default = null)
    {
        return $this->get_valor_o_default($this->get, $key, $default);
    }

    public function post($key = null, $default = null)
    {
        return $this->get_valor_o_default($_POST, $key, $default);
    }

    public function get_body_json()
    {
        return $this->post;
    }

    public function headers($key = null, $default = null)
    {
        return $this->get_valor_o_default($this->headers, $key, $default);
    }

    public function get_body()
    {
        return $this->post;
    }

    public function get_host()
    {
        return 'testeo';
    }

    public function get_puerto()
    {
        return 80;
    }

    public function get_esquema()
    {
        return 'https';
    }

    public function get_url()
    {
        return $this->url;
    }

    public function get_request_uri()
    {
        return $this->url;
    }
}
