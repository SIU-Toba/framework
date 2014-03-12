<?php

namespace rest\http;

abstract class vista_respuesta
{

	protected $respuesta;

	function __construct(respuesta $respuesta)
	{
		$this->respuesta = $respuesta;
	}


	public function escribir()
	{
		$this->respuesta->add_headers(array('Content-Type' => $this->get_content_type()));
		$this->escribir_encabezados();
		echo $this->get_cuerpo();
	}


	protected abstract function get_content_type();

	public abstract function get_cuerpo();

	protected function escribir_encabezados()
	{
		$status = $this->respuesta->get_status();
		//Send status
		if (strpos(PHP_SAPI, 'cgi') === 0) {
			header(sprintf('Status: %s', respuesta::getMessageForCode($status)));
			//echo sprintf('Status: %s', self::getMessageForCode($this->status));

		} else {
			header(sprintf('HTTP/%s %s', '1.1', respuesta::getMessageForCode($status)));
			//echo sprintf('HTTP/%s %s', '1.1', self::getMessageForCode($this->status));
		}

		//Send headers
		foreach ($this->respuesta->headers as $name => $value) {
			$hValues = explode("\n", $value);
			foreach ($hValues as $hVal) {
				header("$name: $hVal", false);
			}
		}
	}
}