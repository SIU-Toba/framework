<?php

namespace rest\http;

use rest\lib\rest_error_interno;

/**
 * Abstae la respuesta HTTP. Permite setearle estados, headers
 * y contenido que subclases puede imprimir con otro formato o
 * con los helpers apropiados.
 *
 */
class respuesta
{
	/**
	 * @var int HTTP status code
	 */
	protected $status;

	/**
	 * @var array
	 */
	public $headers;

	/**
	 * @var mixed Los datos del cuerpo
	 */
	protected $data;


	protected $encoding;

	/**
	 * @var array codigos HTTP para los encabezados
	 */
	protected static $messages = array(
		//Informational 1xx
		100 => '100 Continue',
		101 => '101 Switching Protocols',
		//Successful 2xx
		200 => '200 OK',
		201 => '201 Created',
		202 => '202 Accepted',
		203 => '203 Non-Authoritative Information',
		204 => '204 No Content',
		205 => '205 Reset Content',
		206 => '206 Partial Content',
		//Redirection 3xx
		300 => '300 Multiple Choices',
		301 => '301 Moved Permanently',
		302 => '302 Found',
		303 => '303 See Other',
		304 => '304 Not Modified',
		305 => '305 Use Proxy',
		306 => '306 (Unused)',
		307 => '307 Temporary Redirect',
		//Client Error 4xx
		400 => '400 Bad Request',
		401 => '401 Unauthorized',
		402 => '402 Payment Required',
		403 => '403 Forbidden',
		404 => '404 Not Found',
		405 => '405 Method Not Allowed',
		406 => '406 Not Acceptable',
		407 => '407 Proxy Authentication Required',
		408 => '408 Request Timeout',
		409 => '409 Conflict',
		410 => '410 Gone',
		411 => '411 Length Required',
		412 => '412 Precondition Failed',
		413 => '413 Request Entity Too Large',
		414 => '414 Request-URI Too Long',
		415 => '415 Unsupported Media Type',
		416 => '416 Requested Range Not Satisfiable',
		417 => '417 Expectation Failed',
		418 => '418 I\'m a teapot',
		422 => '422 Unprocessable Entity',
		423 => '423 Locked',
		//Server Error 5xx
		500 => '500 Internal Server Error',
		501 => '501 Not Implemented',
		502 => '502 Bad Gateway',
		503 => '503 Service Unavailable',
		504 => '504 Gateway Timeout',
		505 => '505 HTTP Version Not Supported'
	);

	/**
	 * Constructor
	 * @param mixed $data    El cuerpo de la respuesta
	 * @param int $status    El status HTTP
	 * @param array $headers Headers
	 */
	public function __construct($data = null, $status = 200, $headers = array())
	{
		$this->set_status($status);
		$this->headers = array_merge(array('Content-Type' => 'text/html'), $headers);
		$this->set_data($data);
	}


	public function set_encoding_datos($encoding)
	{
		$this->encoding = $encoding;
	}

	public function get_encoding_datos()
	{
		return $this->encoding;
	}

	public function get_status()
	{
		return $this->status;
	}

	public function set_status($status)
	{
		$this->status = (int) $status;
		return $this;
	}

	public function add_headers(array $headers)
	{
		$this->headers = array_merge($this->headers, $headers);
		return $this;
	}

	public function get_data()
	{
		return $this->data;
	}

	public function set_data($content)
	{
		$this->data = $content;
		return $this;
	}


	/**
	 * Realiza chequeos sobre el formato de la respuesta antes de enviarla
	 */
	public function finalizar()
	{
		if (in_array($this->status, array(204, 304))) {
			unset($this->headers['Content-Type']);
			unset($this->headers['Content-Length']);
			$this->set_data('');
		}
		if (!isset($this->data)) {
			throw new rest_error_interno("El contenido de la respuesta no puede ser nulo. Si no se desea una respuesta, inicializar
            en '' o arreglo vacio");
		}
	}


	/**
	 * Get message for HTTP status code
	 * @param  int $status
	 * @return string|null
	 */
	public static function getMessageForCode($status)
	{
		if (isset(self::$messages[$status])) {
			return self::$messages[$status];
		} else {
			return null;
		}
	}
}