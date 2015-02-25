<?php

namespace SIUToba\rest\lib;

use Exception;

/**
 * Class rest_error_interno
 * Solamente un wrapper de Exception para lanzar excepciones que son errores de programación/configuracion
 * y no deberían exponerse en la api
 * @package SIUToba\rest\lib
 */
class rest_error_interno extends \Exception
{


} 