<?php
class ci_ws_logs extends toba_ci
{
	protected $s__filtro;
	protected $s__id_solicitud;
	protected $s__dir_logs_wsf;

	function ini__operacion()
	{
		if (! is_null(admin_instancia::get_proyecto_defecto())) {
			$this->s__filtro = array('proyecto' => admin_instancia::get_proyecto_defecto());
		}		
	}

	function ini()
	{
		$id_solicitud = toba::memoria()->get_parametro('auditoria_id_solicitud');
		if (isset($id_solicitud)) {
			$this->s__id_solicitud = $id_solicitud;
			$this->set_pantalla('pant_detalle');
		}		
	}
	
	function conf()
	{
		$this->s__dir_logs_wsf = ini_get('wsf.log_path');
		if ($this->s__dir_logs_wsf === false || trim($this->s__dir_logs_wsf) == '') {			//Si no esta activa la extension o no tiene valor la variable
			$this->pantalla()->evento('ver_log_server')->anular();				//Quito los vinculos
			$this->pantalla()->evento('ver_log_cliente')->anular();
		} else {								
			if (! file_exists("{$this->s__dir_logs_wsf}/wsf_php_server.log")) {		//Lo mismo si no existe alguno de los archivos
				$this->pantalla()->evento('ver_log_server')->anular();
			}
			if (! file_exists("{$this->s__dir_logs_wsf}/wsf_php_client.log")) {
				$this->pantalla()->evento('ver_log_cliente')->anular();
			}
		}		
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__volver()
	{
		$this->set_pantalla('pant_inicial');
	}
	
	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------	
	
	function conf__pant_detalle(toba_ei_pantalla $pantalla)
	{
		$datos =	consultas_instancia::get_solicitudes_web_service(array('solicitud' => $this->s__id_solicitud));
		
		if (! empty($datos)) {		
			$obs = consultas_instancia::get_solicitud_observaciones($this->s__id_solicitud);
			$datos = current($datos);
	
			$desc = 'Solicitud del proyecto <strong>'.$this->s__filtro['proyecto'].'</strong><br>';
			$desc .= 'IP: <strong>'.$datos['ip'].'</strong><br>';
			
			foreach ($obs as $observacion) {
				$desc .= 'Tipo: <strong>'.$observacion['descripcion'].'</strong><br>';		
				$desc .= 'Observaciones: <strong>'.$observacion['observacion'].'</strong><br>';
			}
			$this->pantalla()->set_descripcion($desc);	
			
			//Busco el archivo de log del pedido.
			$log = $this->archivo_log_ws($datos);
			if ($log !== false) {
				$pantalla->set_contenido_archivo_log($log);
			}

		}
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__filtro)) {			
			$datos =	consultas_instancia::get_solicitudes_web_service($this->s__filtro);
			$cuadro->set_datos($datos);
		}	

	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->s__id_solicitud = $seleccion['id'];
		$this->set_pantalla('pant_detalle');
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
		$this->s__filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
	}

	//--------------------------------------------------------------------------------------------------//
	function archivo_log_ws($datos)
	{
		$directorio = toba::instancia()->get_path_instalacion_proyecto($this->s__filtro['proyecto']).'/logs/web_services';		
		$nombre_archivo = $directorio."/web_services_{$datos['ip']}_{$this->s__id_solicitud}.log";

		if (file_exists($nombre_archivo)) {
			return file_get_contents($nombre_archivo);
		}
		return false;
	}

	//---------------------------------------------------------------------------------------------------//
	//				ARCHIVOS DE LOG WSF
	//---------------------------------------------------------------------------------------------------//
	function servicio__dl_log_server()			//Queda para el dia que se puedan acceder los archivos
	{
		$this->s__dir_logs_wsf = ini_get('wsf.log_path');
		toba::memoria()->desactivar_reciclado();				
		$nombre = 'wsf_php_server.log';
		$mime_type = 'text/plain';
		
		//Aca tengo que enviar los headers para el archivo y hacer el passthrough
		$archivo = $this->s__dir_logs_wsf.'/'.$nombre;
		$this->envio_archivo($archivo, $mime_type);
	}
	
	function servicio__dl_log_cliente()
	{
		$this->s__dir_logs_wsf = ini_get('wsf.log_path');
		toba::memoria()->desactivar_reciclado();				
		$nombre = 'wsf_php_client.log';
		$mime_type = 'text/plain';
		
		//Aca tengo que enviar los headers para el archivo y hacer el passthrough
		$archivo = $this->s__dir_logs_wsf.'/'.$nombre;
		$this->envio_archivo($archivo, $mime_type);
	}
	
	private function envio_archivo($archivo, $mime_type)
	{
		if (file_exists($archivo)) {
			$long = filesize($archivo);
			$handler = fopen($archivo, 'r');
			
			toba_http::headers_download($mime_type, basename($archivo), $long);
			fpassthru($handler);
			fclose($handler);
		}		
	}

}
?>