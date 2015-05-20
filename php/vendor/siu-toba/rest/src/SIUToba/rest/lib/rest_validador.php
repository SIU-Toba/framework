<?php
namespace SIUToba\rest\lib;

class rest_validador
{
    public static $mensajes = array(
        self::TIPO_INT => "El campo '%s' debe ser un número entero. Se recibió '%s'.%s",
        self::TIPO_NUMERIC => "El campo '%s' debe ser un número decimal. Se recibió '%s'.%s",
        self::TIPO_ALPHA => "El campo '%s' debe ser de texto a-zA-Z. Se recibió '%s'.%s",
        self::TIPO_ALPHANUM => "El campo '%s' debe ser alfanumérico. Se recibió '%s'.%s",
        self::TIPO_DATE => "El campo '%s' debe ser una fecha. Se recibió '%s'.%s",
        self::TIPO_TIME => "El campo '%s' debe ser una hora. Se recibió '%s'.%s",
        self::TIPO_LONGITUD => "El campo '%s' debe tener longitud apropiada. Se recibió '%s'.%s",
        self::OBLIGATORIO => "El campo '%s' es obligatoio.%s",
        self::TIPO_ENUM => "El campo '%s' no pertenece a la lista de opciones válidas. Se recibió '%s'.%s",
        self::TIPO_MAIL => "El campo '%s' debe ser un mail. Se recibió '%s'.%s",
        self::TIPO_CUSTOM => "El campo '%s' no es válido. Se recibió '%s'.%s",
        'campos_no_permitidos' => "Se encontraron campos no permitidos: %s.",

    );
    const TIPO_INT = 'int';
    const TIPO_NUMERIC = 'numerico';
    const TIPO_ALPHA = 'alpha';
    const TIPO_ALPHANUM = 'alphanum';
    const TIPO_DATE = 'date'; //Parametros: format -> http://php.net/manual/en/datetime.createfromformat.php
    const TIPO_TIME = 'time'; //Parametros: format -> http://php.net/manual/en/datetime.createfromformat.php
    const TIPO_LONGITUD = 'longitud'; //Parametros: format -> min, max
    const OBLIGATORIO = 'obligatorio';
    const TIPO_TEXTO = 'texto';
    const TIPO_CUSTOM = 'custom';
    const TIPO_MAIL = 'mail';
    const TIPO_ENUM = 'enum'; //Parametros: array(opc1, opc2 ..)

    const MAIL_MAX_LENGTH = 127;

    /**
     * Todos los campos en los datos tienen que estar en las reglas (con un array vacio al menos)
     * Esto es para que no se introduzcan campos no desados y se puedan procesar automaticamente para hacer sqls.
     * Si se ingresan campos no aceptados, se lanza un error.
     * Para la especificacion se utiliza la misma que el hidratador, agrupando en un array _validar las reglas.
     * Notar las reglas que tienen parametros.
     * Ejemplo:
     * rest_validador::validar($data, array(
     * 'id_curso_externo' => array('_validar' => array(rest_validador::TIPO_LONGITUD => array('min' =>1, 'max' => 50), rest_validador::OBLIGATORIO )),
     * 'nombre'           => array(),
     * 'plataforma'    => array( '_mapeo' => 'id_plat', '_validar' => (rest_validador::TIPO_INT))
     * );.
     *
     * @param $data
     * @param $reglas_spec array
     * @param $relajar_ocultos boolean no valida la obligatoriedad de los campos que no están presentes
     *
     * @throws rest_error
     */
    public static function validar($data, $reglas_spec, $relajar_ocultos = false)
    {
        $errores = array();

        foreach ($reglas_spec as $nombre_campo => $spec_campo) {
            if (is_array($spec_campo) && isset($spec_campo['_validar'])) {
                $reglas = $spec_campo['_validar'];
            } else {
                $reglas = array();
            }

            if ($relajar_ocultos
                && !isset($data[$nombre_campo])
                && (array_search(self::OBLIGATORIO, $reglas)) !== false
            ) {
                unset($data[$nombre_campo]);
                continue; //no valido
            }

            $valor_campo = (isset($data[$nombre_campo])) ? $data[$nombre_campo] : null;
            unset($data[$nombre_campo]);

            self::aplicar_reglas($reglas, $nombre_campo, $valor_campo, $errores); //&errores
        }
        if (!empty($data)) {
            $errores['campos_no_permitidos'][] = sprintf(self::$mensajes['campos_no_permitidos'], implode(', ', array_keys($data)));
        }

        if (!empty($errores)) {
            throw new rest_error(400, "Error en la validación del recurso", $errores);
        }
    }

