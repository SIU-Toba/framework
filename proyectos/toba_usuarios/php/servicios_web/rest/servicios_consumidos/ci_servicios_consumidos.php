<?php
class ci_servicios_consumidos extends toba_ci
{
	protected $s__filtro;
	protected $s__seleccionado;
	protected $s__datos = array();
	
	//protected $nombre_archivo;	
	protected $cert;
	protected $key;
	protected $ca;

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
			$filtro = array_merge($this->s__filtro, array('tipo' => 'rest'));
			$datos = consultas_instancia::get_servicios_web_consumidos($filtro);
			$this->s__datos = $this->complementar_datos($datos);
		}		
	}
	
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
		$proyecto = $this->get_modelo_proyecto();
		$url = (isset($this->s__datos[$this->s__seleccionado]['to'])) ? $this->s__datos[$this->s__seleccionado]['to'] : null;		
		$pwd = (isset($this->s__datos[$this->s__seleccionado]['cert_pwd'])) ? $this->s__datos[$this->s__seleccionado]['cert_pwd'] : null;
		$usr = (isset($this->s__datos[$this->s__seleccionado]['usr'])) ? $this->s__datos[$this->s__seleccionado]['usr'] : null;
		$usr_pwd = (isset($this->s__datos[$this->s__seleccionado]['usr_pwd'])) ? $this->s__datos[$this->s__seleccionado]['usr_pwd'] : null;
		
		//Archivos de certificados
		$dir_base = toba_modelo_rest::get_dir_consumidor($proyecto->get_dir_instalacion_proyecto(), $this->s__seleccionado);
		$ca = $key = $cert = null;
		if  (isset($this->ca['path'])) {
			$ca = $dir_base. '/'.  basename($this->ca['path']);
			rename($this->ca['path'], $ca);
		}		
		if (isset($this->key['path'])) {
			$key = $dir_base. '/'.  basename($this->key['path']);
			rename($this->key['path'], $key);
		}		
		if (isset($this->cert['path'])) {
			$cert = $dir_base. '/'.  basename($this->cert['path']);
			rename($this->cert['path'], $cert);
		}
		
		$servicio = new toba_modelo_servicio_web($proyecto, $this->s__seleccionado, 'rest');
		$servicio->generar_configuracion_cliente($ca, $url, $cert, $key, $pwd , $usr, $usr_pwd);
		
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
			if (file_exists($this->cert['path'])) { unlink($this->cert['path']);}
			$this->cert = null;
		}
		if (isset($this->key)) {
			if (file_exists($this->key['path'])) { unlink($this->key['path']);}
			$this->key = null;
		}
		if (isset($this->ca)) {
			 if (file_exists($this->ca['path'])) { unlink($this->ca['path']);}
			$this->ca = null;
		}
		unset($this->s__datos);
		$this->s__datos = array();
		$this->set_pantalla('pant_inicial');		
	}
	
	function ajax__test_configuracion($clave_param, toba_ajax_respuesta $respuesta)
	{
		toba::memoria()->desactivar_reciclado();		
		//Recupero la fila del cuadro
		$parametro = toba_ei_cuadro::recuperar_clave_fila('33000186', $clave_param);
		if (is_null($parametro)) {							//Si no existe la fila informada desde el cliente retorno.
			$respuesta->set('Esta seguro que este es un servicio correcto?');
			return false;
		}
		
		$rest = toba::servicio_web_rest($parametro['servicio_web']);
		$response = $rest->guzzle()->get('status');				//Hay que crear este servicio para que todos puedan responder
		$respuesta->set($response->json());
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
			
			if (isset($datos['key_file']) && ($datos['key_file']['error'] == 0)) {		//Si se subio un archivo de certificado
				$nombre_archivo = toba_manejador_archivos::nombre_valido($datos['key_file']['name']);
				$this->key = toba::proyecto()->get_www_temp($nombre_archivo);				
				move_uploaded_file($datos['key_file']['tmp_name'], $this->key['path']);
			}
			
			if (isset($datos['cert_CA']) && ($datos['cert_CA']['error'] == 0)) {		//Si se subio un archivo de certificado
				$nombre_archivo = toba_manejador_archivos::nombre_valido($datos['cert_CA']['name']);
				$this->ca = toba::proyecto()->get_www_temp($nombre_archivo);				
				move_uploaded_file($datos['cert_CA']['tmp_name'], $this->ca['path']);
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
		
		//Tengo que agarrar los archivos ini de configuracion.
		foreach ($datos as $dato) {
			$id_servicio = $dato['servicio_web'];	
			$conf_inicial = toba_modelo_rest::get_ini_cliente($proyecto, $id_servicio);		//Intento obtener la info del archivo de configuracion
			if ($conf_inicial->existe_entrada('conexion')) {
				$to = $conf_inicial->get('conexion', 'to', '', false);
				$conf_final[$id_servicio] =  (trim($to) != '') ? array_merge($dato, array('to' => $to, 'link_to' => "<a href='$to'> $to </a>")) : $dato;
			} else {
				$conf_final[$id_servicio] = $dato;
			}
			if ($conf_inicial->existe_entrada('conexion', 'cert_file')) {
				$conf_final[$id_servicio]['cert_file'] = basename($conf_inicial->get('conexion', 'cert_file'));
			}
			if ($conf_inicial->existe_entrada('conexion', 'key_file')) {
				$conf_final[$id_servicio]['key_file'] = basename($conf_inicial->get('conexion', 'key_file'));
			}
			if ($conf_inicial->existe_entrada('conexion', 'cert_CA')) {
				$conf_final[$id_servicio]['cert_CA'] = basename($conf_inicial->get('conexion', 'cert_CA'));
			}			
		}
		return $conf_final;
	}
	
	function get_tipos_autenticacion()
	{
		return array (array('id' => 0, 'nombre' => 'ssl'),array('id' => 1, 'nombre' =>  'digest'), array('id' =>2, 'nombre' =>  'basic'));
	}
}
?>