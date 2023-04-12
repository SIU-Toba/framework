<?php

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;

/**
 * ACCESO y MANEJO de los servicios REST de ARAI-Usuarios.
 */
class rest_arai_usuarios
{
    private static $instancia;
    protected $cliente;

    /**
     * Metodo para instanciar un cliente compatible de API
     * @param boolean $recargar Flag que determina si recarga la instancia
     */
    public static function instancia($recargar=false)
    {
        if (!isset(self::$instancia) || $recargar) {
            $wrapper = new rest_arai_usuarios();
            self::$instancia = $wrapper->instanciarClienteCompatible();
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

    private function instanciarClienteCompatible()
    {
        $this->cliente = $this->get_cliente_rest();
        $major = $this->getVersionApi();
        switch ($major) {
            case '2':
                $cliente = api_usuarios_2::instancia($this->cliente);
                break;
            case '1':
            default:
                $cliente = api_usuarios_1::instancia($this->cliente);
        }
        return $cliente;
    }

    private function getVersionApi()
    {
        try {
            $response = $this->cliente->get('info');
            $datos = rest_decode($response->getBody()->__toString());
            return (! empty($datos)) ? $datos[0]['api_major'] : null;
        } catch (RequestException $ex) {
            $this->manejar_excepcion_request($ex);
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            toba_logger::instancia()->error($msg);
            throw new toba_error(toba::escaper()->escapeJs($msg));
        }
    }
    //-----------------------------------------------------------------------------------
    //---- Auxiliares -------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    /**
     * Ver http://docs.guzzlephp.org/en/latest/docs.html
     * @return GuzzleHttp\Client
     */
    protected function get_cliente_rest()
    {
        try {
            $opciones = array();
            $cliente = toba::servicio_web_rest('rest_arai_usuarios', $opciones);
            return $cliente->guzzle();
        } catch (toba_error $e) {
            toba_logger::instancia()->error("Hay un problema de configuración del cliente REST.\n Mensaje: " . $e->get_mensaje());
            throw new toba_error_usuario('Hay un problema de configuración del cliente REST. Por favor asegurese de configurarlo correctamente en el archivo cliente.ini del servicio usado.');
        }
    }

    private function manejar_excepcion_request(RequestException $e)
    {
        $msg = $e->getMessage() . PHP_EOL . Psr7\Message::toString($e->getRequest()). PHP_EOL;
        if ($e->hasResponse()) {
            $msg .= Psr7\Message::toString($e->getResponse()) . PHP_EOL;
        }
        toba_logger::instancia()->error($msg);
        throw new toba_error(toba::escaper()->escapeJs($e->getMessage()));
    }
}
