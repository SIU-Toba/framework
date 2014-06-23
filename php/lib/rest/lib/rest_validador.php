<?php
namespace rest\lib;

class rest_validador
{

	public static $mensajes = array(
		self::TIPO_INT         => "El campo '%s' debe ser un n�mero entero. Se recibi� '%s'.%s",
		self::TIPO_NUMERIC     => "El campo '%s' debe ser un n�mero decimal. Se recibi� '%s'.%s",
		self::TIPO_ALPHA       => "El campo '%s' debe ser de texto a-zA-Z. Se recibi� '%s'.%s",
		self::TIPO_ALPHANUM    => "El campo '%s' debe ser alfanum�rico. Se recibi� '%s'.%s",
		self::TIPO_DATE        => "El campo '%s' debe ser una fecha. Se recibi� '%s'.%s",
		self::TIPO_TIME        => "El campo '%s' debe ser una hora. Se recibi� '%s'.%s",
		self::TIPO_LONGITUD    => "El campo '%s' debe tener longitud apropiada. Se recibi� '%s'.%s",
		self::OBLIGATORIO      => "El campo '%s' es obligatoio.%s",
		self::TIPO_ENUM        => "El campo '%s' no pertenece a la lista de opciones v�lidas. Se recibi� '%s'.%s",
		self::TIPO_MAIL        => "El campo '%s' debe ser un mail. Se recibi� '%s'.%s",
		self::TIPO_CUSTOM      => "El campo '%s' no es v�lido. Se recibi� '%s'.%s",
		'campos_no_permitidos' => "Se encontraron campos no permitidos: %s.",

	);
	const TIPO_INT = 'int';
	const TIPO_NUMERIC = 'numerico';
	const TIPO_ALPHA = 'alpha';
	const TIPO_ALPHANUM = 'alphanum';
	const TIPO_DATE = 'date';
	const TIPO_TIME = 'time';
	const TIPO_LONGITUD = 'longitud';
	const OBLIGATORIO = 'obligatorio';
	const TIPO_TEXTO = 'texto';
	const TIPO_CUSTOM = 'custom';
	const TIPO_MAIL = 'mail';
	const TIPO_ENUM = 'enum';

	const MAIL_MAX_LENGTH = 127;

	/**
	 * Todos los campos en los datos tienen que estar en las reglas (con un array vacio al menos)
	 * Esto es para que no se introduzcan campos no desados y se puedan procesar automaticamente para hacer sqls.
	 * Si se ingresan campos no aceptados, se lanza un error.
	 * Ejemplo:
	 * rest_validador::validar($data, array(
	 * 'id_curso_externo' => array(rest_validador::TIPO_LONGITUD => array('min' =>1, 'max' => 50), rest_validador::OBLIGATORIO),
	 * 'nombre'           => array(rest_validador::OBLIGATORIO),
	 * 'id_plataforma'    => array(rest_validador::TIPO_INT),
	 * 'estado'           => array(rest_validador::TIPO_ENUM => array('A', 'B'))
	 * );
	 * @param $data
	 * @param $reglas_spec array array( 'campo1' => array('es_entero', 'entre' => array(0, 2) ..)
	 * @throws rest_error con los erores de validacion
	 */
	public static function validar($data, $reglas_spec)
	{
		$errores = array();

		foreach ($reglas_spec as $nombre_campo => $reglas) {
			$valor_campo = (isset($data[$nombre_campo])) ? $data[$nombre_campo] : null;
			unset($data[$nombre_campo]);

			self::aplicar_reglas($reglas, $nombre_campo, $valor_campo, $errores); //&errores
		}
		if (!empty($data)) {
			$errores['campos_no_permitidos'][] = sprintf(self::$mensajes['campos_no_permitidos'], implode(', ', array_keys($data)));
		}

		if (!empty($errores)) {
			throw new rest_error(400, "Error en la validaci�n del recurso", $errores);
		}
	}

	protected static function aplicar_reglas($reglas, $nombre_campo, $valor_campo, &$errores)
	{
		if (!is_array($reglas)) {//es valido, es solo un campo permitido
			return;
		}

		foreach ($reglas as $regla_key => $regla) { //para todas las reglas del campo
			if(is_numeric($regla_key)){
				$nombre_regla = $regla;
				$regla_params = array();
			}else {
				$nombre_regla = $regla_key;
				$regla_params = $regla;
			}

			if (!self::es_valido($valor_campo, $nombre_regla, $regla_params)) {
				$args = $regla_params?
					" Par�metros: " . implode(', ', array_keys($regla_params)) . " => ". implode(', ', $regla_params)
					: '';
				$errores[$nombre_campo][] = sprintf(self::$mensajes[$nombre_regla], $nombre_campo, $valor_campo, $args);
			}
		}
	}


	/**
	 * Retorna si un valor es valido, vacio es valido.
	 */
	static function es_valido($valor, $tipo, $options = array())
	{
		$valor = self::validar_campo($valor, $tipo, $options);
		return $valor !== false;
	}

	static function validar_campo($valor, $tipo, $options = array())
	{
		$filter_options = array();
		$flags = '';

		$vacio =  empty($valor) && 0 !== $valor; //
		if ($vacio) {
			return ($tipo != self::OBLIGATORIO); //vacio es valido
		}else {
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
			'flags'   => $flags
		));
	}

	static function checktime($hour, $minute, $seconds = 0)
	{
		if ($hour > -1 && $hour < 24 && $minute > -1 && $minute < 60 && $seconds > -1 && $seconds < 60) {
			return true;
		}

		return false;
	}

	static function const_name($value)
	{
		$x = $value;
		$fooClass = new \ReflectionClass ('\rest\lib\rest_validador');
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