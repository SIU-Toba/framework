<?php 
require_once('ci_abm_basico.php');

class ci_tipos_pagina extends ci_abm_basico
{
	private$_parametros_edicion = array();
	
	function get_datos_listado()
	{
		return toba_info_editores::get_tipos_pagina_proyecto();
	}

	function evt__cuadro__seleccion($seleccion)
	{
		parent::evt__cuadro__seleccion($seleccion);

		$this->_parametros_edicion['proyecto_extension'] = $seleccion['proyecto'];
		$this->_parametros_edicion['id_extension'] = $seleccion['pagina_tipo'];
		$this->_parametros_edicion['elemento_tipo'] = 'tipo_pagina';
		$this->_parametros_edicion[apex_hilo_qs_menu] = '1';			//Limpio la memoria de la operacion para que no queden cosas cargadas
	}

	function conf__formulario(toba_ei_formulario $form)
	{
		if ( $this->dependencia('datos')->hay_cursor() ) {
			$datos = $this->dependencia('datos')->get();
			$form->set_datos($datos);
			//Seteo los iconos apropiados para el ef_popup
			$iconos = admin_util::get_ef_popup_utileria_extension_php($this->_parametros_edicion);			
			if ( isset($datos['clase_archivo']) &&  admin_util::existe_archivo_subclase($datos['clase_archivo'])) {
				array_push($iconos, current(admin_util::get_ef_popup_utileria_abrir_php()));
			}
			$form->ef('clase_archivo')->set_iconos_utilerias($iconos);
		}
	}
}

?>