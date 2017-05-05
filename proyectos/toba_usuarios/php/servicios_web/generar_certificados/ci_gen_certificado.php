<?php
class ci_gen_certificado extends toba_ci
{
	protected $s__datos_proyecto;
	protected $modelo_proyecto;
	private $existe_archivo = false;
	private $pisa_archivo = false;
	
	function ini()
	{
		$proy_defecto = admin_instancia::get_proyecto_defecto();
		if (! is_null($proy_defecto) && ! isset($this->s__datos_proyecto)) {
			$this->s__datos_proyecto = array('proyecto' => $proy_defecto);
		}		
	}
	
	function get_modelo_proyecto()
	{
		if (! isset($this->modelo_proyecto)) {
			$modelo = toba_modelo_catalogo::instanciacion();	
			$modelo->set_db(toba::db());	
			$this->modelo_proyecto = $modelo->get_proyecto(toba::instancia()->get_id(), $this->s__datos_proyecto['proyecto']);
		}
		return $this->modelo_proyecto;
	}

	
	function conf__pant_edicion(toba_ei_pantalla $pantalla)
	{
		$proyecto = $this->get_modelo_proyecto();
		$this->existe_archivo = $this->verificar_existencia_private_key($proyecto);
		
		//Si el archivo con la clave aun no existe, quito al diablo el form de arriba.
		if (! $this->existe_archivo) {
			$pantalla->eliminar_dep('form_muestra');			
			$pantalla->eliminar_dep('form');
			$pantalla->set_descripcion('Aún no se ha configurada la Clave privada y el Certificado público del proyecto');
		} else {
			$pantalla->evento('generar')->set_etiqueta('Descartar y &Generar');
		}
	}

	function evt__generar()
	{	
		//Luego invocar los metodos que generan el certificado y la exportacion de clave publica.
		$proyecto = $this->get_modelo_proyecto();
		if ($this->verificar_existencia_private_key($proyecto) && ! $this->pisa_archivo) {
			throw new toba_error_usuario('El archivo ya existe, no se permitira la generación a menos que se reemplace');
		}	
		
		try {
			toba_modelo_servicio_web::generar_certificados($proyecto);
		} catch (toba_error_usuario $e) {
			toba::logger()->error($e->getMessage());
			toba::notificacion()->agregar('Se ha producido un error generando el certificado, verifique los logs', 'error');
		}
	}

	function evt__volver()
	{
		$this->set_pantalla('pant_inicial');
		unset($this->s__datos_proyecto);
	}
	
	//-----------------------------------------------------------------------------------
	//---- form -------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form(toba_ei_formulario $form)
	{	
		$form->agregar_notificacion('El archivo ya existe, si desea reemplazarlo indiquelo con el tilde, tenga en cuenta que los clientes/servidores que consuman este certificado dejarán de funcionar');
	}
	
	function evt__form__modificacion($datos)
	{
		$this->pisa_archivo = (isset($datos['reemplazar_existente']) && $datos['reemplazar_existente'] == '1') ? true : false;
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(form_proyecto $form)
	{
		if (isset($this->s__datos_proyecto)) {
			$form->set_datos($this->s__datos_proyecto);
		}
	}

	function evt__filtro__filtrar($datos)
	{
		$this->s__datos_proyecto = $datos;
		$this->set_pantalla('pant_edicion');
	}
	//-----------------------------------------------------------------------------------
	//---- form_muestra -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_muestra(toba_ei_formulario $form)
	{
		$proyecto = $this->get_modelo_proyecto();
		$datos['clave_privada'] = realpath(toba_modelo_servicio_web::path_clave_privada($proyecto));
		$datos['clave_publica'] = realpath(toba_modelo_servicio_web::path_clave_publica($proyecto));
		$form->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
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

	function verificar_existencia_private_key($proyecto)
	{
		return toba_modelo_servicio_web::existe_archivo_certificado($proyecto);
	}

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		if ($this->existe_archivo) {			
			$msg = 'Confirme que desea reemplazar la clave privada / certificado publico actual. Todos los clientes/servidores que consuman este certificado dejarán de funcionar';
			echo toba::escaper()->escapeJs($this->objeto_js) .
			".evt__generar = function()
			{
				var valor = this.dep('form').ef('reemplazar_existente').chequeado();
				if (! valor && confirm('$msg')) {
					this.dep('form').ef('reemplazar_existente').chequear(true, false);
				}
			}
			";
		}
	}


}
?>