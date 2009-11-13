<?php 

class ci_proyecto extends toba_ci
{
	protected $iniciado = false;
	
	function ini()
	{
		if (!$this->iniciado) {
			$this->dependencia('datos')->cargar(array('proyecto'=>toba_editor::get_proyecto_cargado()));
			$this->iniciado = true;
		}	
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'iniciado';
		return $propiedades;
	}

	function evt__modificacion()
	{
		$nuevas = $this->dep('datos')->get();
		$this->dependencia('datos')->sincronizar();
		
		//---Actualiza los parámetros del proyecto en la sesión activa
		if (toba::manejador_sesiones()->existe_sesion_activa($nuevas['proyecto'])) {
			toba::manejador_sesiones()->recargar_info_proyecto($nuevas['proyecto']);
		}
	}

	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	//---- basica -------------------------------------------------------

	function evt__basica__modificacion($datos)
	{
		$this->dependencia('datos')->set($datos);
	}

	function conf__basica()
	{
		$datos = $this->dependencia('datos')->get();
		if (isset($datos['item_inicio_sesion'])) {
			$datos['carpeta_inicio_sesion'] = toba_info_editores::get_carpeta_de_item($datos['item_inicio_sesion'], 
																					$datos['proyecto']);
		}
		return $datos;
	}

	//---- login -------------------------------------------------------

	function evt__login__modificacion($datos)
	{
		$this->dependencia('datos')->set($datos);
	}

	function conf__login()
	{
		$datos = $this->dependencia('datos')->get();
		if (isset($datos['item_pre_sesion'])) {
			$datos['carpeta_pre_sesion'] = toba_info_editores::get_carpeta_de_item($datos['item_pre_sesion'], 
																				$datos['proyecto']);
		}
		return $datos;
	}

	//---- nucleo -------------------------------------------------------

	function evt__nucleo__modificacion($datos)
	{
		$this->dependencia('datos')->set($datos);
	}

	function conf__nucleo(toba_ei_formulario $form)
	{
		$parametros_edicion = array();
		$datos = $this->dependencia('datos')->get();
		if (isset($datos['item_set_sesion'])) {
			$datos['carpeta_set_sesion'] = toba_info_editores::get_carpeta_de_item($datos['item_set_sesion'], 
																				$datos['proyecto']);
		}

		//Cargo los datos basicos para el vinculo
		$parametros_edicion['proyecto_extension'] = $datos['proyecto'];
		$parametros_edicion['id_extension'] = $datos['proyecto'];

		// Coloco los iconos para el contexto de ejecucion
		$parametros_edicion['elemento_tipo'] = 'contexto_ejecucion';
		$parametros_edicion[apex_hilo_qs_menu] = '1';
		$iconos  = admin_util::get_ef_popup_utileria_extension_php($parametros_edicion);
		if (isset($datos['contexto_ejecucion_subclase_archivo']) && admin_util::existe_archivo_subclase($datos['contexto_ejecucion_subclase_archivo'])) {
				array_push($iconos, current(admin_util::get_ef_popup_utileria_abrir_php()));
		}
		$form->ef('ce_subclase_archivo')->set_iconos_utilerias($iconos);

		//Coloco los iconos para el campo sesion
		$parametros_edicion['elemento_tipo'] = 'sesion';
		$iconos  = admin_util::get_ef_popup_utileria_extension_php($parametros_edicion);
		if (isset($datos['sesion_subclase_archivo']) && admin_util::existe_archivo_subclase($datos['sesion_subclase_archivo'])) {
			array_push($iconos, current(admin_util::get_ef_popup_utileria_abrir_php()));
		}
		$form->ef('sesion_subclase_archivo')->set_iconos_utilerias($iconos);

		//Coloco los iconos para el campo usuario
		$parametros_edicion['elemento_tipo'] = 'usuario';
		$iconos  = admin_util::get_ef_popup_utileria_extension_php($parametros_edicion);
		if (isset($datos['usuario_subclase_archivo']) && admin_util::existe_archivo_subclase($datos['usuario_subclase_archivo'])) {
				array_push($iconos, current(admin_util::get_ef_popup_utileria_abrir_php()));
		}
		$form->ef('usuario_subclase_archivo')->set_iconos_utilerias($iconos);
		
		$form->ef('salida_impr_html_a')->set_iconos_utilerias(admin_util::get_ef_popup_utileria_php());
		$form->set_datos($datos);
	}

	//---- version -------------------------------------------------------
	
	function evt__version__modificacion($datos)
	{
		$this->dependencia('datos')->set($datos);
	}

	function conf__version()
	{
		return $this->dependencia('datos')->get();
	}
}
?>