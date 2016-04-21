<?php
class ci_servicios_ofrecidos extends toba_ci
{
	protected $cert;
	protected $s__auth_ssl;
	protected $s__filtro;
	protected $s__datos;

	protected $modelo_proyecto;
	
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
		$modelo = $this->get_modelo_proyecto();
		$ini = toba_modelo_rest::get_ini_server($modelo);
		$this->s__auth_ssl = ($ini->existe_entrada('autenticacion') && $ini->get('autenticacion') == 'ssl');
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

		
	//----------------------- PANTALLA EDICION ---------------------//	
	//-----------------------------------------------------------------------------------
	//---- cuadro_sel_conf --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_sel_conf(toba_ei_cuadro $cuadro)
	{
		$disponibles = $this->recuperar_clientes_configurados();
		$cuadro->set_datos($disponibles);
	}

	function evt__cuadro_sel_conf__agregar()
	{
		$this->set_pantalla('pant_edicion');		
	}
	
	function evt__cuadro_sel_conf__eliminar($seleccion)
	{
		$this->eliminar_config($seleccion['usuario']);
	}
	
	//-----------------------------------------------------------------------------------
	//---- form_basico ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_basico(toba_ei_formulario $form)
	{
		if ($this->s__auth_ssl) {
			$efs = array('usuario', 'password');
		} else {
			$efs = array('cert_file');
		}
		$form->desactivar_efs($efs);
	}

	function evt__form_basico__modificacion($datos)
	{
		$this->s__datos = $datos;
		if ($this->s__auth_ssl) {
			if (isset($datos['cert_file']) && ($datos['cert_file']['error'] == 0)) {		//Si se subio un archivo de certificado
				$nombre_archivo = toba_manejador_archivos::nombre_valido($datos['cert_file']['name']);
				$this->cert = toba::proyecto()->get_www_temp($nombre_archivo);				
				move_uploaded_file($datos['cert_file']['tmp_name'], $this->cert['path']);
			}
		}
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function guardar_config()
	{	
		$proyecto = $this->get_modelo_proyecto();		
		//Copiar los certificados a otro lugar
		$usr = (isset($this->s__datos['usuario'])) ? $this->s__datos['usuario'] : null;
		$usr_pwd = (isset($this->s__datos['password'])) ? $this->s__datos['password'] : null;
		$tipo_auth = ($this->s__auth_ssl) ? 'ssl' : 'digest';
		
		$dir_base = toba_modelo_rest::get_dir_proveedor($proyecto->get_dir_instalacion_proyecto());
		$cert = null;		
		if (isset($this->cert['path'])) {
			$cert = $dir_base. '/'.  basename($this->cert['path']);
			rename($this->cert['path'], $cert);
		}
		$rest = new toba_modelo_rest($proyecto);
		$rest->generar_configuracion_servidor($usr, $usr_pwd, $cert, $tipo_auth);
	}
		
	function eliminar_config($usuario)
	{
		$proyecto = $this->get_modelo_proyecto();							
		$ini = toba_modelo_rest::get_ini_usuarios($proyecto);
		$ini->existe_entrada($usuario);
		$ini->eliminar_entrada($usuario);
		$ini->guardar();
	}
		
	function evt__procesar()
	{	
		$this->guardar_config();
		$this->finalizar_operacion();
		$this->set_pantalla('pant_inicial');		
	}

	function evt__cancelar()
	{
		$this->finalizar_operacion();
		$this->set_pantalla('pant_inicial');
	}
		
	function finalizar_operacion()
	{
		if (isset($this->cert) && file_exists($this->cert['path'])) {
			unlink($this->cert['path']);
			$this->cert = null;
		}
		$this->s__datos = array();
		unset($this->s__auth_ssl);
	}
		
	protected function recuperar_clientes_configurados()
	{	
		$datos = array();
		$proyecto = $this->get_modelo_proyecto();
		$campo = (isset($this->s__auth_ssl) && $this->s__auth_ssl) ? 'fingerprint' : 'password';				//Defino que campo buscar de acuerdo al tipo de autenticacion		
		
		$ini_usr = toba_modelo_rest::get_ini_usuarios($proyecto);
		$entradas = $ini_usr->get_entradas();
		foreach ($entradas as $clave => $valores) {
			if (is_array($valores) && isset($valores[$campo])) {
				$datos[] = array('usuario' => $clave);
			}
		}
		return $datos;
	}	
}
?>