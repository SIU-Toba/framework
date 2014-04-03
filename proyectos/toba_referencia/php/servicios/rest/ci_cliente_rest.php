<?php

class ci_cliente_rest extends toba_ci
{
	protected $dump_url;
	protected $dump_pedido;
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
	
	function debug($request, $response)
	{
		$this->dump_pedido = $request->getRawHeaders();
		$this->dump_url = $request->getUrl();
		$this->dump_respuesta = $response->getBody(); //un string encodeado utf-8
	}

	/**
	 * Ver http://docs.guzzlephp.org/en/latest/docs.html
	 * @return Guzzle\Service\Client
	 */
	function get_cliente_rest(){
		
		try {
			//Se fija la url en codigo porque apunta a la instalacion actual de toba_referencia (es cliente y servidor)			
			$url = toba_http::get_protocolo() . toba_http::get_nombre_servidor() . toba_rest::url_rest();
			$opciones = array('to' => $url,);
			$cliente = toba::servicio_web_rest('rest_localhost', $opciones);
			return $cliente->guzzle();
		} catch (toba_error $e) {
			throw new toba_error_usuario("Hay un problema de configuracion del cliente REST. Por favor asegurese de configurarlo correctamente en el archivo cliente.ini.\n<br/><br/>Mensaje: ".$e->get_mensaje());
		}
	}
	
	function conf__form_debug_rest(toba_ei_formulario $form)
	{
		$datos = array(
			'url'		=> "<a style='font-size: 16px' href='".$this->dump_url."'>".urldecode($this->dump_url)."</a>",
			'pedido'	=> "<pre>".$this->dump_pedido."</pre>",
			'respuesta' => "<pre>".$this->dump_respuesta."</pre>"
		);
        if(isset($this->imagen_persona)){ //muestro solo la imagen porque el texto es muy largo
            $img = "<br/><img width='400px' src='data:image/png;base64,{$this->imagen_persona}'<br/>";
            $datos['respuesta'] = $img;
        }
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
			$this->rs_personas = rest_decode($response->json());
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
			$this->rs_personas = array(rest_decode($response->json()));
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
			$this->rs_personas = array(rest_decode($response->json()));
		} catch (Exception $e) {
			throw new toba_error($e);
		}
	}

    function evt__get__personas_alias()
    {
        $cliente = $this->get_cliente_rest();
        $request = $cliente->get('personas/confoto');
        try {
            $response = $request->send();
            $this->debug($request, $response);
            $this->rs_personas = array(rest_decode($response->json()));
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
		$request = $cliente->post('personas', null,  rest_encode($datos));
		try {
			$response = $request->send();
			$this->debug($request, $response);
			$persona = rest_decode($response->json());
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
		$request = $cliente->put('personas/'.$datos['id'], null, rest_encode($datos));
		try {
			$response = $request->send();
			$this->debug($request, $response);
			$persona = rest_decode($response->json());
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
			$this->rs_personas = rest_decode($response->json());
		} catch (Exception $e) {
			throw new toba_error($e);
		}
	}

    //-----------------------------------------------------------------------------
    //----  PANT_IMAGEN  ----------------------------------------------------------
    //-----------------------------------------------------------------------------

    function evt__imagen__put_imagen($datos){

        if (isset($datos['imagen'])) {
            $path = $this->mover_a_directorio_propio($datos['imagen']);
            $imagen = file_get_contents($path, FILE_BINARY);
            $img_para_ws = base64_encode($imagen);
            $mensaje = array('imagen' => $img_para_ws);
            $cliente = $this->get_cliente_rest();

            $request = $cliente->put('personas/'.$datos['persona'], null, rest_encode($mensaje));

            try {
                $response = $request->send();
                $this->debug($request, $response);
                toba::notificacion()->info("Persona actualizada");
            } catch (Exception $e) {
                throw new toba_error($e);
            }
        }else {
            toba::notificacion()->info("Debe escoger una imagen para usar esta acción");
        }
    }

    function evt__imagen__get_imagen($datos){
        $cliente = $this->get_cliente_rest();
        $request = $cliente->get('personas/'.$datos['persona'] .'?con_imagen=1');
        try {
            $response = $request->send();
            $this->debug($request, $response);
            $rs_persona = rest_decode($response->json());
            $this->imagen_persona = $rs_persona['imagen'];
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

    protected function mover_a_directorio_propio($archivo)
    {
        $nombre_archivo = $archivo['name'];
        $img = toba::proyecto()->get_www_temp($nombre_archivo);
        move_uploaded_file($archivo['tmp_name'], $img['path']);
        return $img['path'];
    }


}