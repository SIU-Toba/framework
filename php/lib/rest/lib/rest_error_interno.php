<?php

namespace rest\lib;

use Exception;

/**
 * Class rest_error_interno
 * Solamente un wrapper de Exception para lanzar excepciones que son errores de programaci�n/configuracion
 * y no deber�an exponerse en la api
 * @package rest\lib
 */
class rest_error_interno extends \Exception
{


} 