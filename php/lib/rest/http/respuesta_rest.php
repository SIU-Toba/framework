<?php

namespace rest\http;

use rest\lib\rest_error;


/**
 * Configuraciones comunes de respuestas para REST
 */
class respuesta_rest extends respuesta
{
    protected static $not_found_message = 'No se pudo encontrar el recurso en el servidor';

    /**
     * GET de un recurso - Devuelve 200 si es existoso.
     * Si es falso retorna un error 404 Not Found
     * @param mixed $data Array si es exitoso, o false en caso de que no exista el recurso
     * @throws \rest\lib\rest_error
     * @return $this
     */
	public function get($data)
	{//@todo - get del listado puede arrojar vacio, get/ID seria un 404.  Y hay que separar los errores 400 (bad req)
		if($data !== false){
			$this->data = $data;
			$this->status = 200;
		}else{
            throw new rest_error(404, self::$not_found_message);
		}
		return $this;
	}

    /**
     * POST a la lista. Retorna el id del recurso creado, o un error si no se pudo crear (el identificador es nulo)
     * @param $data array arreglo asociativo con la columna id y el id del recurso creado
     * @param $errores array errores que impiden la modificación exitosa
     * @throws \rest\lib\rest_error
     * @return $this
     */
	public function post($data, $errores = array()){
		if(!empty($data)){
			$this->data = $data;
			$this->status = 201; //created
		}else{
            $this->not_found(self::$not_found_message, $errores);

		}
		return $this;
		//se podria incluir un header con un Location, pero hay que hacer una api para URLs primero
	}

    /**
     * PUT a un recurso. Retorna 204 sin contenido en caso de exito, o un error si el parametro no es vacio
     * Si el recurso no existía, enviar un not_found()
     * @param $errores array errores que impiden la modificación exitosa (ej: modelo + errores validacion)
     * @return $this
     */
    public function put($errores = array()){
        $this->data = $errores;
        if(empty($errores)){
            $this->status = 204; //sin contenido
        }else{
            $this->status = 400; //
        }
        return $this;
    }


    /**
     * Retorna un 204 si es exitoso. Si hay errores se envia 400 con el detalle.
     * Si el recurso no existía, enviar un not_found()
     * @param array $errores errores por los cuales no se pudo borrar el recurso
     * @internal param $exito
     */
    public function delete($errores = array()){
        $this->put($errores);
    }

    public function not_found($mensaje = '', $errores = array())
    {
        if($mensaje == ''){
            $mensaje = self::$not_found_message;
        }
        throw new rest_error(404, $mensaje, $errores);
    }

	/**
	 * Redirect
	 */
	public function redirect ($url, $status = 302)
	{
		$this->set_status($status);
		$this->headers['Location'] = $url;
		return $this;
	}

}