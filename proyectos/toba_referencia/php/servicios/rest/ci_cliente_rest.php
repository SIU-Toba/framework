<?php

class ci_cliente_rest extends toba_ci
{
	protected $dump_url;
	protected $dump_pedido;
	protected $dump_respuesta;
	protected $rs_personas;
	protected $cant_personas;
	protected $s__orden;
	protected $s__filtro;
	
	protected $path_servicio = "rest/padres/padres.php";
	
	function ini()
	{
	}
	
	function debug($request, $response)
	{
		$this->dump_pedido = $request->getRawHeaders();
		$this->dump_url = $request->getUrl();
		$this->dump_respuesta = $response->getBody();		
	}

	/**
	 * Ver http://docs.guzzlephp.org/en/latest/docs.html
	 * @return Guzzle\Service\Client
	 */
	function get_cliente_rest(){
		
		//Se fija la url en codigo porque apunta a la instalacion actual de toba_referencia (es cliente y servidor)
		$opciones = array();
		$cliente = toba::servicio_web('rest_localhost', $opciones);
		return $cliente->guzzle();
	}
	
	function conf__form_debug_rest(toba_ei_formulario $form)
	{
		$datos = array(
			'url'		=> "<a style='font-size: 16px' href='".$this->dump_url."'>".urldecode($this->dump_url)."</a>",
			'pedido'	=> "<pre>".$this->dump_pedido."</pre>",
			'respuesta' => "<pre>".$this->dump_respuesta."</pre>"
			
		);
		$form->set_datos($datos);
		
	}
	
	function conf()
	{
		if (! isset($this->dump_respuesta)) {
			$this->pantalla()->eliminar_dep("form_debug_rest");
		}
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
			$request = $cliente->get('personas');
			$response = $request->send();
			$this->debug($request, $response);
			$this->rs_personas = $response->json();
		} catch (Exception $e) {
			throw new toba_error($e);
		}
	}
	
	function evt__get__persona()
	{
		$cliente = $this->get_cliente_rest();
		$request = $cliente->get('personas/1');
		try {
			$response = $request->send();
			$this->debug($request, $response);
			$this->rs_personas = array($response->json());
		} catch (Exception $e) {
			throw new toba_error($e);
		}
	}
		
	function evt__get__persona_juegos()
	{
		$cliente = $this->get_cliente_rest();
		$request = $cliente->get('personas/1/juegos');
		try {
			$response = $request->send();			
			$this->debug($request, $response);
			$this->rs_personas = array($response->json());
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
		$request = $cliente->post('personas', null,  json_encode($datos));
		try {
			$response = $request->send();
			$this->debug($request, $response);
			$persona = $response->json();
			toba::notificacion()->info("Persona creada con id: ".$persona['id']);
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
		$request = $cliente->delete('personas/'.$datos['id']);
		try {
			$response = $request->send();
			$this->debug($request, $response);
			toba::notificacion()->info("Persona borrada");
		} catch (Guzzle\Http\Exception\BadResponseException $e) {
			throw new toba_error($e->getResponse()->getBody());
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
		$request = $cliente->put('personas/'.$datos['id'], null, json_encode($datos));
		try {
			$response = $request->send();
			$this->debug($request, $response);
			$persona = $response->json();
			toba::notificacion()->info("Persona actualizada");
		} catch (Exception $e) {
			throw new toba_error($e);
		}
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
			$url = "personas?";
			if (isset($this->s__orden)) {
				$sentido = $this->s__orden['sentido'] == 'asc' ? "+" : "-";
				$url .= 'order='.urlencode($sentido).$this->s__orden['columna'].'&';
			}
			$url .= 'limit='.$this->dep('get_cuadro')->get_tamanio_pagina().'&';
			$url .= 'page='.$this->dep('get_cuadro')->get_pagina_actual().'&';			
			foreach ($this->s__filtro as $id => $campo) {
				if (is_array($campo['valor'])) {
					$valor = $campo['valor']['desde'].';'.$campo['valor']['hasta'];
				} else {
					$valor = $campo['valor'];
				}
				$url .= $id.'='.$campo['condicion'].';'.$valor.'&';
			}

			$request = $cliente->get($url);
			$response = $request->send();
			$this->dump_pedido = $request->getRawHeaders();
			$this->dump_url = $request->getUrl();
			$this->dump_respuesta = $response->getBody();	
			$this->cant_personas = (string) $response->getHeader("Cantidad-Registros");
			$this->rs_personas = $response->json();
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
		$url_cliente = toba::vinculador()->get_url('toba_editor', '30000014', array('archivo' => $cliente), array('prefijo'=>toba_editor::get_punto_acceso_editor()));		
		$url_servicio = toba::vinculador()->get_url('toba_editor', '30000014', array('archivo' => $this->path_servicio), array('prefijo'=>toba_editor::get_punto_acceso_editor()));
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
		return  "<pre $estilo>".htmlentities($valor).'</pre>';
	}
	
	
		
	
}