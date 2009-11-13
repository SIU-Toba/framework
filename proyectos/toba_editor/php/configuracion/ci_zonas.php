<?php 
require_once('ci_abm_basico.php');

class ci_zonas extends ci_abm_basico
{
	private$_parametros_edicion = array();
	
	function get_datos_listado()
	{
		$sql = 'SELECT proyecto, zona, nombre
				FROM apex_item_zona
				WHERE proyecto = ' .quote(toba_editor::get_proyecto_cargado());
		return toba::db()->consultar($sql);
	}

	function evt__cuadro__seleccion($seleccion)
	{
		parent::evt__cuadro__seleccion($seleccion);
		
		$this->_parametros_edicion['proyecto_extension'] = $seleccion['proyecto'];
		$this->_parametros_edicion['id_extension'] = $seleccion['zona'];
		$this->_parametros_edicion['elemento_tipo'] = 'zona';
		$this->_parametros_edicion[apex_hilo_qs_menu] = '1';			//Limpio la memoria de la operacion para que no queden cosas cargadas
	}
	
	function conf__formulario(toba_ei_formulario $form)
	{
		if ( $this->dependencia('datos')->hay_cursor() ) {
			$datos = $this->dependencia('datos')->get();
			$form->set_datos($datos);

			//Agrego los iconos para editar y abrir el archivo de la subclase
			$iconos = admin_util::get_ef_popup_utileria_extension_php($this->_parametros_edicion);
			if ( isset($datos['archivo']) && admin_util::existe_archivo_subclase($datos['archivo'])) {
				array_push($iconos, current(admin_util::get_ef_popup_utileria_abrir_php()));
			}
			$form->ef('archivo')->set_iconos_utilerias($iconos);
			$form->ef('consulta_archivo')->set_iconos_utilerias(admin_util::get_ef_popup_utileria_php());
		}
	}
}

?>