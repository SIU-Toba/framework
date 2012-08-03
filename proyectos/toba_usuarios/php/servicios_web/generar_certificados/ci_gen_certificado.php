<?php
class ci_gen_certificado extends toba_ci
{
	protected $s__datos_proyecto;
	protected $s__checkea;
	protected $modelo_proyecto;
	
	function get_modelo_proyecto()
	{
		if (! isset($this->modelo_proyecto)) {
			$modelo = toba_modelo_catalogo::instanciacion();	
			$modelo->set_db(toba::db());	
			$this->modelo_proyecto = $modelo->get_proyecto(toba::instancia()->get_id(), $this->s__datos_proyecto['proyecto']);
		}
		return $this->modelo_proyecto;
	}
		
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	function servicio__descargame()
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
	
	//-----------------------------------------------------------------------------------
	//---- form -------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form(toba_ei_formulario $form)
	{	
		$proyecto = $this->get_modelo_proyecto();
		if ($this->verificar_existencia_private_key($proyecto)) {
			$this->s__datos_proyecto['cert_file'] = realpath(toba_modelo_servicio_web::path_clave_privada($proyecto));
			if ($this->s__checkea) {
				$form->agregar_notificacion('El archivo ya existe, si desea reemplazarlo indiquelo con el tilde');
			}
		} else {
			$this->s__datos_proyecto['cert_file'] = 'No definido' ;
		}
		
		$form->set_datos($this->s__datos_proyecto);
	}
	
	function evt__form__generar($datos)
	{	
		//Luego invocar los metodos que generan el certificado y la exportacion de clave publica.
		$proyecto = $this->get_modelo_proyecto();
		if ($this->verificar_existencia_private_key($proyecto) && $datos['reemplazar_existente'] == 0) {
			throw new toba_error_usuario('El archivo ya existe, no se permitira la generación a menos que se reemplace');
		}	
		
		try {
			toba_modelo_servicio_web::generar_certificados($proyecto);
		} catch (toba_error_usuario $e) {
			toba::logger()->error($e->getMessage());
			toba::notificacion()->agregar('Se ha producido un error generando el certificado, verifique los logs' , 'error');
		}
		$this->s__checkea = false;
	}

	function verificar_existencia_private_key($proyecto)
	{
		return toba_modelo_servicio_web::existe_archivo_certificado($proyecto);
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$cuadro->set_datos(consultas_instancia::get_lista_proyectos());
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->s__datos_proyecto = $seleccion;
		$this->set_pantalla('pant_edicion');
	}
	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__pant_inicial__salida()
	{
		$this->s__checkea = true;
	}

	function evt__volver()
	{
		$this->set_pantalla('pant_inicial');
		unset($this->s__datos_proyecto);
	}

}
?>