<?php
class ci_servicios_consumidos extends toba_ci
{
	protected $s__filtro;
	protected $s__seleccionado;
	protected $s__datos = array();
	
	protected $nombre_archivo;	
	protected $cert;

	function ini__operacion()
	{
		$proy_defecto = admin_instancia::get_proyecto_defecto();
		if (! is_null($proy_defecto)) {
			$this->s__filtro = array('proyecto' => $proy_defecto);
		}
	}
		
	function get_modelo_proyecto()
	{
		if (! isset($this->modelo_proyecto)) {
			$modelo = toba_modelo_catalogo::instanciacion();	
			$modelo->set_db(toba::db());	
			$this->modelo_proyecto = $modelo->get_proyecto(toba::instancia()->get_id(), $this->s__filtro['proyecto']);
		}
		return $this->modelo_proyecto;
	}

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__pant_inicial(toba_ei_pantalla $pantalla)
	{
		if (isset($this->s__filtro) && empty($this->s__datos)) {
			$datos = consultas_instancia::get_servicios_web_consumidos($this->s__filtro);
			$this->s__datos = $this->complementar_datos($datos);
		}		
	}
	
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
		$proyecto = $this->get_modelo_proyecto();
		$url = (isset($this->s__datos[$this->s__seleccionado]['param_to'])) ? $this->s__datos[$this->s__seleccionado]['param_to'] : null;
		
		if (isset($this->cert)) {											//Si existe el archivo de certificado
			$servicio = new toba_modelo_servicio_web($proyecto, $this->s__seleccionado);
			$servicio->generar_configuracion_cliente($this->cert['path'], $url);
		} elseif (! is_null($url)) {										//Estoy modificando los datos de la URL en el ini
			$ini = toba_modelo_servicio_web::get_ini_cliente($proyecto, $this->s__seleccionado);
			if ($ini->existe_entrada('conexion')) {
				$ini->set_datos_entrada('conexion', array('to' => $url));
			} else {
				$ini->agregar_entrada('conexion', array('to' => $url));
			}
			$ini->guardar();
		}
		
