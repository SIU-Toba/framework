<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ci_cliente_rest extends toba_ci
{
	protected $dump_url;
	//    protected $dump_pedido;
	protected $dump_respuesta;
	protected $rs_personas;
	protected $imagen_persona;
	protected $cant_personas;
	protected $s__orden;
	protected $s__filtro;

	protected $path_servicio = "rest/padres/padres.php";

	function ini()
	{
	}

	function debug($response)
	{
		$this->dump_respuesta = 'status: ' . $response->getStatusCode() . "<br/>body: <br/>" . $response->getBody()->getContents();	 //un string encodeado utf-8
		//$this->dump_url = $response->getBody()->getMetadata('uri');
	}

	/**
	 * Ver http://docs.guzzlephp.org/en/latest/docs.html
	 * @return GuzzleHttp\Client
	 */
	function get_cliente_rest()
	{
		try {
			//Se fija la url en codigo porque apunta a la instalacion actual de toba_referencia (es cliente y servidor)
			$url = toba_http::get_protocolo() . toba_http::get_nombre_servidor() . toba_rest::url_rest(). '/';
			$opciones = array();		//array('to' => $url,); //Se comenta para poder usarse desde Docker
			$cliente = toba::servicio_web_rest('rest_localhost', $opciones);
			return $cliente->guzzle();
		} catch (toba_error $e) {
			throw new toba_error_usuario("Hay un problema de configuracion del cliente REST. Por favor asegurese de configurarlo correctamente en el archivo cliente.ini.\n<br/><br/>Mensaje: " . $e->get_mensaje());
		}
	}

	function conf__form_debug_rest(toba_ei_formulario $form)
	{
		$escapador = toba::escaper();
		$datos = array(
			'url' => "<a style='font-size: 16px' href='" . $escapador->escapeHtmlAttr($this->dump_url) . "'>" . $escapador->escapeHtml(urldecode($this->dump_url)) . "</a>",
		         //'pedido' => "<pre>" . $this->dump_pedido . "</pre>",
			'respuesta' => "<pre>" . $escapador->escapeHtml($this->dump_respuesta) . "</pre>"
		);
		if (isset($this->imagen_persona)) { //muestro solo la imagen porque el texto es muy largo
			$img = "<br/><img width='400px' src='data:image/png;base64,". $escapador->escapeHtmlAttr($this->imagen_persona)."'<br/>";
			$datos['respuesta'] = $img;
		}
		$form->set_datos($datos);
	}

	function conf()
	{
		if (!isset($this->dump_respuesta)) {
			$this->pantalla()->eliminar_dep("form_debug_rest");
		}
	}

	function evt__version()
	{
		$url = toba_http::get_protocolo(true, true) . toba_http::get_nombre_servidor() . toba_rest::url_rest(). '/';
		$opciones = array('to' => $url);
		$cliente = toba::servicio_web_rest('rest_localhost', $opciones);
		$resp = $cliente->guzzle()->get('personas');
		if (!$resp->hasHeader('API-Version')) {
			toba::notificacion()->agregar('El header correspondiente a la version de la API no existe');
			return;
		}
		$version = $cliente->get_version_api($resp);		
		toba::notificacion()->agregar('Version de la API rest: '. toba::escaper()->escapeHtml($version->__toString()), 'info');
	}
	
	//-----------------------------------------------------------------------------
	//----  PANT_GET  -------------------------------------------------------------
	//------------------------------------------------------------------------------

	function conf__get(toba_ei_cuadro $cuadro)
	{
		if (isset($this->rs_personas)) {
			$cuadro->set_datos($this->rs_personas);
		}
	}

	function evt__get__personas()
	{
		try {
			$cliente = $this->get_cliente_rest();
			$response = $cliente->get('personas');
			$this->debug($response);
			$this->rs_personas = rest_decode($response->getBody()->__toString());
		} catch (RequestException $e) {
			$this->manejar_excepcion_request($e);
		} catch (Exception $e) {
			throw new toba_error($e);
		}
	}

	function evt__get__persona()
	{
		$cliente = $this->get_cliente_rest();

		try {
			$response = $cliente->get('personas/1');
			$this->debug($response);
			$this->rs_personas = array(rest_decode($response->getBody()->__toString()));
		} catch (RequestException $e) {
			$this->manejar_excepcion_request($e);
		} catch (Exception $e) {
			throw new toba_error($e);
		}
	}

	function evt__get__persona_juegos()
	{
		$cliente = $this->get_cliente_rest();
		try {
			$response = $cliente->get('personas/1/juegos');
			$this->debug($response);
			$this->rs_personas = array(rest_decode($response->getBody()->__toString()));
		} catch (RequestException $e) {
			$this->manejar_excepcion_request($e);
		} catch (Exception $e) {
			throw new toba_error($e);
		}
	}

	function evt__get__personas_alias()
	{
		$cliente = $this->get_cliente_rest();
		try {
			$response = $cliente->get('personas/confoto');
			$this->debug($response);
			$this->rs_personas = array(rest_decode($response->getBody()->__toString()));
		} catch (RequestException $e) {
			$this->manejar_excepcion_request($e);
		} catch (Exception $e) {
			throw new toba_error($e);
		}
	}

	//-----------------------------------------------------------------------------
	//----  PANT_POST  -------------------------------------------------------------
	//------------------------------------------------------------------------------

	function evt__post__persona($datos)
	{
		$cliente = $this->get_cliente_rest();
		try {
			$response = $cliente->post('personas', array(
				'body' => rest_encode($datos)
			));
			$this->debug($response);
			$persona = rest_decode($response->getBody()->__toString());
			toba::notificacion()->info("Persona creada con id: " . $persona['id']);
		} catch (RequestException $e) {
			$this->manejar_excepcion_request($e);
		} catch (Exception $e) {
			throw new toba_error($e);
		}
	}

	//-----------------------------------------------------------------------------
	//----  PANT_DELETE  -------------------------------------------------------------
	//------------------------------------------------------------------------------

	function evt__delete__persona($datos)
	{
		$cliente = $this->get_cliente_rest();

		try {
			$response = $cliente->delete('personas/' . $datos['id']);
			$this->debug($response);
			toba::notificacion()->info("Persona borrada");
		} catch (RequestException $e) {
			$this->manejar_excepcion_request($e);
		} catch (Exception $e) {
			throw new toba_error($e);
		}
	}


	//-----------------------------------------------------------------------------
	//----  PANT_PUT  -------------------------------------------------------------
	//------------------------------------------------------------------------------

	function evt__put__persona($datos)
	{
		$cliente = $this->get_cliente_rest();
		$id = $datos['id'];
		unset($datos['id']);
		try {
			$response = $cliente->put('personas/' . $id, array(
				'body' => rest_encode($datos)
			));
			$this->debug($response);
			$persona = rest_decode($response->getBody()->__toString());
			toba::notificacion()->info("Persona actualizada");
		} catch (RequestException $e) {
			$this->manejar_excepcion_request($e);
		} catch (Exception $e) {
			throw new toba_error($e);
		}
	}


	protected function manejar_excepcion_request(RequestException $e)
	{
		/*$msg = $e->getRequest() . "\n";

		if ($e->hasResponse()) {
			$msg .= $e->getResponse() . "\n";
		}*/
		
		$msg = $e->getMessage(). "\n";
		$msg .= $e->getRequest()->getMethod();
		throw new toba_error($msg);
	}



	//-----------------------------------------------------------------------------
	//----  PANT_FILTRO  -------------------------------------------------------------
	//------------------------------------------------------------------------------

	function evt__get_cuadro__cambiar_pagina($pagina)
	{
		$this->filtrar();
	}

	function conf__get_cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->rs_personas)) {
			if (isset($this->cant_personas)) {
				$cuadro->set_total_registros($this->cant_personas);
			}
			$cuadro->set_datos($this->rs_personas);
		}

	}

	function evt__get_cuadro__ordenar($orden)
	{
		$this->s__orden = $orden;
		$this->filtrar();
	}

	function conf__get_filtro(toba_ei_filtro $filtro)
	{
		if (isset($this->s__filtro)) {
			$filtro->set_datos($this->s__filtro);
		}
	}

	function evt__get_filtro__filtrar($filtro)
	{
		$this->s__filtro = $filtro;
		$this->filtrar();
	}

	function evt__get_filtro__cancelar()
	{
		if (isset($this->s__orden)) unset($this->s__orden);
		if (isset($this->s__filtro)) unset($this->s__filtro);
	}

	function filtrar()
	{
		try {

			$cliente = $this->get_cliente_rest();
			$query = array();
			$url = "personas";

			if (isset($this->s__orden)) {
				$sentido = $this->s__orden['sentido'] == 'asc' ? "+" : "-";
				$query['order'] = urlencode($sentido) . $this->s__orden['columna'];
			}
			$query['limit'] = $this->dep('get_cuadro')->get_tamanio_pagina();
			$query['page'] = $this->dep('get_cuadro')->get_pagina_actual();


			foreach ($this->s__filtro as $id => $campo) {
				if (is_array($campo['valor'])) {
					$valor = $campo['valor']['desde'] . ';' . $campo['valor']['hasta'];
				} else {
					$valor = $campo['valor'];
				}
				$query[$id] = $campo['condicion'] . ';' . $valor;
			}

			$response = $cliente->get($url, array('query' => $query));						//Esta opcion usa http_build_query, sino hay que hacer un query request con el string

			$this->dump_respuesta = $response->getBody()->__toString();
			$header_values = $response->getHeader("Cantidad-Registros");
			$this->cant_personas = (is_array($header_values)) ? current($header_values) : 0;
			$this->rs_personas = rest_decode($response->getBody()->__toString());
		} catch (RequestException $e) {
			$this->manejar_excepcion_request($e);
		} catch (Exception $e) {
			throw new toba_error($e);
		}
	}

	//-----------------------------------------------------------------------------
	//----  PANT_IMAGEN  ----------------------------------------------------------
	//-----------------------------------------------------------------------------

	function evt__imagen__put_imagen($datos)
	{

		if (isset($datos['imagen'])) {
			$path = $this->mover_a_directorio_propio($datos['imagen']);
			$imagen = file_get_contents($path, FILE_BINARY);
			$img_para_ws = base64_encode($imagen);
			$mensaje = array('imagen' => $img_para_ws);
			$cliente = $this->get_cliente_rest();
			try {
				$response = $cliente->put('personas/' . $datos['persona'], array(
					'body' => rest_encode($mensaje)
				));

				$this->debug($response);
				toba::notificacion()->info("Persona actualizada");
			} catch (RequestException $e) {
				$this->manejar_excepcion_request($e);
			} catch (Exception $e) {
				throw new toba_error($e);
			}
		} else {
			toba::notificacion()->info("Debe escoger una imagen para usar esta acción");
		}
	}

	function evt__imagen__get_imagen($datos)
	{
		$cliente = $this->get_cliente_rest();
		try {
			$response = $cliente->get('personas/' . $datos['persona'], array('query' => array('con_imagen' => 1)));
			$this->debug($response);
			$rs_persona = rest_decode($response->getBody()->__toString());
			$this->imagen_persona = $rs_persona['imagen'];
		} catch (RequestException $e) {
			$this->manejar_excepcion_request($e);
		} catch (Exception $e) {
			throw new toba_error($e);
		}

	}

	//-----------------------------------------------------------------------------
	//---- Utilidades  -----------------------------------------------------------
	//------------------------------------------------------------------------------

	function post_configurar()
	{
		parent::post_configurar();
		$img = toba_recurso::imagen_toba('nucleo/php.gif', true);
		$cliente = 'servicios/rest/ci_rutas_rest.php';
		$url_api_doc = toba_rest::url_rest();
		$url_cliente = toba::vinculador()->get_url('toba_editor', '30000014', array('archivo' => $cliente), array('prefijo' => toba_editor::get_punto_acceso_editor()));
		$url_servicio = toba::vinculador()->get_url('toba_editor', '30000014', array('archivo' => $this->path_servicio), array('prefijo' => toba_editor::get_punto_acceso_editor()));
		$html = "<div style='float:right'>
		<a style='font-weight:bold' href='$url_api_doc'>Consola y Documentacion API REST</a> del proyecto<br/>
		<a target='logger' href='$url_cliente'>$img Ver .php del Cliente</a>";
		$html .= "<br><a target='logger' href='$url_servicio'>$img Ver .php del Servicio</a>";
		$url_ejemplos = 'http://repositorio.siu.edu.ar/trac/toba/wiki/Referencia/Rest';
		$html .= "<br>Documentación de <a target='_blank' href='$url_ejemplos'>servicios REST en toba</a></div>";
		$html .= $this->pantalla()->get_descripcion();

		$html .= "<style type='text/css'>
			pre { background-color: #ccc; padding: 5px; border: 1px solid gray; color: #333; }
		</style>";
		$this->pantalla()->set_descripcion($html);
	}

	function formatear_valor($valor)
	{
		$estilo = 'style="background-color: white; border: 1px solid gray; padding: 5px;"';
		return "<pre $estilo>" . htmlentities($valor) . '</pre>';
	}

	protected function mover_a_directorio_propio($archivo)
	{
		$nombre_archivo = $archivo['name'];
		$img = toba::proyecto()->get_www_temp($nombre_archivo);
		move_uploaded_file($archivo['tmp_name'], $img['path']);
		return $img['path'];
	}


}
