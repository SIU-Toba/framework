<?php

namespace SIUToba\rest\http;

use SIUToba\rest\lib\rest_error;

/**
 * Configuraciones comunes de respuestas para REST.
 */
class respuesta_rest extends respuesta
{
    protected static $not_found_message = 'No se pudo encontrar el recurso en el servidor';

    /**
     * GET de un recurso - Devuelve 200 si es existoso.
     * Si es falso retorna un error 404 Not Found.
     *
     * @param mixed $data Array si es exitoso, o false en caso de que no exista el recurso
     *
     * @throws \SIUToba\rest\lib\rest_error
     *
     * @return $this
     */
    public function get($data)
    {
        if ($data !== false) {
            $this->get_list($data);
        } else {
            $this->not_found();
        }

        return $this;
    }

    /**
     * GET a una lista - A diferencia del get(), siempre es exitoso, ya que una lista vacia es valida.
     */
    public function get_list($data)
    {
        $this->data = $data;
        $this->status = 200;

        return $this;
    }

    /**
     * POST a la lista. Data contiene un arreglo con el identificador del nuevo recurso.
     */
    public function post($data)
    {
        $this->data = $data;
        $this->status = 201; //created
        return $this;
        //se podria incluir un header con un Location, pero hay que hacer una api para URLs primero
    }

    /**
     * PUT a un recurso. Retorna 204 (sin contenido) o 200 (con contenido) en caso de exito,
     * Si el recurso no existía, enviar un not_found().
     *
     * @return $this
     */
    public function put($data = null)
    {
        if (! isset($data)) {
            $this->status = 204; //sin contenido
        } else {
            $this->data = $data;
            $this->status = 200; //Ok
        }
        return $this;
    }

    /**
     * Retorna un 204 si es exitoso.
     * Si el recurso no existía, enviar un not_found().
     *
     */
    public function delete()
    {
        $this->put();
    }

    /**
     * Ocurrió un error de negocio- validacion, falta de datos, datos incorrectos,
     * se adjunta un mensaje con indicaciones para corregir el mensaje.
     */
    public function error_negocio($errores, $status = 400)
    {
        $this->data = $errores;
        $this->status = $status; //
        return $this;
    }

    /**
     * NO se encontró el recurso en el servidor.
     */
    public function not_found($mensaje = '', $errores = array())
    {
        if ($mensaje == '') {
            $mensaje = self::$not_found_message;
        }
        throw new rest_error(404, $mensaje, $errores);
    }

    /**
     * Redirect.
     */
    public function redirect($url, $status = 302)
    {
        $this->set_status($status);
        $this->headers['Location'] = $url;

        return $this;
    }
}