		$this->finalizar_operacion();
	}

	function evt__cancelar()
	{
		$this->finalizar_operacion();
	}

	function finalizar_operacion()
	{
		unset($this->s__seleccionado);
		if (isset($this->cert)) {
			unlink($this->cert['path']);
		}
		unset($this->s__datos);
		$this->s__datos = array();
		$this->set_pantalla('pant_inicial');		
	}

	
	//-----------------------------------------------------------------------------------
	//---- form_muestra -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_muestra(toba_ei_formulario $form)
	{
		$proyecto = $this->get_modelo_proyecto();
		if (toba_modelo_servicio_web::existe_archivo_certificado($proyecto)) {
			$datos['clave_privada'] = toba_modelo_servicio_web::path_clave_privada($proyecto);
			$datos['clave_publica'] = toba_modelo_servicio_web::path_clave_publica($proyecto);
			$form->set_datos($datos);
		} else {
			$form->evento('download')->anular();
		}
	}
	
	function servicio__download_certificado()
	{
		toba::memoria()->desactivar_reciclado();				
		$nombre = 'publica.crt';
		$mime_type = 'application/pkix-cert';
		
		//Aca tengo que enviar los headers para el archivo y hacer el passthrough
		$proyecto = $this->get_modelo_proyecto();
		$archivo = toba_modelo_servicio_web::path_clave_publica($proyecto);
		if (file_exists($archivo)) {
			$long = filesize($archivo);
			$handler = fopen($archivo, 'r');
			
			toba_http::headers_download($mime_type, $nombre, $long);
			fpassthru($archivo);
			fclose($handler);
		}
	}
	
	function ajax__test_configuracion($clave_param, toba_ajax_respuesta $respuesta)
	{
		toba::memoria()->desactivar_reciclado();		
		//Recupero la fila del cuadro
		$parametro = toba_ei_cuadro::recuperar_clave_fila('33000078', $clave_param);	
		if (is_null($parametro)) {							//Si no existe la fila informada desde el cliente retorno.
			$respuesta->set('Esta seguro que este es un servicio correcto?');
			return false;
		}
		
		//Armo el payload para el servicio de eco con el random a testear
		$rnd = xml_encode(md5(rand(1, 435)));				
		$payload = <<<XML
<ns1:eco xmlns:ns1="http://siu.edu.ar/toba/serv_pruebas"><texto>$rnd</texto></ns1:eco>
XML;
		//---------------------------------------------------------------------//			
		try {			
			//Lo armo asi porque esta configurado en otro proyecto entonces no puedo usar toba::servicio_web
			$servicio = toba_servicio_web_cliente::conectar($parametro['servicio_web'], array(), $this->s__filtro['proyecto']);
			$respuesta_ws = $servicio->request(new toba_servicio_web_mensaje($payload, array('action' => 'eco')));
		} catch (toba_error_servicio_web $s) {													//Capturo errores del servicio web			
			$respuesta->set('Se produjo un error inesperado en la atención del servicio, comuniquese con el proveedor del mismo. Si es un proyecto toba verifique el log de servicios web de ese proyecto (Ubicado en toba_usuarios > Auditoría > 
Logs de Servicios Web Ofrecidos) y el log general del sistema');
			toba::logger_ws()->debug($s->getMessage());
			return false;
		} catch (toba_error $e) {																//Capturo cualquier otro error local a la creacion del pedido
			toba::logger()->debug($e->getMessage());
			$respuesta->set('Se produjo un error inesperado en la inicializacion del pedido. Verifique que la URL sea correcta (abrirla en en el navegador y ver que responda bien)');
			return false;
		} 

		//Parseo el XML de la respuesta para obtener el dato y comparo con el random que envie
		$xml_rta = new SimpleXMLElement($respuesta_ws->get_payload());
		if ((string) $rnd == (string) $xml_rta->texto) {
			$respuesta->set('Ok. La configuracion es correcta');
		} else {
			toba::logger()->debug("Enviado: $rnd");
			toba::logger()->debug('Recibido: '. $xml_rta->texto);	
			$respuesta->set('La configuración no es correcta, o la respuesta ('.(string) $xml_rta->texto.') no coincide con la esperada '.
					$rnd.'). Revise el log');
		}
	}
	//-----------------------------------------------------------------------------------
	//---- form_basico_ws ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_basico_ws(toba_ei_formulario $form)
	{
		
		if (isset($this->s__seleccionado) && isset($this->s__datos[$this->s__seleccionado])) {
			$form->set_datos($this->s__datos[$this->s__seleccionado]);
		}
	}

	function evt__form_basico_ws__modificacion($datos)
	{
		if (isset($this->s__seleccionado)) {
			$this->s__datos[$this->s__seleccionado] = $datos;
			if (isset($datos['cert_file']) && ($datos['cert_file']['error'] == 0)) {		//Si se subio un archivo de certificado
				$nombre_archivo = toba_manejador_archivos::nombre_valido($datos['cert_file']['name']);
				$this->cert = toba::proyecto()->get_www_temp($nombre_archivo);				
				move_uploaded_file($datos['cert_file']['tmp_name'], $this->cert['path']);
			}
		}
	}

	
	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro($filtro)
	{
		if (isset($this->s__filtro)) {
			$filtro->set_datos($this->s__filtro);
		}
	}

	function evt__filtro__filtrar($datos)
	{
		if (isset($this->s__filtro)) {				//Si ya tenia valor el filtro, entonces es que se cambia proyecto
			$this->finalizar_operacion();
			$this->s__datos = array();
		}
		$this->s__filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{		
		$cuadro->set_datos($this->s__datos);		
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->s__seleccionado = $seleccion['servicio_web'];
		$this->set_pantalla('pant_edicion');
	}
	
	//-------------------------------------------------------------------------------------------------//
	function complementar_datos($datos)
	{
		$conf_final = array();
		$proyecto = $this->get_modelo_proyecto();
		$clave_privada = 'No hay archivo para la clave privada del proyecto';
		if (toba_modelo_servicio_web::existe_archivo_certificado($proyecto)) {
			$clave_privada = toba_modelo_servicio_web::path_clave_privada($proyecto);
		}
		
		//Tengo que agarrar los archivos ini de configuracion.
		foreach ($datos as $dato) {
			$id_servicio = $dato['servicio_web'];	
			$conf_inicial = toba_modelo_servicio_web::get_ini_cliente($proyecto, $id_servicio);		//Intento obtener la info del archivo de configuracion
			if ($conf_inicial->existe_entrada('conexion')) {
				$to = $conf_inicial->get('conexion', 'to');				
				$conf_final[$id_servicio] = array_merge($dato, array('param_to' => $to, 'link_to' => "<a href='$to'> $to </a>"));
			} else {
				$conf_final[$id_servicio] = $dato;
			}
			if ($conf_inicial->existe_entrada('certificado', 'cert_servidor')) {
				$conf_final[$id_servicio]['cert_file'] = basename($conf_inicial->get('certificado', 'cert_servidor'));
			}
			$conf_final[$id_servicio]['clave_privada'] = $clave_privada;			
		}
		return $conf_final;
	}

}
?>