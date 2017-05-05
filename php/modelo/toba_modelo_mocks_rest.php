<?php
/**
 * Este archivo contienen clases mocks de aquellas brindadas por la libreria REST, se usan desde el modelo para simular un pedido
 * @package Centrales
 * @subpackage Modelo
 */

use SIUToba\rest\http\respuesta_rest;
use SIUToba\rest\http\request;
use SIUToba\rest\seguridad\rest_usuario;
use SIUToba\rest\seguridad\proveedor_autenticacion;

class mock_autenticador extends SIUToba\rest\seguridad\proveedor_autenticacion
{
	public function get_usuario(SIUToba\rest\http\request $request = null)
	{
		$usuario = new rest_usuario();
		$usuario->set_usuario('usuario1');
		return $usuario;
	}

	public function requerir_autenticacion(SIUToba\rest\http\respuesta_rest $rta){}

    public function atiende_pedido(request $request){}
}

class mock_request extends  SIUToba\rest\http\request
{
	protected $url;

	 public function __construct($url)
	{
		 $this->url = $url;
		$this->headers = $this->extract_headers();
	}

	protected function extract_headers()
	{
		return array('REQUEST_METHOD' => 'GET', 'REQUEST_URI' => $this->url);
	}

	 public function get_request_uri()
	{
		return $this->headers["REQUEST_URI"];
	}

	public function get_method()
	{
		return $this->headers['REQUEST_METHOD'];
	}
}
?>