    protected static function aplicar_reglas($reglas, $nombre_campo, $valor_campo, &$errores)
    {
        if (!is_array($reglas)) { //es valido, es solo un campo permitido
            return;
        }

        foreach ($reglas as $regla_key => $regla) { //para todas las reglas del campo
            if (is_numeric($regla_key)) {
                $nombre_regla = $regla;
                $regla_params = array();
            } else {
                $nombre_regla = $regla_key;
                $regla_params = $regla;
            }

            if (!self::es_valido($valor_campo, $nombre_regla, $regla_params)) {
                $args = $regla_params ?
                    " Parámetros: ".implode(', ', array_keys($regla_params))." => ".implode(', ', $regla_params)
                    : '';
                $errores[$nombre_campo][] = sprintf(self::$mensajes[$nombre_regla], $nombre_campo, $valor_campo, $args);
            }
        }
    }

    /*
     * Retorna si un valor es valido, vacio es valido.
     */
    public static function es_valido($valor, $tipo, $options = array())
    {
        $valor = self::validar_campo($valor, $tipo, $options);

        return $valor !== false;
    }

    public static function validar_campo($valor, $tipo, $options = array())
    {
        $filter_options = array();
        $flags = '';

        $vacio = empty($valor) && 0 !== $valor; //
        if ($vacio) {
            return ($tipo != self::OBLIGATORIO); //vacio es valido
        } else {
            if ($tipo == self::OBLIGATORIO) {
                return true;
            }
        }

        switch ($tipo) {
            case self::TIPO_ALPHA:
                $filter = FILTER_VALIDATE_REGEXP;
                $filter_options = array('regexp' => "/^[a-zA-Z]+$/");
                break;
            case self::TIPO_ALPHANUM:
                $filter = FILTER_VALIDATE_REGEXP;
                $filter_options = array('regexp' => "/^[a-zA-Z0-9]+$/");
                break;
            case self::TIPO_INT:
                $is_integer = is_integer($valor);
                $all_digits = ctype_digit($valor);
                if (($is_integer || $all_digits)) {
                    if (isset($options['min']) && $valor < $options['min']) {
                        return false;
                    }
                    if (isset($options['max']) && $valor > $options['max']) {
                        return false;
                    }

                    return $valor;
                }

                return false;
            case self::TIPO_NUMERIC:
                if (is_numeric($valor)) {
                    if (isset($options['min']) && $valor < $options['min']) {
                        return false;
                    }
                    if (isset($options['max']) && $valor > $options['max']) {
                        return false;
                    }

                    return $valor;
                }

                return false;
            case self::TIPO_MAIL:
                $filter = FILTER_VALIDATE_EMAIL;
                if (strlen($valor) > self::MAIL_MAX_LENGTH) {
                    return false;
                }
                break;
            case self::TIPO_TEXTO:
                return $valor;
                break;
            case self::TIPO_DATE:
                $date = date_parse_from_format($options['format'], $valor);
                if ($date['error_count'] == 0) {
                    if (checkdate($date['month'], $date['day'], $date['year'])) {
                        return $valor;
                    }
                }

                return false;
            case self::TIPO_TIME:
                $date = date_parse_from_format($options['format'], $valor);
                if ($date['error_count'] == 0) {
                    if (self::checktime($date['hour'], $date['minute'], $date['second'])) {
                        return $valor;
                    }
                }

                return false;
            case self::TIPO_LONGITUD:
                $l = strlen($valor);
                $min = isset($options['min']) ? $options['min'] : false;
                $max = isset($options['max']) ? $options['max'] : false;

                return (false === $min || $l >= $min) && (false === $max || $l <= $max);
            case self::TIPO_ENUM:
                return in_array($valor, $options);
            case self::TIPO_CUSTOM:
                $filter = FILTER_VALIDATE_REGEXP;
                $format = $options['format'];
                $filter_options = array('regexp' => "/$format$/");
                break;
        }

        return filter_var($valor, $filter, array(
            'options' => $filter_options,
            'flags' => $flags,
        ));
    }

    public static function checktime($hour, $minute, $seconds = 0)
    {
        if ($hour > -1 && $hour < 24 && $minute > -1 && $minute < 60 && $seconds > -1 && $seconds < 60) {
            return true;
        }

        return false;
    }

    public static function const_name($value)
    {
        $x = $value;
        $fooClass = new \ReflectionClass('\SIUToba\rest\lib\rest_validador');
        $constants = $fooClass->getConstants();

        $constName = null;
        foreach ($constants as $name => $value) {
            if ($value == $x) {
                $constName = $name;
                break;
            }
        }

        return $constName;
    }
}
