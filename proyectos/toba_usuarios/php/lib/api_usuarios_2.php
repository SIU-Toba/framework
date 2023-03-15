<?php

use GuzzleHttp\Exception\RequestException;

/**
 * ACCESO y MANEJO de los servicios REST de ARAI-Usuarios.
 * para la API v2
 */
class api_usuarios_2 extends api_usuarios_1 implements InterfaseApiUsuarios
{
    private static $instancia;
    private $uid;

    public static function instancia($cliente)
    {
        if (!isset(self::$instancia)) {
            self::$instancia = new api_usuarios_2($cliente);
        }
        return self::$instancia;
    }

    /**
    *	Eliminar la instancia actual
    */
    public static function eliminar_instancia()
    {
        self::$instancia = null;
    }

    //-----------------------------------------------------------------------------------
    //---- Auxiliares -------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    public function get_usuarios($filtro=array(), $excluir_aplicacion = null)
    {
        $query = $datos = array();
        $url = 'usuarios';
        if (! is_null($excluir_aplicacion)) {
            $query['excluir_aplicacion'] = $excluir_aplicacion;
        }

        if (isset($filtro) && is_array($filtro)) {
            foreach ($filtro as $id => $campo) {
                $valor = (is_array($campo['valor'])) ? $campo['valor']['desde'] . ';' . $campo['valor']['hasta'] : $campo['valor'];
                $query[$id] = $campo['condicion'] . ';' . $valor;
            }
        }
        try {
            // obtengo la respuesta
            $response = $this->cliente->get($url, array('query' => $query));
            $datos = rest_decode($response->getBody()->__toString());
            foreach ($datos as $clave => $dato) {
                $datos[$clave]['nombre_apellido'] = $dato['nombre'] . ' ' . $dato['apellido'];
            }
        } catch (RequestException $e) {
            $this->manejar_excepcion_request($e);
        } catch (Exception $e) {
            throw new toba_error(toba::escaper()->escapeJs($e));
        }
        return $datos;
    }

    public function get_usuario($identificador)
    {
        try {
            $uid = $this->get_uid_x_identificador($identificador);
            if ($uid !== null) {
                $url = "usuarios/$uid";
                // obtengo la respuesta
                $response = $this->cliente->get($url);
                $datos = rest_decode($response->getBody()->__toString());
                if (! empty($datos)) {
                    $datos['nombre_apellido'] = $datos['nombre'] . ' ' . $datos['apellido'];
                }
            }
        } catch (RequestException $e) {
            $this->manejar_excepcion_request($e);
        } catch (Exception $e) {
            throw new toba_error(toba::escaper()->escapeJs($e));
        }
        return $datos;
    }

    public function agregar_cuenta($identificador_aplicacion, $datos_cuenta)
    {
        try {
            $uid = $this->get_uid_x_identificador($datos_cuenta['identificador_usuario']);
            if (null !== $uid) {
                $url = "aplicaciones/$identificador_aplicacion/cuentas";
                $datos_cuenta['identificador_usuario'] = $uid;                  //Reemplaza el "identificador" de v1 por UID en v2

                // obtengo la respuesta
                $response = $this->cliente->post($url, array('body' => rest_encode($datos_cuenta)));
                $datos = rest_decode($response->getBody()->__toString());
            }
        } catch (RequestException $e) {
            $this->manejar_excepcion_request($e);
        } catch (Exception $e) {
            throw new toba_error(toba::escaper()->escapeJs($e));
        }
        return $datos;
    }

    public function get_identificador_x_aplicacion_cuenta($identificador_aplicacion, $cuenta)
    {
        $datos = $this->get_cuenta($identificador_aplicacion, $cuenta);
        if (isset($datos) && !empty($datos)) {
            return $datos['identificador_usuario'];
        } else {
            return null;
        }
    }

    private function get_uid_x_identificador($identificador)
    {
        if (! isset($this->uid)) {
            $response = $this->cliente->get("uuid/$identificador");
            $datos = rest_decode($response->getBody()->__toString());
            if (! empty($datos) && $identificador == $datos['identificador']) {
                $this->uid = $datos['uid'];
            } else {
                toba_logger::instancia()->error('La api devolvio: '. var_export($datos, true));
                throw new toba_error('Usuario no válido');
            }
        }

        return $this->uid;
    }
}
