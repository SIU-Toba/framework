<?php
class ci_servicios_ofrecidos extends toba_ci
{
	protected $cert;	
	protected $s__filtro;
	protected $s__datos = array();
	protected $s__seleccionado;
	
	protected $s__conf_disponibles;
	protected $s__conf_activa;	
	protected $s__parametros = array();

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
		if (isset($this->s__filtro)) {
			$this->s__datos = $this->complementar_datos($this->get_modelo_proyecto()->get_servicios_web_ofrecidos());
		}
		

	}	
	
	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(form_proyecto $form)
	{	
		if (isset($this->s__filtro)) {
			$form->set_datos($this->s__filtro);
		}
	}

	function evt__filtro__filtrar($datos)
	{
		if (isset($this->s__filtro)) {		//Si estaba seteado el filtro, entonces cambie de proyecto
			$this->finalizar_operacion();
		}
		$this->s__filtro = $datos;
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function servicio__ejecutar()
	{
		toba_http::headers_standart();		
		$valor = toba::memoria()->get_parametro('fila_safe');
		$clave = toba_ei_cuadro::recuperar_clave_fila('33000088', $valor);
		
		if (! is_null($clave)) {								//Si existe la clave que le pasaron
			$id_servicio = $clave['servicio_web'];		
			$proyecto = $this->get_modelo_proyecto();			
			$nuevo_estado = (! toba_modelo_servicio_web::esta_activo($proyecto, $id_servicio)) ? 1 : 0;
			toba_modelo_servicio_web::set_estado_activacion($proyecto, $id_servicio, $nuevo_estado);
			toba::vinculador()->navegar_a();	//Hago una redireccion a la misma operacion para refrescar el cuadro.
		}
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{		
		$cuadro->set_datos($this->s__datos);					
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->s__seleccionado = $seleccion['servicio_web'];
		$this->set_pantalla('pant_sel_conf');
	}
	
	function complementar_datos($datos)
	{
		$conf_final = array();	
		//Tengo que agarrar los archivos ini de configuracion.
		foreach ($datos as $dato) {
			$id_servicio = $dato['servicio_web'];
			$activo = toba_modelo_servicio_web::esta_activo($this->get_modelo_proyecto(), $id_servicio);
			$aux = $this->recuperar_clientes_configurados($id_servicio);
			$url_serv = toba::vinculador()->get_url_ws($this->s__filtro['proyecto'], $id_servicio, null, array( 'html' => true, 'texto' => 'url'));
			$url_wsdl = toba::vinculador()->get_url_ws($this->s__filtro['proyecto'], $id_servicio, null, array( 'html' => true, 'texto' => 'wsdl 1.1', 'wsdl' => true));
			$url_wsdl2 = toba::vinculador()->get_url_ws($this->s__filtro['proyecto'], $id_servicio, null, array( 'html' => true, 'texto' => 'wsdl 2.0', 'wsdl2' => true));
			$conf_final[$id_servicio] = array_merge($dato, array('activado' => $activo, 'cantidad_configuraciones' => count($aux), 'wsdl' => "$url_wsdl / $url_wsdl2", 'url'  => $url_serv));
		}
		return $conf_final;
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
			$form->evento('download')->desactivar();
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
			fpassthru($handler);
			fclose($handler);
		}
	}
	
	//----------------------- PANTALLA EDICION ---------------------//
	
	//-----------------------------------------------------------------------------------
	//---- cuadro_sel_conf --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_sel_conf(toba_ei_cuadro $cuadro)
	{
		if (! isset($this->s__conf_disponibles)) {
			$this->s__conf_disponibles = $this->recuperar_clientes_configurados($this->s__seleccionado);
			$cuadro->set_datos($this->s__conf_disponibles);			
		}
	}

	function evt__cuadro_sel_conf__seleccion($seleccion)
	{
		$this->s__conf_activa = $seleccion['headers'];
		$this->set_pantalla('pant_edicion');
	}

	function evt__cuadro_sel_conf__agregar()
	{
		$this->set_pantalla('pant_edicion');		
	}
	
	function evt__cuadro_sel_conf__eliminar($seleccion)
	{
		$this->s__conf_activa = $seleccion['headers'];
		$this->eliminar_config();
	}
	
	function recuperar_clientes_configurados($servicio)
	{	
		$datos = array();
		$proyecto = $this->get_modelo_proyecto();
		$ini = toba_modelo_servicio_web::get_ini_server($proyecto, $servicio);
		$entradas = $ini->get_entradas();
		$claves = (is_array($entradas))? array_keys($entradas): array();
		foreach ($claves as $clave) {
			if (strpos($clave, '=') !== false) {	//Si la entrada corresponde a un grupo de headers			
				$archivo = $ini->get($clave, 'archivo');
				$datos[$clave] = array('headers' => $clave, 'cert_file' => basename($archivo));
			}
		}
		return $datos;
	}
	
	
	//-----------------------------------------------------------------------------------
	//---- form_basico ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_basico(toba_ei_formulario $form)
	{
		$form->desactivar_efs(array('param_to'));
		if (isset($this->s__seleccionado) && isset($this->s__datos[$this->s__seleccionado])) {
			$form->set_datos($this->s__datos[$this->s__seleccionado]);			
		}
		if (isset($this->s__conf_activa) && isset($this->s__conf_disponibles[$this->s__conf_activa])) {
			$form->set_datos(array('cert_file' => $this->s__conf_disponibles[$this->s__conf_activa]['cert_file']));
		}
	}

	function evt__form_basico__modificacion($datos)
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
	//---- form_parametros --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_parametros(toba_ei_formulario_ml $form_ml)
	{
		if (empty($this->s__parametros) && isset($this->s__conf_activa)) {			
			$subclaves = explode(',', $this->s__conf_activa);					
			foreach ($subclaves as $subvalores) {
				list($parametro, $valor) = explode('=', $subvalores);
				$this->s__parametros[] = array('parametro' => $parametro, 'valor' => $valor);
			}
		}
		$form_ml->set_datos($this->s__parametros);
	}

	function evt__form_parametros__modificacion($datos)
	{
		$this->s__parametros = $datos;
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function guardar_config()
	{	
		$proyecto = $this->get_modelo_proyecto();							
		$headers = array();
		foreach ($this->s__parametros as $param) {	
			if ($param['apex_ei_analisis_fila'] != 'B') {
				$headers[$param['parametro']] = $param['valor'];		
			}
		}
		
		if (isset($this->s__conf_activa)) {					//Si existe la entrada editada, entonces leo sus datos y la elimino
			$ini = toba_modelo_servicio_web::get_ini_server($proyecto, $this->s__seleccionado);
			$ini->existe_entrada($this->s__conf_activa);
			$temp_data = $ini->get_datos_entrada($this->s__conf_activa);
			$ini->eliminar_entrada($this->s__conf_activa);
			$ini->guardar();
		}
	
		if (isset($this->cert)) {													//Si se agrega un archivo de certificado, le paso los parametros nuevos			
			$servicio = new toba_modelo_servicio_web($proyecto, $this->s__seleccionado);
			$servicio->generar_configuracion_servidor($this->cert['path'], $headers);
		} elseif (! empty($this->s__parametros) && isset($this->s__conf_activa)) {									//En este caso, cambio los parametros
			$nombre = toba_modelo_servicio_web::generar_id_entrada_cliente($headers);
			$ini->agregar_entrada($nombre, $temp_data);
			$ini->guardar();			
		} 
	}
		
	function eliminar_config()
	{
		if (isset($this->s__conf_activa)) {					//Si existe la entrada editada, entonces leo sus datos y la elimino
			$proyecto = $this->get_modelo_proyecto();							
			$ini = toba_modelo_servicio_web::get_ini_server($proyecto, $this->s__seleccionado);
			$ini->existe_entrada($this->s__conf_activa);
			$ini->eliminar_entrada($this->s__conf_activa);
			$ini->guardar();
			unset($this->s__conf_activa);
			unset($this->s__conf_disponibles);
		}		
	}
	
	
	function evt__procesar_parcial()
	{	
		$this->guardar_config();
		$this->finalizar_operacion();
		$this->set_pantalla('pant_sel_conf');
	}
	
	function evt__cancelar_parcial()
	{
		$this->finalizar_operacion();
		$this->set_pantalla('pant_sel_conf');
	}

	function evt__cancelar()
	{
		$this->s__datos = array();
		unset($this->s__seleccionado);
		unset($this->s__conf_disponibles);
		
		$this->finalizar_operacion();
		$this->set_pantalla('pant_inicial');
	}
		
	function finalizar_operacion()
	{
		$this->s__parametros = array();
		unset($this->s__conf_activa);
		unset($this->s__conf_disponibles);				
		if (isset($this->cert)) {
			unlink($this->cert['path']);
		}		
	}

	
}
?>